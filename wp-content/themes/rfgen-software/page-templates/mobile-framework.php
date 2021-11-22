<?php
/**
 * Template Name: Mobile Framework
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<?php /*?><div class="section">
<div class="container">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
</div>
</div><?php */?>

<?php if( get_field('cta_section_content') ): ?>
<div class="cta-bg-content section" style="background-image:url(<?php echo get_field('cta_section_bg');?>);">
<div class="container">
<div class="row justify-content-end">
<div class="col-lg-8 col-md-9">
<?php echo get_field('cta_section_content');?>
</div>
</div>
</div>
</div>
<?php endif;?>


<?php 
if( have_rows('first_section_content') ): ?>
<div class="section md-1">
<div class="container">
<?php 
while( have_rows('first_section_content') ): the_row(); ?>
<div class="section-block">
<?php the_sub_field('section_title'); ?>
<?php 
if( have_rows('box_content') ): ?>
<div class="row">
<?php 
while( have_rows('box_content') ): the_row();
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
<div class="coleql_height">
<?php the_sub_field('content'); ?>
</div>
<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary mt-4"><?php the_sub_field('btn_label'); ?></a><?php endif;?>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
<?php endif;?>
</div>
<?php endwhile;?>
</div>
</div>
<?php endif;?>


<?php if( have_rows('chequred_content') ): ?>
<div class="section gradient-back">
<div class="container">
<div class="chequred-content">
<?php while( have_rows('chequred_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="row">
<div class="col-lg-6 full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
<div class="col-lg-6">
<?php the_sub_field('content'); ?>
<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary mt-4"><?php the_sub_field('btn_label'); ?></a><?php endif;?>
</div>
</div>
<?php endwhile;?>
</div>
</div>
</div>
<?php endif;?>


<?php if (have_rows('testimonials')): ?>
<div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_section_bg'); ?>);">
<div class="container">
<div class="testi-carousel">
<div class="owl-carousel owl-theme CarouselOwl-1">
<?php while (have_rows('testimonials')): the_row(); ?>
<div class="item">
<div class="media">
<img src="<?php echo get_template_directory_uri(); ?>/images/quote.png" alt="" class="mr-3">
<div class="media-body">
<blockquote><?php the_sub_field('quote'); ?></blockquote>
<cite>
<strong><?php the_sub_field('client'); ?></strong>
<?php the_sub_field('company'); ?>
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

<?php get_footer(); ?>
