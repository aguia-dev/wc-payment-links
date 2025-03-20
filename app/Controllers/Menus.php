<?php

namespace WCPaymentLink\Controllers;

class Menus {

    private function defineMenus(): array
    {
        return [
            ['Links', __('Payment Links', 'wc-payment-links')]
        ];
    }

    public function initializeMenus(): void
    {
        $controllers = $this->defineMenus();
        $menus = [];

        foreach ($controllers as $key => $controller) {

            $slug     = $this->getMenuSlug($controller[0]);
            $function = wcplConfig()->pluginNamespace() . "\\Controllers\\Menus\\$controller[0]";
            $menu     = [
                'title'    => $controller[1],
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
                $menu['function']);
        }

        ## Remove default submenu
        remove_submenu_page(wcplConfig()->pluginSlug() ,wcplConfig()->pluginSlug());
    }
}
