<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Material;
use Illuminate\Http\Request;

class StockController extends Controller
{ public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        $this->middleware('permission:manage-stock', ['only' => ['index']]);
        $this->middleware('permission:stock-view', ['only' => ['show']]);
        $this->middleware('permission:stock-create', ['only' => ['store']]);
        $this->middleware('permission:stock-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:stock-delete', ['only' => ['destroy']]);
        $this->middleware('permission:stock-add-material', ['only' => ['addMaterial']]);
        $this->middleware('permission:stock-remove-material', ['only' => ['removeMaterial']]);
    }

    public function index()
    {
        $stocks = Stock::all();
        return view('stocks.index', compact('stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Stock::create($request->only('name', 'location'));

        return redirect()->route('stocks.index')->with('success', 'Stock created successfully.');
    }

    public function show($id)
    {
        $stock = Stock::with('materials')->findOrFail($id); 
        $materials = Material::all(); 
        return view('stocks.show', compact('stock', 'materials'));
    }

    public function addMaterial(Request $request, $id)
    {
        $request->validate([
            'materials' => 'required|array|min:1', 
            'materials.*' => 'exists:materials,id', 
            'quantities' => 'required|array', 
        ]);
    
        $selectedMaterials = $request->input('materials');   
        foreach ($selectedMaterials as $materialId) {
            $request->validate([
                "quantities.$materialId" => 'required|integer|min:1', 
            ]);
        }
    
        $stock = Stock::findOrFail($id);
    
        foreach ($selectedMaterials as $materialId) {
            $quantity = $request->input("quantities.$materialId");
    
            if ($stock->materials()->where('material_id', $materialId)->exists()) {
                $existingQuantity = $stock->materials()->where('material_id', $materialId)->first()->pivot->quantity;
                $newQuantity = $existingQuantity + $quantity;
                $stock->materials()->updateExistingPivot($materialId, ['quantity' => $newQuantity]);
            } else {
                $stock->materials()->attach($materialId, ['quantity' => $quantity]);
            }
        }
    
        return redirect()->route('stocks.show', $id)->with('success', 'Materials added/updated successfully.');
    }
    
    

public function removeMaterial(Request $request, $stockId, $materialId)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $stock = Stock::findOrFail($stockId);

    $existingMaterial = $stock->materials()->where('material_id', $materialId)->firstOrFail();
    $existingQuantity = $existingMaterial->pivot->quantity;

    $quantityToRemove = $request->input('quantity');
    $newQuantity = $existingQuantity - $quantityToRemove;

    if ($newQuantity <= 0) {
        $stock->materials()->detach($materialId);
    } else {
        $stock->materials()->updateExistingPivot($materialId, ['quantity' => $newQuantity]);
    }

    return redirect()->route('stocks.show', $stockId)->with('success', 'Material quantity updated successfully.');
}


public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
    ]);

    $stock = Stock::find($id);
    if (!$stock) {
        return redirect()->back()->with('error', 'Stock not found');
    }

    $stock->update([
        'name' => $request->name,
        'location' => $request->location,
    ]);

    return redirect()->route('stocks.index')->with('success', 'Stock updated successfully');
}
    public function getMaterials(Stock $stock)
{
    $materials = $stock->materials()->withPivot('quantity')->get();

    return response()->json($materials);
}


    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }
}
