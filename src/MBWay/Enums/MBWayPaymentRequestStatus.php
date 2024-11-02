<?php

namespace Upgradelabs\Ifthenpay\MBWay\Enums;

enum MBWayPaymentRequestStatus: string
{
    case Success = '000';
    case CouldNotComplete = '100';
    case TransactionDeclined = '122';
    case Error = '999';

}

/*
 * 000 - Request initialized successfully (pending acceptance).

100 - The initialization request could not be completed. You can try again.

122 - Transaction declined to the user.

999 - Error on initializing the request. You can try again.
 */
