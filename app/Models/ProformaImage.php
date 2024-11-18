<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaImage extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'project_id', 'image_path', 'proforma_type', 'description','status'];

    // A proforma image belongs to a seller
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // A proforma image belongs to a project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
