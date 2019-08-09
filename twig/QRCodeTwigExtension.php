<?php

namespace Grav\Plugin;

class QRCodeTwigExtension extends \Twig_Extension
{
    /**
     * Returns extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'QRCodeTwigExtension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('qrcode_image_data_uri', [$this, 'generateImage']),
            new \Twig_SimpleFunction('qrcode_image_element', [$this, 'generateImage'])
        ];
    }

    /**
     * @param string $text Text
     * @param array $params
     */
    public function qrCodeImageDataUri($text, $params = [])
    {
        user_error(__FUNCTION__ . ' is deprecated and will be removed. Please use "qrcode_data" instead', E_USER_DEPRECATED);
    }

    /**
     * @param string $text Text
     * @param array $params Parameters
     */
    public function qrCodeImageElement($text, $params = [])
    {
        user_error(__FUNCTION__ . ' is deprecated and will be removed. Please use "qrcode_image" instead', E_USER_DEPRECATED);
    }

    public function generateImage($content, $params = [])
    {
        return "<div class='qrcode-image'>{$content}</div>";
    }

    public function generateData($content, $params = [])
    {
        return "<div class='qrcode-data-uri'>{$content}</div>";
    }
}