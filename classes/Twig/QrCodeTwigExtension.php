<?php

namespace Grav\Plugin\QrCode\Twig;

use Grav\Common\GravTrait;
use Endroid\QrCode\QrCode;

/**
 * Class QrCodeTwigExtension
 * @package Grav\Plugin\QrCode\Twig
 */
class QrCodeTwigExtension extends \Twig_Extension
{

    use GravTrait;

    const HEX_REGEX = '/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/i';
    const RGB_REGEX = '/rgb\(\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*\)/i';
    const RGBA_REGEX = '/rgba\(\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*((?:0.[0-9]+)|[01])\s*\)/i';

    private function buildColorArray($color)
    {
        preg_match(static::RGB_REGEX, $color, $matches);
        if (!empty($matches)) return ['r' => $matches[1], 'g' => $matches[2], 'b' => $matches[3], 'a' => 1];

        preg_match(static::RGBA_REGEX, $color, $matches);
        if (!empty($matches)) return ['r' => $matches[1], 'g' => $matches[2], 'b' => $matches[3], 'a' => $matches[4]];

        preg_match(static::HEX_REGEX, $color, $matches);
        if (!empty($matches)) return ['r' => hexdec($matches[1]), 'g' => hexdec($matches[2]), 'b' => hexdec($matches[3]), 'a' => 1];

        return null;
    }

    /**
     * Returns an array of available twig functions
     * @return array
     */
    public function getFunctions()
    {
        return [
          new \Twig_SimpleFunction('qrcode_image_data', [$this, 'qrCodeImageData']),
          new \Twig_SimpleFunction('qrcode_image_element', [$this, 'qrCodeImageElement'])
        ];
    }

    public function qrCodeImageData($text, $params = [])
    {
        $qrCode = new QrCode();
        $qrCode->setText($text);
        $grav = static::getGrav();

        if (isset($params['size'])) $qrCode->setSize((int)$params['size']);
        if (isset($params['padding'])) $qrCode->setPadding((int)$params['padding']);
        if (isset($params['image_type'])) $qrCode->setImageType(strtolower((string)$params['image_type']));
        if (isset($params['error_correction_level'])) $qrCode->setErrorCorrection($params['error_correction_level']);
        if (isset($params['border'])) $qrCode->setDrawBorder(boolval($params['border']));
        if (isset($params['quiet_zone'])) $qrCode->setDrawQuietZone(boolval($params['quiet_zone']));

        // Label
        if (isset($params['label'])) {
            $qrCode->setLabel($params['label']);
            if (isset($params['label_font_size'])) $qrCode->setLabelFontSize((int)$params['label_font_size']);
        }

        // Logo
        if (isset($params['logo']) && file_exists($params['logo'])) {
            $qrCode->setLogo($params['logo']);
            if (isset($params['logo']['size'])) $qrCode->setLogoSize((int)$params['logo']['size']);
        }

        // Foreground
        if (isset($params['foreground_color'])) {
            $color = $this->buildColorArray($params['foreground_color']);
            if (!is_null($color)) $qrCode->setForegroundColor($color);
        }

        // Background
        if (isset($params['background_color'])){
            $color = $this->buildColorArray($params['background_color']);
            if (!is_null($color)) $qrCode->setBackgroundColor($color);
        }

        return $qrCode->getDataUri();
    }

    public function qrCodeImageElement($text, $params = [])
    {
        $data_uri = $this->qrCodeImageData($text, $params);
        $image = "<img class=\"qrcode\" src=\"{$data_uri}\"";

        // Maybe an alt attribute shall be used
        if (isset($params['alt_attribute'])) {
            if ((strtolower($params['alt_attribute']) == 'label') && (!empty($params['label']))) {
                $image.= " alt=\"{$params['label']}\"";
            } elseif ((strtolower($params['alt_attribute']) == 'text') && (!empty($text))) {
                $image.= " alt=\"{$text}\"";
            }
        }

        $image.= ' />';
        return $image;
    }
}