<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'city_id',
    ];

    protected $with = ['city'];

    public function city()
    {
        return $this->belongsTo(City::class)->without('province');
    }
}
