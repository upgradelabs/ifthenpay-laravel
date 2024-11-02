<?php

namespace Upgradelabs\Ifthenpay\MBWay\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
