<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectAgreement;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectAgreementController extends Controller
{
    protected $notificationService;

    // public function __construct(NotificationService $notificationService)
    // {
    //     $this->middleware('auth');
    //     $this->notificationService = $notificationService;
        
    //     $this->middleware('permission:manage-project', ['only' => ['index', 'store', 'destroy']]);
    //     $this->middleware('permission:project-view', ['only' => ['show', 'download']]);
    // }

    /**
     * Display agreements for a specific project
     */
    public function index(Project $project)
    {
        $agreements = $project->agreements()
            ->with(['uploadedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'agreements' => $agreements,
            'project' => $project
        ]);
    }

    /**
     * Store a new agreement file
     */
    public function store(Request $request, Project $project)
    {
       
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('project_agreements/' . $project->id, $fileName, 'public');

        $agreement = ProjectAgreement::create([
            'project_id' => $project->id,
            'uploaded_by' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);

        // Create notification for new agreement
        $this->notificationService->create([
            'type' => 'agreement_uploaded',
            'message' => "New agreement file '{$agreement->file_name}' uploaded for project '{$project->name}'",
            'user_id' => auth()->id(),
            'action_url' => route('projects.show', $project->customer_id),
            'data' => [
                'agreement_id' => $agreement->id,
                'file_name' => $agreement->file_name,
                'project_id' => $project->id,
                'project_name' => $project->name,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agreement file uploaded successfully!',
            'agreement' => $agreement->load(['uploadedBy'])
        ]);
    }

    /**
     * Download an agreement file
     */
    public function download(ProjectAgreement $agreement)
    {
        if (!Storage::disk('public')->exists($agreement->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Storage::disk('public')->download($agreement->file_path, $agreement->file_name);
    }

    /**
     * Delete an agreement
     */
    public function destroy(ProjectAgreement $agreement)
    {
        // Delete the file
        if (Storage::disk('public')->exists($agreement->file_path)) {
            Storage::disk('public')->delete($agreement->file_path);
        }

        $agreement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agreement file deleted successfully!'
        ]);
    }
}
