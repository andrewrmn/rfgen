<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php /*?><div class="full-img mb-4">
<?php if ( has_post_thumbnail() ) {
the_post_thumbnail('full', array('class' => 'img-crop', 'data-reference' => '', 'data-crop-image-ratio' => 0.6,));
} else { ?>
<img src="<?php bloginfo('template_directory'); ?>/images/default-image.jpg" class="img-crop" data-crop-image-ratio="0.6" alt="<?php the_title(); ?>" />
<?php } ?>
</div><?php */?>


<?php /*?><h3 style="color:#000;"><?php the_title();?></h3>
<div class="entry-meta">
<h6><?php echo get_the_date(); ?>, by  <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>"><?php the_author(); ?></a> </h6>
</div><?php */?>

<div class="entry-content">
<?php
the_content(
sprintf(
wp_kses(
/* translators: %s: Name of current post. Only visible to screen readers */
__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentynineteen' ),
array(
'span' => array(
'class' => array(),
),
)
),
get_the_title()
)
);

wp_link_pages(
array(
'before' => '<div class="page-links">' . __( 'Pages:', 'twentynineteen' ),
'after'  => '</div>',
)
);
?>
</div><!-- .entry-content -->

<?php if ( ! is_singular( 'attachment' ) ) : ?>
<?php get_template_part( 'template-parts/post/author', 'bio' ); ?>
<?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
