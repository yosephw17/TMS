<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Service;
use App\Models\Material;
use App\Models\ProjectFile;
use App\Models\Team;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $pendingProjects = Project::where('status', 'pending')->get();
    $completedProjects = Project::where('status', 'completed')->get();
    $canceledProjects = Project::where('status', 'cancelled')->get();

    return view('projects.index', compact('pendingProjects', 'completedProjects', 'canceledProjects'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'starting_date' => 'required|date',
        'ending_date' => 'required|date',
        'description' => 'required|string',
        'location' => 'required|string|max:255',
        'customer_id' => 'required|exists:customers,id',
        'service_detail_ids' => 'nullable|array',
        'team_ids' => 'nullable|array',
    ]);

    // Create the project
    $project = Project::create($validatedData);

    // Attach selected service details to the project
    if ($request->has('service_detail_ids')) {
        $project->serviceDetails()->sync($request->service_detail_ids);
    }

    // Attach selected teams to the project
    if ($request->has('team_ids')) {
        $project->teams()->sync($request->team_ids);
    }

        return redirect()->route('projects.show',$request->customer_id)->with('success', 'Project created successfully.');
    }
    

    /**
     * Display the specified resource.
     */
    public function show($customerId)
    {
        // Retrieve the customer and their associated projects
        $customer = Customer::findOrFail($customerId);
        $projects = Project::where('customer_id', $customerId)->get();
$services=Service::all();
$materials=Material::all();
$teams=Team::all();
        // Return the view with customer and projects data
        return view('projects.show', compact('customer', 'projects','services','materials','teams'));
    }
    public function showProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $customer = $project->customer;
         $services=Service::all();
        $materials=Material::all();
        $profileProformas = $project->proformas()->where('type', 'aluminium_profile')->get();
        $accessoriesProformas = $project->proformas()->where('type', 'aluminium_accessories')->get();
        $workProformas = $project->proformas()->where('type', 'work')->get();
        return view('projects.view', compact('customer', 'project','services','materials','profileProformas', 'accessoriesProformas', 'workProformas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'starting_date' => 'required|date',
            'ending_date' => 'nullable|date',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'total_price' => 'nullable|numeric',
            'status' => 'nullable|string',
            'service_detail_ids' => 'required|array', // Validating an array of service detail IDs
            'service_detail_ids.*' => 'exists:service_details,id', // Each ID should exist in service_details table
            'team_ids' => 'nullable|array',

        ]);
    
        // Update project details
        $project->update($request->except('service_detail_ids'));
    
        // Sync the selected services with the project
        $project->serviceDetails()->sync($request->service_detail_ids);
        if ($request->has('team_ids')) {
            $project->teams()->sync($request->team_ids);
        }
    
        return redirect()->route('projects.show',$request->customer_id)->with('success', 'Project created successfully.');    }
    
    public function addMaterials(Request $request, Project $project)
    {
        $selectedMaterials = $request->input('materials'); // Array of selected material IDs
        $quantities = $request->input('quantities'); // Array of quantities for each material ID
        
        foreach ($selectedMaterials as $materialId) {
            $quantity = $quantities[$materialId];
    
            // Attach the material to the project with the quantity
            $project->materials()->attach($materialId, ['quantity' => $quantity]);
        }
    
        // Retrieve the customer related to the project
        $customerId = $project->customer->id;
    
        // Redirect to the route with the customer ID
        return redirect()->route('projects.show', $customerId)->with('success', 'Materials added successfully.');
    }
    
    public function updateMaterial(Request $request, $projectId, $materialId)
{
    $project = Project::findOrFail($projectId);

    // Update the material quantity for the specific project
    $project->materials()->updateExistingPivot($materialId, ['quantity' => $request->input('quantity')]);
    $customerId = $project->customer->id;

    return redirect()->route('projects.show', $customerId)->with('success', 'Material updated successfully.');
}
public function uploadFiles(Request $request, $projectId)
    {
        $project = Project::find($projectId);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('project_files', 'public');

                ProjectFile::create([
                    'project_id' => $project->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Files uploaded successfully.');
    }
public function destroyMaterial(Project $project, Material $material)
{
    // Remove the material from the project's materials pivot table
    $project->materials()->detach($material->id);

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Material deleted successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
