<?php

namespace Upgradelabs\Ifthenpay\MBWay\Models;

use Illuminate\Database\Eloquent\Model;

class MBWayPaymentStatusModel extends Model
{
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
