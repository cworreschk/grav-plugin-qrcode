<?php

namespace Grav\Plugin\Shortcodes;

use Grav\Plugin\QrCode\QRCodeGenerator;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Class QRCodeShortcode
 *
 * @package Grav\Plugin\Shortcodes
 */
class QRCodeShortcode extends Shortcode
{
    public function init()
    {
        $this->shortcode->getHandlers()->add('qrcode', function (ShortcodeInterface $sc) {

            $content = trim($sc->getContent());

            $hash = md5($content);
            $code = $this->grav['qrcode']->generate($content);

            $options = $this->grav['qrcode']->getDefaults();

            // Alt attribute
            $alt = '';
            if (isset($options['html_element_alt'])) {
                switch($options['html_element_alt']) {
                    case 'content': $alt = $content; break;
                    case 'label':   $alt = empty($options['label_text']) ? '' : $options['label_text']; break;
                }
            }

            // CSS classes
            $classes = ['qrcode'];
            if (!empty($options['html_element_classes'])) {
                $classes = array_merge($classes, $options['html_element_classes']);
            }

            $output = $this->twig->processTemplate('partials/qrcode.html.twig', [
                'source'  => $code,
                'hash'    => $hash,
                'classes' => implode(' ', $classes),
                'alt'     => $alt
            ]);

            return $output;

        });
    }
}