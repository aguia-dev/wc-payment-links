<?php

declare(strict_types=1);

namespace WCPaymentLink\Services;

final class Menus implements InterfaceService
{
    public function initialize(): void
    {
        add_action('admin_menu', [$this, 'registerDomains'], 10);
    }

    public function registerDomains(): void
    {
        $this->createAdminMenu();
        $this->removeDefaultSubmenu();
    }

    private function defineMenus(): array
    {
        return [
            ['Links', __('Payment Links', 'wc-payment-links')]
        ];
    }

    public function createAdminMenu(): void
    {
        $classes = $this->defineMenus();
        $menus = [];

        foreach ($classes as $key => $class) {

            $slug     = $this->getMenuSlug($class[0]);
            $function = wcplConfig()->pluginNamespace() . "\\Domain\\Menus\\$class[0]";
            $menu     = [
                'title'    => $class[1],
                'slug'     => 'wc-payment-links-' . $slug,
                'function' => [new $function, 'request'],
                'position' => $key
            ];

            array_push($menus, $menu);
        }

        $this->createMenus($menus);
    }

    public function getMenuSlug(string $controller): string
    {
        $split = str_split($controller);
        $slug = '';
        $count = 0;

        foreach ($split as $letter) {
            if (ctype_upper($letter)) {
                if ($count == 0) {
                    $slug .= strtolower($letter);
                } else {
                    $slug .= '_' . strtolower($letter);
                }
            } else {
                $slug .= $letter;
            }
            $count++;
        }

        return $slug;
    }

    private function createMenus(array $menus): void
    {
        foreach ($menus as $menu) {
            add_submenu_page(
                'woocommerce',
                $menu['title'],
                $menu['title'],
                'manage_woocommerce',
                $menu['slug'],
                $menu['function']
            );
        }

        ## Remove default submenu
        remove_submenu_page(wcplConfig()->pluginSlug() ,wcplConfig()->pluginSlug());
    }

    private function removeDefaultSubmenu(): void
    {
        $pluginSlug = wcplConfig()->pluginSlug();
        remove_submenu_page($pluginSlug, $pluginSlug);
    }
}
