<?php


namespace WCPaymentLink\Controllers\Render;

abstract class AbstractRender implements InterfaceRender
{
    protected bool $hasEnqueue = true;

    protected function enqueueScripts(array $script): void
    {
        $link = isset($script['external']) ? $script['external'] : wcplConfig()->distUrl($script['file']);
        wp_enqueue_script($script['name'], $link);
    }

    protected function enqueueStyles(array $style): void
    {
        $link = isset($style['external']) ? $style['external'] : wcplConfig()->distUrl($style['file']);
        wp_enqueue_style($style['name'], $link);
    }

    private function enqueueDefault(): void
    {
        if ($this->hasEnqueue) {}
    }

    public function render(string $file, array $data): string
    {
        $this->enqueueDefault();
        return wcplUtils()->render($file, $data);
    }
}
