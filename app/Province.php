<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'state_id',
        'country_id',
        'iso_id',
        'name',
        'timezone'
    ];

    protected $with = ['country'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
