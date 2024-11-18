<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
    $this->middleware('auth');
    
    $this->middleware('permission:manage-customer', ['only' => ['index']]);
    $this->middleware('permission:customer-view', ['only' => ['show']]);
    $this->middleware('permission:customer-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
    $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
}
    public function index()
    {
        $customers = Customer::all(); 
        return view('customers.index', compact('customers')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        Customer::create($request->all()); 
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.'); // Redirect with success message
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer')); 
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        $customer->update($request->all()); 
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.'); // Redirect with success message
    }

    public function destroy(Customer $customer)
    {
        $customer->delete(); 
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']); // Return success response
    }
}
