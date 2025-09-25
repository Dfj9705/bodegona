<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Brand extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'name'
    ];
    
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
