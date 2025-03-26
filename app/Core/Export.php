<?php

declare(strict_types=1);

namespace WCPaymentLink\Core;

final class Export
{
    private array $data;

    public function __construct()
    {
        $this->data = [
            'override_templates' => $this->checkOverwrittenTemplates(),
            'site_data'          => [
                'active_plugins'   => $this->getSiteActivePlugins(),
                'active_theme'     => $this->getSiteActiveTheme()
            ]
        ];
    }

    public function get_data(): array
    {
        return $this->data;
    }

    private function checkOverwrittenTemplates(): bool
    {
        return isdinamicDir(get_templatedinamicDirectory() . wcplConfig()->baseFolder() . '/');
    }

    private function getSiteActiveTheme(): array
    {
        $theme = wp_get_theme();

        return [
            'name'    => $theme->name,
            'version' => $theme->version
        ];
    }

    private function getSiteActivePlugins(): array
    {
        return wp_get_active_and_valid_plugins();
    }
}
