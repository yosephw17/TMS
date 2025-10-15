<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Project;
use App\Models\Material;
use App\Models\Stock;
use App\Services\NotificationService;
use App\Services\ProjectCostService;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    protected $notificationService;
    protected $projectCostService;

    public function __construct(NotificationService $notificationService, ProjectCostService $projectCostService)
    {
        // Apply authentication middleware
        $this->middleware('auth');
        $this->notificationService = $notificationService;
        $this->projectCostService = $projectCostService;

        $this->middleware('permission:manage-purchase-request', ['only' => ['index']]);
        $this->middleware('permission:purchase-request-view', ['only' => ['show']]);
        $this->middleware('permission:purchase-request-create', ['only' => ['store']]);
        $this->middleware('permission:purchase-request-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase-request-delete', ['only' => ['destroy']]);
        $this->middleware('permission:purchase-request-approve', ['only' => ['approve']]);
        $this->middleware('permission:purchase-request-decline', ['only' => ['decline']]);
    }

    public function index()
    {
        $user = auth()->user();
        $materials = Material::all();



        $projects = Project::whereHas('customer', function($query) {
            $query->where('type', 'project');
        })->where('status', 'pending')->get();

        $stocks = Stock::all();
        
        if ($user->hasRole('Admin')) {
            $purchaseRequests = PurchaseRequest::with('project', 'materials')->get();
        } else {
            $purchaseRequests = PurchaseRequest::where('user_id', $user->id)->get();
        }   
        
        return view('purchase_requests.index', compact('purchaseRequests','projects','stocks','materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|in:material_stock,material_non_stock,labour,transport',
            'stock_id' => 'nullable|exists:stocks,id',
            'materials' => 'nullable|array',
            'materials.*.quantity' => 'nullable|integer|min:1',
            'non_stock_name' => 'nullable|required_if:type,material_non_stock|string|max:255',
            'non_stock_price' => 'nullable|required_if:type,material_non_stock|numeric|min:0',
            'labour_transport_price' => 'nullable|required_if:type,labour,transport|numeric|min:0',
            'non_stock_quantity' => 'nullable|required_if:type,material_non_stock|integer|min:1',
            'details' => 'nullable|required_if:type,labour,transport|string|max:1000',
        ]);
    
        $price = null;
        if (in_array($request->type, ['labour', 'transport'])) {
            $price = $request->labour_transport_price;
        } elseif ($request->type === 'material_non_stock') {
            $price = $request->non_stock_price;
        }
    
        $purchaseRequest = PurchaseRequest::create([
            'project_id' => $request->project_id,
            'stock_id' => $request->stock_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'non_stock_name' => $request->non_stock_name,
            'non_stock_price' => $price,
            'non_stock_quantity' => $request->non_stock_quantity,
            'non_stock_image' => $request->file('non_stock_image')
                                 ? $request->file('non_stock_image')->store('non_stock_materials', 'public')
                                 : null,
            'details' => $request->details,
        ]);
    
        if ($request->type === 'material_stock' && $request->has('materials')) {
            foreach ($request->materials as $materialId => $materialData) {
                if (isset($materialData['selected'])) {
                    if (empty($materialData['quantity'])) {
                        return redirect()->back()->withErrors([
                            'materials' => 'Quantity must be provided for selected materials.',
                        ])->withInput();
                    }
                    
                    $purchaseRequest->materials()->attach($materialId, [
                        'quantity' => $materialData['quantity']
                    ]);
                }
            }
        }

        // Create notification for new purchase request
        $project = Project::find($request->project_id);
        $itemName = $this->getPurchaseRequestItemName($purchaseRequest);
        
        $this->notificationService->createForUsersWithNotificationPermission('purchase_request_created', [
            'type' => 'purchase_request_created',
            'message' => "New purchase request for '{$itemName}' requires approval (Project: {$project->name})",
            'action_url' => route('purchase_requests.index'),
            'data' => [
                'purchase_request_id' => $purchaseRequest->id,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'item_name' => $itemName,
                'type' => $request->type,
                'price' => $price
            ],
            'created_by' => auth()->id()
        ]);
    
        return redirect()->route('purchase_requests.index')->with('success', 'Purchase request created successfully.');
    }

    public function approve($id)
    {
        try {
            $purchaseRequest = null;
    
            DB::transaction(function () use ($id, &$purchaseRequest) {
                $purchaseRequest = PurchaseRequest::with('materials')->findOrFail($id);
    
                // Prevent double approval
                if ($purchaseRequest->status === 'approved') {
                    throw new \Exception("Purchase request is already approved.");
                }
    
                // Handle materials if applicable
                if ($purchaseRequest->type === 'material_stock') {
                    foreach ($purchaseRequest->materials as $material) {
                        // Use our improved FIFO-Average function
                        $this->processFIFODeduction($purchaseRequest, $material);
                    }
                }
    
                // Mark as approved
                $purchaseRequest->update(['status' => 'approved']);
    
                // Send notification
                $itemName = $this->getPurchaseRequestItemName($purchaseRequest);
    
                $this->notificationService->createForUsersWithNotificationPermission('purchase_request_approved', [
                    'type'       => 'purchase_request_approved',
                    'message'    => "Purchase request for '{$itemName}' has been approved",
                    'action_url' => route('purchase_requests.index'),
                    'data'       => [
                        'purchase_request_id' => $purchaseRequest->id,
                        'item_name'           => $itemName,
                        'approved_by'         => auth()->user()->name
                    ],
                    'created_by' => auth()->id()
                ]);
            });
    
            // Update project cost after approval
            if ($purchaseRequest) {
                $this->projectCostService->updateProjectCostsOnApproval($purchaseRequest);
            }
    
            return redirect()->back()->with('success', 'Purchase Request approved successfully.');
    
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    
    private function processFIFODeduction($purchaseRequest, $material)
    {
        $requiredQuantity = $material->pivot->quantity;
    
        // Prevent re-pricing if already processed
        if ($material->pivot->weighted_avg_price !== null) {
            \Log::info("Skipping FIFO processing for already priced material", [
                'purchase_request_id' => $purchaseRequest->id,
                'material_id'         => $material->id,
                'existing_price'      => $material->pivot->weighted_avg_price
            ]);
            return;
        }
    
        /**
         * STEP 1: Get all stock entries for this material in FIFO order
         */
        $stockEntries = $material->stocks()
            ->wherePivot('stock_id', $purchaseRequest->stock_id)
            ->wherePivot('remaining_quantity', '>', 0)
            ->wherePivot('status', 'active')
            ->orderBy('material_stock.created_at', 'asc')       // ensure FIFO order
            ->get();
    
        if ($stockEntries->sum('pivot.remaining_quantity') < $requiredQuantity) {
            throw new \Exception(
                "Insufficient stock for material: {$material->name}. Required: {$requiredQuantity}"
            );
        }
    
        /**
         * STEP 2: Compute simple average price from ALL available stock entries (not weighted by quantity)
         */
        $totalPrice = 0;
        $entryCount = 0;
    
        foreach ($stockEntries as $entry) {
            $totalPrice += $entry->pivot->unit_price;
            $entryCount++;
        }
    
        $averagePrice = $entryCount > 0 ? round($totalPrice / $entryCount, 2) : 0;
        
        // Debug logging to understand what's happening
        \Log::info("Simple Average Price Calculation Debug", [
            'material_id' => $material->id,
            'material_name' => $material->name,
            'stock_entries_count' => $stockEntries->count(),
            'stock_entries_details' => $stockEntries->map(function($entry) {
                return [
                    'id' => $entry->pivot->id,
                    'unit_price' => $entry->pivot->unit_price,
                    'remaining_quantity' => $entry->pivot->remaining_quantity,
                ];
            })->toArray(),
            'total_price_sum' => $totalPrice,
            'entry_count' => $entryCount,
            'calculated_simple_average' => $averagePrice,
            'note' => 'Simple average of unit prices (not weighted by quantity)'
        ]);
    
        /**
         * STEP 3: Deduct quantity in FIFO order but charge at average price
         */
        $remaining     = $requiredQuantity;
        $consumedInfo  = [];
    
        foreach ($stockEntries as $entry) {
            if ($remaining <= 0) break;
    
            $deduct = min($remaining, $entry->pivot->remaining_quantity);
            $remaining -= $deduct;
    
            // Record for logging
            $consumedInfo[] = [
                'quantity'    => $deduct,
                'unit_price'  => $entry->pivot->unit_price,
                'entry_cost'  => $deduct * $entry->pivot->unit_price,
                'charged_cost' => $deduct * $averagePrice // Cost actually charged to project
            ];
    
            // Deduct actual quantity from the stock entry
            $this->updateStockEntry([
                'entry'              => $entry,
                'quantity_to_deduct' => $deduct,
                'unit_price'         => $entry->pivot->unit_price,
                'total_cost'         => $deduct * $averagePrice, // Use average price for cost calculation
            ], $purchaseRequest);
        }
    
        /**
         * STEP 4: Save to pivot
         */
        $purchaseRequest->materials()->updateExistingPivot($material->id, [
            'total_cost'         => $averagePrice * $requiredQuantity,
            'weighted_avg_price' => $averagePrice
        ]);
        
        // Debug: Verify the pivot update
        \Log::info("Pivot Update Debug", [
            'purchase_request_id' => $purchaseRequest->id,
            'material_id' => $material->id,
            'average_price' => $averagePrice,
            'required_quantity' => $requiredQuantity,
            'total_cost_saved' => $averagePrice * $requiredQuantity,
            'weighted_avg_price_saved' => $averagePrice
        ]);
    
        \Log::info("FIFO Deduction with Average Cost Charging", [
            'purchase_request_id' => $purchaseRequest->id,
            'material_name'       => $material->name,
            'required_quantity'   => $requiredQuantity,
            'consumed_entries'    => $consumedInfo,
            'average_price'       => $averagePrice,
            'final_cost_charged'  => $averagePrice * $requiredQuantity,
            'note' => 'Physical deduction follows FIFO, but project is charged at average price'
        ]);
    }
    
    
    
    public function decline($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);
        $purchaseRequest->update(['status' => 'rejected']);

        // Create notification for decline
        $itemName = $this->getPurchaseRequestItemName($purchaseRequest);
        
        $this->notificationService->createForUsersWithNotificationPermission('purchase_request_rejected', [
            'type' => 'purchase_request_rejected',
            'message' => "Purchase request for '{$itemName}' has been declined",
            'action_url' => route('purchase_requests.index'),
            'data' => [
                'purchase_request_id' => $purchaseRequest->id,
                'item_name' => $itemName,
                'declined_by' => auth()->user()->name
            ],
            'created_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Purchase request declined.');
    }

    /**
     * Process FIFO stock deduction for a material in purchase request
     */
 
    /**
     * Update individual stock entry with FIFO deduction
     */
    private function updateStockEntry($fifoEntry, $purchaseRequest)
    {
        $entry = $fifoEntry['entry'];
        $quantityToDeduct = $fifoEntry['quantity_to_deduct'];
        $unitPrice = $fifoEntry['unit_price'];
        $totalCost = $fifoEntry['total_cost'];
        
        $currentRemaining = $entry->pivot->remaining_quantity;
        $newRemaining = $currentRemaining - $quantityToDeduct;
        $newTotalUsed = $entry->pivot->total_used + $quantityToDeduct;
        $newTotalUsedValue = $entry->pivot->total_used_value + $totalCost;
        
        // Update movement log
        $movementLog = json_decode($entry->pivot->movement_log, true) ?? [];
        $movementLog[] = [
            'type' => 'purchase_request_deduction',
            'quantity' => $quantityToDeduct,
            'unit_price' => $unitPrice,
            'total_cost' => $totalCost,
            'remaining_after' => $newRemaining,
            'purchase_request_id' => $purchaseRequest->id,
            'project_id' => $purchaseRequest->project_id,
            'timestamp' => now(),
            'user' => auth()->user()->name,
            'notes' => "FIFO deduction for purchase request #{$purchaseRequest->id}"
        ];

        // Determine new status and current total value
        $status = $newRemaining <= 0 ? 'depleted' : 'active';
        $newCurrentTotalValue = $newRemaining * $unitPrice;

        // Update the material stock entry
        DB::table('material_stock')
            ->where('id', $entry->pivot->id)
            ->update([
                'remaining_quantity' => $newRemaining,
                'total_used' => $newTotalUsed,
                'total_used_value' => $newTotalUsedValue,
                'current_total_value' => $newCurrentTotalValue,
                'status' => $status,
                'last_movement_at' => now(),
                'movement_log' => json_encode($movementLog)
            ]);
    }

    private function getPurchaseRequestItemName($purchaseRequest)
    {
        switch ($purchaseRequest->type) {
            case 'material_non_stock':
                return $purchaseRequest->non_stock_name;
            case 'labour':
                return 'Labour Services';
            case 'transport':
                return 'Transport Services';
            case 'material_stock':
                return 'Stock Materials';
            default:
                return 'Purchase Item';
        }
    }
}