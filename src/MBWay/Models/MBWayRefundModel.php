<?php

namespace Upgradelabs\Ifthenpay\MBWay\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MBWayRefundModel extends Model
{
    protected $table = 'ifthenpay_mbway_refunds';

    protected $fillable = [
        'order_id',
        'status',
        'amount',
        'message',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
