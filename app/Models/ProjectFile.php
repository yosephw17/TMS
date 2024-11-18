<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'file_name', 'file_path', 'file_type'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'project_team')->withTimestamps();
    }
}
