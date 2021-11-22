<?php
/**
 * The template for displaying all single media
 */

get_header();

$post_id = get_the_ID();
?>


<?php
while ( have_posts() ) :
the_post();
?>
<div class="section gradient-back pb-0 member-intro">
<div class="container">
<div class="row">
<div class="col-md-6 align-self-center d-none d-md-block">
<h1><?php the_title();?></h1>
<h3><?php echo get_field('designation');?></h3>
</div>
<div class="col-md-6 text-center"><?php the_post_thumbnail( 'full' ); ?> </div>
</div>
</div>
</div>
<div class="section">
<div class="container">
<div class="member-intro d-block d-md-none mb-4">
<h1><?php the_title();?></h1>
<h3><?php echo get_field('designation');?></h3>
</div>
<?php the_content();?>
</div>
</div>
<?php endwhile; // end of the loop. ?>

<?php
/*
* Related Members
*/
$cat_ids = array();
    $categories = get_the_terms($post_id, 'team_category');

    if(!empty($categories) && !is_wp_error($categories)):
        foreach ($categories as $category):
            array_push($cat_ids, $category->term_id);
        endforeach;
    endif;

    $current_post_type = get_post_type($post_id);

    $query_args = array( 
        'post_type'      => 'team',
        'post__not_in'    => array($post_id),
        'posts_per_page'  => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'team_category',
				'field' => 'term_id',
				'terms' => $cat_ids
			)
		),
		'meta_key' => 'display_order',
		'orderby' => 'meta_value',
		'order' => 'ASC'
     );
    $related_cats_post = new WP_Query( $query_args );
	if($related_cats_post->have_posts()):
?>
<div class="section gradient-back" style="overflow:hidden">
<div class="container">
<h2>Our Team</h2>
<div class="row team-member">
<?php while($related_cats_post->have_posts()): $related_cats_post->the_post(); 
if(get_the_ID() == 6508){ continue; }
?>
<div class="col-sm-6 col-md-4 col-lg-3 mb-4">
<div class="member-block p-0">
<a href="<?php the_permalink(); ?>" class="d-block m-0">
<div class="embed-responsive embed-responsive-1by1"><div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div></div>
<div class="content coleql_height">
<h5><?php the_title(); ?></h5>
<p><?php the_field('designation');?></p>
</div>
</a>
</div>
</div>
<?php endwhile;
wp_reset_query();
?>
</div>
</div>
</div>
<?php endif; ?>


<?php
get_footer();
