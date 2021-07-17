<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends General
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'postal_code',
        'district_id',
        'addressable_id',
        'addressable_type',
    ];

    protected $with = ['district'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function addressable()
    {
        return $this->morphTo();
    }
}
