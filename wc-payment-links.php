<?php
/**
 * Plugin Name: WC Payment Links
 * Plugin URI:  https://github.com/aguia-dev/wc-payment-links
 * Description: Payment links for WooCommerce
 * Author:      Matheus Aguiar
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

function wc_payment_link_fill_cart($request) {
    $token = sanitize_text_field($request['token']);
    $repository = new WCPaymentLink\Repository\LinkRepository();
    $woocommerce = WC()->cart;

    try {
        $links = $repository->findBy('token', $token);

        if (empty($links)) {
            return new WP_Error('no_link', 'Token inválido', ['status' => 404]);
        }

        $link = array_shift($links);

        if ($link->getExpireAt() <= new DateTime()) {
            return new WP_Error('expired_token', 'Token expirado', ['status' => 403]);
        }

        $woocommerce->empty_cart();

        foreach ($link->getProducts() as $product) {
            $woocommerce->add_to_cart($product['product'], $product['quantity']);
        }

        if ($link->getCoupon()) {
            $woocommerce->apply_coupon($link->getCoupon());
        }

        return rest_ensure_response(['message' => 'Carrinho atualizado com sucesso']);
    } catch (Exception $e) {
        return new WP_Error('error', $e->getMessage(), ['status' => 500]);
    }
}
