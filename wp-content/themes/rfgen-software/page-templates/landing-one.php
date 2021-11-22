<?php
/**
 * Template Name: New Landing Page Template
*/


get_header(); ?>
<?php get_template_part( 'content', 'banner' ); ?>
<nav class="scroll-menu header-sticky">
	<div class="container">
		<ul id="nav justify-content-center">
			<?php if( get_field('first_section_menu_label') ): ?>
			<li><a href="#section-1"><?php $image = get_field('first_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('first_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('help_section_menu_label') ): ?>
			<li><a href="#section-2"><?php $image = get_field('help_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('help_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('benefit_section_menu_label') ): ?>
			<li><a href="#section-3"><?php $image = get_field('benefit_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('benefit_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('second_section_menu_label') ): ?>
			<li><a href="#section-4"><?php $image = get_field('second_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('second_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('third_section_menu_label') ): ?>
			<li><a href="#section-5"><?php $image = get_field('third_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('third_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('casestudy_section_menu_label') ): ?>
			<li><a href="#section-6"><?php $image = get_field('casestudy_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('casestudy_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('integrate_section_menu_label') ): ?>
			<li><a href="#section-7"><?php $image = get_field('integrate_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('integrate_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('advantage_section_menu_label') ): ?>
			<li><a href="#section-8"><?php $image = get_field('advantage_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('advantage_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('fourth_section_menu_label') ): ?>
			<li><a href="#section-9"><?php $image = get_field('fourth_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('fourth_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('features_section_menu_label') ): ?>
			<li><a href="#section-10"><?php $image = get_field('features_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('features_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('fifty_section_menu_label') ): ?>
			<li><a href="#section-11"><?php $image = get_field('fifty_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('fifty_section_menu_label');?></span></a></li>
			<?php endif; ?>
			<?php if( get_field('sixth_section_menu_label') ): ?>
			<li><a href="#section-12"><?php $image = get_field('sixth_section_icon'); $size = 'full'; if( $image ) { echo wp_get_attachment_image( $image, $size );}?><span><?php echo get_field('sixth_section_menu_label');?></span></a></li>
			<?php endif; ?>
		</ul>
	</div>
</nav>
<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
	<div class="container">
		<?php get_template_part( 'content', 'cta' ); ?>
	</div>
</div>
<?php if( have_rows('first_section_box_content') ): ?>
<div class="<?php if( get_field('show_first_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_first_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-1">
		<div class="section md-1">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center mb-4">
						<?php echo get_field('first_section_content');?>
					</div>
				</div>
				<div class="row">
					<?php while( have_rows('first_section_box_content') ): the_row(); 
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
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('help_section_content') ): ?>
<div class="<?php if( get_field('show_help_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_help_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-2">
		<div class="section md-2 gradient-back">
			<div class="container">
				<div class="text-center mb-4">
					<?php echo get_field('help_section_title');?>
				</div>
				<div class="row">
					<?php while( have_rows('help_section_content') ): the_row(); ?>
					<div class="col-md-6 mt-4">
						<div class="content coleql_height">
							<?php the_sub_field('content'); ?>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<div class="<?php if( get_field('show_benefit_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_benefit_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<?php
	$benefits = new WP_Query(array('post_type' => 'benefits', 'posts_per_page' => '400'));
	if ($benefits->have_posts()):
	?>
	<div class="anchor" id="section-3">
		<div class="section sol-w3">
			<div class="container">
				<div class="text-center mb-4"><?php echo get_field('benefit_section_title');?></div>
				<div class="row">
					<?php
					while($benefits->have_posts()): $benefits->the_post();
					?>
					<div class="col-md-6 col-lg-3 mt-4">
						<div class="box all-box">
							
							<div class="icon mr-3 cobalt"><?php the_post_thumbnail( 'full' ); ?></div>
							<div class="content coleql_height">
								
								<h5><?php echo get_field('numbers');?><?php echo get_field('unit');?>&nbsp;<span><?php the_title();?></span></h5>
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
	</div>
	<?php endif; ?>
</div>
<?php if( have_rows('second_section_box_content') ): ?>
<div class="<?php if( get_field('show_second_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_second_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-4">
		<div class="section md-1 gradient-back">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center mb-4">
						<?php echo get_field('second_section_content');?>
					</div>
				</div>
				<div class="row">
					<?php while( have_rows('second_section_box_content') ): the_row(); 
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
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('third_section_box_content') ): ?>
<div class="<?php if( get_field('show_third_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_third_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-5">
		<div class="section md-4">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center mb-4">
						<?php echo get_field('third_section_content');?>
					</div>
				</div>
				<div class="row">
					<?php while( have_rows('third_section_box_content') ): the_row(); 
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
							<div class="content coleql_height pb-4">
								<?php the_sub_field('content'); ?>
							</div>
							<a href="<?php the_sub_field('link'); ?>" class="link">Learn More<i class="fas fa-arrow-right ml-2"></i></a>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if (!empty(get_field('case_study_tag'))):?>
<div class="<?php if( get_field('show_casestudy_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_casestudy_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-6">
		<?php
			$term_ids = get_field('case_study_tag');
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
		<div class="section md-5 gradient-back">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center mb-4">
						<?php echo get_field('casestudy_section_intro_content');?>
					</div>
				</div>
				<div class="row post-landing-list">
					<?php while ($case_studies->have_posts()): $case_studies->the_post(); ?>
					<div class="col-sm-6 col-md-6 col-lg-4  mt-4 mb-0">
						<div class="d-block">
							<div class="post-box">
								<a href="<?php the_permalink(); ?>">
									<div class="embed-responsive embed-responsive-4by3">
										<div class="full-img nobg">
											<?php if ( has_post_thumbnail() ) {
												the_post_thumbnail('full');
												} else { ?>
											<img src="<?php bloginfo('template_directory'); ?>/images/default-image.jpg" alt="<?php the_title(); ?>" />
											<?php } ?> 
										</div>
									</div>
								</a>
								<div class="content">
									<div class="pb-4 coleql_height">
										<h4><?php the_title();?></h4>
										<?php the_excerpt(); ?>
										<?php echo get_the_term_list( get_the_ID(), 'media_tag', '<ul><li>', ',</li><li>', '</li></ul>' );?>
										<?php
											/*$object_terms = wp_get_object_terms(get_the_ID(), 'media_tag', array('fields' => 'names'));
											if ($object_terms):
											?>
										<ul>
											<li><?php echo implode('</li><li>', $object_terms); ?></li>
										</ul>
										<?php endif;*/ ?>
									</div>
									<strong>Learn More<i class="fas fa-arrow-right ml-2"></i></strong>
								</div>
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
</div>
<?php endif; ?>
<?php if( get_field('integrate_section_content') ): ?>
<div class="<?php if( get_field('show_integrate_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_integrate_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-7">
		<div class="section md-5">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center mb-4">
						<?php echo get_field('integrate_section_content');?>
					</div>
				</div>
				<?php endif; ?>
				<?php if( have_rows('integrate_section_box_content') ): ?>
				<div class="row">
					<?php while( have_rows('integrate_section_box_content') ): the_row(); 
						$image = get_sub_field('icon');
						?>
					<div class="col-md-4 col-lg-4 mt-4">
						<div class="box-content all-box text-left">
							<div class="icon mb-3 cobalt"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
							<div class="content coleql_height">
								<?php the_sub_field('content'); ?>
							</div>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
				<?php endif; ?>
				<?php if( get_field('integrate_section_bottom_content') ): ?>
				<div class="mt-5 text-center">
					<?php echo get_field('integrate_section_bottom_content');?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if (have_rows('advantage_section_content')): ?>
<div class="<?php if( get_field('show_advantage_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_advantage_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-8">
		<div class="section pro-w3 gradient-back">
			<div class="container">
				<div class="row justify-content-center text-center">
					<div class="col-lg-10 mb-5">
						<?php echo get_field('advantage_section_intro_content');?>
					</div>
				</div>
				<div class="icon-section">
					<?php if( have_rows('advantage_section_content') ):
						// loop through the rows of data
						// add a counter
						$count = 0;
						$group = 0;
						while ( have_rows('advantage_section_content') ) : the_row(); 
						// vars
						$content = get_sub_field('content');
						$image = get_sub_field('icon');
						$imageh = get_sub_field('hover_icon');
						if ($count % 3 == 0) {
						$group++;
						?>
					<div id="sol-<?php echo $group; ?>" class="row no-gutters cf group-<?php echo $group; ?>">
						<?php 
							}
							?>
						<div class="col-md-4">
							<div class="content">
								<div class="icon"><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" /><img src="<?php echo $imageh['url']; ?>" alt="<?php echo $imageh['alt'] ?>" /></div>
								<?php echo $content; ?>
							</div>
						</div>
						<?php 
							if ($count % 3 == 2) {
							?>
					</div>
					<!-- #teachers -->
					<?php 
						}
						$count++;
						endwhile;
						endif;
						?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if(!empty(get_field('fourth_section_content'))):?>
<div class="<?php if( get_field('show_fourth_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_fourth_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-9">
		<div class="section md-6">
			<div class="container">
				<div class="row">
					<div class="col-lg-5 full-img">
						<?php 
							$image = get_field('fourth_section_image');
							$size = 'full'; // (thumbnail, medium, large, full or custom size)
							if( $image ) {
							echo wp_get_attachment_image( $image, $size );
							}?>
					</div>
					<div class="col-lg-7">
						<?php echo get_field('fourth_section_content');?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('features') ): ?>
<div class="<?php if( get_field('show_features_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_features_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-10">
		<div class="section mb-7 pro-w4" style="background-image:url(<?php echo get_field('features_section_bg');?>);">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center mb-3">
						<?php echo get_field('features_section_intro_content');?>
					</div>
				</div>
				<div class="row justify-content-center">
					<?php while( have_rows('features') ): the_row(); 
						$image = get_sub_field('icon');
						?>
					<div class="col-md-6 col-lg-4 mt-4"><a href="<?php the_sub_field('link'); ?>" class="media"><?php echo wp_get_attachment_image( $image, 'full' ); ?><span class="media-body"><?php the_sub_field('label'); ?></span></a></div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( get_field('fifty_section_content') ): ?>
<div class="<?php if( get_field('show_fifty_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_fifty_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-11">
		<div class="section md-8">
			<div class="container c-accordion">
				<?php echo get_field('fifty_section_content');?>
				<?php if( have_rows('faqs') ): ?>
				<div class="custom-accordion accordionjs">
					<?php while( have_rows('faqs') ): the_row(); ?>
					<div class="csa">
						<div><?php the_sub_field('title'); ?></div>
						<div class="tab-container">
							<?php the_sub_field('content'); ?>
						</div>
					</div>
					<?php endwhile; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('sixth_section_btns') ): ?>
<div class="<?php if( get_field('show_sixth_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_sixth_section') == 'Hide' ): ?>d-none<?php endif; ?>">
	<div class="anchor" id="section-12">
		<div class="section md-9 gradient-back">
			<div class="container">
				<?php echo get_field('sixth_section_content');?>
				<div class="row justify-content-center">
					<?php while( have_rows('sixth_section_btns') ): the_row(); 
						$image = get_sub_field('icon');
						?>
					<div class="col-sm-6 col-6 col-md-6 col-lg-3 mt-4"><a href="<?php the_sub_field('link'); ?>/" class="btn btn-primary btn-block"><?php echo wp_get_attachment_image( $image, 'full' ); ?><span class="d-block"><?php the_sub_field('label'); ?></span></a></div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
	<div class="container">
		<?php get_template_part( 'content', 'ctabottom' ); ?>
	</div>
</div>
<?php get_footer(); ?>