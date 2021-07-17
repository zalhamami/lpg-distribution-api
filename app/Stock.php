<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends General
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'owner_id',
        'owner_type',
    ];

    protected $with = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function owner()
    {
        return $this->morphTo();
    }
}
