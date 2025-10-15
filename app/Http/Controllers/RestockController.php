<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestockEntry;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Stock;
use App\Services\ProjectCostService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RestockController extends Controller
{
    protected $projectCostService;
    protected $notificationService;

    public function __construct(ProjectCostService $projectCostService, NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->projectCostService = $projectCostService;
        $this->notificationService = $notificationService;
        
        // Add permissions
        // $this->middleware('permission:restock-create', ['only' => ['store']]);
        // $this->middleware('permission:restock-approve', ['only' => ['approve', 'reject']]);
        // $this->middleware('permission:restock-view', ['only' => ['index', 'show']]);
    }

    /**
     * Display restock entries for a project
     */
    public function index(Request $request)
    {
        $query = RestockEntry::with(['project', 'material', 'stock', 'restockedBy', 'approvedBy']);
        
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $restockEntries = $query->orderBy('created_at', 'desc')->paginate(20);
        $projects = Project::all();
        
        return view('restock.index', compact('restockEntries', 'projects'));
    }

    /**
     * Store a new restock entry
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'material_id' => 'required|exists:materials,id',
            'stock_id' => 'required|exists:stocks,id',
            'quantity_restocked' => 'required|numeric|min:0.001',
            'unit_price' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $project = Project::findOrFail($request->project_id);
                $material = Material::findOrFail($request->material_id);
                $purchaseRequest = PurchaseRequest::findOrFail($request->purchase_request_id);
                
                // Calculate total cost to be deducted
                $totalCostDeducted = $request->quantity_restocked * $request->unit_price;
                
                // Generate reference number
                $restockReference = RestockEntry::generateRestockReference($project, $material);
                
                // Store original purchase data for audit
                $originalPurchaseData = [
                    'purchase_request_id' => $purchaseRequest->id,
                    'original_quantity' => $purchaseRequest->materials()
                        ->where('material_id', $material->id)
                        ->first()->pivot->quantity ?? 0,
                    'original_cost' => $purchaseRequest->materials()
                        ->where('material_id', $material->id)
                        ->first()->pivot->total_cost ?? 0,
                    'purchase_date' => $purchaseRequest->created_at,
                    'requested_by' => $purchaseRequest->user->name
                ];
                
                $restockEntry = RestockEntry::create([
                    'project_id' => $request->project_id,
                    'purchase_request_id' => $request->purchase_request_id,
                    'material_id' => $request->material_id,
                    'stock_id' => $request->stock_id,
                    'restocked_by' => Auth::id(),
                    'quantity_restocked' => $request->quantity_restocked,
                    'unit_price' => $request->unit_price,
                    'total_cost_deducted' => $totalCostDeducted,
                    'restock_reference' => $restockReference,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'original_purchase_data' => $originalPurchaseData,
                    'status' => 'pending'
                ]);

                // Create notification for approval
                $this->notificationService->create([
                    'type' => 'restock_pending_approval',
                    'message' => "Restock request #{$restockReference} requires approval for {$material->name} (Project: {$project->name})",
                    'user_id' => Auth::id(),
                    'action_url' => route('restock.show', $restockEntry->id),
                    'data' => [
                        'restock_id' => $restockEntry->id,
                        'project_name' => $project->name,
                        'material_name' => $material->name,
                        'quantity' => $request->quantity_restocked,
                        'total_cost' => $totalCostDeducted,
                        'reference' => $restockReference
                    ]
                ]);
            });

            return redirect()->back()->with('success', 'Restock request submitted successfully and is pending approval.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error creating restock entry: ' . $e->getMessage());
        }
    }

    /**
     * Show specific restock entry
     */
    public function show(RestockEntry $restockEntry)
    {
        $restockEntry->load(['project', 'material', 'stock', 'restockedBy', 'approvedBy', 'purchaseRequest']);
        
        return view('restock.show', compact('restockEntry'));
    }

    /**
     * Approve restock entry
     */
    public function approve(RestockEntry $restockEntry)
    {
        try {
            DB::transaction(function () use ($restockEntry) {
                $restockEntry->approve(Auth::id());
                
                // Create approval notification
                $this->notificationService->create([
                    'type' => 'restock_approved',
                    'message' => "Restock request #{$restockEntry->restock_reference} has been approved",
                    'user_id' => $restockEntry->restocked_by,
                    'action_url' => route('restock.show', $restockEntry->id),
                    'data' => [
                        'restock_id' => $restockEntry->id,
                        'project_name' => $restockEntry->project->name,
                        'material_name' => $restockEntry->material->name,
                        'approved_by' => Auth::user()->name
                    ]
                ]);
            });

            return redirect()->back()->with('success', 'Restock entry approved successfully. Stock has been updated and project costs adjusted.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error approving restock: ' . $e->getMessage());
        }
    }

    /**
     * Reject restock entry
     */
    public function reject(Request $request, RestockEntry $restockEntry)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            $restockEntry->reject(Auth::id(), $request->rejection_reason);
            
            // Create rejection notification
            $this->notificationService->create([
                'type' => 'restock_rejected',
                'message' => "Restock request #{$restockEntry->restock_reference} has been rejected",
                'user_id' => $restockEntry->restocked_by,
                'action_url' => route('restock.show', $restockEntry->id),
                'data' => [
                    'restock_id' => $restockEntry->id,
                    'project_name' => $restockEntry->project->name,
                    'material_name' => $restockEntry->material->name,
                    'rejected_by' => Auth::user()->name,
                    'reason' => $request->rejection_reason
                ]
            ]);

            return redirect()->back()->with('success', 'Restock entry rejected.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error rejecting restock: ' . $e->getMessage());
        }
    }

  /**
 * Get materials for a specific purchase request (AJAX)
 */
public function getMaterialsForPurchaseRequest(PurchaseRequest $purchaseRequest)
{
    try {
        $materials = $purchaseRequest->materials()
            ->get()
            ->map(function ($material) use ($purchaseRequest) {
                // Use the simple average price stored in pivot table
                $weightedPrice = $material->pivot->weighted_avg_price ?? 0;

                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'unit_of_measurement' => $material->unit_of_measurement,
                    'quantity_used' => $material->pivot->quantity ?? 0,
                    'weighted_avg_price' => $weightedPrice,
                    'stock_id' => $purchaseRequest->stock_id
                ];
            });

        return response()->json($materials);
        
    } catch (\Exception $e) {
        \Log::error('Error loading materials for purchase request: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to load materials',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get restock analytics for dashboard
     */
    public function getAnalytics()
    {
        $analytics = [
            'total_restock_entries' => RestockEntry::count(),
            'pending_approvals' => RestockEntry::pending()->count(),
            'approved_this_month' => RestockEntry::approved()
                ->whereMonth('approved_at', now()->month)
                ->count(),
            'total_cost_recovered' => RestockEntry::approved()->sum('total_cost_deducted'),
            'top_restocked_materials' => RestockEntry::approved()
                ->select('material_id', DB::raw('SUM(quantity_restocked) as total_quantity'))
                ->with('material')
                ->groupBy('material_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json($analytics);
    }
}
