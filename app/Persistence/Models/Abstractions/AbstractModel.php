<?php

declare(strict_types=1);

namespace WCPaymentLink\Persistence\Models\Abstractions;

abstract class AbstractModel
{
	public \DateTime $updatedAt;
	public \DateTime $createdAt;
}
