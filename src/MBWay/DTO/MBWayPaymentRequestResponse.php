<?php

namespace Upgradelabs\Ifthenpay\MBWay\DTO;

final readonly class MBWayPaymentRequestResponse
{
    public function __construct(
        public string $Amount,
        public string $Message,
        public string $OrderId,
        public string $RequestId,
        public string $Status,
    ) {}
}

/*
 * {"Amount":"1.12","Message":"Pending","OrderId":"1234","RequestId":"cVlXMfzgZmOyRT5bLkED","Status":"000"}
 */
