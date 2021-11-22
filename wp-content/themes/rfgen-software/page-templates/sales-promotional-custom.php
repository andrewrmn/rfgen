<?php

get_header(); 
global $post;
$posts = get_posts( array( 
    'name' => get_query_var( 'node' ), 
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => 1
) );

$parent_id = isset($posts[0]->ID) ? $posts[0]->ID : 0;
$posts = get_posts( array( 
	'name' => '2-2', 
	'post_type' => 'page',
	'post_parent' => $parent_id,
	'post_status' => 'publish',
	'posts_per_page' => 1
) );
$post_id = isset($posts[0]->ID) ? $posts[0]->ID : 0;
$post = get_post( $post_id, OBJECT );
setup_postdata( $post );
$args['post_id'] = $my_posts[0]->ID;
$args = array('post_id' => $post_id);
echo '<input type="hidden" value="'.$post_id.'" id="edit_it" />';
?>
<?php get_template_part( 'content', 'br', $args ); ?>
<div class="top-cta <?php $cta = get_field( 'select_cta', $post_id ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
    <div class="container">
        <?php get_template_part('content', 'ctac', $args); ?>
    </div>
</div>
<div class="section">
	<div class="container">
		<article id="post-<?php $post_id; ?>" <?php echo get_post_class('', $post_id); ?>>
		<div class="entry-content">
		<?php
		the_content($post_id);
		wp_link_pages(
		array(
		'before' => '<div class="page-links">' . __( 'Pages:', 'twentynineteen' ),
		'after'  => '</div>',
		)
		);
		?>
		</div><!-- .entry-content -->
		</article>
		<?php if (have_rows('steps', $post_id)): ?>
		<div class="steps">
			<div class="row no-gutters">
				<?php
					$irr = 1;
					while (have_rows('steps', $post_id)): the_row();
					?>
				<div class="col-lg-4">
					<div class="content">
						<div class="step-circle"><span><small>Step</small><?php echo $irr; ?></span></div>
						<?php echo get_sub_field('content');?>   
					</div>
				</div>
				<?php
					$irr++;
					endwhile;
					?>
			</div>
		</div>
		<?php endif; ?>  
		<?php echo get_field('first_section_bottom_content', $post_id);?>
	</div>
</div>

<?php if( have_rows('second_section_box_content', $post_id) ): ?>
<div class="section md-1 gradient-back <?php if ( get_field('hide_second_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center mb-4">
				<?php echo get_field('second_section_content', $post_id);?>
			</div>
		</div>
		<div class="row">
			<?php while( have_rows('second_section_box_content', $post_id) ): the_row(); 
				$image = get_sub_field('icon');
				$icon_check = get_sub_field('icon_check');
				?>
			<div class="col-md-4 col-lg-4 mt-4">
				<div class="box-content all-box">
					<div class="icon mb-3 cobalt">
						<?php if($icon_check == 'Image Icon'): ?>
						<?php echo wp_get_attachment_image( $image, 'full' ); ?>
						<?php endif; ?>
						<?php if($icon_check == 'Font Icon'): ?>
						<?php the_sub_field('font_icon'); ?>
						<?php endif; ?>
					</div>
					<div class="content">
						<div class="pb-4 coleql_height">
							<?php the_sub_field('content'); ?>
						</div>
						<?php if(!empty(get_sub_field('btn_link'))){?>
						<a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary btn-sm"><?php the_sub_field('btn_label'); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('third_section_box_content', $post_id) ): ?>
<div class="section md-1 <?php if ( get_field('hide_third_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center mb-4">
				<?php echo get_field('third_section_content', $post_id);?>
			</div>
		</div>
		<div class="row">
			<?php while( have_rows('third_section_box_content', $post_id) ): the_row(); 
				$image = get_sub_field('icon');
				$icon_check = get_sub_field('icon_check');
				?>
			<div class="col-md-6 col-lg-3 mt-4">
				<div class="box-content all-box">
					<div class="icon mb-3 cobalt">
						<?php if($icon_check == 'Image Icon'): ?>
						<?php echo wp_get_attachment_image( $image, 'full' ); ?>
						<?php endif; ?>
						<?php if($icon_check == 'Font Icon'): ?>
						<?php the_sub_field('font_icon'); ?>
						<?php endif; ?>
					</div>
					<div class="content coleql_height">
						<?php the_sub_field('content'); ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if ( get_field( 'fourth_section_content', $post_id ) ): ?>
<div class="section gradient-back <?php if ( get_field('hide_fourth_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container text-center">
		<?php echo get_field('fourth_section_content', $post_id);?>
	</div>
</div>
<?php endif; ?>
<div class="<?php if (get_field('show_fifth_section', $post_id) == 'Show'): ?>d-block<?php endif; ?><?php if (get_field('show_fifth_section', $post_id) == 'Hide'): ?>d-none<?php endif; ?>">
	<?php
		$benefits = new WP_Query(array('post_type' => 'benefits', 'posts_per_page' => '400'));
		if ($benefits->have_posts()):
		?>
	<div class="section sol-w2">
		<div class="container">
			<div class="text-center mb-4"><?php echo get_field('fifth_section_content', $post_id);?></div>
			<div class="row">
				<?php
					while ($benefits->have_posts()): $benefits->the_post();
					?>
				<div class="col-md-6 col-lg-3 mt-4">
					<div class="box all-box">
						<div class="media">
							<div class="icon mr-3 cobalt"><?php the_post_thumbnail('full'); ?></div>
							<div class="media-body">
								<h5><?php echo get_field('numbers'); ?><?php echo get_field('unit');?><span><?php the_title(); ?></span></h5>
							</div>
						</div>
						<div class="content coleql_height">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
				<?php
					endwhile;
					wp_reset_query();
					 ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php if( have_rows('sixth_section_box_content', $post_id) ): ?>
<div class="section md-1 gradient-back <?php if ( get_field('hide_sixth_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center mb-4">
				<?php echo get_field('sixth_section_content', $post_id);?>
			</div>
		</div>
		<div class="row">
			<?php while( have_rows('sixth_section_box_content', $post_id) ): the_row(); 
				$image = get_sub_field('icon');
				$icon_check = get_sub_field('icon_check');
				?>
			<div class="col-md-4 col-lg-4 mt-4">
				<div class="box-content all-box">
					<div class="icon mb-3 cobalt">
						<?php if($icon_check == 'Image Icon'): ?>
						<?php echo wp_get_attachment_image( $image, 'full' ); ?>
						<?php endif; ?>
						<?php if($icon_check == 'Font Icon'): ?>
						<?php the_sub_field('font_icon'); ?>
						<?php endif; ?>
					</div>
					<div class="content coleql_height">
						<?php the_sub_field('content'); ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('seventh_section_content', $post_id) ): ?>
<div class="section pro-scroll-section <?php if ( get_field('hide_seventh_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="owl-carousel owl-theme CarouselOwl-full">
			<?php while( have_rows('seventh_section_content', $post_id) ): the_row(); 
				$image = get_sub_field('image');
				?>
			<div class="item">
				<div class="row">
					<div class="col-lg-6 full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
					<div class="col-lg-6">
						<?php the_sub_field('content'); ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('testimonials', $post_id) ): ?>
<div class="page-testimonial section <?php if ( get_field('hide_testimonial_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"" style="background-image:url(<?php echo get_field('testimonial_section_bg');?>);">
	<div class="container">
		<div class="testi-carousel">
			<div class="owl-carousel owl-theme CarouselOwl-1">
				<?php while( have_rows('testimonials', $post_id) ): the_row(); ?>
				<div class="item">
					<div class="media">
						<img src="<?php bloginfo('template_directory'); ?>/images/quote.png" alt="" class="mr-3">
						<div class="media-body">
							<blockquote><?php the_sub_field('quote'); ?></blockquote>
							<cite>
							<strong><?php the_sub_field('client'); ?></strong>
							<?php the_sub_field('designation'); ?>, <?php the_sub_field('company'); ?> 
							</cite>
						</div>
					</div>
				</div>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php
	if (!empty(get_field('case_study_tags', $post_id))):
	$term_ids = get_field('case_study_tags', $post_id);
	 $case_studies = new WP_Query(array(
	        'post_type' => 'media',
	        'tax_query' => array(
				'relation' => 'AND',
	            array(
	                'taxonomy' => 'media_tag',
	                'field' => 'term_id',
	                'terms' => $term_ids
	            ),
				array(
	                'taxonomy' => 'media_category',
	                'field' => 'slug',
	                'terms' => 'success-stories'
	            )
	        ),
	        'posts_per_page' => 3,
	        'orderby' => 'rand'
	    ));
	if ($case_studies->have_posts()):
	?>
<div class="section pro-w6 <?php if ( get_field('hide_case_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="text-center mb-4">
			<?php echo get_field('case_study_section_content', $post_id);?>
		</div>
		<div class="row justify-content-center">
			<?php while ($case_studies->have_posts()): $case_studies->the_post(); ?>
			<div class="col-6 col-sm-6 col-md-3 mt-4"><a href="<?php the_permalink(); ?>" class="embed-responsive embed-responsive-1by1"><?php echo wp_get_attachment_image(get_field('company_logo', get_the_ID()), 'full'); ?></a></div>
			<?php
				endwhile;
				wp_reset_query();
				?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if ( get_field( 'cta_section_content', $post_id ) ): ?>
<div class="cta-grad cta-grad-dark <?php if ( get_field('hide_cta_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<?php echo get_field('cta_section_content', $post_id);?>
			</div>
			<?php if ( get_field( 'cta_section_btn_link', $post_id ) ): ?>
			<div class="col-md-4 text-center align-self-center"><a href="<?php echo get_field('cta_section_btn_link', $post_id);?>" class="btn btn-primary"><?php echo get_field('cta_section_btn_label', $post_id);?></a></div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('eleventh_section_box_content', $post_id) ): ?>
<div class="section md-1 <?php if ( get_field('hide_eleventh_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center mb-4">
				<?php echo get_field('eleventh_section_content', $post_id);?>
			</div>
		</div>
		<div class="row">
			<?php while( have_rows('eleventh_section_box_content', $post_id) ): the_row(); 
				$image = get_sub_field('icon');
				$icon_check = get_sub_field('icon_check');
				?>
			<div class="col-md-4 col-lg-4 mt-4">
				<div class="box-content all-box">
					<div class="icon mb-3 cobalt">
						<?php if($icon_check == 'Image Icon'): ?>
						<?php echo wp_get_attachment_image( $image, 'full' ); ?>
						<?php endif; ?>
						<?php if($icon_check == 'Font Icon'): ?>
						<?php the_sub_field('font_icon'); ?>
						<?php endif; ?>
					</div>
					<div class="content coleql_height">
						<?php the_sub_field('content'); ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('twelveth_section_box_content', $post_id) ): ?>
<div class="section mb-7 pro-w4 <?php if ( get_field('hide_twelvth_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"" style="background-image:url(<?php echo get_field('twelveth_section_copy');?>);">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center mb-3">
				<?php echo get_field('twelveth_section_content', $post_id);?>
				</p>
			</div>
		</div>
		<div class="row justify-content-center">
			<?php while( have_rows('twelveth_section_box_content', $post_id) ): the_row(); 
				$image = get_sub_field('icon');
				?>
			<div class="col-md-6 col-lg-4 mt-4"><a href="<?php the_sub_field('link'); ?>" class="media"><?php echo wp_get_attachment_image( $image, 'full' ); ?><span class="media-body"><?php the_sub_field('label'); ?></span></a></div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('thirteenth_section_box_content', $post_id) ): ?>
<div class="section md-1 <?php if ( get_field('hide_thirteenth_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center mb-4">
				<?php echo get_field('thirteenth_section_content', $post_id);?>
			</div>
		</div>
		<div class="row">
			<?php while( have_rows('thirteenth_section_box_content', $post_id) ): the_row(); 
				$image = get_sub_field('icon');
				?>
			<div class="col-md-4 col-lg-4 mt-4">
				<div class="box-content all-box">
					<div class="icon mb-3 cobalt"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
					<div class="content coleql_height">
						<?php the_sub_field('content'); ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if ( get_field( 'cta_section_lg_content', $post_id ) ): ?>
<div class="cta-bg-content section <?php if ( get_field('hide_cta_lg_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"" style="background-image:url(<?php echo get_field('cta_section_lg_bg');?>);">
	<div class="container">
		<div class="row justify-content-end">
			<div class="col-lg-8 col-md-9">
				<?php echo get_field('cta_section_lg_content', $post_id);?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php 
	$gets = get_field('fourteenth_section_content', $post_id);
	if(isset($gets[0]['title']) && !empty($gets[0]['title'])){
	if( have_rows('fourteenth_section_content', $post_id) ): ?>
<div class="section md-1 <?php if ( get_field('hide_fourteenth_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<?php 
			while( have_rows('fourteenth_section_content', $post_id) ): the_row(); ?>
		<div class="section-block">
			<?php the_sub_field('title'); ?>
			<?php 
				if( have_rows('box_content') ): ?>
			<div class="row">
				<?php 
					while( have_rows('box_content') ): the_row();
					$image = get_sub_field('icon');
					$icon_check = get_sub_field('icon_check');
					?>
				<div class="col-md-4 col-lg-4 mt-4">
					<div class="box-content all-box">
						<div class="icon mb-3 cobalt">
							<?php if($icon_check == 'Image Icon'): ?>
							<?php echo wp_get_attachment_image( $image, 'full' ); ?>
							<?php endif; ?>
							<?php if($icon_check == 'Font Icon'): ?>
							<?php the_sub_field('font_icon'); ?>
							<?php endif; ?>
						</div>
						<div class="content">
							<div class="coleql_height">
								<?php the_sub_field('content'); ?>
							</div>
							<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary mt-4"><?php the_sub_field('btn_label'); ?></a><?php endif;?>
						</div>
					</div>
				</div>
				<?php endwhile; ?>
			</div>
			<?php endif;?>
		</div>
		<?php endwhile;?>
	</div>
</div>
<?php endif;?>
<?php } ?>
<?php
	/*
	 * Show Posts by Category
	 */
	$cat_ids = get_field('blog_cat', $post_id);
	//print_r($cat_ids);
	if (!empty($cat_ids)):
	    $the_query = new WP_Query(array(
	        'post_type' => 'post',
	        'tax_query' => array(
	            array(
	                'taxonomy' => 'category',
	                'field' => 'term_id',
	                'terms' => $cat_ids,
	            )
	        ),
	        'posts_per_page' => 12,
	        'orderby' => 'rand'
	    ));
	    //echo "Last SQL-Query: {$the_query->request}";
	    if ($the_query->have_posts()):
	        ?>
<div class="section pro-w7 gradient-back <?php if ( get_field('hide_blog_section', $post_id) == false ) { ?>d-none<?php } else { ?>d-block<?php } ?>"">
	<div class="container">
		<div class="text-center mb-5">
			<?php echo get_field('blog_section_content', $post_id); ?>
		</div>
		<div class="owl-carousel owl-theme CarouselOwl-More">
			<?php while ($the_query->have_posts()): $the_query->the_post(); ?>
			<div class="item">
				<div class="content">
					<a href="<?php the_permalink(); ?>" class="d-block">
						<div class="full-img"><?php the_post_thumbnail('full'); ?></div>
						<div class="title coleql_height"><?php the_title(); ?></div>
					</a>
				</div>
			</div>
			<?php
				endwhile;
				wp_reset_query();
				?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>
<div class="footer-cta <?php $cta = get_field( 'bottom_cta', $post_id ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottomc', $args ); ?>
</div>
</div>
<?php get_footer(); ?>