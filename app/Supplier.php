<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'contact',
        'user_id',
        'city_id',
    ];

    protected $with = ['city', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
