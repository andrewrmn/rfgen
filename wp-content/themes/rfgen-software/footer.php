<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */
?>

</div><!-- #content -->


<footer class="site-footer">
<div class="site-footer-top">
<div class="container">
<div class="row no-gutters">
<div class="col-lg-12 col-xl-12 mb-4"><div class="site-footer-logo">
<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                        <?php
                        $image = get_field('footer_logo', 3471);
                        if (!empty($image)):
                            ?>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                        <?php endif; ?>
                    </a>
</div></div>

<div class="col-lg-4 col-xl-3 mb-5">
<?php dynamic_sidebar('footer-1'); ?>
</div>
<div class="col-lg-8 col-xl-8 ml-auto">
<div class="row">
<div class="col-md-4"><?php dynamic_sidebar('footer-2'); ?></div>
<div class="col-md-4"><?php dynamic_sidebar('footer-3'); ?></div>
<div class="col-md-4">
<?php dynamic_sidebar('footer-4'); ?>
<?php if (have_rows('socials', 3471)): ?>
<ul class="social-links">
<?php while (have_rows('socials', 3471)): the_row(); ?>
<li><a href="<?php the_sub_field('link'); ?>" target="_blank"><?php the_sub_field('icon'); ?></a></li>
<?php endwhile; ?>
</ul>
<?php endif; ?>

</div>
</div>
</div>
</div>
</div>
</div>


<div class="site-footer-bottom">
<div class="container clearfix">
<div class="copy-text">
<?php echo get_field('copyright_text', 3471);?>
</div>
<div class="footmenu">
<?php dynamic_sidebar('footer-5'); ?>
</div>
</div>
</div>

</footer>

<?php /*?><footer class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-3 footer-logo"><a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                        <?php
                        $image = get_field('footer_logo', 3471);
                        if (!empty($image)):
                            ?>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                        <?php endif; ?>
                    </a></div>
                <div class="col-md-6 mb-3 clearfix align-self-center">
                    <?php if (have_rows('socials', 3471)): ?>
                        <ul class="social">
                            <?php while (have_rows('socials', 3471)): the_row(); ?>
                                <li><a href="<?php the_sub_field('link'); ?>" target="_blank"><?php the_sub_field('icon'); ?></a></li>
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <div class="col-md-12 col-lg-3 mt-4">
                        <div class="footer-info">
                            <?php dynamic_sidebar('footer-1'); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (is_active_sidebar('footer-2')) : ?>
                    <div class="col-md-4 col-lg-3 mt-4">
                        <?php dynamic_sidebar('footer-2'); ?>
                    </div>
                <?php endif; ?>

                <?php if (is_active_sidebar('footer-3')) : ?>
                    <div class="col-md-4 col-lg-3 mt-4">
                        <?php dynamic_sidebar('footer-3'); ?>
                    </div>
                <?php endif; ?>

                <?php if (is_active_sidebar('footer-4')) : ?>
                    <div class="col-md-4 col-lg-3 mt-4">
                        <?php dynamic_sidebar('footer-4'); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container"><?php echo get_field('copyright_text', 3471); ?> </div>
    </div>
</footer><?php */?>
<a href="#" class="back-to-top"><i class="fas fa-angle-up"></i></a>

</div><!-- #page -->

<?php wp_footer(); ?>
<script>
jQuery( '.main-menu' ).mobileMegaMenu({
changeToggleText: true,
enableWidgetRegion: true,
prependCloseButton: true,
stayOnActive: true,
toogleTextOnClose: 'Close Menu',
menuToggle: 'main-menu-toggle'
});

jQuery( '.quick-links' ).mobileMegaMenu({
changeToggleText: true,
enableWidgetRegion: true,
prependCloseButton: true,
stayOnActive: true,
toogleTextOnClose: 'Close Quick Links',
menuToggle: 'quick-links-toggle'
});
</script>

<script type="text/javascript">
jQuery('.counter').countUp();
jQuery('.coleql_height').matchHeight();
jQuery(document).ready(function ($) {
	jQuery('.stellarnav').stellarNav({
		theme: 'light',
		breakpoint: 991,
		position: 'right',
	});
	x=10;
	jQuery('.widget_categories ul:not(.children) > li').each(function(){
		size_li = jQuery(this).find('li').length;
		jQuery(this).find('li:lt('+x+')').show();
		if(size_li > 10){
			jQuery(this).append('<div class="loadMore">View All</div>');
		}
	});
	jQuery('.loadMore').click(function () {
		var obj = jQuery(this);
		obj.closest('li').find('.children li').show();
		obj.hide();
	});
	jQuery('.wishlist-member-login-password a').click(function (e) {
		e.stopPropagation();
		jQuery(this).attr('href', 'javascript:void(0)');
		if(jQuery(this).hasClass('dashicons-visibility')){
			jQuery('#wlm_form_field_pwd').attr('type', 'text');
			jQuery(this).removeClass('dashicons-visibility');
			jQuery(this).addClass('dashicons-hidden');
		}
		else{
			jQuery('#wlm_form_field_pwd').attr('type', 'password');
			jQuery(this).removeClass('dashicons-hidden');
			jQuery(this).addClass('dashicons-visibility');
		}
		var obj = jQuery(this);
		obj.closest('li').find('.children li').show();
		obj.hide();
	});
	if(jQuery('.wlm3-form').length){
		jQuery('.wlm3-form p:last-child').append("<a href='/register' style='float: right;'>Don't have an account?</a>");
	}
});
jQuery(document).ready(function ($) {
	jQuery(".custom-accordion").accordionjs();
	if(jQuery("#edit_it").length){
		jQuery('<li id="wp-admin-bar-edit"><a class="ab-item" href="<?php echo site_url(); ?>/wp-admin/post.php?post='+jQuery("#edit_it").val()+'&action=edit">Edit Page</a></li>').insertAfter('#wp-admin-bar-new-content');
	}
});
jQuery(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 420) {
		jQuery(".header-sticky").removeClass("sticky-bar");
	} else {
		jQuery(".header-sticky").addClass("sticky-bar");
	}
});

jQuery(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 420) {
		jQuery(".header-sticky").removeClass("sticky");
	} else {
		jQuery(".header-sticky").addClass("sticky");
	}
});
jQuery(document).on('click', '.d-click', function(e){
    if (!$(e.target).hasClass('dw-fls')) {
		location.href = jQuery(this).children("a:first").attr('href');
    }
});
jQuery(document).ready(function () {
	jQuery('#nav').onePageNav();
});
document.addEventListener( 'wpcf7mailsent', function( event ) {
   if ( '1986' == event.detail.contactFormId || '3625' == event.detail.contactFormId ) { 
    location = 'https://www.rfgen.com/about/schedule-now/success/';
    }
}, false );
	
(function ($){
$.fn.responsiveTabs = function() {
this.addClass('responsive-tabs'),
this.append($('<span class="dropdown-arrow"></span>')),

this.on("click", "li > a.active, span.dropdown-arrow", function (){
this.toggleClass('open');
}.bind(this)), this.on("click", "li > a:not(.active)", function() {
this.removeClass("open")
}.bind(this)); 
}
})(jQuery);

(function ($) {
$('.nav-tabs').responsiveTabs();
})(jQuery);
</script>

</body>
</html>
