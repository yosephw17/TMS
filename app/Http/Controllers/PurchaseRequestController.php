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
    //
    public function index()
    {
        $materials=Material::all();
        $projects=Project::where('status', 'pending')->get();
        $stocks=Stock::all();
        $purchaseRequests = PurchaseRequest::with('project', 'materials')->get();
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
        'non_stock_price' => 'nullable|required_if:type,[material_non_stock,labour,transport]|numeric|min:0', // Non-stock price must be numeric
        'non_stock_quantity' => 'nullable|required_if:type,material_non_stock|integer|min:1', // Non-stock quantity must be integer
        'details' => 'nullable|string|max:1000', // Details for labour/transport must be string
    ]);

    // Create a new purchase request
    $purchaseRequest = PurchaseRequest::create([
        'project_id' => $request->project_id,
        'stock_id' => $request->stock_id,
        'user_id' => auth()->id(),
        'type' => $request->type,
        'non_stock_name' => $request->non_stock_name,
        'non_stock_price' => $request->non_stock_price,
        'non_stock_quantity' => $request->non_stock_quantity,
        'non_stock_image' => $request->file('non_stock_image') 
                             ? $request->file('non_stock_image')->store('non_stock_materials', 'public') 
                             : null,
        'details' => $request->details,
    ]);

    // If the type is material from stock, attach materials to the pivot table
    if ($request->type === 'material_stock' && $request->has('materials')) {
        foreach ($request->materials as $materialId => $materialData) {
            if (isset($materialData['selected'])) {
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

            // Check if the request is for stock materials
            if ($purchaseRequest->type === 'material_stock') {
                // Loop through materials and subtract quantity from stock
                foreach ($purchaseRequest->materials as $material) {
                    $stockMaterial = DB::table('material_stock')
                        ->where('material_id', $material->id)
                        ->where('stock_id', $purchaseRequest->stock_id)
                        ->first();

                    // Check if stock is available
                    if (!$stockMaterial || $stockMaterial->quantity < $material->pivot->quantity) {
                        throw new \Exception("Insufficient stock for material: {$material->name}");
                    }

                    // Subtract the requested quantity from stock
                    DB::table('material_stock')
                        ->where('material_id', $material->id)
                        ->where('stock_id', $purchaseRequest->stock_id)
                        ->update([
                            'quantity' => $stockMaterial->quantity - $material->pivot->quantity,
                        ]);
                }
            }

            // Change the request status to 'approved'
            $purchaseRequest->update(['status' => 'approved']);
        });

        // Return success message
        return redirect()->back()->with('success', 'Purchase Request approved successfully.');

    } catch (\Exception $e) {
        // Handle error and rollback
        return redirect()->back()->withErrors($e->getMessage());
    }
}


public function decline($id)
{
    $purchaseRequest = PurchaseRequest::findOrFail($id);

    // Change the request status to 'declined'
    $purchaseRequest->update(['status' => 'rejected']);

    return redirect()->back()->with('success', 'Purchase Request declined.');
}

}
