<?php

namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Plugin\QrCode\Twig\QrCodeTwigExtension;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class QrCodePlugin
 * @package Grav\Plugin
 */
class QrCodePlugin extends Plugin
{

    const QRCODE_REGEX = '/\[qrcode\](.*)\[\/qrcode\]/i';

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        require_once(__DIR__.'/vendor/autoload.php');
        return [
          'onPageContentRaw' => ['onPageContentRaw', 0],
          'onTwigExtensions'    => ['onTwigExtensions', 0],
          'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPageContentRaw' => ['onPageContentRaw', 0]
        ]);
    }

    /**
     * Add QrCode twig extension
     */
    public function onTwigExtensions()
    {
        require_once __DIR__ . '/classes/Twig/QrCodeTwigExtension.php';
        $this->grav['twig']->twig->addExtension(new QrCodeTwigExtension());
    }

    /**
     * Add plugin templates to twig path
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onPageContentRaw(Event $e)
    {
        $page = $e['page'];
        $config = $this->mergeConfig($page, true);
        if (!$config->get('enabled')) return;

        // Function
        $twig = $this->grav['twig'];
        $function = function ($matches) use ($twig, $config) {
            $search = $matches[0];

            // double check to make sure we found a text
            if (!isset($matches[1])) return $search;

            // build the replacement embed HTML string
            $replace = $twig->processTemplate('partials/qrcode.html.twig', [
              'text'   => trim($matches[1]),
              'parameters' => $config->get('parameters', [])
            ]);

            // do the replacement
            return str_replace($search, $replace, $search);
        };

        $raw_content = preg_replace_callback(static::QRCODE_REGEX, $function, $page->getRawContent());
        $page->setRawContent($raw_content);

    }
}
