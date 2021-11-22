<?php
/**
 * Template Name: Landing Page Template
 */

get_header(); ?>

<?php get_template_part( 'content', 'banner' ); ?>

<div class="section landing-section landing-w1" style="background-color:#2a2e2f">
<div class="container">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
</div>
</div>


<div class="section landing-section landing-w2" style="background-color:#32373a">
<div class="container">
<?php echo get_field('second_section_content');?>
</div>
</div>


<?php if( have_rows('third_section_content') ): ?>
<div class="section landing-section landing-w3" style="background-color:#231f20">
<div class="container">
<div class="row">
<?php while( have_rows('third_section_content') ): the_row(); ?>
<div class="col-md-4">
<div class="content coleql_height">
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if(!empty(get_field('fourth_section_content'))): ?>
<div class="section landing-section landing-w4 pb-0" style="background-color:#2a2e2f">
<div class="container">
<div class="row">
<div class="col-lg-6">
<?php echo get_field('fourth_section_content');?>
</div>
<div class="col-lg-6 text-center">
<?php 
$image = get_field('fourth_section_image');
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if(!empty(get_field('fifth_section_content'))): ?>
<div class="section landing-section landing-w5" style="background-color:#32373a">
<div class="container">
<div class="row">
<div class="col-lg-4 text-center">
<?php 
$image = get_field('fifth_section_image');
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?>
</div>
<div class="col-lg-8">
<?php echo get_field('fifth_section_content');?>
<?php if( have_rows('fifth_section_accordion') ): ?>
<div class="custom-accordion accordionjs mt-4">
<?php while( have_rows('fifth_section_accordion') ): the_row(); ?>
<div class="csa">
<div class="title-acc"><?php the_sub_field('title'); ?></div>
<div class="content">
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if(!empty(get_field('sixth_section_content'))): ?>
<div class="section landing-section landing-w6" style="background-color:#2a2e2f">
<div class="container">
<?php echo get_field('sixth_section_content');?>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('seventh_section_content') ): ?>
<div class="section landing-section landing-w7" style="background-color:#32373a">
<div class="container">
<?php echo get_field('seventh_section_title');?>
<?php while( have_rows('seventh_section_content') ): the_row(); ?>
<div class="content">
<?php the_sub_field('content'); ?>
</div>
<?php endwhile; ?>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('eighth_section_accordion') ): ?>
<div class="section landing-section landing-w8" style="background-color:#2a2e2f">
<div class="container">
<?php echo get_field('eighth_section_content');?>
<div class="custom-accordion accordionjs mt-4">
<?php while( have_rows('eighth_section_accordion') ): the_row(); ?>
<div class="csa">
<div class="title-acc"><?php the_sub_field('title'); ?></div>
<div class="content">
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
