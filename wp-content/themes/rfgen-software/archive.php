<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>

<?php get_template_part( 'content', 'banner' ); ?>

<div class="top-cta">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>

<section id="primary" class="content-area section pb-0">
<main id="main" class="site-main container">

<?php //get_template_part( 'content', 'blogfilter' ); ?>

<div class="row">
<?php query_posts(array(
'posts_per_page' => 12,
'post_type' => 'post',
'paged' => $paged
)
); ?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<div class="col-sm-6 col-md-4 mb-4">
<div class="post-items">
<a href="<?php the_permalink(); ?>" rel="bookmark"><div class="embed-responsive embed-responsive-4by3"><div class="full-img">
<?php if ( has_post_thumbnail() ) {
the_post_thumbnail('full');
} else { ?>
<img src="<?php bloginfo('template_directory'); ?>/images/default-image.jpg" alt="<?php the_title(); ?>" />
<?php } ?> 
</div></div></a>
<div class="content content-blog coleql_height">
<ol>
<li><?php echo get_the_date(); ?></li>
<?php /*?><li>4 min read</li><?php */?>
</ol>
<h5><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title();?></a></h5>
</div>
</div>
</div>
<?php endwhile; ?>
<?php wp_reset_query(); ?>
</div>
<?php twentynineteen_the_posts_navigation(); ?>

</main><!-- .site-main -->
</section>
<!-- .content-area -->

<div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>

<?php
get_footer();
