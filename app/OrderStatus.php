<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends General
{
    use HasFactory, SoftDeletes;

    const CREATED = 'created';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const PROCESSED = 'processed';
    const FINISHED = 'finished';

    protected $fillable = [
        'order_id',
        'status',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
