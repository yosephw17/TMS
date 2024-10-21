<?php
namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Models\Project;
use App\Models\Material;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;

class ProformaController extends Controller
{
    // Display list of proformas
    public function index(Project $project)
    {
        $proformas = $project->proformas;
        return view('proformas.index', compact('proformas', 'project'));
    }

    // Store a new proforma
    public function store(Request $request)
    {        
        
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'customer_id' => 'required|exists:customers,id',
            'ref_no' => 'nullable|string|unique:proformas,ref_no',
            'type' => 'required|in:aluminium_profile,aluminium_accessories,work',
            'vat_percentage' => 'required|numeric|min:0|max:100',
            'before_vat_total' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'after_vat_total' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'final_total' => 'nullable|numeric|min:0',
            'payment_validity' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'date' => 'required|date',
            'materials' => 'nullable|array',
            'materials.*.quantity' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0', // For work proforma
            'other_costs' => 'nullable|numeric|min:0', // For work proforma
        ]);
    

        // Calculate totals
        $beforeVatTotal = 0;
        $materials = $request->input('materials', []);
        
        foreach ($materials as $materialId => $materialData) {
            if (isset($materialData['selected'])) {
                $material = Material::findOrFail($materialId);
                
                $beforeVatTotal += $material->unit_price * $materialData['quantity'];
            }
        }

        // Add labor and other costs for work proforma
        if ($request->type === 'work') {
            $beforeVatTotal += $request->input('labor_cost', 0) + $request->input('other_costs', 0);
        }

        $vatAmount = ($beforeVatTotal * $request->vat_percentage) / 100;
        $afterVatTotal = $beforeVatTotal + $vatAmount;
        $finalTotal = $afterVatTotal - $request->input('discount', 0);

        // Create proforma
        $proforma = Proforma::create([
            'project_id' => $request->input('project_id'),
            'customer_id' => $request->input('customer_id'),
            'ref_no' => $request->input('ref_no'),
            'type' => $request->input('type'),
            'before_vat_total' => $beforeVatTotal,
            'vat_percentage' => $request->input('vat_percentage'),
            'vat_amount' => $vatAmount,
            'after_vat_total' => $afterVatTotal,
            'discount' => $request->input('discount', 0),
            'final_total' => $finalTotal,
            'payment_validity' => $request->input('payment_validity'),
            'delivery_terms' => $request->input('delivery_terms'),
            'date' => $request->input('date'),
        ]);

        // Attach materials to the proforma
        if (!empty($materials)) {
            foreach ($materials as $materialId => $materialData) {
                if (isset($materialData['selected']) && $materialData['selected'] == true) {
                    // Retrieve the material
                    $material = Material::findOrFail($materialId);
                    
                    // Attach the material to the proforma with quantity and total price
                    $proforma->materials()->attach($materialId, [
                        'quantity' => $materialData['quantity'],
                        'total_price' => $material->unit_price * $materialData['quantity'],
                    ]);
                }
            }
        }
        

        return redirect()->route('projects.show', $request->input('project_id'))
                         ->with('success', 'Proforma created successfully.');
    }

    // Edit a proforma
    public function edit(Proforma $proforma)
    {
        $materials = Material::all();
        return view('proformas.edit', compact('proforma', 'materials'));
    }

    // Update a proforma
    public function update(Request $request, Proforma $proforma)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'customer_id' => 'required|exists:customers,id',
            'ref_no' => 'nullable|string|unique:proformas,ref_no,' . $proforma->id,
            'type' => 'required|in:aluminium_profile,aluminium_accessories,work',
            'vat_percentage' => 'required|numeric|min:0|max:100',
            'before_vat_total' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'after_vat_total' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'final_total' => 'nullable|numeric|min:0',
            'payment_validity' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'date' => 'required|date',
            'materials' => 'nullable|array',
            'materials.*.quantity' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0', // For work proforma
            'other_costs' => 'nullable|numeric|min:0', // For work proforma
        ]);
    
        // Calculate totals
        $beforeVatTotal = 0;
        $materials = $request->input('materials', []);
        
        foreach ($materials as $materialId => $materialData) {
            if (!empty($materialData['selected'])) { // Only consider selected materials
                $material = Material::findOrFail($materialId);
                
                $beforeVatTotal += $material->unit_price * $materialData['quantity'];
            }
        }
    
        // Add labor and other costs for work proforma
        if ($request->type === 'work') {
            $beforeVatTotal += $request->input('labor_cost', 0) + $request->input('other_costs', 0);
        }
    
        $vatAmount = ($beforeVatTotal * $request->vat_percentage) / 100;
        $afterVatTotal = $beforeVatTotal + $vatAmount;
        $finalTotal = $afterVatTotal - $request->input('discount', 0);
    
        // Update proforma
        $proforma->update([
            'project_id' => $request->input('project_id'),
            'customer_id' => $request->input('customer_id'),
            'ref_no' => $request->input('ref_no'),
            'type' => $request->input('type'),
            'before_vat_total' => $beforeVatTotal,
            'vat_percentage' => $request->input('vat_percentage'),
            'vat_amount' => $vatAmount,
            'after_vat_total' => $afterVatTotal,
            'discount' => $request->input('discount', 0),
            'final_total' => $finalTotal,
            'payment_validity' => $request->input('payment_validity'),
            'delivery_terms' => $request->input('delivery_terms'),
            'date' => $request->input('date'),
        ]);
    
        // Sync materials to the proforma
        if (!empty($materials)) {
            $syncData = [];
            foreach ($materials as $materialId => $materialData) {
                if (!empty($materialData['selected'])) { // Only attach selected materials
                    // Retrieve the material
                    $material = Material::findOrFail($materialId);
                    
                    // Prepare data for sync
                    $syncData[$materialId] = [
                        'quantity' => $materialData['quantity'],
                        'total_price' => $material->unit_price * $materialData['quantity'],
                    ];
                }
            }
    
            // Sync materials, this will update the pivot table
            $proforma->materials()->sync($syncData);
        } else {
            // If no materials are provided, detach all materials
            $proforma->materials()->detach();
        }
    
        return redirect()->route('projects.show', $request->input('project_id'))
                         ->with('success', 'Proforma updated successfully.');
    }
    public function print($id)
{
    $proforma = Proforma::with('customer', 'materials')->findOrFail($id);
    $companyInfo = CompanyInfo::find(3);
    return view('print.aluminiumProfilePrint', compact('proforma','companyInfo'));
}
    public function printAccessories($id)
{
    $companyInfo = CompanyInfo::find(3);
    $proforma = Proforma::with('customer', 'materials')->findOrFail($id);
    
    return view('print.aluminiumAccessoriesPrint', compact('proforma','companyInfo'));
}

public function storeAccessoriesProforma(Request $request)
{
    $validated = $request->validate([
        'ref_no' => 'required|string|max:255',
        'date' => 'required|date',
        'before_vat_total' => 'required|numeric',
        'vat_percentage' => 'required|numeric',
        'after_vat_total' => 'required|numeric',
        'discount' => 'nullable|numeric',
        'final_total' => 'required|numeric',
        'payment_validity' => 'nullable|string|max:255',
        'delivery_terms' => 'nullable|string|max:255',
        'materials' => 'required|array', // Keep using 'materials'
    ]);

    $proforma = Proforma::create([
        'project_id' => $request->project_id,
        'ref_no' => $validated['ref_no'],
        'date' => $validated['date'],
        'before_vat_total' => $validated['before_vat_total'],
        'vat_percentage' => $validated['vat_percentage'],
        'after_vat_total' => $validated['after_vat_total'],
        'discount' => $validated['discount'],
        'final_total' => $validated['final_total'],
        'payment_validity' => $validated['payment_validity'],
        'delivery_terms' => $validated['delivery_terms'],
        'type' => 'aluminium_accessory', // Set type for accessories
    ]);

    // Attach selected materials with quantities
    foreach ($validated['materials'] as $materialId => $materialData) {
        if (!empty($materialData['selected'])) {
            $quantity = $materialData['quantity'] ?? 0;
            $proforma->materials()->attach($materialId, ['quantity' => $quantity]);
        }
    }

    return redirect()->route('accessoriesProformas.index', ['project_id' => $request->project_id])->with('success', 'Proforma created successfully.');
}

// Function to update an Aluminium Accessories Proforma
public function updateAccessoriesProforma(Request $request, Proforma $proforma)
{
    $validated = $request->validate([
        'ref_no' => 'required|string|max:255',
        'date' => 'required|date',
        'before_vat_total' => 'required|numeric',
        'vat_percentage' => 'required|numeric',
        'after_vat_total' => 'required|numeric',
        'discount' => 'nullable|numeric',
        'final_total' => 'required|numeric',
        'payment_validity' => 'nullable|string|max:255',
        'delivery_terms' => 'nullable|string|max:255',
        'materials' => 'required|array', // Keep using 'materials'
    ]);

    $proforma->update([
        'ref_no' => $validated['ref_no'],
        'date' => $validated['date'],
        'before_vat_total' => $validated['before_vat_total'],
        'vat_percentage' => $validated['vat_percentage'],
        'after_vat_total' => $validated['after_vat_total'],
        'discount' => $validated['discount'],
        'final_total' => $validated['final_total'],
        'payment_validity' => $validated['payment_validity'],
        'delivery_terms' => $validated['delivery_terms'],
    ]);

    // Sync selected materials with quantities
    $proforma->materials()->detach();
    foreach ($validated['materials'] as $materialId => $materialData) {
        if (!empty($materialData['selected'])) {
            $quantity = $materialData['quantity'] ?? 0;
            $proforma->materials()->attach($materialId, ['quantity' => $quantity]);
        }
    }

    return redirect()->route('accessoriesProformas.index', ['project_id' => $proforma->project_id])->with('success', 'Proforma updated successfully.');
}

// Function to delete an Aluminium Accessories Proforma
public function destroyAccessoriesProforma(Proforma $proforma)
{
    $proforma->delete();
    return redirect()->route('accessoriesProformas.index', ['project_id' => $proforma->project_id])->with('success', 'Proforma deleted successfully.');
}

    // Delete a proforma
    public function destroy(Proforma $proforma)
    {
        $proforma->materials()->detach();
        $proforma->delete();

        return redirect()->route('projects.show', $proforma->project_id)
                         ->with('success', 'Proforma deleted successfully.');
    }
}
