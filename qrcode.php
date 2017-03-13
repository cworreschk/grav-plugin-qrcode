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

    protected $locale;

    const QRCODE_REGEX = '/\[qrcode(.*?)\](.*?)\[\/qrcode\]/is';
    const QRCODE_ATTRIBUTES_REGEX = '/(\w+)\s*=\s*((?:[^\"\'\s]+)|\'(?:[^\']*)\'|\"(?:[^\"]*)\")/i';

    /**
     *  Initialize the correct locale
     */
    private function initializeLocale()
    {
        $locales = [];
        $language = $this->grav['language'];

        // Available Languages
        if (isset($this->grav['user']) && $this->grav['user']->authenticated) $locales[] = $this->grav['user']->language;
        if ($language->enabled())$locales[] = $language->getLanguage();
        $locales[] = 'en';

        $locales = array_unique(array_filter($locales));
        foreach ($locales as $locale) {
            if (isset($this->grav['languages'][$locale]['PLUGIN_QRCODE'])) {
                $this->locale = $locale;
                break;
            }
        }
    }

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
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        $this->initializeLocale();

        if ($this->isAdmin()) {
            $this->enable([
                'onAssetsInitialized' => ['onAssetsInitialized', 0]
            ]);
        } else {
            require_once(__DIR__.'/vendor/autoload.php');
            $this->enable([
                'onTwigExtensions'    => ['onTwigExtensions', 0],
                'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
                'onPageContentRaw'    => ['onPageContentRaw', 0]
            ]);
        }
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

    /**
     * @param string $inline
     * @param array $config_params
     * @return array
     */
    private function buildParameters($inline, $config_params)
    {
        preg_match_all(static::QRCODE_ATTRIBUTES_REGEX, trim($inline), $matches);
        if ((!isset($matches[1])) || empty($matches[1])) return $config_params;

        foreach ($matches[1] as $idx => $key){
            $config_params[$key] = preg_replace('/[\"\'](.*)[\"\']/', "$1", $matches[2][$idx]); // Without quotes
        }

        return $config_params;
    }

    /**
     * @param string $text
     * @param string $attributes
     * @param array $page_params
     * @return string
     */
    private function buildQrCode($text='', $attributes='', $page_params=[])
    {
        if (empty($text)) return '';

        // Parameters
        $parameters = $page_params;
        $attributes = trim($attributes);
        if (!empty($attributes)) {
            $parameters = $this->buildParameters($attributes, $page_params);
        }

        // Build the replacement embed HTML string
        $twig = $this->grav['twig'];
        $code = $twig->processTemplate('partials/qrcode.html.twig', [
            'text'       => trim($text),
            'parameters' => $parameters
        ]);

        return $code;
    }

    /**
     * Add Editor Button JS if wanted
     */
    public function onAssetsInitialized()
    {
        if ((!$this->isAdmin()) || (!$this->config->get('plugins.qrcode.editor_button', false))) return;

        $plugin_translations = $this->grav['languages'][$this->locale]['PLUGIN_QRCODE'];
        $translations = [
            'EDITOR_BUTTON_TOOLTIP' => $plugin_translations['EDITOR_BUTTON_TOOLTIP'],
            'EDITOR_BUTTON_PROMPT'  => $plugin_translations['EDITOR_BUTTON_PROMPT']
        ];
        $code = 'this.GravQrCodePlugin = this.GravQrCodePlugin || {};';
        $code.= 'if (!this.GravQrCodePlugin.translations) this.GravQrCodePlugin.translations = '.json_encode($translations, JSON_UNESCAPED_SLASHES) .';';

        $this->grav['assets']->addInlineJs($code);
        $this->grav['assets']->add('plugin://qrcode/admin/editor-button/js/button.js');
    }

    public function onPageContentRaw(Event $e)
    {
        $page = $e['page'];
        $config = $this->mergeConfig($page, true);
        if (!$config->get('enabled')) return;


        // Page parameters
        $page_params = $config->get('parameters', []);
        if (isset($page_params['logo']) && (!is_string($page_params['logo'])) && (! empty($page_params['logo']))) {
            $logo = reset($page_params['logo']);
            if (isset($logo['path'])) $page_params['logo'] = $logo['path'];
        }

        preg_match_all(static::QRCODE_REGEX, $page->getRawContent(), $matches);
        $matches = array_filter($matches);

        if (empty($matches) || empty($matches[2])) return;

        $raw_content = $page->getRawContent();
        $match_count = count($matches[0]);

        for ($idx = 0; $idx < $match_count; $idx++) {
            // Double check to make sure we found something
            if (!isset($matches[2][$idx])) continue;

            $code = $this->buildQrCode($matches[2][$idx], $matches[1][$idx], $page_params);
            $raw_content = str_replace($matches[0][$idx], $code, $raw_content);
        }

        $page->setRawContent($raw_content);
    }
}
