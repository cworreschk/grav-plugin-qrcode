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

    const QRCODE_REGEX = '/\[qrcode(.*)\](.*)\[\/qrcode\]/i';
    const QRCODE_ATTRIBUTES_REGEX = '/(\w+)\s*=\s*((?:[^\"\'\s]+)|\'(?:[^\']*)\'|\"(?:[^\"]*)\")/i';

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

    private function buildParameters($inline, $config_params)
    {
        preg_match_all(static::QRCODE_ATTRIBUTES_REGEX, $inline, $matches);
        if ((!isset($matches[1])) || empty($matches[1])) return $config_params;

        foreach ($matches[1] as $idx => $key){
            $value = preg_replace('/[\"\'](.*)[\"\']/', "$1", $matches[2][$idx]);
            if (strpos($key, 'label_') === 0) {
                $key = substr($key, 6);
                $config_params['label'][$key] = $value;
            } else {
                $config_params[$key] = $value;
            }
        }

        return $config_params;
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

            // Double check to make sure we found something
            if (!isset($matches[2])) return $search;

            // Parameters
            $parameters = $config->get('parameters', []);
            if (isset($matches[1]) && (!empty($matches[1]))) {
                $parameters = $this->buildParameters($matches[1], $parameters);
            }

            // Build the replacement embed HTML string
            $replace = $twig->processTemplate('partials/qrcode.html.twig', [
              'text'   => trim($matches[2]),
              'parameters' => $parameters
            ]);

            // do the replacement
            return str_replace($search, $replace, $search);
        };

        $raw_content = preg_replace_callback(static::QRCODE_REGEX, $function, $page->getRawContent());
        $page->setRawContent($raw_content);

    }
}
