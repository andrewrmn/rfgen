<?php
/**
 * Template Name: Team Category Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="section">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-12">
<?php
/* Start the Loop */
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End of the loop.
?>
</div>
</div>


<?php
if (!empty(get_field('team_cat'))):
$term_ids = get_field('team_cat');
$team = new WP_Query(array(
'post_type' => 'team',
'tax_query' => array(
array(
'taxonomy' => 'team_category',
'field' => 'term_id',
'terms' => $term_ids
)
),
'posts_per_page' => 400,
));
if ($team->have_posts()):
?>
<div class="row team-member justify-content-center">
<?php while ($team->have_posts()): $team->the_post(); ?>
<div class="col-sm-5 colmd-4 col-lg-3">
<?php
	$slug = get_post_field( 'post_name', get_the_ID() );
	$terms = get_the_terms( get_the_ID() , 'team_category' );
	if($terms[0]->slug == 'enterprise-mobility-experts'){
		$link = '/about/consulting-services/enterprise-mobility-experts/' . $slug;
	}
	else{
		$link = '/about/'.$terms[0]->slug.'/' . $slug;
	}
?>
<a href="<?php echo $link; ?>/" class="d-block">
<div class="embed-responsive embed-responsive-1by1"><div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div></div>
<div class="content coleql_height">
<h5><?php the_title(); ?></h5>
<p><?php the_field('designation'); ?></p>
</div>
</a>
</div>
<?php
endwhile;
wp_reset_query();
?>
</div>
<?php endif; ?>
<?php endif; ?>

</div>
</div>


<?php get_footer(); ?>
