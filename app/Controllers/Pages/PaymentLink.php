<?php

namespace WCPaymentLink\Controllers\Pages;

use DateTime;
use Exception;
use WCPaymentLink\Controllers\Render\AbstractRender;
use WCPaymentLink\Exceptions\ExpiredTokenException;
use WCPaymentLink\Repository\LinkRepository;
use WCPaymentLink\Services\WooCommerce\Logs\Logger;
use Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils;

class PaymentLink extends AbstractRender
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
        $GLOBALS['post'] = get_post(get_option('woocommerce_checkout_page_id'));
    }

    private function getCheckoutFile(): string
    {
        $fileName = CartCheckoutUtils::is_checkout_block_default() ? 'blocks' : 'classic';
        return "Pages/checkout/{$fileName}.php";
    }

    private function enqueue(): void
    {
        $this->enqueueScripts([
            'name' => 'wc-payment-links',
            'file' => 'scripts/theme/pages/checkout/index.js'
        ]);
    }

    public function request(): void
    {
        $this->setPostVar();
        $this->fillCart();
        $this->enqueue();

        echo $this->render($this->getCheckoutFile(), $this->fields);

        exit;
    }
}
