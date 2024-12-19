<?php

namespace Upgradelabs\Ifthenpay\MBWay\Models;

use Illuminate\Database\Eloquent\Model;

class MBWayPaymentRequestModel extends Model
{
    protected $table = 'ifthenpay_mbway_payment_requests';

    protected $fillable = [
        'order_id',
        'request_id',
        'status',
        'amount',
        'message',
    ];
}
