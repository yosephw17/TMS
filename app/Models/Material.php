<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'symbol', 'unit_price', 'unit_of_measurement'];

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'material_stock')->withPivot('quantity');
    }
}

