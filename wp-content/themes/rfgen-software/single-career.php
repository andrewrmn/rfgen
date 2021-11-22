<?php
/**
 * The template for displaying all Single Career
 */
get_header();
?>


<?php get_template_part( 'content', 'banner' ); ?>
<?php
while ( have_posts() ) :
the_post();
?>
<div class="section gradient-back">
<div class="container">
<div class="career-content">
<div class="content">
<?php the_content();?>
</div>
</div>

<?php if( have_rows('job_info') ): ?>
<?php while( have_rows('job_info') ): the_row(); ?>
<div class="career-content">
<div class="content">
<h3><?php the_sub_field('title'); ?></h3>
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
<?php endif; ?>

</div>
</div>
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
