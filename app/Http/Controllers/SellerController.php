<?php
namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:manage-seller', ['only' => ['index']]);
        $this->middleware('permission:seller-view', ['only' => ['show']]);
        $this->middleware('permission:seller-create', ['only' => ['store']]);
        $this->middleware('permission:seller-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:seller-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $sellers = Seller::all();
        return view('sellers.index', compact('sellers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        Seller::create($request->all());

        return redirect()->route('sellers.index')->with('success', 'Seller created successfully.');
    }

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

    public function destroy($id)
    {
        Seller::findOrFail($id)->delete();

        return redirect()->route('sellers.index')->with('success', 'Seller deleted successfully.');
    }
}
