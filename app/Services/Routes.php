<?php

declare(strict_types=1);

namespace WCPaymentLink\Services;

final class Routes implements InterfaceService
{
	public function initialize(): void
	{
        add_action('rest_api_init', [$this, 'registerDomains'], 9999);
	}

	public function registerDomains(): void
    {
        $routes = apply_filters('wp-parresia-dahsboard_register-routes', []);

		foreach ($routes as $route) {
			if (class_exists($route)) {
				$class = new $route;
				$class->register();
			}
		}
    }
}
