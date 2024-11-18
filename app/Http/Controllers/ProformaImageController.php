<?php

namespace App\Http\Controllers;

use App\Models\ProformaImage;
use App\Models\Seller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ProformaImageController extends Controller
{

 public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:manage-proforma-image', ['only' => ['index']]);
        $this->middleware('permission:proforma-image-create', ['only' => ['store']]);
        $this->middleware('permission:proforma-image-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:proforma-image-delete', ['only' => ['destroy']]);
        $this->middleware('permission:proforma-image-approve', ['only' => ['approve']]);
        $this->middleware('permission:proforma-image-decline', ['only' => ['decline']]);
    }
        public function index($seller_id)
    {
        $seller = Seller::findOrFail($seller_id);
        $projects = Project::all();
        $proformaImages = ProformaImage::where('seller_id', $seller_id)->with('project')->get();

        return view('sellers.show', compact('seller', 'projects', 'proformaImages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'project_id' => 'required|exists:projects,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'type' => 'required|string',
            'description' => 'nullable|string'
        ]);
    
        $imagePath = $request->file('image')->store('proforma_images', 'public');
    
        ProformaImage::create([
            'seller_id' => $request->seller_id,
            'project_id' => $request->project_id,
            'image_path' => $imagePath,
            'proforma_type' => $request->type, 
            'description' => $request->description,
        ]);
    
        return redirect()->back()->with('success', 'Proforma image uploaded successfully.');
    }
    public function approve($id)
    {
        $proformaImage = ProformaImage::findOrFail($id);
        $proformaImage->status = 'approved'; 
        $proformaImage->save();

        return redirect()->back()->with('success', 'Proforma image approved successfully.');
    }

    public function decline($id)
    {
        $proformaImage = ProformaImage::findOrFail($id);
        $proformaImage->status = 'declined'; 
        $proformaImage->save();

        return redirect()->back()->with('success', 'Proforma image declined successfully.');
    }
    public function destroy($id)
    {
        $proformaImage = ProformaImage::findOrFail($id);

        if ($proformaImage->image_path) {
            Storage::delete($proformaImage->image_path);
        }

        $proformaImage->delete();

        return redirect()->back()->with('success', 'Proforma image deleted successfully.');
    }
    
}
