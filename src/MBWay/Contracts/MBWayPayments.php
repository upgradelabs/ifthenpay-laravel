<?php

namespace Upgradelabs\Ifthenpay\MBWay\Contracts;

interface MBWayPayments
{
    public function send(): array;
}
