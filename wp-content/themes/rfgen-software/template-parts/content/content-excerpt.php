<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky-post">%s</span>', _x( 'Featured', 'post', 'twentynineteen' ) );
		}
		the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
		?>
	</header><!-- .entry-header -->

	<?php twentynineteen_post_thumbnail(); ?>

	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->

	<?php /*?><footer class="entry-footer">
		<?php twentynineteen_entry_footer(); ?>
	</footer><?php */?><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
