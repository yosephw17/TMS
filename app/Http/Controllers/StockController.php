<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Material;
use App\Models\MaterialStockEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function __construct()
    {
        // Apply authentication middleware
        $this->middleware('auth');

        $this->middleware('permission:manage-stock', ['only' => ['index']]);
        $this->middleware('permission:stock-view', ['only' => ['show']]);
        $this->middleware('permission:stock-create', ['only' => ['store']]);
        $this->middleware('permission:stock-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:stock-delete', ['only' => ['destroy']]);
        $this->middleware('permission:stock-add-material', ['only' => ['addMaterial']]);
        $this->middleware('permission:stock-remove-material', ['only' => ['removeMaterial']]);
    }

    public function index()
    {
        $stocks = Stock::all();
        return view('stocks.index', compact('stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        Stock::create($request->only('name', 'location'));

        return redirect()->route('stocks.index')->with('success', 'Stock created successfully.');
    }

    public function show($id)
    {
        $stock = Stock::with(['materials' => function($query) {
            $query->withPivot('id');
        }])->findOrFail($id); 
        $materials = Material::all(); 
        return view('stocks.show', compact('stock', 'materials'));
    }

    public function addMaterial(Request $request, $id)
    {
        $request->validate([
            'materials' => 'required|array|min:1', 
            'materials.*' => 'exists:materials,id', 
            'quantities' => 'required|array',
            'unit_prices' => 'required|array',
            'suppliers' => 'nullable|array',
            'batch_numbers' => 'nullable|array',
            'expiry_dates' => 'nullable|array',
        ]);
    
        $selectedMaterials = $request->input('materials');   
        foreach ($selectedMaterials as $materialId) {
            $request->validate([
                "quantities.$materialId" => 'required|integer|min:1',
                "unit_prices.$materialId" => 'required|numeric|min:0',
            ]);
        }
    
        $stock = Stock::findOrFail($id);
    
        foreach ($selectedMaterials as $materialId) {
            $quantity = $request->input("quantities.$materialId");
            $unitPrice = $request->input("unit_prices.$materialId");
            $totalPrice = $quantity * $unitPrice;
            $notes = $request->input("notes.$materialId");
            $supplier = $request->input("suppliers.$materialId");
            $batchNumber = $request->input("batch_numbers.$materialId");
            $expiryDate = $request->input("expiry_dates.$materialId");
            
            // Generate reference number
            $referenceNumber = $this->generateReferenceNumber($stock->id, $materialId);
            
            // Add to material_stock pivot table with full tracking
            $stock->materials()->attach($materialId, [
                'quantity' => $quantity,
                'original_quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'reference_number' => $referenceNumber,
                'batch_number' => $batchNumber,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'original_total_price' => $totalPrice,
                'current_total_value' => $totalPrice,
                'total_used' => 0,
                'total_used_value' => 0,
                'status' => 'active',
                'supplier' => $supplier,
                'expiry_date' => $expiryDate,
                'notes' => $notes,
                'movement_log' => json_encode([
                    [
                        'type' => 'initial_stock',
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'timestamp' => now(),
                        'user' => auth()->user()->name,
                        'notes' => 'Initial stock entry'
                    ]
                ])
            ]);
        }
    
        return redirect()->route('stocks.show', $id)->with('success', 'Materials added with reference numbers successfully.');
    }

    /**
     * Generate reference number following project pattern
     */
    private function generateReferenceNumber($stockId, $materialId)
    {
        $stock = Stock::find($stockId);
        $material = Material::find($materialId);
        
        // Get first 3 characters of stock name and material name
        $stockCode = strtoupper(substr($stock->name, 0, 3));
        $materialCode = strtoupper(substr($material->name, 0, 3));
        
        // Get today's date
        $date = Carbon::now()->format('Ymd');
        
        // Get sequence number for today
        $todayEntries = DB::table('material_stock')
            ->where('stock_id', $stockId)
            ->where('material_id', $materialId)
            ->whereDate('created_at', Carbon::today())
            ->whereNotNull('reference_number')
            ->count();
        
        $sequence = str_pad($todayEntries + 1, 3, '0', STR_PAD_LEFT);
        
        return "{$stockCode}-{$materialCode}-{$date}-{$sequence}";
    }

    /**
     * Get average price for materials in purchase requests
     */
    public function getAveragePrice($stockId, $materialId)
    {
        $entries = DB::table('material_stock')
            ->where('stock_id', $stockId)
            ->where('material_id', $materialId)
            ->where('quantity', '>', 0)
            ->whereNotNull('unit_price')
            ->get();

        if ($entries->isEmpty()) {
            $material = Material::find($materialId);
            return $material ? $material->unit_price : 0;
        }

        $totalValue = $entries->sum(function ($entry) {
            return $entry->unit_price * $entry->quantity;
        });
        
        $totalQuantity = $entries->sum('quantity');
        
        return $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
    }

    public function removeMaterial(Request $request, $stockId, $materialId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $stock = Stock::findOrFail($stockId);

        $existingMaterial = $stock->materials()->where('material_id', $materialId)->firstOrFail();
        $existingQuantity = $existingMaterial->pivot->quantity;

        $quantityToRemove = $request->input('quantity');
        $newQuantity = $existingQuantity - $quantityToRemove;

        if ($newQuantity <= 0) {
            $stock->materials()->detach($materialId);
        } else {
            $stock->materials()->updateExistingPivot($materialId, ['quantity' => $newQuantity]);
        }

        return redirect()->route('stocks.show', $stockId)->with('success', 'Material quantity updated successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $stock = Stock::find($id);
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock not found');
        }

        $stock->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully');
    }

    public function getMaterials(Stock $stock)
    {
        $materials = $stock->materials()->withPivot('quantity')->get();

        return response()->json($materials);
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }

    /**
     * Update material quantity (for outgoing materials)
     */
    public function updateMaterialQuantity(Request $request, $stockId, $materialId, $pivotId)
    {
        $request->validate([
            'quantity_used' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $stock = Stock::findOrFail($stockId);
        $materialEntry = DB::table('material_stock')
            ->where('id', $pivotId)
            ->where('stock_id', $stockId)
            ->where('material_id', $materialId)
            ->first();

        if (!$materialEntry) {
            return redirect()->back()->withErrors('Material entry not found.');
        }

        $quantityUsed = $request->quantity_used;
        
        if ($quantityUsed > $materialEntry->remaining_quantity) {
            return redirect()->back()->withErrors('Cannot use more than available quantity.');
        }

        $newRemainingQuantity = $materialEntry->remaining_quantity - $quantityUsed;
        $usedValue = ($materialEntry->unit_price * $quantityUsed);
        $newCurrentValue = $materialEntry->current_total_value - $usedValue;
        $newTotalUsed = $materialEntry->total_used + $quantityUsed;
        $newTotalUsedValue = $materialEntry->total_used_value + $usedValue;

        // Update movement log
        $movementLog = json_decode($materialEntry->movement_log, true) ?? [];
        $movementLog[] = [
            'type' => 'outgoing',
            'quantity' => $quantityUsed,
            'remaining_after' => $newRemainingQuantity,
            'unit_price' => $materialEntry->unit_price,
            'value_used' => $usedValue,
            'reason' => $request->reason,
            'project_id' => $request->project_id,
            'timestamp' => now(),
            'user' => auth()->user()->name
        ];

        // Determine new status
        $status = $newRemainingQuantity <= 0 ? 'depleted' : 'active';

        // Update the record
        DB::table('material_stock')
            ->where('id', $pivotId)
            ->update([
                'remaining_quantity' => $newRemainingQuantity,
                'current_total_value' => $newCurrentValue,
                'total_used' => $newTotalUsed,
                'total_used_value' => $newTotalUsedValue,
                'status' => $status,
                'last_movement_at' => now(),
                'movement_log' => json_encode($movementLog)
            ]);

        return redirect()->route('stocks.show', $stockId)
            ->with('success', 'Material quantity updated successfully.');
    }

    /**
     * Print material entries by reference number
     */
    public function printByReference(Request $request, $stockId)
    {
        $referenceNumber = $request->get('reference');
        $stock = Stock::findOrFail($stockId);
        
        // Get ALL materials with the same reference number
        $materials = DB::table('material_stock')
            ->join('materials', 'material_stock.material_id', '=', 'materials.id')
            ->where('material_stock.stock_id', $stockId)
            ->where('material_stock.reference_number', $referenceNumber)
            ->select(
                'material_stock.id as pivot_id',
                'material_stock.quantity',
                'material_stock.unit_price', 
                'material_stock.total_price',
                'material_stock.reference_number',
                'material_stock.batch_number',
                'material_stock.supplier',
                'material_stock.notes',
                'material_stock.created_at',
                'materials.id as material_id',
                'materials.name', 
                'materials.unit_of_measurement', 
                'materials.color',
                'materials.symbol'
            )
            ->get();

        if ($materials->isEmpty()) {
            return redirect()->back()->withErrors('No materials found with this reference number.');
        }

        return view('stocks.print', compact('stock', 'materials', 'referenceNumber'));
    }

    /**
     * Print all materials in stock
     */
    public function printStock($stockId)
    {
        $stock = Stock::with(['materials' => function($query) {
            $query->wherePivot('remaining_quantity', '>', 0);
        }])->findOrFail($stockId);

        return view('stocks.print-all', compact('stock'));
    }

    /**
     * Print active materials only
     */
    public function printActiveStock($stockId)
    {
        $stock = Stock::findOrFail($stockId);
        
        $materials = DB::table('material_stock')
            ->join('materials', 'material_stock.material_id', '=', 'materials.id')
            ->where('material_stock.stock_id', $stockId)
            ->where('material_stock.status', 'active')
            ->where('material_stock.remaining_quantity', '>', 0)
            ->select('material_stock.*', 'materials.name', 'materials.unit_of_measurement', 'materials.color', 'materials.symbol')
            ->get();

        $referenceNumber = 'ACTIVE-MATERIALS';
        
        return view('stocks.print', compact('stock', 'materials', 'referenceNumber'));
    }
}
