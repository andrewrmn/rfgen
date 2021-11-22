<?php
/**
 * Template Name: App Description Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="section">
<div class="container">
<div class="row">
<div class="col-lg-8">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>

<?php if( have_rows('content_btns') ): ?>
<?php while( have_rows('content_btns') ): the_row(); ?>
<a href="<?php the_sub_field('link'); ?>" target="_blank" class="btn btn-primary mt-4 mr-3"><i class="far fa-file-pdf mr-2"></i><?php the_sub_field('label'); ?></a>
<?php endwhile; ?>
<?php endif; ?>
</div>


<div class="col-lg-4">
<div class="blog-sidebar">
<?php echo get_field('sidebar_title');?>
<?php if( have_rows('sidebar_content') ): ?>
<?php while( have_rows('sidebar_content') ): the_row(); ?>
<div class="widget">
<h3><?php the_sub_field('title'); ?></h3>
<ul>
<li><?php the_sub_field('content'); ?></li>
</ul>
</div>
<?php endwhile; ?>
<?php endif; ?>
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
