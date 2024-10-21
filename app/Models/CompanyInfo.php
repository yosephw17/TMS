<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;
    protected $fillable=[
'name',
'phone',
'fax',
'po_box',
'email',
'motto',
     'logo'
    ];
}
