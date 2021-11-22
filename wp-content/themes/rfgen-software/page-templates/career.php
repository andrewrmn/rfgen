<?php
/**
 * Template Name: Career Page Template
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

<?php
$career = new WP_Query(array('post_type' => 'career', 'posts_per_page' => '3000'));
if ($career->have_posts()):
?>
<div class="section gradient-back">
<div class="container">
<?php echo get_field('job_section_title');?>
<div class="row pt-3">
<?php
while ($career->have_posts()): $career->the_post();
?>
<div class="col-sm-6 col-md-6 col-lg-4 mt-4">
<div class="job-list-box">
<a href="<?php the_permalink(); ?>" class="d-block coleql_height">
<h4><?php the_title();?></h4>
<?php the_excerpt(); ?>
</a>
</div>
</div>
<?php
endwhile;
wp_reset_query();
?>
</div>
</div>
</div>
<?php endif; ?>


<div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
