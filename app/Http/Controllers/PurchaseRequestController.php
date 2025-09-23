<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Project;
use App\Models\Material;
use App\Models\Stock;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        // Apply authentication middleware
        $this->middleware('auth');
        $this->notificationService = $notificationService;

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
        $projects = Project::where('status', 'pending')->get();
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
        
        $this->notificationService->create([
            'type' => 'purchase_request_created',
            'message' => "New purchase request for '{$itemName}' requires approval (Project: {$project->name})",
            'user_id' => auth()->id(),
            'action_url' => route('purchase_requests.index'),
            'data' => [
                'purchase_request_id' => $purchaseRequest->id,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'item_name' => $itemName,
                'type' => $request->type,
                'price' => $price
            ]
        ]);
    
        return redirect()->route('purchase_requests.index')->with('success', 'Purchase request created successfully.');
    }

    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchaseRequest = PurchaseRequest::with('materials')->findOrFail($id);

                if ($purchaseRequest->type === 'material_stock') {
                    foreach ($purchaseRequest->materials as $material) {
                        $stockMaterial = DB::table('material_stock')
                            ->where('material_id', $material->id)
                            ->where('stock_id', $purchaseRequest->stock_id)
                            ->first();

                        if (!$stockMaterial || $stockMaterial->quantity < $material->pivot->quantity) {
                            throw new \Exception("Insufficient stock for material: {$material->name}");
                        }

                        DB::table('material_stock')
                            ->where('material_id', $material->id)
                            ->where('stock_id', $purchaseRequest->stock_id)
                            ->update([
                                'quantity' => $stockMaterial->quantity - $material->pivot->quantity,
                            ]);
                    }
                }

                $purchaseRequest->update(['status' => 'approved']);

                // Create notification for approval
                $itemName = $this->getPurchaseRequestItemName($purchaseRequest);
                
                $this->notificationService->create([
                    'type' => 'purchase_request_approved',
                    'message' => "Purchase request for '{$itemName}' has been approved",
                    'user_id' => $purchaseRequest->user_id,
                    'action_url' => route('purchase_requests.index'),
                    'data' => [
                        'purchase_request_id' => $purchaseRequest->id,
                        'item_name' => $itemName,
                        'approved_by' => auth()->user()->name
                    ]
                ]);
            });

            return redirect()->back()->with('success', 'Purchase Request approved successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function decline($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);
        $purchaseRequest->update(['status' => 'declined']);

        // Create notification for decline
        $itemName = $this->getPurchaseRequestItemName($purchaseRequest);
        
        $this->notificationService->create([
            'type' => 'purchase_request_rejected',
            'message' => "Purchase request for '{$itemName}' has been declined",
            'user_id' => $purchaseRequest->user_id,
            'action_url' => route('purchase_requests.index'),
            'data' => [
                'purchase_request_id' => $purchaseRequest->id,
                'item_name' => $itemName,
                'declined_by' => auth()->user()->name
            ]
        ]);

        return redirect()->back()->with('success', 'Purchase request declined.');
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
