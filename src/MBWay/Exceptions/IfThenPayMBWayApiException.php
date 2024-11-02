<?php

namespace Upgradelabs\Ifthenpay\MBWay\Exceptions;

use Exception;

class IfThenPayMBWayApiException extends Exception
{
    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 400
    ) {
        parent::__construct($message, $statusCode);
    }
}
