<?php
namespace App\Http\Controllers;

use App\Models\DailyActivity;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    public function __construct()
    {
    $this->middleware('auth');
   
    $this->middleware('permission:daily-activity-create', ['only' => ['create', 'store']]);
  
}
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        DailyActivity::create([
            'project_id' => $request->project_id,
            'description' => $request->description,
            'user_id' => $request->user_id,
        ]);

        return back()->with('success', 'Daily activity added successfully.');
    }
}
