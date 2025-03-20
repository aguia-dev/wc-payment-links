<?php
/**
 * Plugin Name: WC Payment Links
 * Plugin URI:  https://github.com/aguia-dev/wc-payment-links
 * Description: Payment links for WooCommerce
 * Author:      AGUIA.DEV
 * Domain Path: /languages
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
			__("The WC Payment Links isn't compatible to your PHP version. ", 'wc-payment-links'),
			__('The PHP version has to be a less 8.0!', 'wc-payment-links')
		),
		'The WC Payment Links -- Error',
		['back_link' => true]
	);
}

new WCPaymentLink\Core\Boot;
