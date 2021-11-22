<?php
/**
 * Template Name: Listing Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>
<div class="section resources-w1">
<div class="container">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
<?php if( have_rows('listing_content') ): ?>
<div class="row post-landing-list">
<?php while( have_rows('listing_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="col-sm-6 col-md-6 col-lg-4">
<a href="<?php the_sub_field('link'); ?>" class="d-block">
<div class="post-box">
<div class="embed-responsive embed-responsive-21by9"><div class="full-img nobg"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div></div>
<div class="content">
<div class="pb-2 coleql_height">
<h5><?php the_sub_field('title'); ?></h5>
<?php the_sub_field('content'); ?>
</div>
<strong>Learn More<i class="fas fa-arrow-right ml-2"></i></strong>
</div>
</div>
</a>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>
</div>
<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>

<?php get_footer(); ?>
