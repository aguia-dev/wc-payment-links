<?php

declare(strict_types=1);

namespace WCPaymentLink\Services;

final class WooCommerce implements InterfaceService
{
    public function initialize(): void
	{
        add_action('woocommerce_init', [$this, 'registerDomain']);
	}

    public function registerDomains(): void
    {
        if (class_exists('WooCommerce')) {
            $this->inicializeWooommerce();
        }
    }

    public function inicializeWooommerce(): void
    {
        add_filter('woocommerce_locate_template', [$this, 'setOverwrittenWoocommerceTemplates'], 10, 3);
        add_filter('wc_get_template_part', [$this, 'overrideWoocommerceTemplatePart'], 10, 3);
    }

    public function setOverwrittenWoocommerceTemplates($template, $templateName, $template_path)
    {
        $templateDir = wcplConfig()->viewsDir() . 'WooCommerce/';

        $path = $templateDir . $templateName;

        return file_exists($path) ? $path : $template;
    }

    public function overrideWoocommerceTemplatePart($template, $slug, $name)
    {
        $template_directory = wcplConfig()->viewsDir() . 'WooCommerce/';
        $path = $name ? $template_directory . "{$slug}-{$name}.php" : $template_directory . "{$slug}.php";

        return file_exists( $path ) ? $path : $template;
    }
}
