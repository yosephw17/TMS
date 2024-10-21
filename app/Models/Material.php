<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name','code', 'symbol','color','unit_price', 'unit_of_measurement'];

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'material_stock')->withPivot('quantity');
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
}

