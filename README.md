[![Build Status](https://travis-ci.org/LightboxDigital/wp-dynamic-image-resizer.svg?branch=master)](https://travis-ci.org/LightboxDigital/wp-dynamic-image-resizer)

# WordPress Dynamic Image Resizer

License: MIT
License URI: https://opensource.org/licenses/MIT

Resize WordPress images on the fly without needing to register image sizes.

## Description

Thanks to [Tom McFarlin](https://twitter.com/tommcfarlin)
and [Devin Vinson](https://twitter.com/devinvinson) for
their work on [WordPress Plugin Boilerplate Generator](http://wppb.me/).

Thanks to [Tim Kinnane](https://twitter.com/timkinnane) and [Dalton Rooney](https://twitter.com/dalton)
for their work on the [initial resizer code](https://gist.github.com/timkinnane/e82eb87d9cc489620b80).

Please be aware that currently this code does not support responsive images.

Whilst this is developed as a plugin, it can just as easily be used through composer. The dual
development approach meant that ongoing dev was more straight forward in terms of unit tests etc.

## Installation

1. Upload `dynamic-image-resizer` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Alternatively install using composer: `composer require lightboxdigital/wp-dynamic-image-resizer`

## Changelog

#### 1.0
* Completely refactored on top of WPDB.
* Added unit tests.
* Added PHPCS ruleset.
