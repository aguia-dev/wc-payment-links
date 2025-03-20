<?php

namespace WCPaymentLink\Core;

class Boot
{
    public function __construct()
    {
        add_action('init', [
            new Functions,
            'initialize'
        ]);

        add_action('init', [
            new Functions,
            'defineCustomPayPermalink'
        ]);

        add_action('init', [
            new Functions,
            'enqueueGlobalScripts'
        ], 10);

        add_action('admin_init', [
            new Functions,
            'desactivationFunction'
        ]);

        add_action('activated_plugin', [
            new Functions,
            'activationFunction'
        ]);

        add_action('admin_init', [
            new Functions,
            'checkMissingDependencies'
        ]);

        add_action('admin_menu', [
            new Functions,
            'createAdminMenu'
        ]);

        add_action('woocommerce_init', [
            new Functions,
            'woocommerce'
        ]);

        add_action('plugin_action_links', [
            new Functions,
            'setSettingsLink'
        ], 10, 2);

        add_action('rest_api_init', [
            new Functions,
            'registerRestAPI'
        ]);

        add_action('template_redirect', [
            new Functions,
            'customPayCheckout'
        ]);
    }
}

