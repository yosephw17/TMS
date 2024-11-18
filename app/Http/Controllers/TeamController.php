<?php
namespace App\Http\Controllers;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        $this->middleware('permission:manage-team', ['only' => ['index']]);
        $this->middleware('permission:team-view', ['only' => ['show']]);
        $this->middleware('permission:team-create', ['only' => ['store']]);
        $this->middleware('permission:team-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:team-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $teams = Team::all();
        return view('teams.index', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Team::create($request->all());

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team->update($request->all());

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }
}
