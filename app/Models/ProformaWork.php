<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'proforma_id', 
        'work_name', 
        'work_unit', 
        'work_amount', 
        'work_quantity', 
        'work_total'
    ];

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }
}
