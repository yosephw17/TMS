<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
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
    
      
    
        return view('welcome',compact('dates', 'counts'));
    }
}

