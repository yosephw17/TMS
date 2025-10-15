<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;

        $this->middleware('permission:manage-customer', ['only' => ['index']]);
        $this->middleware('permission:customer-view', ['only' => ['show']]);
        $this->middleware('permission:customer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'type' => 'required|in:project,material',
        ]);

        $customer = Customer::create($request->all());

        // Create notification for new customer - send to all users with customer-notification permission
        $this->notificationService->createForUsersWithNotificationPermission('customer_added', [
            'type' => 'customer_added',
            'message' => "New customer '{$customer->name}' has been added to the system",
            'action_url' => route('customers.index'),
            'data' => [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone
            ],
            'created_by' => auth()->id()
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
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
            'type' => 'required|in:project,material',
        ]);

        $oldName = $customer->name;
        $customer->update($request->all());

        // Create notification for customer update - send to all users with customer-notification permission
        $this->notificationService->createForUsersWithNotificationPermission('customer_updated', [
            'type' => 'customer_updated',
            'message' => "Customer '{$customer->name}' information has been updated",
            'action_url' => route('customers.index'),
            'data' => [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'old_name' => $oldName
            ],
            'created_by' => auth()->id()
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
    }
}
