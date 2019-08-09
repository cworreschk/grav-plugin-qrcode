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

            $content = $sc->getContent();

            $hash = md5($content);
            $code = $this->grav['qrcode']->generate($content);

            $output = $this->twig->processTemplate('partials/qrcode.html.twig', [
                'source' => $code,
                'label'  => $content,
                'hash'   => $hash
            ]);

            return $output;

        });
    }
}