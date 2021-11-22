<?php
/**
 * Single Post Template: Blog Details Page Template
 */

get_header(); ?>


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


<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part('content', 'cta'); ?>
</div>
</div>


<?php if(have_rows('blog_repeater')): ?>
<nav class="scroll-menu header-sticky">
<div class="container">
<ul id="nav">
	<?php $i = 1; while(have_rows('blog_repeater')): the_row();?>
<li><a href="#section-<?php echo $i; ?>"><?php echo wp_get_attachment_image(get_sub_field('icon'), 'full'); ?><span><?php the_sub_field('label'); ?></span></a></li>
<?php $i++; endwhile; ?>
</ul>
</div>
</nav>

<?php $j = 1; while(have_rows('blog_repeater')): the_row();?>
<div class="anchor" id="section-<?php echo $j; ?>">
<div class="section">
<div class="container">
<?php the_sub_field('details'); ?>
</div>
</div>
</div>
<?php $j++; endwhile; ?>
<?php endif; ?>

<div class="form-content"><?php the_field('blog_form'); ?></div>


<div class="footer-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>

<?php get_footer(); ?>
