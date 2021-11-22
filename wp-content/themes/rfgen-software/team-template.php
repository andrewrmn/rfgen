<?php
/**
 * The template for displaying all single media
 */

get_header();
global $post;
$author_a = get_query_var( 'author_a' );
$author_b = get_query_var( 'author_b' );
$author_c = get_query_var( 'author_c' );
if(!empty($author_a)){
	$cat_slug = 'rfgen-leadership-team';
	$post_name = $author_a;
}
elseif(!empty($author_b)){
	$cat_slug = 'content-contributors';
	$post_name = $author_b;
}
else{
	$cat_slug = 'enterprise-mobility-experts';
	$post_name = $author_c;
}
$posts_array = get_posts(
    array(
		'name' => $post_name,
        'posts_per_page' => 1,
        'post_type' => 'team',
        'tax_query' => array(
            array(
                'taxonomy' => 'team_category',
                'field' => 'slug',
                'terms' => $cat_slug,
            )
        )
    )
);
$post_id = isset($posts_array[0]) ? $posts_array[0]->ID : 0;
$post = get_post( $post_id, OBJECT );
setup_postdata( $post );
?>
<?php
if(!empty($posts_array[0])){
echo '<input type="hidden" value="'.$post_id.'" id="edit_it" />';
?>
<div class="section gradient-back pb-0 member-intro">
	<div class="container">
		<div class="row">
			<div class="col-md-6 align-self-center d-none d-md-block">
				<h1><?php echo $posts_array[0]->post_title;?></h1>
				<h3><?php echo get_field('designation', $post_id);?></h3>
			</div>
			<div class="col-md-6 text-center"><?php echo get_the_post_thumbnail( $post_id, 'full' ); ?> </div>
		</div>
	</div>
</div>
<div class="section">
	<div class="container">
		<div class="member-intro d-block d-md-none mb-4">
			<h1><?php echo $posts_array[0]->post_title;?></h1>
			<h3><?php echo get_field('designation', $post_id);?></h3>
		</div>
		<?php the_content(); ?>
	</div>
</div>
<?php } ?>
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
		<div class="owl-carousel owl-theme CarouselOwlTeam team-member">
			<?php while($related_cats_post->have_posts()): $related_cats_post->the_post(); 
			if(get_the_ID() == 6508){ continue; }
			?>
			<div class="item">
				<div class="member-block">
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
					<a href="<?php echo $link; ?>" class="d-block">
						<div class="embed-responsive embed-responsive-1by1">
							<div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div>
						</div>
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