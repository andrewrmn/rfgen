<?php
/**
 * Template Name: PDF List Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="section">
<div class="container">
<?php if( have_rows('pdfs') ): ?>
<div class="row post-landing-list">
<?php while( have_rows('pdfs') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="col-sm-6 col-md-6 col-lg-4">
<a href="<?php the_sub_field('file'); ?>" class="d-block"  target="_blank">
<div class="post-box">
<div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div></div>
<div class="content">
<div class="pb-4 coleql_height">
<h4 class="media"><img src="<?php echo get_template_directory_uri(); ?>/images/pdf-file.svg" alt="" class="mr-3" /><span class="media-body"><?php the_sub_field('title'); ?></span></h4>
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
</div>

<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
