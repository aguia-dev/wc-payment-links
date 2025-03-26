<?php

declare(strict_types=1);

namespace WCPaymentLink\Services\WooCommerce\Logs;

use WC_Logger;

final class Logger
{
  private WC_Logger $wc;
  private string $prefix;
  private bool $enabled;

  public function __construct(bool $enabled = true)
  {
    $this->wc      = new WC_Logger();
    $this->enabled = $enabled;
    $this->prefix  = wcplConfig()->pluginSlug();
  }

  public function add($var, string $type = 'database'): void
  {
    switch ($type) {
      case 'error':
        $log   = "{$this->prefix}-error";
        $title = '--- WC PAYMENT LINK ERROR LOG ---';
        break;

      case 'success':
        $log   = "{$this->prefix}-success";
        $title = '--- WC PAYMENT LINK SUCCESS LOG ---';
        break;

      default:
        $log   = "{$this->prefix}-database";
        $title = '--- WC PAYMENT LINK REQUEST LOG ---';
        break;
    }

    if ($this->enabled) {
      $this->wc->add($log, "\n{$title} : \n" . print_r($var, true) . "\n");
    }
  }
}
