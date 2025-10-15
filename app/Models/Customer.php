<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone', 'address', 'type'];
    
    protected $casts = [
        'type' => 'string',
    ];
    
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    
    public function getTypeDisplayAttribute()
    {
        return $this->type === 'project' ? 'Project Customer' : 'Material Customer';
    }
}
