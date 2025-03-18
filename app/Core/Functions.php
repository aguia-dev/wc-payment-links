<?php

namespace WCPaymentLink\Core;

use WCPaymentLink\API\Routes;
use WCPaymentLink\Controllers\Menus;
use WCPaymentLink\Controllers\Pages\PaymentLink;
use WCPaymentLink\Services\WooCommerce\WooCommerce;

class Functions
{
    public function initialize(): void
    {
        $locale = apply_filters( 'plugin_locale', get_locale(), wcplConfig()->pluginSlug() );

		load_textdomain( wcplConfig()->pluginSlug(), wcplConfig()->dynamicDir() . "/languages/" . wcplConfig()->pluginSlug() . "-$locale.mo" );
		load_plugin_textdomain( wcplConfig()->pluginSlug(), false, wcplConfig()->dynamicDir() . '/languages/' );

        load_plugin_textdomain(wcplConfig()->pluginSlug(), false);
    }

    public function defineCustomPayPermalink(): void
    {
        add_rewrite_rule('^pay/([^/]+)/?', 'index.php?token=$matches[1]', 'top');
        add_rewrite_tag('%token%', '([^&]+)');
        flush_rewrite_rules();
    }

    public function createAdminMenu(): void
    {
        if (empty(self::getMissingDependencies())) {
            $menus = new Menus();
            $menus->initializeMenus();
        }
    }


    public function woocommerce(): void
    {
        if (class_exists('WooCommerce')) {
            $woocommerce = new WooCommerce;
            $woocommerce->inicializeWooommerce();
        }
    }


    public function setSettingsLink(array $arr, string $name): array
    {
        if ($name === wcplConfig()->baseFile()) {

            $label = sprintf(
                '<a href="admin.php?page=wc-payment-links-links" id="deactivate-wc-payment-links" aria-label="%s">%s</a>',
                __('Links', 'wc-payment-links'),
                __('Links', 'wc-payment-links')
            );

            $arr['settings'] = $label;
        }

        return $arr;
    }

    public function activationFunction(string $plugin): void
    {
        if (wcplConfig()->baseFile() === $plugin) {
            $boot = new \WCPaymentLink\Infrastructure\Bootstrap();
            $boot->initialize();
        }
    }

    public function desactivationFunction(): void
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        if (!isset($_REQUEST['action']) || !isset($_REQUEST['plugin'])) {
            return;
        }

        $action = filter_var($_REQUEST['action'], FILTER_SANITIZE_SPECIAL_CHARS);
        $plugin = filter_var($_REQUEST['plugin'], FILTER_SANITIZE_SPECIAL_CHARS);

        if ($action === 'deactivate' && $plugin === wcplConfig()->baseFile()) {
            $uninstall = new Uninstall;
            $uninstall->reset();
        }
    }

    public function checkMissingDependencies(): void
    {
        $missingDependencies = self::getMissingDependencies();

        if (is_array($missingDependencies) && !empty($missingDependencies)) {
            add_action('admin_notices', [
                $this, 'displayDependencyNotice'
            ]);
        }
    }

    public function getMissingDependencies(): array
    {
        $plugins = wp_get_active_and_valid_plugins();

        $neededs = [
            'WooCommerce' => wcplConfig()->dynamicDir( __DIR__, 3 ) . '/woocommerce/woocommerce.php'
        ];

        foreach ($neededs as $key => $needed ) {
            if ( in_array( $needed, $plugins ) ) {
                unset( $neededs[$key] );
            }
        }

        return $neededs;
    }

    public function displayDependencyNotice(): void
    {
        $class = 'notice notice-error';
        $title = __('WC Payment Links', 'wc-payment-links');

        $message = __(
            'This plugin needs the following plugins to work properly:',
            'wc-payment-links'
        );

        $keys = array_keys(self::getMissingDependencies());
        printf(
            '<div class="%1$s"><p><strong>%2$s</strong> - %3$s <strong>%4$s</strong>.</p></div>',
            esc_attr($class),
            esc_html($title),
            esc_html($message),
            esc_html(implode(', ', $keys))
        );
    }

    public function registerRestAPI(): void
    {
        $routes = new Routes();
        $routes->register();
    }

    public function customPayCheckout(): void
    {
        global $wp;
        if (isset($wp->query_vars['token'])) {
            $paymentLink = new PaymentLink($wp->query_vars['token']);
            $paymentLink->request();
        };
    }
}
