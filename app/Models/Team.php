<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    // ...
    use HasFactory;
    protected $fillable = ['name', 'description'];
    public function users(): BelongsToMany
    {

        return $this->belongsToMany(User::class, 'team_user')->withTimestamps();
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_team')->withTimestamps();
    }
}
