<?php

namespace App;

use App\Helpers\DateTimeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'contact',
        'open_hour',
        'close_hour',
        'user_id',
        'supplier_id',
    ];

    protected $with = ['address', 'supplier'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->without('address');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
