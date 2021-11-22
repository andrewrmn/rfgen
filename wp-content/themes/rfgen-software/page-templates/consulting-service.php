<?php
/**
 * Template Name: Consulting Services Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>


<div class="section">
<div class="container">
<div class="row content-row">
<div class="<?php echo (!empty(get_field('quote'))) ? 'col-lg-8' : 'col-lg-12'; ?>">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
</div>
<div class="col-lg-4">
<?php if( get_field('quote') ): ?>
<div class="consult-quote mb-5">
<blockquote><?php echo get_field('quote');?></blockquote>
<cite><?php if( get_field('client') ): ?><strong><?php echo get_field('client');?></strong><?php endif; ?>
<?php if( get_field('designation') ): ?><?php echo get_field('designation');?><?php endif; ?><?php if( get_field('copmany') ): ?>, <?php echo get_field('copmany');?><?php endif; ?></cite>
</div>
<?php endif; ?>

<div class="text-center">
<?php 
$image = get_field('side_bar_image');
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?>
</div>
</div>
</div>
</div>
</div>


<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
