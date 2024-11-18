<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceDetail;
class ServiceDetailController extends Controller
{
    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        $this->middleware('permission:manage-service-detail', ['only' => ['index']]);
        $this->middleware('permission:service-detail-view', ['only' => ['show']]);
        $this->middleware('permission:service-detail-create', ['only' => ['store']]);
        $this->middleware('permission:service-detail-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:service-detail-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'detail_name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|string',
        ]);
    
        ServiceDetail::create($request->all());
    $serviceId=$request->service_id;
        return redirect()->route('services.show', $serviceId)

            ->with('success', 'Service detail created successfully.');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Find the service detail by ID
    $serviceDetail = ServiceDetail::findOrFail($id);

    // Validate the incoming request
    $request->validate([
        'detail_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'service_id' => 'required|exists:services,id', // Ensure the service ID exists
    ]);

    // Update the service detail with the validated data
    $serviceDetail->update($request->only('detail_name', 'description', 'service_id'));

    // Redirect to the service's show page with a success message
    return redirect()->route('services.show', $serviceDetail->service_id)
        ->with('success', 'Service detail updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the service detail by ID
        $serviceDetail = ServiceDetail::findOrFail($id);
        
        // Store the service ID before deletion
        $serviceId = $serviceDetail->service_id;
    
        // Delete the service detail
        $serviceDetail->delete();
    
        // Redirect to the service's show page with a success message
        return redirect()->route('services.show', $serviceId)
            ->with('success', 'Service detail deleted successfully.');
    }
    
}
