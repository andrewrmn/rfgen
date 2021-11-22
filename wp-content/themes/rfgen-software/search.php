<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>

<div class="page-banner">
<div class="container">
<h1 class="page-title">
<?php _e( 'Search results for: ', 'twentynineteen' ); ?>
<span class="page-description"><?php echo get_search_query(); ?></span>
</h1>
</div>
<div class="page-banner-image">
<?php 
$image = get_field('search_page_banner',3471);
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?>
</div>
</div>

<div id="primary" class="content-area section">
<main id="main" class="site-main container search-list">
<?php if ( have_posts() ) : ?>
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
/*
 * Include the Post-Format-specific template for the content.
 * If you want to override this in a child theme, then include a file
 * called content-___.php (where ___ is the Post Format name) and that
 * will be used instead.
 */
get_template_part( 'template-parts/content/content', 'excerpt' );
// End the loop.
endwhile;
// Previous/next page navigation.
twentynineteen_the_posts_navigation();
// If no content, include the "No posts found" template.
else :
get_template_part( 'template-parts/content/content', 'none' );
endif;
?>
</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
