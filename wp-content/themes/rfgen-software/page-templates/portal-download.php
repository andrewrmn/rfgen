<?php
/**
 * Template Name: Portal Downloads Page Template
*/
	
get_header(); ?>
<?php 
get_template_part( 'content', 'banner' );
global $wpdb;
if ( current_user_can( 'administrator' ) ) {
	$flag = 0;
}
else{
	$getLevels = $wpdb->get_row("SELECT GROUP_CONCAT(level_id) as lv FROM ".$wpdb->prefix."wlm_userlevels WHERE user_id = " . get_current_user_id() , ARRAY_A);
	if(!empty($getLevels['lv'])){
		$user_levels = explode(',', $getLevels['lv']);
	}
	else{
		$user_levels = array();
	}
	$flag = 1;
}
$args = array(
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'post_parent'    => get_the_ID(),
    'meta_key'			=> 'order_num',
	'orderby'			=> 'meta_value',
	'order'				=> 'ASC'
 );


$all_pages = new WP_Query( $args );
if ( $all_pages->have_posts() ) : ?>
<div class="section">
	<div class="container">
		<div class="row post-landing-list">
			<?php while ( $all_pages->have_posts() ) : $all_pages->the_post(); 
			if(get_the_ID() == 13449 || get_the_ID() == 13451){ continue; }
			$getPostLevels = $wpdb->get_row("SELECT GROUP_CONCAT(level_id) as lv FROM ".$wpdb->prefix."wlm_contentlevels WHERE content_id = " . get_the_ID() , ARRAY_A);
			if(!empty($getPostLevels['lv'])){
				$post_levels = explode(',', $getPostLevels['lv']);
			}
			else{
				$post_levels = array();
			}
			if($flag == 1){
				if(!empty($post_levels) && empty(array_intersect($post_levels, $user_levels))){
					continue;
				}
			}
			?>
			<div class="col-sm-6 col-md-6 col-lg-4">
				<a href="<?php the_permalink(); ?>" class="d-block">
					<div class="post-box">
						<div class="embed-responsive embed-responsive-4by3">
							<div class="full-img nobg"><img src="<?php echo get_the_post_thumbnail_url(get_the_ID()); ?>" alt=""></div>
						</div>
						<div class="content">
							<div class="pb-4 coleql_height" style="height: 145.3px;">
								<h4><?php the_title(); ?></h4>
								<p><?php the_excerpt(); ?></p>
							</div>
							<strong>Learn More<i class="fas fa-arrow-right ml-2"></i></strong>
						</div>
					</div>
				</a>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; wp_reset_postdata(); ?>
<?php get_footer(); ?>