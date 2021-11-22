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

<div class="page-banner">
<div class="container">
<div class="row">
<div class="col-lg-9 align-self-center">
<?php
$categories = get_the_terms($post->ID, "category");
if (!empty($categories)) {
echo '<ul class="blog-tag">';
foreach ($categories as $value) {
echo '<li>' . esc_html($value->name) . '</lo>';
}
echo '</ul>';
}
?>
<h1><?php the_title();?></h1>
<div class="media blog-aothor">
<!--<div class="author-img"><div class="embed-responsive embed-responsive-1by1"><div class="full-img"><?php echo get_avatar( get_the_author_meta( 'ID' )); ?></div></div></div>-->
<div class="media-body">
<div>Written by <a href="<?php echo get_the_author_meta('user_url', get_the_author_meta( 'ID' )); ?>"><?php the_author(); ?></a></div>
<div><strong><?php echo get_the_date(); ?></strong></div>
</div>
</div>
</div>
</div>
</div>
<div class="page-banner-image"><?php the_post_thumbnail( 'full' ); ?> </div>
</div>

<?php if(have_rows('blog_repeater')): ?>
<nav class="scroll-menu header-sticky">
<div class="container">
<ul id="nav">
	<?php while(have_rows('blog_repeater')): the_row();?>
<li><a href="<?php the_sub_field('details'); ?>"><?php echo wp_get_attachment_image(get_sub_field('icon'), 'full'); ?><span><?php the_sub_field('label'); ?></span></a></li>
<?php endwhile; ?>
</ul>
</div>
</nav>
<?php endif; ?>


<div class="top-cta">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>
<div id="primary" class="section pb-0">
<main id="main" class="site-main container">
<div class="row">
<div class="col-lg-8 blog-details">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'single' );
endwhile; // End the loop.
?>
</div>
<style>
.blog-sidebar .widget .children li{display: none}
.loadMore { cursor:pointer; margin-top:15px;  border:none; color:#007bff;}
</style>
<?php get_sidebar(); ?>
</div>
</main><!-- #main -->
</div><!-- #primary -->
<div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>
<?php
get_footer();
