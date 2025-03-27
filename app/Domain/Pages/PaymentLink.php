<?php

declare(strict_types=1);

namespace WCPaymentLink\Domain\Pages;

use DateTime;
use Exception;
use WCPaymentLink\Exceptions\ExpiredTokenException;
use WCPaymentLink\Persistence\Repositories\LinkRepository;
use WCPaymentLink\Integrations\WooCommerce\Logs\Logger;
use Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils;

final class PaymentLink
{
    private array $fields = [];
    private LinkRepository $repository;
    private Logger $logger;

    public function __construct(private string $token)
    {
        $this->repository = new LinkRepository();
        $this->logger = new Logger();
    }

    private function fillCart(): void
    {
        global $woocommerce;

        try {
            $links = $this->repository->findBy('token', $this->token);

            if (!empty($links)) {
                $link = array_shift($links);

                if ($link->getExpireAt() <= new DateTime()) {
                    throw new ExpiredTokenException($link->getToken());
                }

                $woocommerce->cart->empty_cart();
                $products = $link->getProducts();

                foreach($products as $product) {
                    $woocommerce->cart->add_to_cart($product['product'], $product['quantity']);
                }

                if ($link->getCoupon()) {
                    $woocommerce->cart->apply_coupon($link->getCoupon());
                }

                $this->fields['token'] = $this->token;

            } else {
                wp_redirect(home_url('/404'));
            }

        } catch (Exception $e) {
            $this->logger->add([
                'type'   => 'ACCESS PAYMENT LINK',
                'object' => [$e->getMessage()]
            ], 'error');

            wp_redirect(home_url('/404'));
        }
    }

    private function setPostVar(): void
    {
        $post = get_post(get_option('woocommerce_checkout_page_id'));

        $GLOBALS['post'] = $post;
        $this->fields['postId'] = $post->ID;
    }

    private function getCheckoutFile(): string
    {
        $fileName = CartCheckoutUtils::is_checkout_block_default() ? 'blocks' : 'classic';
        return "Pages/checkout/{$fileName}.php";
    }

    private function enqueue(): void
    {
        wp_enqueue_scripts('wc-payment-links-checkout', wcplConfig()->distUrl('scripts/theme/pages/checkout/index.js'), [], wcplConfig()->pluginVersion());
    }

    public function request(): void
    {
        $this->setPostVar();
        $this->fillCart();
        $this->enqueue();

        echo wcplUtils()->render($this->getCheckoutFile(), $this->fields);

        exit;
    }
}
