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

    public function create()
    {
        //
    }

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

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, $id)
{
    $serviceDetail = ServiceDetail::findOrFail($id);
    $request->validate([
        'detail_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'service_id' => 'required|exists:services,id', 
    ]);

    $serviceDetail->update($request->only('detail_name', 'description', 'service_id'));

    return redirect()->route('services.show', $serviceDetail->service_id)
        ->with('success', 'Service detail updated successfully.');
}

    public function destroy(string $id)
    {
        $serviceDetail = ServiceDetail::findOrFail($id);
                $serviceId = $serviceDetail->service_id;
    
        $serviceDetail->delete();
    
        return redirect()->route('services.show', $serviceId)
            ->with('success', 'Service detail deleted successfully.');
    }
    
}
