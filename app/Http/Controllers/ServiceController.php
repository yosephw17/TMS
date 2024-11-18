<?php
    
namespace App\Http\Controllers;
    
use App\Models\Service;
use App\Models\Patient;
use Illuminate\Http\Request;

class ServiceController extends Controller
{ 
    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        $this->middleware('permission:manage-service', ['only' => ['index']]);
        $this->middleware('permission:service-view', ['only' => ['show']]);
        $this->middleware('permission:service-create', ['only' => ['store']]);
        $this->middleware('permission:service-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:service-delete', ['only' => ['destroy']]);
    }
 
    public function index()
    {
        $services = Service::all();
        // $serviceGroups = ServiceFieldGroup::all();
        return view('services.index',compact('services'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
        ]);

        Service::create($validatedData);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }
  
    
    public function show($id)
    {
        $service = Service::with('serviceDetails')->findOrFail($id);
        return view('services.show', compact('service'));
    }
public function edit($id)
{
    $service = Service::find($id);
    return response()->json($service);
}

public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'details' => 'nullable|string',
    ]);

    $service = Service::find($id);
    $service->update($validatedData);

    return redirect()->route('services.index')
        ->with('success', 'Service updated successfully.');
} 
 
    public function destroy(Service $service)
    {
        $service->delete();
    
        return redirect()->route('services.index')
                        ->with('success','Service deleted successfully');
    }
      
}