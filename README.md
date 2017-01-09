# QR Code Plugin

**This README.md file should be modified to describe the features, installation, configuration, and general usage of this plugin.**

The **QR Code** Plugin is for [Grav CMS](http://github.com/getgrav/grav). QR code generator

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
```

## Usage

**Describe how to use the plugin.**

## Credits

**Did you incorporate third-party code? Want to thank somebody?**

## Contributing
The **Vimeo Grav Plugin** follows the [GitFlow branching model](http://nvie.com/posts/a-successful-git-branching-model), from development to release. The ```master``` branch always reflects a production-ready state while the latest development is taking place in the ```develop``` branch.

Each time you want to work on a fix or a new feature, create a new branch based on the ```develop``` branch: ```git checkout -b BRANCH_NAME develop```. Only pull requests to the ```develop``` branch will be merged.

## Copyright and license

Copyright &copy; 2017 Christian Worreschk under the [MIT Licence](http://opensource.org/licenses/MIT). See [README](LICENSE).
