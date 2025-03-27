<?php

declare(strict_types=1);

namespace WCPaymentLink\Core;

use WCPaymentLink\Services\Menus;
use WCPaymentLink\Services\Routes;

final class Boot
{
    public function __construct()
    {
        add_action('activated_plugin', [$this, 'activationFunction']);
        add_action('init', [$this, 'defineCustomPayPermalink']);
        add_action('admin_init', [$this, 'checkMissingDependencies']);
        add_action('admin_init', [$this, 'enqueueGlobalScripts']);
        add_action('admin_init', [$this, 'desactivationFunction']);
        add_action('plugin_action_links', [$this, 'setSettingsLink'], 10, 2);
        add_action('init', [$this, 'initialize'], 999);
        //add_action('template_redirect', [$this, 'customPayCheckout']);

        $this->loadServices();
    }

    public function loadServices(): void
    {
        $services = [
            Routes::class,
            Menus::class
        ];

        if (empty(self::getMissingDependencies())) {
            foreach ($services as $service) {
                if (class_exists($service)) {
                    $class = new $service;
                    $class->initialize();
                }
            }
        }
    }

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

    public function enqueueGlobalScripts(): void
    {
        if(isset($_REQUEST['page']) && $_REQUEST['page'] === 'wc-payment-links-settings') {
            wp_enqueue_script(
                'wc-payment-links-settings',
                wcplConfig()->distUrl('scripts/admin/menus/settings/index.js'),
                [],
                wcplConfig()->pluginVersion()
            );

            wp_enqueue_style('wc-payment-links-tailwind-css', wcplConfig()->distUrl('styles/app.css'), [], wcplConfig()->pluginVersion());
        }
    }

    public function setSettingsLink(array $arr, string $name): array
    {
        if ($name === wcplConfig()->baseFile() && empty(self::getMissingDependencies())) {
            $label = sprintf(
                '<a href="admin.php?page=wc-payment-links-settings" id="deactivate-wc-payment-links" aria-label="%s">%s</a>',
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
            $boot = new \WCPaymentLink\Core\Database();
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
            $uninstall = new Uninstall();
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
        $title = __('Payment links for WooCommerce', 'wc-payment-links');

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

    public function customPayCheckout(): void
    {
        global $wp;
        if (isset($wp->query_vars['token'])) {
            $paymentLink = new PaymentLink($wp->query_vars['token']);
            $paymentLink->request();
        };
    }
}

