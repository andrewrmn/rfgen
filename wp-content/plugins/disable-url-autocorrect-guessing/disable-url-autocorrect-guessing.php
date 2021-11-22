<?php
/*
Plugin Name: Disable URL Autocorrect Guessing
Description: Disables Wordpress' URL autocorrection guessing feature. If you for example enter the URL http://www.myblog.com/proj you won't be redirected to http://www.myblog.com/project-2013 anymore.
Author: Hauke Pribnow
Version: 2.0
*/

// This code is based on the example here: https://make.wordpress.org/core/2020/06/26/wordpress-5-5-better-fine-grained-control-of-redirect_guess_404_permalink/

add_filter( 'do_redirect_guess_404_permalink', '__return_false' );
