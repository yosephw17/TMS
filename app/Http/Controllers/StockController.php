<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Material;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::all(); // Fetch all stocks
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
        $stock = Stock::with('materials')->findOrFail($id); // Load stock with its related materials
        $materials = Material::all(); // Get all available materials
        return view('stocks.show', compact('stock', 'materials'));
    }

    public function addMaterial(Request $request, $id)
{
    $request->validate([
        'material_id' => 'required|exists:materials,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $stock = Stock::findOrFail($id);
    $materialId = $request->input('material_id');
    $quantity = $request->input('quantity');

    // Check if the material is already attached to this stock
    if ($stock->materials()->where('material_id', $materialId)->exists()) {
        // Update the existing quantity
        $existingQuantity = $stock->materials()->where('material_id', $materialId)->first()->pivot->quantity;
        $newQuantity = $existingQuantity + $quantity;
        $stock->materials()->updateExistingPivot($materialId, ['quantity' => $newQuantity]);
    } else {
        // Attach the material with the new quantity
        $stock->materials()->attach($materialId, ['quantity' => $quantity]);
    }

    return redirect()->route('stocks.show', $id)->with('success', 'Material added/updated successfully.');
}

public function removeMaterial(Request $request, $stockId, $materialId)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $stock = Stock::findOrFail($stockId);

    // Check if the material exists in the stock
    $existingMaterial = $stock->materials()->where('material_id', $materialId)->firstOrFail();
    $existingQuantity = $existingMaterial->pivot->quantity;

    // Subtract the quantity
    $quantityToRemove = $request->input('quantity');
    $newQuantity = $existingQuantity - $quantityToRemove;

    if ($newQuantity <= 0) {
        // Remove the material entirely if quantity becomes zero or less
        $stock->materials()->detach($materialId);
    } else {
        // Update the quantity in the pivot table
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

        $stock = Stock::findOrFail($id);
        $stock->update($request->only('name', 'location'));

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }
}
