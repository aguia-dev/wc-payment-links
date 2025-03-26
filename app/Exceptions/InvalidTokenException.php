<?php

declare(strict_types=1);

namespace WCPaymentLink\Exceptions;

final class InvalidTokenException extends \UnexpectedValueException
{
    public function __construct()
    {
        parent::__construct(
            __("The value used for token is not a valid uuid!", 'wc-payment-links')
        );
    }
}
