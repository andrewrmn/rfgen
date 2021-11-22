<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>


<div class="col-sm-6 col-md-4 mb-4">
<div class="post-items">
<a href="<?php the_permalink(); ?>" rel="bookmark"><div class="embed-responsive embed-responsive-16by9"><div class="full-img">
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
