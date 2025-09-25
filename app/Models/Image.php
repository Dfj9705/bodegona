<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
    ];

    public function producto()
    {
        return $this->belongsTo( Product::class , 'product_id' );
    }
}
