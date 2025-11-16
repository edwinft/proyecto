<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'status',
        'gateway_response'
    ];

    protected $casts = [
        'gateway_response' => 'array',
    ];

    // Estados permitidos
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS    = 'success';
    const STATUS_FAILED     = 'failed';
    const STATUS_REFUNDED   = 'refunded';
    const STATUS_CANCELED   = 'canceled';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}