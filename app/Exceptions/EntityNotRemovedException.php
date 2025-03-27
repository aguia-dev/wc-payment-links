<?php

declare(strict_types=1);

namespace WCPaymentLink\Exceptions;

use DomainException;

class EntityNotRemovedException extends DomainException
{
    public function __construct(string $entity)
    {
		$message = sprintf(
			__('Could not remove database record: %s', 'wc-payment-links'),
			$entity
		);

        parent::__construct(
			$message,
            422
        );
    }
}
