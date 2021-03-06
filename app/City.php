<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'province_id',
        'iso_id',
        'name',
    ];

    protected $with = ['province'];

    public function province()
    {
        return $this->belongsTo(Province::class)->without('country');
    }
}
