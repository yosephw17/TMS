<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable=[
'name', 'customer_id', 'starting_date', 'ending_date', 'description','location','total_price','status'
    ];

    public function serviceDetails()
    {
        return $this->belongsToMany(ServiceDetail::class, 'project_service_detail');
    }   
    public function materials()
{
    return $this->belongsToMany(Material::class)->withPivot('quantity');
}
public function customer()
{
    return $this->belongsTo(Customer::class);
}
public function files()
{
    return $this->hasMany(ProjectFile::class);
}   

public function teams()
{
    return $this->belongsToMany(Team::class, 'project_team')->withTimestamps();
}
public function dailyActivities(){
    return $this->hasMany(DailyActivity::class);
}
public function proformas(){
    return $this->hasMany(Proforma::class);
}
public function purchaseRequests(){
    return $this->hasMany(PurchaseRequest::class);
}
}
