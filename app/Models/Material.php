<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name','code', 'symbol','color', 'unit_of_measurement', 'type'];

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'material_stock')
                    ->withPivot([
                        'quantity', 'original_quantity', 'remaining_quantity',
                        'reference_number', 'batch_number', 'unit_price', 
                        'total_price', 'original_total_price', 'current_total_value',
                        'total_used', 'total_used_value', 'status', 'supplier',
                        'expiry_date', 'notes', 'movement_log', 'last_movement_at'
                    ])
                    ->withTimestamps();
    }
    public function projects()
    {
    return $this->belongsToMany(Project::class)->withPivot('quantity');
    }
    public function proformas()
    {
        return $this->belongsToMany(Proforma::class, 'proforma_material')
                    ->withPivot('quantity', 'total_price')
                    ->withTimestamps();
    }

    /**
     * Get total available quantity across all stocks
     */
    public function getTotalAvailableQuantity()
    {
        return $this->stocks()->sum('material_stock.quantity');
    }

    /**
     * Get average price across all available stock entries
     */
    public function getAveragePrice()
    {
        $stockEntries = $this->stocks()
            ->wherePivot('quantity', '>', 0)
            ->wherePivotNotNull('unit_price')
            ->get();

        if ($stockEntries->isEmpty()) {
            return $this->unit_price; // Fallback to material's base unit price
        }

        $totalValue = $stockEntries->sum(function ($stock) {
            return $stock->pivot->unit_price * $stock->pivot->quantity;
        });
        
        $totalQuantity = $stockEntries->sum('pivot.quantity');
        
        return $totalQuantity > 0 ? $totalValue / $totalQuantity : $this->unit_price;
    }

    /**
     * Get available quantity in a specific stock
     */
    public function getAvailableQuantityInStock($stockId)
    {
        $stock = $this->stocks()->where('stock_id', $stockId)->first();
        return $stock ? $stock->pivot->quantity : 0;
    }
}
