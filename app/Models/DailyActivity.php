<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $fillable = ['project_id', 'user_id', 'description'];

    // Relation to Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relation to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
