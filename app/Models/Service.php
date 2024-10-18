<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
class Service extends Model
{
    use HasFactory,HasRoles;
  
    /**
     * 
     *	
     * @var array
     */
    protected $fillable = [
        'name','details',
    ];
   
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_services')->withTimestamps();
    }
    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetail::class);
    }
    


    
}