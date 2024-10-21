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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'symbol' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',        
            'color' => 'nullable|string|max:255',
            'unit_price' => 'required|numeric',
            'unit_of_measurement' => 'required|string|max:255',
        ]);

        $material = new Material();
        $material->name = $request->name;
        $material->code = $request->code;
        $material->color = $request->color;
        $material->unit_price = $request->unit_price;
        $material->unit_of_measurement = $request->unit_of_measurement;
        if ($request->hasFile('symbol')) {
            $imagePath = $request->file('symbol')->store('materials', 'public'); 
            $material->symbol = $imagePath;
        }
        $material->save();
        return redirect()->route('materials.index')->with('success', 'Material created successfully.');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'symbol' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the symbol as an image
            'unit_price' => 'required|numeric',
            'unit_of_measurement' => 'required|string|max:255',
            'color' => 'nullable|string|max:255', // Validate the color field
        ]);
    
        // Update the material's name, unit price, unit of measurement, and color
        $material->name = $request->name;
        $material->code = $request->code;
        $material->unit_price = $request->unit_price;
        $material->unit_of_measurement = $request->unit_of_measurement;
        $material->color = $request->color;
    
        // Handle the symbol (image) upload
        if ($request->hasFile('symbol')) {
            // Store the new image and update the symbol field
            $imagePath = $request->file('symbol')->store('materials', 'public'); 
            $material->symbol = $imagePath;
        }
    
        // Save the changes to the material
        $material->save();
    
        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }
    

    // Remove the specified resource from storage
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['success' => true]);
    }
}
