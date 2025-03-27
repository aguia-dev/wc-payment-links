<?php

declare(strict_types=1);

namespace WCPaymentLink\Domain\Routes\Abstractions;

use Exception;
use Throwable;

abstract class Route
{
	private string $namespace;

	protected function registerRoute(string $route, $callback, array $methods = ['GET']): void
	{
		register_rest_route($this->namespace, $route, [
			'methods'  => $methods,
			'callback' => $callback,
		    'permission_callback' => '__return_true',
		] );
	}
	protected function sendJsonResponse(string $message = '', bool $success = true, int $code = 200, array $data = [])
	{
		$args = [
			"message" => $message,
			"success" => $success,
			"data"    => $data,
		];

		foreach ($args as $key => $item) {
			if (empty($item)) {
				unset($args[$key]);
			}
		}

		if (!empty($data)) {
			$args['data'] = $data;
		}

		return wp_send_json($args, $code);
	}

	protected function getNamespace(): string
	{
		return $this->namespace;
	}

	protected function setNamespace(string $namespace = ''): void
	{
		$this->namespace = wcplConfig()->pluginSlug();

        if ($namespace) {
            $this->namespace .= "/$namespace";
        }
	}

	protected function validateAuthentication(array $headers): void
	{
		$logged = false;

		try {
			if (isset($headers['authorization']) && is_array($headers['authorization'])) {
				$auth = array_shift($headers['authorization']);
				$basic = explode(':', base64_decode(str_replace('Basic ', '', $auth)));

				if (is_array($basic) && count($basic) === 2) {
					$logged = wp_signon([
						'user_login' => $basic[0],
						'user_password' => $basic[1]]
					);
				}
			}

			if (!$logged || is_wp_error($logged) || !in_array('administrator', $logged->roles)) {
				throw new Exception(__('User not authorized! Please, contact the site adminstrator', 'wc-payment-links'));
			}


		} catch (Throwable $t) {
			$this->sendJsonResponse(
				$t->getMessage(),
				false,
				401
			);
		}
	}
}
