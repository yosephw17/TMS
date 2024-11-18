<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Customer;
use Carbon\Carbon;  
use DB;

class ChartController extends Controller
{
    public function getMonthlyRegistrations()
    {
        $projects = Project::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month');

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $projects->get($i, 0); // Use 0 if there's no data for that month
        }

        return response()->json($data);
    }
    public function getCustomers()
    {
        $customers = Customer::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month');

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $customers->get($i, 0);
        }

        return response()->json($data);
    }
    public function getChartData()
    {
        $projects = Project::with('serviceDetails.service')->get();

        $labels = [];
        $data = [];

        foreach ($projects as $project) {
            foreach ($project->serviceDetails as $serviceDetail) {
                $serviceName = $serviceDetail->service->name; // Assuming Service model has a 'name' attribute

                if (!in_array($serviceName, $labels)) {
                    $labels[] = $serviceName;
                    $data[] = 1;
                } else {
                    $index = array_search($serviceName, $labels);
                    $data[$index] += 1;
                }
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    
    }
}
