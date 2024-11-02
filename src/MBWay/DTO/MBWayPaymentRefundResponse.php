<?php

namespace Upgradelabs\Ifthenpay\MBWay\DTO;

class MBWayPaymentRefundResponse
{
    public function __construct(
        public int $Code,
        public string $Message,
    ) {}
}
