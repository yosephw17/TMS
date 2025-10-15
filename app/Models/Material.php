<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'code',
        'unit_of_measurement', 
        'unit_price', 
        'color', 
        'symbol',
        'type'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    protected $attributes = [
        'unit_price' => 0.00,
    ];

    /**
     * Relationship with stocks (many-to-many with pivot data)
     */
    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'material_stock')
                    ->withPivot([
                        'id', 'quantity', 'original_quantity', 'remaining_quantity',
                        'reference_number', 'batch_number', 'unit_price', 
                        'total_price', 'original_total_price', 'current_total_value',
                        'total_used', 'total_used_value', 'status', 'supplier',
                        'expiry_date', 'notes', 'movement_log', 'last_movement_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Relationship with purchase requests
     */
    public function purchaseRequests()
    {
        return $this->belongsToMany(PurchaseRequest::class, 'purchase_request_material')
                    ->withPivot(['quantity', 'total_cost', 'weighted_avg_price'])
                    ->withTimestamps();
    }

    /**
     * Get available quantity in a specific stock
     */
    public function getAvailableQuantityInStock($stockId)
    {
        return $this->stocks()
            ->where('stock_id', $stockId)
            ->wherePivot('remaining_quantity', '>', 0)
            ->wherePivot('status', 'active')
            ->sum('material_stock.remaining_quantity');
    }

    /**
     * Get total remaining quantity across all stocks
     */
    public function getTotalRemainingQuantity()
    {
        return $this->stocks()
            ->wherePivot('remaining_quantity', '>', 0)
            ->wherePivot('status', 'active')
            ->sum('material_stock.remaining_quantity');
    }

    /**
     * Get weighted average price for this material in a specific stock
     * Uses professional accounting method: (Qty1×Price1 + Qty2×Price2) ÷ (Qty1 + Qty2)
     */
    public function getWeightedAveragePrice($stockId)
    {
        $stockEntries = $this->stocks()
            ->where('stock_id', $stockId)
            ->wherePivot('remaining_quantity', '>', 0)
            ->wherePivot('status', 'active')
            ->wherePivotNotNull('unit_price')
            ->get();
    
        if ($stockEntries->isEmpty()) {
            return $this->unit_price ?? 0;
        }
    
        // 👉 Calculate arithmetic average of all unit prices
        $priceSum = 0;
        $entryCount = 0;
    
        foreach ($stockEntries as $entry) {
            $priceSum += $entry->pivot->unit_price;
            $entryCount++;
        }
    
        return $entryCount > 0 ? round($priceSum / $entryCount, 2) : ($this->unit_price ?? 0);
    }
    

    /**
     * Get FIFO entries for stock deduction (First In, First Out)
     * Returns entries ordered by creation date (oldest first)
     */
     /**
     * Get FIFO entries for stock deduction (First In, First Out)
     * Returns entries ordered by creation date (oldest first)
     */
    public function getFIFOEntries($stockId, $requiredQuantity)
    {
        $entries = $this->stocks()
            ->where('stock_id', $stockId)
            ->wherePivot('remaining_quantity', '>', 0)
            ->wherePivot('status', 'active')
            ->orderBy('material_stock.created_at', 'asc') // FIFO: oldest first
            ->get();

        $fifoEntries = [];
        $remainingNeeded = $requiredQuantity;

        foreach ($entries as $entry) {
            if ($remainingNeeded <= 0) break;

            $availableQuantity = $entry->pivot->remaining_quantity;
            $quantityToTake = min($remainingNeeded, $availableQuantity);
            $unitPrice = $entry->pivot->unit_price ?? ($this->unit_price ?? 0);

            $fifoEntries[] = [
                'entry' => $entry,
                'quantity_to_deduct' => $quantityToTake,
                'unit_price' => $unitPrice,
                'total_cost' => round($quantityToTake * $unitPrice, 2)
            ];

            $remainingNeeded -= $quantityToTake;
        }

        $totalAvailable = $entries->sum('pivot.remaining_quantity');

        return [
            'entries' => $fifoEntries,
            'total_cost' => round(array_sum(array_column($fifoEntries, 'total_cost')), 2),
            'can_fulfill' => $remainingNeeded <= 0,
            'shortage' => max(0, $remainingNeeded),
            'total_available' => $totalAvailable,
            'entries_found' => $entries->count()
        ];
    }
    /**
     * Get all stock entries for this material with details
     */
    public function getStockEntries($stockId = null)
    {
        $query = $this->stocks()
            ->wherePivot('remaining_quantity', '>', 0)
            ->wherePivot('status', 'active');

        if ($stockId) {
            $query->where('stock_id', $stockId);
        }

        return $query->orderBy('material_stock.created_at', 'asc')->get();
    }

    /**
     * Check if material has sufficient stock
     */
    public function hasSufficientStock($stockId, $requiredQuantity)
    {
        $availableQuantity = $this->getAvailableQuantityInStock($stockId);
        return $availableQuantity >= $requiredQuantity;
    }

    /**
     * Get movement history for this material in a specific stock
     */
    public function getMovementHistory($stockId)
    {
        $entries = $this->stocks()
            ->where('stock_id', $stockId)
            ->get();

        $movements = [];
        foreach ($entries as $entry) {
            $movementLog = json_decode($entry->pivot->movement_log, true) ?? [];
            foreach ($movementLog as $movement) {
                $movement['reference_number'] = $entry->pivot->reference_number;
                $movement['batch_number'] = $entry->pivot->batch_number;
                $movements[] = $movement;
            }
        }

        // Sort by timestamp (newest first)
        usort($movements, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $movements;
    }
}