<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:manage-material', ['only' => ['index']]);
        $this->middleware('permission:material-view', ['only' => ['show']]);
        $this->middleware('permission:material-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:material-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:material-delete', ['only' => ['destroy']]);
    }
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
            'unit_of_measurement' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $material = new Material();
        $material->name = $request->name;
        $material->code = $request->code;
        $material->color = $request->color;
        $material->unit_of_measurement = $request->unit_of_measurement;
        $material->type = $request->type;
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
            'unit_of_measurement' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'nullable|string|max:255', 
        ]);
    
        $material->name = $request->name;
        $material->code = $request->code;
        $material->unit_of_measurement = $request->unit_of_measurement;
        $material->type = $request->type;
        $material->color = $request->color;
    
        if ($request->hasFile('symbol')) {
            $imagePath = $request->file('symbol')->store('materials', 'public'); 
            $material->symbol = $imagePath;
        }
    
        $material->save();
    
        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }
    

    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['success' => true]);
    }
}
