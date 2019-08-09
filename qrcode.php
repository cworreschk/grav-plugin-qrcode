<?php

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use Grav\Plugin\QrCode\QRCodeGenerator;

/**
 * Class QRCodePlugin
 *
 * @package Grav\Plugin
 */
class QRCodePlugin extends Plugin
{

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [['onPluginsInitialized', 0]]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {

        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        require_once __DIR__ . '/vendor/autoload.php';

        $this->grav['qrcode'] = $generator = new QRCodeGenerator($this->config->get('plugins.qrcode'));

        $generator->generate('Hallo Welt');

        // Enable the main events we are interested in
        $this->enable([
            'onShortcodeHandlers' => ['onShortcodeHandlers', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigExtensions'    => ['onTwigExtensions', 0]
        ]);
    }

    /**
     * Add templates to twig lookup paths
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__.'/templates';
    }

    /**
     * Add QrCode twig extension
     */
    public function onTwigExtensions()
    {
        $this->grav['twig']->twig->addExtension(new QRCodeTwigExtension());
    }

    /**
     * Register shortcodes
     */
    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes(__DIR__.'/shortcodes');
    }

}
