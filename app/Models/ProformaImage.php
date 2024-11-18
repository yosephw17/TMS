<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaImage extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'project_id', 'image_path', 'proforma_type', 'description','status'];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
