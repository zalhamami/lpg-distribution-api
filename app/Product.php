<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'supplier_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->without(['city', 'address']);
    }
}
