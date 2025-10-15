<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Service;
use App\Models\Material;
use App\Models\ProjectFile;
use App\Models\Team;
use App\Models\CompanyInfo;
use App\Services\NotificationService;

class ProjectController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        // Apply authentication middleware
        $this->middleware('auth');
        $this->notificationService = $notificationService;

        $this->middleware('permission:manage-project', ['only' => ['index']]);
        $this->middleware('permission:project-view', ['only' => ['showProject']]);
        $this->middleware('permission:project-create', ['only' => ['store']]);
        $this->middleware('permission:project-edit', ['only' => ['edit', 'update','updateMaterial']]);
        $this->middleware('permission:project-delete', ['only' => ['destroy','destroyMaterial']]);
        $this->middleware('permission:project-material-print', ['only' => ['printMaterials']]);
        $this->middleware('permission:project-upload-file', ['only' => ['uploadFiles']]);
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->can('project-view-all')) {
            $projects = Project::all();
        } else {
            $projects = Project::whereHas('teams', function ($query) use ($user) {
                $query->whereIn('teams.id', $user->teams->pluck('id'));
            })->get();
        }

        $pendingProjects   = $projects->where('status', 'pending');
        $completedProjects = $projects->where('status', 'completed');
        $canceledProjects  = $projects->where('status', 'cancelled');

        return view('projects.index', compact('pendingProjects', 'completedProjects', 'canceledProjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        // Different validation for material customers
        if ($customer->type === 'material') {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'starting_date' => 'required|date',
                'description' => 'required|string',
                'customer_id' => 'required|exists:customers,id',
                'ending_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
            ]);

            // Set default values for material customers
            $validatedData['ending_date'] = $validatedData['ending_date'] ?? $validatedData['starting_date'];
            $validatedData['location'] = $validatedData['location'] ?? 'Material Order';
        } else {
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
        }

        $project = Project::create($validatedData);

        if ($request->has('service_detail_ids')) {
            $project->serviceDetails()->sync($request->service_detail_ids);
        }

        if ($request->has('team_ids')) {
            $project->teams()->sync($request->team_ids);
        }

        // Create notification for new project - send to all users with project-notification permission
        $customer = Customer::find($validatedData['customer_id']);
        $this->notificationService->createForUsersWithNotificationPermission('project_created', [
            'type' => 'project_created',
            'message' => "New project '{$project->name}' has been created for customer {$customer->name}",
            'action_url' => route('projects.index'),
            'data' => [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'starting_date' => $project->starting_date,
                'ending_date' => $project->ending_date
            ],
            'created_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    public function show($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $projects = Project::with(['proformaImages' => function ($query) {
            $query->where('status', 'approved');
        }])
            ->where('customer_id', $customerId)
            ->get();
        $services = Service::all();
        $materials = Material::all();
        $teams = Team::all();

        // Check if customer is material type - show different view
        if ($customer->type === 'material') {
            return view('projects.materials', compact('customer', 'projects', 'materials'));
        }

        return view('projects.show', compact('customer', 'projects', 'services', 'materials', 'teams'));
    }

    public function showProject($projectId)
    {
        $project = Project::with(['proformaImages' => function ($query) {
            $query->where('status', 'approved');
        }])->findOrFail($projectId);
        $customer = $project->customer;
        $services = Service::all();
        $materials = Material::all();
        $profileProformas = $project->proformas()->where('type', 'aluminium_profile')->get();
        $accessoriesProformas = $project->proformas()->where('type', 'aluminium_accessories')->get();
        $workProformas = $project->proformas()->where('type', 'work')->get();
        $teams = Team::all();
        $dailyActivities = $project->dailyActivities;

        // Check if customer is material type - show simplified view
        if ($customer->type === 'material') {
            return view('projects.material-view', compact(
                'customer',
                'project',
                'materials',
                'profileProformas',
                'accessoriesProformas'
            ));
        }

        return view('projects.view', compact(
            'customer',
            'project',
            'services',
            'materials',
            'profileProformas',
            'accessoriesProformas',
            'workProformas',
            'dailyActivities',
            'teams'
        ));
    }

    public function edit(string $id)
    {
        //
    }

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
            'service_detail_ids' => 'required|array',
            'service_detail_ids.*' => 'exists:service_details,id',
            'team_ids' => 'nullable|array',
        ]);

        $project->update($request->except('service_detail_ids'));

        $project->serviceDetails()->sync($request->service_detail_ids);
        if ($request->has('team_ids')) {
            $project->teams()->sync($request->team_ids);
        }

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    public function addMaterials(Request $request, Project $project)
    {
        $selectedMaterials = $request->input('materials', []); // Array of selected material IDs
        $quantities = $request->input('quantities', []); // Array of quantities for each material ID

        $errors = [];
        foreach ($selectedMaterials as $materialId) {
            if (empty($quantities[$materialId]) || $quantities[$materialId] <= 0) {
                $materialName = Material::find($materialId)->name ?? 'Unknown Material';
                $errors["quantities.$materialId"] = "Quantity is required for the selected material: $materialName.";
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        foreach ($selectedMaterials as $materialId) {
            $quantity = $quantities[$materialId] ?? 1; // Default to 1 if not set
            $project->materials()->attach($materialId, ['quantity' => $quantity]);
        }

        return redirect()->back()->with('success', 'Materials added successfully.');
    }

    public function updateMaterial(Request $request, $projectId, $materialId)
    {
        $project = Project::findOrFail($projectId);

        // Update the material quantity for the specific project
        $project->materials()->updateExistingPivot($materialId, ['quantity' => $request->input('quantity')]);
        $customerId = $project->customer->id;

        return redirect()->back()->with('success', 'Materials updated successfully.');
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
        $project->materials()->detach($material->id);

        return redirect()->back()->with('success', 'Material deleted successfully.');
    }

    public function printMaterials(Project $project)
    {
        $project->load('materials'); // Load materials relationship
        $companyInfo = CompanyInfo::find(1);
        return view('print.materialsPrint', compact('project', 'companyInfo'));
    }

    public function destroy(string $id)
    {
        //
    }
}
