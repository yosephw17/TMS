<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        // Apply permission middleware for specific actions
        $this->middleware('permission:manage-permission', ['only' => ['index']]);
        $this->middleware('permission:permission-view', ['only' => ['show']]);
        $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $permissions = Permission::all(); // Fetch all permissions
        return view('permissions.index', compact('permissions'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->all());
        return redirect()->route('permissions.index')
                         ->with('success', 'Permission updated successfully');
    }

    public function store(Request $request)
    {
        Permission::create($request->all());
        return redirect()->route('permissions.index')
                         ->with('success', 'Permission created successfully');
    }

    public function destroy($id)
    {
        Permission::destroy($id);
        return response()->json(['success' => 'Permission deleted successfully']);
    }
}
