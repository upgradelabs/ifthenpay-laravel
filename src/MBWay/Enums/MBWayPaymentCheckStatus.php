<?php

namespace Upgradelabs\Ifthenpay\MBWay\Enums;

enum MBWayPaymentCheckStatus: string
{
    case Success = '000';
    case TransactionRejected = '020';
    case TransactionExpired = '101';
    case TransactionDeclined = '122';
    case TransactionAwaitingPayment = '123';
}

/*
 000 - Transaction successfully completed (Payment confirmed).

020 - Transaction rejected by the user.

101 - Transaction expired (the user has 4 minutes to accept the payment in the MB WAY App before expiring)

122 - Transaction declined to the user.
 */
