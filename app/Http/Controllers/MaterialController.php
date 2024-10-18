<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $materials = Material::all();
        return view('materials.index', compact('materials'));
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'unit_price' => 'required|numeric',
            'unit_of_measurement' => 'required|string|max:255',
        ]);

        Material::create($request->all());

        return redirect()->route('materials.index')->with('success', 'Material created successfully.');
    }

    // Show the form for editing the specified resource
    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    // Update the specified resource in storage
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'unit_price' => 'required|numeric',
            'unit_of_measurement' => 'required|string|max:255',
        ]);

        $material->update($request->all());

        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    // Remove the specified resource from storage
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['success' => true]);
    }
}
