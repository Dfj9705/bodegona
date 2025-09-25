<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'brand_id',
        'price',
    ];

    public function images()
    {
        return $this->hasMany( Image ::class);
    }

    public function brand()
    {
        return $this->belongsTo( Brand ::class);
    }

    public function movements()
    {
        return $this->hasMany( Movement::class);
    }
    public function sales()
    {
        return $this->hasMany( DetailSale::class);
    }
}
