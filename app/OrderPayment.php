<?php

namespace App;

use App\Helpers\DateTimeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends General
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'file_url',
        'note',
        'verified_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getVerifiedAtAttribute($value)
    {
        return DateTimeHelper::convertDatetimeToIso($value);
    }

    public function setVerifiedAtAttribute($value)
    {
        $this->attributes['verified_at'] = DateTimeHelper::convertIsoToDatetime($value);
    }
}
