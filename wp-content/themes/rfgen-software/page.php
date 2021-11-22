<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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

<div id="primary" class="content-area section pb-0">
<main id="main" class="site-main container">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
</main><!-- #main -->
</div><!-- #primary -->


<div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>
<?php
get_footer();
