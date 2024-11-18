<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Project;
use App\Models\Material;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
class PurchaseRequestController extends Controller
{
    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

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
        $materials=Material::all();
        $projects=Project::where('status', 'pending')->get();
        $stocks=Stock::all();
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
            'materials.*.quantity' => 'nullable|integer|min:1', // Quantity for stock materials must be integer
            'non_stock_name' => 'nullable|required_if:type,material_non_stock|string|max:255',
            'non_stock_price' => 'nullable|required_if:type,material_non_stock|numeric|min:0', // Non-stock price must be numeric
            'labour_transport_price' => 'nullable|required_if:type,labour,transport|numeric|min:0', // Price for labour/transport
            'non_stock_quantity' => 'nullable|required_if:type,material_non_stock|integer|min:1', // Non-stock quantity must be integer
            'details' => 'nullable|required_if:type,labour,transport|string|max:1000', // Details for labour/transport must be string
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
    
        return redirect()->route('purchase_requests.index')->with('success', 'Purchase request submitted successfully.');
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
        });

        return redirect()->back()->with('success', 'Purchase Request approved successfully.');

    } catch (\Exception $e) {
        return redirect()->back()->withErrors($e->getMessage());
    }
}


public function decline($id)
{
    $purchaseRequest = PurchaseRequest::findOrFail($id);

    $purchaseRequest->update(['status' => 'rejected']);

    return redirect()->back()->with('success', 'Purchase Request declined.');
}

}
