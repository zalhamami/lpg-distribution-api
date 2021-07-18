<?php

namespace App;

use App\Helpers\DateTimeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends General
{
    use HasFactory, SoftDeletes;

    const TYPE_USER = 'App\\User';
    const TYPE_AGENT = 'App\\Agent';
    const TYPE_SUPPLIER = 'App\\Supplier';

    protected $fillable = [
        'seller_id',
        'seller_type',
        'buyer_id',
        'buyer_type',
        'total_price',
        'tax',
        'ordered_at',
        'expired_at',
    ];

    protected $with = ['seller', 'buyer', 'status'];

    public function seller()
    {
        return $this->morphTo();
    }

    public function buyer()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function status()
    {
        return $this->hasOne(OrderStatus::class)->orderByDesc('id');
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class)->orderByDesc('id');
    }

    public function scopeDetails($query)
    {
        return $query->with(['items', 'payment']);
    }

    public function getOrderedAtAttribute($value)
    {
        return DateTimeHelper::convertDatetimeToIso($value);
    }

    public function setOrderedAtAttribute($value)
    {
        $this->attributes['ordered_at'] = DateTimeHelper::convertIsoToDatetime($value);
    }

    public function getExpiredAtAttribute($value)
    {
        return DateTimeHelper::convertDatetimeToIso($value);
    }

    public function setExpiredAtAttribute($value)
    {
        $this->attributes['expired_at'] = DateTimeHelper::convertIsoToDatetime($value);
    }
}
