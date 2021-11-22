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

$curr_term = get_queried_object();
?>
<?php get_template_part( 'content', 'banner' ); ?>

<div class="section">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10">
<?php echo $curr_term->description; ?>
</div>
</div>
<?php if(have_posts()):?>
<div class="row team-member justify-content-center">
<?php while(have_posts()): the_post();?>
<div class="col-sm-5 colmd-4 col-lg-3">
<a href="<?php the_permalink(); ?>" class="d-block">
<div class="embed-responsive embed-responsive-1by1"><div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div></div>
<div class="content coleql_height">
<h5><?php the_title(); ?></h5>
<p><?php the_field('designation'); ?></p>
</div>
</a>
</div>
<?php endwhile;?>
</div>
<?php endif;?>
</div>
</div>

<?php
get_footer();
