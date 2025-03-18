<?php
/**
 * Plugin Name: WC Payment Links
 * Plugin URI:  https://github.com/aguia-dev/wc-payment-link
 * Description: Payment links for WooCommerce
 * Author:      Matheus Aguiar
 * Author URI:  https://github.com/aguia-dev/
 * License:     GPL v3 or later
 * Version: 1.0.4
 *
 * @link    https://github.com/aguia-dev/
 * @since   1.0.1
 * @package WCPaymentLink
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/vendor/autoload.php';


if (version_compare(phpversion(), '8.0') < 0) {
	wp_die(
		sprintf(
			"%s <p>%s</p>",
			__("The WC Payment Links isn't compatible to your PHP version. ", 'wc-payment-link'),
			__('The PHP version has to be a less 8.0!', 'wc-payment-link')
		),
		'The WC Payment Links -- Error',
		['back_link' => true]
	);
}

new WCPaymentLink\Core\Boot;
