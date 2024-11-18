<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;
    protected $fillable=['name','phone'];
    public function proformaImages()
    {
        return $this->hasMany(ProformaImage::class);
    }
}
