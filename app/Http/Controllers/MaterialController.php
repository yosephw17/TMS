<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;

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
            'unit_price' => 'required|numeric|min:0',
            'unit_of_measurement' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $materialData = $request->only(['name', 'code', 'color', 'unit_price', 'unit_of_measurement', 'type']);
        
        if ($request->hasFile('symbol')) {
            $imagePath = $request->file('symbol')->store('materials', 'public'); 
            $materialData['symbol'] = $imagePath;
        }
        
        $material = Material::create($materialData);
        
        // Create notification for new material - send to all users with material-notification permission
        $this->notificationService->createForUsersWithNotificationPermission('material_added', [
            'type' => 'material_added',
            'message' => "New material '{$material->name}' has been added to the system",
            'action_url' => route('materials.index'),
            'data' => [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'material_code' => $material->code,
                'unit_price' => $material->unit_price,
                'type' => $material->type
            ],
            'created_by' => auth()->id()
        ]);
        
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
            'symbol' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit_price' => 'required|numeric|min:0',
            'unit_of_measurement' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'nullable|string|max:255', 
        ]);
    
        $materialData = $request->only(['name', 'code', 'color', 'unit_price', 'unit_of_measurement', 'type']);
        
        if ($request->hasFile('symbol')) {
            $imagePath = $request->file('symbol')->store('materials', 'public'); 
            $materialData['symbol'] = $imagePath;
        }
        
        $oldName = $material->name;
        $material->update($materialData);
        
        // Create notification for material update - send to all users with material-notification permission
        $this->notificationService->createForUsersWithNotificationPermission('material_updated', [
            'type' => 'material_updated',
            'message' => "Material '{$material->name}' has been updated",
            'action_url' => route('materials.index'),
            'data' => [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'old_name' => $oldName,
                'unit_price' => $material->unit_price,
                'type' => $material->type
            ],
            'created_by' => auth()->id()
        ]);
    
        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['success' => true]);
    }
}
