<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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
$image = get_field('error_page_banner',3471);
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?>
</div>
</div>
	<div id="primary" class="content-area section">
		<main id="main" class="site-main container text-center">

			<div class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'twentynineteen' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'twentynineteen' ); ?></p>
					<?php //get_search_form(); ?>
				</div><!-- .page-content -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
