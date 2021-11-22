<?php
/**
 * Template Name: Solution Landing Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>


<div class="section">
<div class="container resources-list">

<?php if( have_rows('solutions') ):
// loop through the rows of data
// add a counter
$count = 0;
$group = 0;
while ( have_rows('solutions') ) : the_row(); 
// vars
$title = get_sub_field('title');
$content = get_sub_field('content');
$link = get_sub_field('link');
$image = get_sub_field('image');
if ($count % 2 == 0) {
$group++;
?>
<div id="sol-<?php echo $group; ?>" class="row justify-content-center cf group-<?php echo $group; ?>">
<?php 
}
?>

<div class="col-6">
<a href="<?php echo $link; ?>" class="d-block">
<div class="media">
<div class="media-body">
<h4><?php echo $title; ?></h4>
<div class="content"><?php echo $content; ?></div>
</div>
<div class="arrow"></div>
</div>
<div class="full-img"><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" /></div>
</a>
</div>

<?php 
if ($count % 2 == 1) {
?>
</div><!-- #teachers -->
<?php 
}
$count++;
endwhile;
else :
// no rows found
endif; ?>

</div>
</div>


<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
