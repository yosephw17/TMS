<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Service;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{

    public function index()
    {
        $customerData = Customer::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        $dates = $customerData->pluck('date')->toArray();
        $counts = $customerData->pluck('count')->toArray();
    
        // Get recent active projects for quick access
        $user = auth()->user();
        
        if ($user->can('project-view-all')) {
            $recentProjects = Project::with('customer')
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        } else {
            $recentProjects = Project::with('customer')
                ->whereHas('teams', function ($query) use ($user) {
                    $query->whereIn('teams.id', $user->teams->pluck('id'));
                })
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        }

        // Get counts for dashboard metrics
        $customersCount = Customer::count();
        $projectsCount = Project::whereIn('status', ['pending', 'in_progress'])->count();
        $servicesCount = Service::count();
        $sellersCount = Seller::count();
    
        return view('welcome', compact(
            'dates', 
            'counts', 
            'recentProjects',
            'customersCount',
            'projectsCount', 
            'servicesCount',
            'sellersCount'
        ));
    }
}
