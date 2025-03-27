<?php

declare(strict_types=1);

namespace WCPaymentLink\Exceptions;

use DomainException;

class EntityNotFoundException extends DomainException
{
    public function __construct(string $entity)
    {
		$message = sprintf(
			__('Could not find record in database: %s', 'wc-payment-links'),
			$entity
		);

        parent::__construct(
			$message,
            422
        );
    }
}
