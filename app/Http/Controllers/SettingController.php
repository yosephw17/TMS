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
            $imagePath = $request->file('logo')->store('logos', 'public'); // Store the image in 'storage/app/public/logos'
            $companyInfoData['logo'] = $imagePath; // Store the file path in the 'logo' field
        }
    
        CompanyInfo::create($companyInfoData);
    
        return redirect()->route('settings.index')->with('success', 'Company info added successfully.');
    }
    
    /**
     * Update the specified company info in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
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
                Rule::unique('company_infos')->ignore($companyInfo->id), // Proper uniqueness check for update
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Prepare company info data
        $companyInfoData = $request->all();
    
        // Handle logo upload if a new one is provided
        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($companyInfo->logo) {
                Storage::disk('public')->delete($companyInfo->logo);
            }
    
            // Store the new logo
            $imagePath = $request->file('logo')->store('logos', 'public');
            $companyInfoData['logo'] = $imagePath;
        }
    
        // Update the company info with the new data
        $companyInfo->update($companyInfoData);
    
        return redirect()->route('settings.index')->with('success', 'Company info updated successfully.');
    }
    

    /**
     * Remove the specified company info from storage.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $companyInfo=CompanyInfo::find($id);
        $companyInfo->delete();
        return redirect()->route('settings.index')->with('success', 'Company info deleted successfully.');
    }
   
}
