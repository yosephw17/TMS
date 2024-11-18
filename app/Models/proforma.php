<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    protected $fillable = [
        'project_id',
        'customer_id',
        'ref_no',
        'before_vat_total',
        'vat_percentage',
        'vat_amount',
        'after_vat_total',
        'discount',
        'final_total',
        'payment_validity',
        'delivery_terms',
        'type',
        'date'
    ];
    protected $casts = [
        
        'date' => 'date', // for proformas
    ];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'proforma_material')
                    ->withPivot('quantity', 'total_price')
                    ->withTimestamps();
    }
    public function works()
    {
        return $this->hasMany(ProformaWork::class);
    }
}
