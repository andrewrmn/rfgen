<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
    <head>

        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({'gtm.start':
                            new Date().getTime(), event: 'gtm.js'});
                var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-5LPLNQN');</script>
        <!-- End Google Tag Manager -->


        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-3024801-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-3024801-1');
        </script>

        <meta name="google-site-verification" content="T8kkFmX_qTDnV4FnfFP-MAoMBNVXJIP9NIzBLbiyUGw" />
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="google-site-verification" content="LeuQCPb9Gi1oQK2PThR9v_bnTEtY6kbpPFGoqpQ-Amc" />
        <link rel="profile" href="https://gmpg.org/xfn/11" />
        <?php wp_head(); ?>
        <?php
        $image = get_field('header_logo', 3471);
        if (!empty($image)):
            ?>
            <style type="text/css">.close-menu { background:url(<?php echo esc_url($image['url']); ?>) no-repeat 15px center; background-size:120px 40px;}</style>
        <?php endif; ?>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    </head>

    <body <?php body_class(); ?>>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5LPLNQN"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->


        <?php wp_body_open(); ?>
        <div id="page" class="site">

            <div class="menu-overlay"></div>
            <div class="modal fade" id="ModalMenu" tabindex="-1" role="dialog" aria-labelledby="ModalMenu" aria-hidden="true">
                <div class="modal-dialog full_screen modal-lg modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                            <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                        <?php
                        $image = get_field('header_logo', 3471);
                        if (!empty($image)):
                            ?>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                        <?php endif; ?>
                    </a>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="sr-only">&times;</span></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="main-menu mobile-mega-menu clearfix">
                                <nav>
                                    <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => '', 'menu_class' => '')); ?>
                                </nav>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <ul class="mobile-action-btn">

                                <?php if( get_field('support_link',3471) ): ?><li><a href="<?php echo get_field('support_link',3471);?>" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/mic-blue.svg" alt=""></span><span class="media-body"><?php echo get_field('support_link_label',3471);?></span></a></li><?php endif; ?>

                           <?php if( get_field('login_btn_link',3471) ): ?>
<?php if (is_user_logged_in()){ ?>
<li><a href="<?php echo wp_logout_url( home_url('/') ); ?>" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/user-blue.svg" alt=""></span><span class="media-body">Logout</span></a></li>
<?php }else{ ?>
<li><a href="<?php echo get_field('login_btn_link',3471);?>" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/user-blue.svg" alt=""></span><span class="media-body">Login</span></a></li>
<?php } ?>
<?php endif; ?>


                            </ul>

                             <?php if( get_field('header_btn_link',3471) ): ?><a href="<?php echo get_field('header_btn_link',3471);?>" class="modal-btn"><?php echo get_field('header_btn_label',3471);?></a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


            <header class="site-header">

                <div class="pre-header d-none d-lg-block">
                    <div class="container">
                        <ul>

                            <?php if( get_field('support_link',3471) ): ?><li><a href="<?php echo get_field('support_link',3471);?>" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/mic-white.svg" alt=""></span><span class="media-body"><?php echo get_field('support_link_label',3471);?></span></a></li><?php endif; ?>

                           <?php if( get_field('login_btn_link',3471) ): ?>
<?php if (is_user_logged_in()){ ?>
<li><a href="<?php echo wp_logout_url( home_url('/') ); ?>" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/user-white.svg" alt=""></span><span class="media-body">Logout</span></a></li>
<?php }else{ ?>
<li><a href="<?php echo get_field('login_btn_link',3471);?>" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/user-white.svg" alt=""></span><span class="media-body">Login</span></a></li>
<?php } ?>
<?php endif; ?>


                            <li><a data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/search-white.svg" alt=""></span><span class="media-body">Search</span></a></li>
                        </ul>
                    </div>
                </div>

                <div class="new-header-search">
                    <div class="collapse" id="collapseSearch">
                        <div class="card card-body">
                            <div class="container">
                                <form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
                                    <input placeholder="<?php echo esc_attr_x( 'Search…', 'placeholder' ) ?>"  value="<?php echo get_search_query() ?>" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" name="s" id="s" type="text">
                                    <input type="submit" value="Search">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <a href="#" data-toggle="modal" data-target="#ModalMenu"  class="menu-toggle-new d-block d-lg-none"><span class="sr-only">Menu</span></a>
                    <div class="site-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                        <?php
                        $image = get_field('header_logo', 3471);
                        if (!empty($image)):
                            ?>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                        <?php endif; ?>
                    </a>
                    </div>
                    <a data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch" class="search-icon-sm d-block d-lg-none"><span class="sr-only">Search</span></a>
 <?php if( get_field('header_btn_link',3471) ): ?>

                   <nav class="main-nav d-none d-lg-block">
                       <?php echo wp_custom_nav_menu('primary'); ?>
                   </nav>

                    <ul class="action">

                      <li><a data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch" class="media"><span class="icon"><img src="<?php echo get_template_directory_uri(); ?>/images/new/search-white.svg" alt=""></span></a></li>

                      <?php if( get_field('support_link',3471) ): ?><li><a href="<?php echo get_field('support_link',3471);?>" class="media"><?php echo get_field('support_link_label',3471);?></span></a></li><?php endif; ?>

                      <?php if( get_field('login_btn_link',3471) ): ?>
                      <?php if (is_user_logged_in()){ ?>
                      <li><a href="<?php echo wp_logout_url( home_url('/') ); ?>" class="media"><span class="media-body">Logout</span></a></li>
                      <?php }else{ ?>
                      <li><a href="<?php echo get_field('login_btn_link',3471);?>" class="media"><span class="media-body">Login</span></a></li>
                      <?php } ?>
                      <?php endif; ?>

                        <li class="hd-btn">
                          <a href="<?php echo get_field('header_btn_link',3471);?>">
                            <span><?php echo get_field('header_btn_label',3471);?> Us</span>
                            <svg viewBox="0 0 20 13" width="20" height="13">
                							<path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
                						</svg>
                          </a>

                        </li>
                    </ul>
<?php endif; ?>




                </div>
            </header>


            <div id="content" class="site-content">
