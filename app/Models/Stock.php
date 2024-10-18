<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['name', 'location'];

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'material_stock')->withPivot('quantity');
    }
}
