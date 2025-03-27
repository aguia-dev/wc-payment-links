<?php
/**
 * Plugin Name:       Payment links for WooCommerce
 * Plugin URI:        https://github.com/aguia-dev/wc-payment-links
 * Description:       Create payment links and share them with your clients.
 * Author:            AGUIA.DEV
 * Domain Path:       /languages
 * Author URI:        https://github.com/aguia-dev/
 * License:           GPL v3 or later
 * Version:           1.0.5
 * Requires PHP:      8.0
 * Requires at least: 6.0
 * Requires Plugins: woocommerce
 *
 * @link    https://github.com/aguia-dev/
 * @package WCPaymentLink
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/vendor/autoload.php';


if (version_compare(phpversion(), '8.0') < 0) {
	wp_die(
		sprintf(
			"%s <p>%s</p>",
			__("The Payment links for WooCommerce isn't compatible to your PHP version. ", 'wc-payment-links'),
			__('The PHP version has to be a less 8.0!', 'wc-payment-links')
		),
		'The Payment links for WooCommerce -- Error',
		['back_link' => true]
	);
}

$boot = new WCPaymentLink\Core\Boot;
$boot->initialize();


function inspect_styles() {
    global $wp_styles;
	error_log( var_export( $wp_styles->queue, true ) );
}
add_action( 'wp_print_styles', 'inspect_styles' );
