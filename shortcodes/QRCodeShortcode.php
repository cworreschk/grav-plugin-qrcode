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

            $alt = '';
            if (isset($options['alt_attribute'])) {
                switch($options['alt_attribute']) {
                    case 'content': $alt = $content; break;
                    case 'label':   $alt = empty($options['label_text']) ? '' : $options['label_text']; break;
                }
            }

            $output = $this->twig->processTemplate('partials/qrcode.html.twig', [
                'source' => $code,
                'hash'   => $hash,
                'alt'    => $alt
            ]);

            return $output;

        });
    }
}