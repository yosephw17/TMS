<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Display a listing of the customers
    public function index()
    {
        $customers = Customer::all(); // Get all customers
        return view('customers.index', compact('customers')); // Return the view with customers
    }

    // Store a newly created customer in storage
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        Customer::create($request->all()); // Create new customer
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.'); // Redirect with success message
    }

    // Show the form for editing the specified customer
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer')); // Return the edit view with customer data
    }

    // Update the specified customer in storage
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        $customer->update($request->all()); // Update customer data
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.'); // Redirect with success message
    }

    // Remove the specified customer from storage
    public function destroy(Customer $customer)
    {
        $customer->delete(); // Delete the customer
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']); // Return success response
    }
}
