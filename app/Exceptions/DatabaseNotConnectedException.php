<?php

declare(strict_types=1);

namespace WCPaymentLink\Exceptions;

use RuntimeException;

class DatabaseNotConnectedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
			__('Unable to connect to database!', 'wc-payment-links'),
            503
        );
    }
}
