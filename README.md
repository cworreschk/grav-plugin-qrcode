# QR Code Plugin

The **QR Code** Plugin for [Grav CMS](http://github.com/getgrav/grav) uses the library [endroid/QrCode](endroid/QrCode) to create QR Codes. You can simply create and embedd QR codes in your site by using the shortcode `[qrcode]`.

### Features
* Simply create QR Codes with the shortcode `[qrcode]`
* Override the plugin parameters for each page
* Inline attributes for the QR Code shortcode element    
* Button to add QR Codes in the content editor
* Twig functions to embed QR Codes in your templates
* Multi-Language Support

## Installation

Installing the QR Code plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install qrcode

This will install the QR Code plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/qrcode`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `qrcode`. You can find these files on [GitHub](https://github.com/christian-worreschk/grav-plugin-qrcode) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/qrcode
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/qrcode/qrcode.yaml` to `user/config/plugins/qrcode.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
editor_button: true

parameters:
  size: 200
  padding: 16
  error_correction_level: medium
  image_type: png
  foreground_color: "rgba(0, 0, 0, 1)"
  background_color: "rgba(255, 255, 255, 1)"
  border: false
  quiet_zone: false
  alt_attribute: none
  label: ""
  label_font_size: 16
  logo: ""
  logo_size: 48
```

* `enabled` Toggles if the QR Code plugin is turned on or off
* `editor_button` Allows you to easily add QR Codes in the page content editor
* `size` Size (width x height) of the QR Code in pixels
* `padding` Padding around the whole QR Code in pixels
* `error_correction_level` Sets how many percent corrupted data can be restored
* `image_type` Image type (`gif`, `jpeg` or `png`)
* `foreground_color` Foreground color (Hexadecimal, RGB or RGBA)
* `background_color` Background color (Hexadecimal, RGB or RGBA)
* `quiet_zone` Required four-module wide margin
* `alt_attribute` Defines, which content is used for the alt attribute in the &lt;img&gt; tag (`label`, `text` or `none`)
* `label` A text that will be printed under the QR Code
* `label_font_size` Font size of the label text in pixels
* `logo` The `Grav file` or the path of an image that will be printed in the center of the QR Code. It must have the same image type like the QR Code
* `logo_size` Logo size (width x height) in pixels

You can also set any of these settings on a per-page basis by adding them under a `qrcode:` setting in your page header. For example:
```markdown
---
title: Example page
qrcode:
  parameters:
    size: 400
    foreground_color: "#aa00cc"
---
```

## Usage

To use this plugin you simply need to include the `[qrcode]` shortcode in your page content:
```markdown
[qrcode]Hello, I'm a QR Code.[/qrcode]
```

This will be converted into the following HTML:
```html
<img class="qrcode" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOgAAADoAQMAAADfZzo7AAAABlBMVEX///8AAABVwtN+AAAAAnRSTlP9/RSN3GkAAADQSURBVFiF7ZVRDsMgDEN9/0t7lYiTjO0ETw0SonnpB44B6Q14+ISe4edrZaA0cxVYVx5HnZzdRcbTcsKd4VJr9EDTqlmzfmYQndss4/9dB6Edafj/wNDquHX2X0/WOgswOo9yrH4qsXQ3fmlCpXWQ60CvY06lmuvrtH0eLyZNy2P8iMKkqUh+CYKkvVovci+AtPqfrsf55tLUSDG+fUEQ7e27QHsfSjN7qZE0kpa5RxTb8xeXtuOX3bl0pIgwVLoluK9tIC0J2vH6cjuOvgGODxhInuDjnW9aAAAAAElFTkSuQmCC" />
```

In the shortcode you can use all available parameters as attributes to overwrite the global or page specific QR Code parameters.
 ```markdown
 [qrcode size=300 label="Scan me" border=true]Hello, I'm a QR Code.[/qrcode]
 ```

## Structure

![QR Code](assets/qrcode.png)

## Twig

This plugin offers two [Twig](http://twig.sensiolabs.org) functions that can be used in your code. `qrcode_image_data_uri()` and `qrcode_image_element()`
 
#### qrcode_image_data_uri( text, parameters=[] )
This function builds a QR Code by the given text and returns the image data uri of it.
  - `text` : The text that will be used for the QR Code.
  - `parameters` : An optional `array` of QR Code parameters. See `parameters` in the configuration file.

#### qrcode_image_element( text, parameters=[] )
This function builds a QR Code and returns the full `<img>` HTML element of it.
  - `text` : The text that will be used for the QR Code.
  - `parameters` : An optional `array` of QR Code parameters. See `parameters` in the configuration file. 

## Contributing
The **QR Code Grav Plugin** follows the [GitFlow branching model](http://nvie.com/posts/a-successful-git-branching-model), from development to release. The ```master``` branch always reflects a production-ready state while the latest development is taking place in the ```develop``` branch.

Each time you want to work on a fix or a new feature, create a new branch based on the ```develop``` branch: ```git checkout -b BRANCH_NAME develop```. Only pull requests to the ```develop``` branch will be merged.

## Copyright and license

Copyright &copy; 2017 Christian Worreschk under the [MIT Licence](http://opensource.org/licenses/MIT). See [README](LICENSE).
