<?php

namespace Upgradelabs\Ifthenpay\MBWay\Models;

use Illuminate\Database\Eloquent\Model;

class MBWayPaymentStatusModel extends Model
{
    protected $table = 'ifthenpay_mbway_payment_statuses';

    protected $fillable = [
        'status',
        'amount',
        'message',
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
