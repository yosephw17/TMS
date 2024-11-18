<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        $this->middleware('permission:manage-role', ['only' => ['index']]);
        $this->middleware('permission:role-view', ['only' => ['show']]);
        $this->middleware('permission:role-create', ['only' => ['store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('roles.index', compact('roles', 'permissions'))
        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validatedData['name']]);

        $permissionNames = $validatedData['permissions'];
        $permissions = Permission::whereIn('name', $permissionNames)->get();

        $role->syncPermissions($permissions);
    
        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }


    

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);
    
        $role = Role::find($id);
    
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }
    
        $role->name = $request->input('name');
        $role->save();
    
        $permissionIds = $request->input('permissions');
    
        $role->revokePermissionTo($role->permissions);
    
        foreach ($permissionIds as $permissionId) {
            $permission = Permission::find($permissionId);
    
            if ($permission) {
                $role->givePermissionTo($permission);
            }
        }
    
            return redirect()->route('roles.index')->with('success', 'role updated successfully');
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
