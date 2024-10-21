<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDetail extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'detail_name', 'description'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_service_detail');
    }
}
