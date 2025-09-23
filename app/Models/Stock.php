<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['name', 'location'];

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'material_stock')
                    ->withPivot([
                        'quantity', 'original_quantity', 'remaining_quantity',
                        'reference_number', 'batch_number', 'unit_price', 
                        'total_price', 'original_total_price', 'current_total_value',
                        'total_used', 'total_used_value', 'status', 'supplier',
                        'expiry_date', 'notes', 'movement_log', 'last_movement_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get materials with their reference-based quantities and average prices
     */
    public function getMaterialsWithReferences()
    {
        return $this->materials()
            ->wherePivot('quantity', '>', 0)
            ->get()
            ->groupBy('id')
            ->map(function ($materialGroup) {
                $material = $materialGroup->first();
                $entries = $materialGroup;
                
                $totalQuantity = $entries->sum('pivot.quantity');
                $totalValue = $entries->sum('pivot.total_price');
                $averagePrice = $totalQuantity > 0 ? $totalValue / $totalQuantity : $material->unit_price;

                $material->total_quantity = $totalQuantity;
                $material->average_price = $averagePrice;
                $material->reference_entries = $entries->pluck('pivot');
                
                return $material;
            });
    }

    /**
     * Get total value of all materials in this stock
     */
    public function getTotalStockValue()
    {
        return $this->materials()
            ->wherePivot('quantity', '>', 0)
            ->wherePivotNotNull('total_price')
            ->get()
            ->sum('pivot.total_price');
    }
}
