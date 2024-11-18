<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyInfo;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:manage-setting', ['only' => ['index']]);
        $this->middleware('permission:setting-view', ['only' => ['show']]);
        $this->middleware('permission:setting-create', ['only' => ['store']]);
        $this->middleware('permission:setting-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:setting-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $companyInfos = CompanyInfo::all();
        return view('company_info.index', compact('companyInfos'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'required|email|unique:company_infos,email',
            'motto' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $companyInfoData = $request->all();
    
        if ($request->hasFile('logo')) {
            $imagePath = $request->file('logo')->store('logos', 'public');
            $companyInfoData['logo'] = $imagePath; 
        }
    
        CompanyInfo::create($companyInfoData);
    
        return redirect()->route('settings.index')->with('success', 'Company info added successfully.');
    }
    

    public function update(Request $request, $id)  
      {
        
        $companyInfo = CompanyInfo::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'fax' => 'nullable|string|max:50',
            'po_box' => 'nullable|string|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('company_infos')->ignore($companyInfo->id),
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $companyInfoData = $request->all();
            if ($request->hasFile('logo')) {
            if ($companyInfo->logo) {
                Storage::disk('public')->delete($companyInfo->logo);
            }
                $imagePath = $request->file('logo')->store('logos', 'public');
            $companyInfoData['logo'] = $imagePath;
        }
    
        $companyInfo->update($companyInfoData);
    
        return redirect()->route('settings.index')->with('success', 'Company info updated successfully.');
    }
    
    public function destroy($id)
    {
        $companyInfo=CompanyInfo::find($id);
        $companyInfo->delete();
        return redirect()->route('settings.index')->with('success', 'Company info deleted successfully.');
    }
   
}
