<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;
    protected $fillable=[
        'project_id', 'user_id','stock_id', 'type', 'non_stock_name', 'non_stock_price', 'non_stock_quantity', 'non_stock_image', 'details','status',

    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'purchase_request_material')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
