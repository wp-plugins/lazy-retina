=== Lazy Retina ===
Contributors: bitnulleins
Tags: lazy load, retina, performance, images, bit01, wordpress
Requires at least: 3.8
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically loads normal or retina versions of images while scrolling.

== Description ==

*Lazy Retina* boost your website performance and reduce the traffic because images outside of viewport (visible part of web page) won't be loaded until the user scrolls to them. This *lazy* technique works with jQuery or zepto.js.

*Lazy Retina* includes by default:

* thumbnails
* images in posts
* images in pages

This plugin is based on [Unveil.js](http://luis-almeida.github.io/unveil/).

= Retina Images =

The Lazy Retina plugin automatically adds retina sizes to every (custom) image size.

You've to [regenerate](http://wordpress.org/plugins/regenerate-thumbnails/) already uploaded images.

= Support in Themes =

If you want to *lazy load* an image use the following code in your template:

`<?php echo lazy_retina_image( $image_id, $size, $attr ); ?>`

= Settings =

Under "Options" -> "Media" you can:

* remove the default link to full image path

= Author =

* [Website (German)](http://www.bit01.de)
* [Google+](https://plus.google.com/u/0/111297209657356291114)

== Installation ==

Go to "Plugins" -> "Install". Search for "Lazy Retina", install and activate it.

Alternative Way:

* Download the zip-File and export it to the ./wp-content/plugins/ path.
* Activate the plugin at the backend.

== Frequently Asked Questions ==

= What if my image is to small for retina version? =

It'll return the normal sized image.

= Which size must an image have for retina? =

The image size have to at least the double of original width and height.

== Screenshots ==

1. Image link settings

== Changelog ==

= 1.0 =
* First version appeared

== Upgrade Notice ==

No upgrades.