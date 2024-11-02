<?php

namespace Upgradelabs\Ifthenpay\MBWay\Enums;

enum MBWayPaymentRefundStatus: int
{
    case Success = 1;
    case InsufficientFunds = -1;
    case Error = 0;
}
