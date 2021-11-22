<?php
/**
 * Template Name: Open Source App Suites Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>


<div class="section">
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

<?php if( have_rows('btns') ): ?>
<div class="section btn-row">
<div class="container">
<div class="row">
<?php while( have_rows('btns') ): the_row(); 
$image = get_sub_field('icon');
?>
<div class="col-lg-4"><a href="<?php the_sub_field('link'); ?>" class="media"><?php echo wp_get_attachment_image( $image, 'full' ); ?><span class="media-body"><h4><?php the_sub_field('label'); ?></h4><h6><?php the_sub_field('sub_label'); ?></h6></span></a></div>
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
