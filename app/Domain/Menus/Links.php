<?php

declare(strict_types=1);

namespace WCPaymentLink\Controllers\Menus;

use WCPaymentLink\Model\LinkModel;
use WCPaymentLink\Repository\LinkRepository;
use WCPaymentLink\Services\WooCommerce\Logs\Logger;
use WP_Query;

final class Links
{
    private array $fields = [];
    private Logger $logger;
    private LinkRepository $linkRepository;

    public function __construct()
    {
        $this->linkRepository = new LinkRepository();
        $this->logger = new Logger();

        $this->handleActions();
    }

    private function handleActions(): void
    {
        $page = filter_input( INPUT_POST, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
        $action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS );

        if ($page != 'wc-payment-links-settings') {
            return;
        }

        if ($action === 'save') {
            $this->saveLink();
        }

        if ($action === 'remove') {
            $this->deleteLink();
        }
    }

    private function saveLink(): void
    {
        try {
            $page = filter_input( INPUT_POST, 'page', FILTER_SANITIZE_SPECIAL_CHARS );

            $fields = $this->getPostFields();
            $errors = $this->validateFields($fields);

            if (!empty($errors)) {
                $this->fields['errors'] = $errors;
                return;
            }

            $date = new \DateTime($fields['expire_at']);
            $date->setTime(
                $fields['hour'] ?? 0,
                0
            );

            $link = new LinkModel(
                $fields['name'],
                $fields['token'],
                $date,
                $fields['coupon'],
            );

            $link->setId($fields['link_id'] ?? 0);
            $products = json_decode(html_entity_decode($fields['products'])) ?? [];
            $linkId = $this->linkRepository->save($link);

            if ($linkId) {
                $link->setId($linkId);
                $link->saveProducts($products);
                $this->fields['success'][] = __('Link saved successfully!', 'wc-payment-links');
                return;
            } else {
                $this->fields['errors'] = [ __('Error message', 'wc-payment-links')];
                $this->logger->add([
                    'type'   => 'SAVE',
                    'object' => $fields
                ], 'error');

                wp_redirect(admin_url("admin.php?page={$page}"));
            }

        } catch (\Exception $e) {
            $this->fields['errors'] = [__('Error message', 'wc-payment-links')];

            $this->logger->add([
                'type'   => 'SAVE',
                'object' => $e->getMessage()
            ], 'error');

            wp_redirect(admin_url("admin.php?page={$page}"));
        }
    }

    private function deleteLink(): void
    {
        $page = filter_input( INPUT_POST, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
        $linkId = filter_input( INPUT_POST, 'link', FILTER_VALIDATE_INT );

        try {
            $link = new LinkModel();
            $link->setId($linkId);

            $data = $this->linkRepository->remove($link);

            if (!$data) {
                $this->fields['errors'] = [ __('Error message', 'wc-payment-links')];
                $this->logger->add([
                    'type'   => 'SAVE',
                    'Link' => $linkId
                ], 'error');

                wp_redirect(admin_url("admin.php?page={$page}"));
            }

            $this->fields['success'][] = __('Link deleted successfully!', 'wc-payment-links');
            return;

        } catch (\Exception $e) {
            $this->logger->add([
                'type'   => 'SAVE',
                'object' => $e->getMessage()
            ], 'error');

            wp_redirect(admin_url("admin.php?page={$page}"));
        }
    }

    private function getPostFields(): array
    {
        return [
            'link_id'   => filter_input( INPUT_POST, 'link_id', FILTER_VALIDATE_INT ),
            'name'      => filter_input( INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '',
            'token'     => filter_input( INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '',
            'expire_at' => filter_input( INPUT_POST, 'expire_at', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '',
            'coupon'    => filter_input( INPUT_POST, 'coupon', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '',
            'hour'      => filter_input( INPUT_POST, 'hour', FILTER_VALIDATE_INT ),
            'products'  => filter_input( INPUT_POST, 'products', FILTER_SANITIZE_SPECIAL_CHARS ) ?? ''
        ];
    }

    private function validateFields(array $fields): array
    {
        $errors = [];
        $needed = [
            'name'      => __('Name', 'wc-payment-links'),
            'token'     => __('Token', 'wc-payment-links'),
            'expire_at' => __('Expire Date', 'wc-payment-links'),
        ];

        foreach ($needed as $key => $value) {
            if (empty($fields[$key])) {
                $pattern = __('The field: <b>%s</b> is required!', 'wc-payment-links');
                $message = sprintf($pattern, $value);

                $errors[] = $message;
            }
        }

        return $errors;
    }
    public function enqueue(): void
    {
        wp_enqueue_scripts('wc-payment-links-settings', wcplConfig()->distUrl('scripts/admin/menus/settings/index.js'), [], wcplConfig()->pluginVersion());
    }

    public function getProducts(): void
    {
        $this->fields['products'] = [];

        $query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1
        ]);

        $products = $query->get_posts();

        if ($products) {
            $this->fields['products'] = $products;
        }
    }

    public function getLinks(): void
    {
        $orderBy = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '';
        $order = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '';
        $page  = filter_input( INPUT_GET, 'table-page', FILTER_SANITIZE_NUMBER_INT ) ?? 1;
        $search  = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '';

        $this->fields['links'] = [];

        $links = $this->linkRepository->findAll(
            $orderBy,
            10,
            $page,
            $order,
            true,
            $search
        );

        if (isset($links['rows'])) {
            $this->fields['links'] = $links['rows'];
            $this->fields['pagination'] = $links['pagination'] ?? [];
        }

        $this->fields['search'] = $search ?? '';
    }

    public function request(): void
    {
        $this->enqueue();
        $this->getProducts();
        $this->getLinks();

        echo wcplUtils()->render('Admin/menus/settings/index.php', $this->fields);
    }
}
