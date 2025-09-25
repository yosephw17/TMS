<?php

namespace App\Services;

use App\Models\Project;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Log;

class ProjectCostService
{
    /**
     * Update project costs when a purchase request is approved
     */
    public function updateProjectCostsOnApproval(PurchaseRequest $purchaseRequest)
    {
        if (!$purchaseRequest->project_id) {
            return false;
        }

        $project = Project::find($purchaseRequest->project_id);
        if (!$project) {
            return false;
        }

        try {
            $project->calculateAndUpdateCosts();
            
            Log::info("Project costs updated", [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'actual_cost' => $project->actual_cost,
                'cost_percentage' => $project->cost_percentage,
                'triggered_by_purchase_request' => $purchaseRequest->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update project costs", [
                'project_id' => $project->id,
                'purchase_request_id' => $purchaseRequest->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Bulk update costs for all projects
     */
    public function updateAllProjectCosts()
    {
        $projects = Project::whereHas('purchaseRequests', function($query) {
            $query->where('status', 'approved');
        })->get();

        $updated = 0;
        $failed = 0;

        foreach ($projects as $project) {
            try {
                $project->calculateAndUpdateCosts();
                $updated++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to update project costs in bulk update", [
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Bulk project cost update completed", [
            'updated' => $updated,
            'failed' => $failed,
            'total' => $projects->count()
        ]);

        return [
            'updated' => $updated,
            'failed' => $failed,
            'total' => $projects->count()
        ];
    }

    /**
     * Update costs for projects that need recalculation
     */
    public function updateStaleProjectCosts()
    {
        $projects = Project::whereHas('purchaseRequests', function($query) {
            $query->where('status', 'approved');
        })->get()->filter(function($project) {
            return $project->costsNeedUpdate();
        });

        $updated = 0;

        foreach ($projects as $project) {
            try {
                $project->calculateAndUpdateCosts();
                $updated++;
            } catch (\Exception $e) {
                Log::error("Failed to update stale project costs", [
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Stale project costs updated", [
            'updated' => $updated,
            'total_checked' => $projects->count()
        ]);

        return $updated;
    }

    /**
     * Get project cost summary with analytics
     */
    public function getProjectCostSummary(Project $project)
    {
        // Ensure costs are up to date
        if ($project->costsNeedUpdate()) {
            $project->calculateAndUpdateCosts();
        }

        return [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'budget' => $project->total_price,
            'actual_cost' => $project->actual_cost,
            'material_cost' => $project->material_cost,
            'labour_cost' => $project->labour_cost,
            'transport_cost' => $project->transport_cost,
            'other_cost' => $project->other_cost,
            'budget_variance' => $project->budget_variance,
            'cost_percentage' => $project->cost_percentage,
            'cost_status' => $project->cost_status_color,
            'last_updated' => $project->cost_last_updated,
            'cost_breakdown' => $project->cost_breakdown,
            'over_budget' => $project->budget_variance > 0,
            'within_budget' => $project->cost_percentage <= 100,
        ];
    }

    /**
     * Get cost analytics for dashboard
     */
    public function getCostAnalytics()
    {
        $projects = Project::whereNotNull('actual_cost')->get();
        
        return [
            'total_projects' => $projects->count(),
            'total_budget' => $projects->sum('total_price'),
            'total_actual_cost' => $projects->sum('actual_cost'),
            'total_variance' => $projects->sum('budget_variance'),
            'projects_over_budget' => $projects->where('budget_variance', '>', 0)->count(),
            'projects_within_60_percent' => $projects->where('cost_percentage', '<=', 60)->count(),
            'projects_within_75_percent' => $projects->whereBetween('cost_percentage', [60, 75])->count(),
            'projects_over_75_percent' => $projects->where('cost_percentage', '>', 75)->count(),
            'average_cost_percentage' => $projects->avg('cost_percentage'),
        ];
    }
}
