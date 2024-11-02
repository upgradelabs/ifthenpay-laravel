<?php

namespace Upgradelabs\Ifthenpay\MBWay\DTO;

class MBWayPaymentStatusResponse
{
    public function __construct(
        public string $CreatedAt,
        public string $Message,
        public string $RequestId,
        public string $Status,
        public string $UpdateAt,

    ) {}
}

/*
 * Response example:
 *
 * {
    "CreatedAt": "03-01-2024 15:15:06",
    "Message": "Success",
    "RequestId": "eR6mcnJzjFx7kOL1Ybdp",
    "Status": "000",
    "UpdateAt": "03-01-2024 15:15:16"
}
 */
