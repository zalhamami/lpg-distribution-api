<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends General
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
    ];
}
