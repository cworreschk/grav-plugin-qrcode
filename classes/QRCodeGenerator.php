<?php

namespace Grav\Plugin\QrCode;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;

class QRCodeGenerator {

    /** @var array $defaults Parameters */
    protected $defaults;

    const HEX_REGEX  = '/#(\S{2})(\S{2})(\S{2})/';
    const RGB_REGEX  = '/rgb\(\s?(\d+)\s?,\s?(\d+)\s?,\s?(\d+)\s?\)/i';
    const RGBA_REGEX = '/rgba\(\s?(\d+)\s?,\s?(\d+)\s?,\s?(\d+)\s?,\s?(\d+)\s?\)/i';

    /**
     * QRCodeGenerator constructor.
     *
     * @param array $parameters Parameters
     */
    public function __construct(array $parameters=[])
    {
        $this->defaults = $parameters;
    }

    protected function buildColorArray(string $color) : array {

        // RGB color
        preg_match(static::RGB_REGEX, $color, $matches);
        if (!empty($matches)){
            return ['r' => $matches[1], 'g' => $matches[2], 'b' => $matches[3], 'a' => 1];
        }

        // RGBA color
        preg_match(static::RGBA_REGEX, $color, $matches);
        if (!empty($matches)){
            return ['r' => $matches[1], 'g' => $matches[2], 'b' => $matches[3], 'a' => $matches[4]];
        }

        // HEX color
        preg_match(static::HEX_REGEX, $color, $matches);
        if (!empty($matches)){
            return ['r' => hexdec($matches[1]), 'g' => hexdec($matches[2]), 'b' => hexdec($matches[3]), 'a' => 1];
        }

        return [];
    }

    /**
     * @param string $content Content
     * @param array $options Options
     * @return string
     */
    public function generate(string $content, array $options=[]) : string {
        $code = new QrCode($content);

        $options = array_merge($this->defaults, $options);

        if (isset($options['size'])) {
            $code->setSize($options['size']);
        }
        if (isset($options['margin'])) {
            $code->setMargin($options['margin']);
        }
        if (isset($options['foreground_color'])) {
            $code->setForegroundColor($this->buildColorArray($options['foreground_color']));
        }
        if (isset($options['background_color'])) {
            $code->setBackgroundColor($this->buildColorArray($options['background_color']));
        }
        if (isset($options['error_correction_level'])) {
            $code->setErrorCorrectionLevel(new ErrorCorrectionLevel($options['error_correction_level']));
        }

        // Label
        if (!empty($options['label_text'])) {
            $font = empty($options['label_font_path']) ? null : ROOT_DIR.ltrim($options['label_font_path'], '/');
            $code->setLabel(
                $options['label_text'],
                $options['label_font_size'],
                $font,
                $options['label_alignment']
            );
            //        $code->setLabelMargin();
        }

        if (isset($options['image_type'])) {
            $code->setWriterByName($options['image_type']);
        }

        // Logo
        if (!empty($options['logo_file'])) {
            $code->setLogoPath(ROOT_DIR.ltrim(key($options['logo_file']), '/'));
            $code->setLogoSize($options['logo_width'], $options['logo_height']);
        }

        header('Content-Type: '.$code->getContentType());
        echo $code->writeString();
        exit();

        return $code->writeDataUri();
    }
}