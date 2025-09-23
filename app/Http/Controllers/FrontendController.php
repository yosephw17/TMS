<?php

namespace App\Http\Controllers;

use App\Models\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrontendController extends Controller
{
    public function index()
    {
        $projects = Frontend::latest()->get();
        return view('frontend.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'client' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file('image') 
            ? $request->file('image')->store('projects', 'public') 
            : null;

        Frontend::create([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'date' => $request->date,
            'client' => $request->client,
            'image' => $imagePath,
        ]);

        return redirect()->route('frontends.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Frontend $frontend)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'client' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($frontend->image) {
                Storage::disk('public')->delete($frontend->image);
            }
            $imagePath = $request->file('image')->store('projects', 'public');
        } else {
            $imagePath = $frontend->image;
        }

        $frontend->update([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'date' => $request->date,
            'client' => $request->client,
            'image' => $imagePath,
        ]);

        return redirect()->route('frontends.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Frontend $frontend)
    {
        if ($frontend->image) {
            Storage::disk('public')->delete($frontend->image);
        }
        $frontend->delete();

        return redirect()->route('frontends.index')->with('success', 'Project deleted successfully.');
    }
}
