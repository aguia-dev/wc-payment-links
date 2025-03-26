<?php

declare(strict_types=1);

namespace WCPaymentLink\Services;

interface InterfaceService
{
    public function initialize(): void;
    public function registerDomains(): void;
}
