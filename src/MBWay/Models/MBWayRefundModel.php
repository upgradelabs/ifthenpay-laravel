<?php

namespace Upgradelabs\Ifthenpay\MBWay\Models;

use Illuminate\Database\Eloquent\Model;


class MBWayRefundModel extends Model
{
    protected $table = 'ifthenpay_mbway_refunds';

    protected $fillable = [
        'order_id',
        'status',
        'amount',
        'message',
    ];


}
