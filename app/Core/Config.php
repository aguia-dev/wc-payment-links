<?php

namespace WCPaymentLink\Core;

class Config
{
    public function distUrl(string $relative = ''): string
    {
        return plugins_url() . '/' . $this->baseFolder() . "/dist/$relative";
    }

    public function imageUrl(string $relative = ''): string
    {
        return plugins_url() . '/' . $this->baseFolder() . "/assets/images/$relative";
    }

    public function assetsUrl(string $relative = ''): string
    {
        return plugins_url() . '/' . $this->baseFolder() . "/assets/$relative";
    }

    public function viewsDir(string $relative = ''): string
    {
        return $this->dynamicDir() . "/app/Views/$relative";
    }

    public function dynamicDir(string $dir = __DIR__, int $level = 2): string
    {
        return dirname($dir, $level);
    }

    public function mainFileDir(): string
    {
        return $this->dynamicDir() . '/' . wcplConfig()->pluginSlug() . ".php";
    }

    public function baseFile(): string
    {
        return $this->baseFolder() . '/' . wcplConfig()->pluginSlug() . ".php";
    }

    public function baseFolder(): string
    {
        $dir = explode('/', $this->dynamicDir());
        return $dir[count($dir) - 1];
    }

    public function pluginName(): string
    {
        return __('WC Payment Links', 'wc-payment-links');
    }

    public function pluginSlug(): string
    {
        return 'wc-payment-links';
    }

    public function pluginNamespace(): string
    {
        return 'WCPaymentLink';
    }

    public function pluginPrefix(): string
    {
        return 'wpl';
    }
}
