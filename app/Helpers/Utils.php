<?php

declare(strict_types=1);

namespace WCPaymentLink\Helpers;

final class Utils
{
    public function render(string $file, array $data): string
    {
        extract($data);
        ob_start();

        $template = get_template_directory() . "/". wcplConfig()->baseFolder() ."/$file";

        if (!file_exists($template)) {
            $template = wcplConfig()->viewsDir($file);
        }

        if (file_exists($template)) {
            require_once $template;
        }

        return ob_get_clean();
    }
}
