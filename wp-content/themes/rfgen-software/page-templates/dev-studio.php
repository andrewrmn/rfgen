<?php
/**
 * Template Name: Development Studio Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>
<style>.all-box{hyphens: none}</style>
<div class="top-cta top-cta-inner <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>

<div class="section section-lg" style="background-image:url(<?php echo get_field('first_section_bg');?>)">
<div class="container">
<div class="row justify-content-end">
<div class="col-lg-8">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
</div>
</div>
</div>
</div>

<?php if( have_rows('second_section_box_content') ): ?>
<div class="section md-1">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-12 text-center mb-4">
<?php echo get_field('second_section_content');?>
</div>
</div>
<div class="row">
<?php while( have_rows('second_section_box_content') ): the_row(); 
$image = get_sub_field('icon');
$icon_check = get_sub_field('icon_check');
?>
<div class="col-md-4 col-lg-4 mt-4">
<div class="box-content all-box">
<div class="icon mb-3 cobalt">
<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
</div>
<div class="content">
<div class="pb-0 coleql_height">
<?php the_sub_field('content'); ?>
</div>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('third_section_box_content') ): ?>
<div class="section gradient-back">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-12 text-center mb-5">
<?php echo get_field('third_section_content');?>
</div>
</div>
<div class="row justify-content-center">
<?php while( have_rows('third_section_box_content') ): the_row(); 
$image = get_sub_field('icon');
$icon_check = get_sub_field('icon_check');
?>
<div class="col-sm-6 col-md-3 mt-4">
<div class="box-content all-box">
<div class="icon mb-3 cobalt">
<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
</div>
<div class="content coleql_height">
<?php the_sub_field('content'); ?>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('testimonials') ): ?>
<div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_bg');?>);">
<div class="container">
<div class="testi-carousel">
<div class="owl-carousel owl-theme CarouselOwl-1">
<?php while( have_rows('testimonials') ): the_row(); ?>
<div class="item">
<div class="media">
<img src="<?php bloginfo('template_directory'); ?>/images/quote.png" alt="" class="mr-3">
<div class="media-body">
<blockquote><?php the_sub_field('quote'); ?></blockquote>
<cite>
<strong><?php the_sub_field('client'); ?></strong>
<?php the_sub_field('designation'); ?>, <?php the_sub_field('company'); ?> 
</cite>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if( get_field('cta_section_content') ): ?>
<div class="cta-bg-content section" style="background-image:url(<?php echo get_field('cta_section_bg');?>);">
<div class="container">
<div class="row">
<div class="col-lg-7 col-md-9">
<?php echo get_field('cta_section_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('fourth_section_content') ): ?>
<div class="section pro-scroll-section">
<div class="container">
<div class="mb-5"><?php echo get_field('fourth_section_title');?></div>
<div class="owl-carousel owl-theme CarouselOwl-full">
<?php while( have_rows('fourth_section_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="item">
<div class="row">
<div class="col-lg-6 full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
<div class="col-lg-6">
<?php the_sub_field('content'); ?>
<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>

<?php get_footer(); ?>
