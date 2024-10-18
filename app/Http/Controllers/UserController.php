<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function edit($id)
    {
        $user = User::find($id);
        $userRole = $user->roles->pluck('name', 'name')->all();

        return response()->json([
            'user' => $user,
            'userRole' => $userRole
        ]);
    }

        
        public function index(Request $request)
        {
            
            $data = User::orderBy('id', 'DESC')->paginate(8);
            
            $roles = Role::all();
            return view('users.index', compact('data', 'roles'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }



   public function store(Request $request)
{
    $this->validate($request, [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|same:confirm-password',
        'roles' => 'required|array',
        'roles.*' => 'exists:roles,name',
        'phone' => 'required|string|max:15', // Add validation for phone
        'address' => 'required|string|max:255', // Add validation for address
    ]);

    $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
        'phone' => $request->input('phone'), // Include phone
        'address' => $request->input('address'), // Include address
    ]);
    $user->syncRoles($request->input('roles'));

    return redirect()->route('users.index')
        ->with('success', 'User created successfully');
}


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::find($id);
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $user->syncRoles($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }


    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }


}
