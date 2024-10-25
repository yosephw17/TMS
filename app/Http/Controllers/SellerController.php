<?php
namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
class SellerController extends Controller
{
    // Display a listing of sellers
    public function index()
    {
        $sellers = Seller::all();
        return view('sellers.index', compact('sellers'));
    }

    // Store a newly created seller in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        Seller::create($request->all());

        return redirect()->route('sellers.index')->with('success', 'Seller created successfully.');
    }

    // Update the specified seller in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $seller = Seller::findOrFail($id);
        $seller->update($request->all());

        return redirect()->route('sellers.index')->with('success', 'Seller updated successfully.');
    }

    // Remove the specified seller from the database
    public function destroy($id)
    {
        Seller::findOrFail($id)->delete();

        return redirect()->route('sellers.index')->with('success', 'Seller deleted successfully.');
    }
}
