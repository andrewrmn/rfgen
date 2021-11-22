=== Disable URL Autocorrect Guessing ===
Contributors: haukep
Tags: redirect, autocorrect, url, canonical
Requires at least: 5.5
Tested up to: 5.5
Stable tag: 2.0a
License: Public Domain

Disables WordPress' URL autocorrection guessing feature.

== Description ==

This plugin disables WordPress' URL autocorrection guessing feature.
If you for example enter the URL `http://www.myblog.com/proj` you won't be redirected
to `http://www.myblog.com/project-2013` anymore.

This plugin is based on the example here: https://core.trac.wordpress.org/ticket/16557

== Installation ==

1. Upload `disable-url-autocorrect-guessing.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 2.0 =
Since ticket https://core.trac.wordpress.org/ticket/16557 is solved now, the implementation
was changed to use the filter do_redirect_guess_404_permalink as provided in the example here:
https://make.wordpress.org/core/2020/06/26/wordpress-5-5-better-fine-grained-control-of-redirect_guess_404_permalink/

= 1.1 =
Added check for `$_GET['p']` to allow `rel=shortlink` according to comment of user esemlabel:
https://core.trac.wordpress.org/ticket/16557#comment:28

= 1.0 =
First (and possibly last) version.
