<?php
/**
 * Template Name: Solutions - New
 */

get_header();
	while ( have_posts() ) : the_post(); ?>


	<div class="hero-split">
		<div class="container">
			<div class="hero-split__content">

				<div class="hero-split__intro">
						<div>
							<?php $tag1 = get_field('sub_heading_tag'); ?>
							<<?php echo $tag1; ?> class="has-accent sub-heading"><?php the_field('sub-heading'); ?></<?php echo $tag1; ?>>
							<?php $tag2 = get_field('heading_tag'); ?>
							<<?php echo $tag2; ?> class="h1"><?php if( get_field('heading') ): the_field('heading'); else: the_title(); endif; ?></<?php echo $tag2; ?>>
						</div>
				</div>

				<div class="hero-split__bd">
					<div class="rte">
						<?php the_field('description'); ?>
					</div>

					<?php $link = get_field('cta'); if( $link ): ?>
				  <a class="btn" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
						<span><?php echo $link['title']; ?></span>
						<svg viewBox="0 0 20 13" width="20" height="13">
							<path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
						</svg>
					</a>
					<?php endif; ?>
				</div>

			</div>
		</div>
		<figure class="hero-split__media">
			<?php the_post_thumbnail(); ?>
		</figure>
	</div>

	<nav class="page-navigation">
		<div class="container">
			<ul>

				<?php if( get_field('anchor_active') ){ ?>
				<li>
					<?php
						$anchor1 = 'Overview';
						$anchor1Slug = sanitize_title($anchor1);
						$ca1 = get_field('anchor_title_1');
						if( $ca1 ) {
							$anchor1 = $ca1;
							$anchor1Slug = sanitize_title($ca1);
						}
					?>
					<a href="#<?php echo $anchor1Slug; ?>" data-scroll-to="<?php echo $anchor1Slug; ?>" data-scroll-offset="118">
						<?php echo $anchor1; ?>
					</a>
				</li>
				<?php } ?>
				<?php if( get_field('anchor_active_2') ){ ?>
				<li>
					<?php
						$anchor2 = 'Features';
						$anchor2Slug = sanitize_title($anchor2);
						$ca2 = get_field('anchor_title_2');
						if( $ca2 ) {
							$anchor2 = $ca2;
							$anchor2Slug = sanitize_title($ca2);
						}
					?>
					<a href="#<?php echo $anchor2Slug; ?>" data-scroll-to="<?php echo $anchor2Slug; ?>" data-scroll-offset="118">
						<?php echo $anchor2; ?>
					</a>
				</li>
				<?php } ?>
				<?php if( get_field('anchor_active_3') ){ ?>
				<li>
					<?php
						$anchor3 = 'Resources';
						$anchor3Slug = sanitize_title($anchor3);
						$ca3 = get_field('anchor_title_3');
						if( $ca3 ) {
							$anchor3 = $ca3;
							$anchor3Slug = sanitize_title($ca3);
						}
					?>
					<a href="#<?php echo $anchor3Slug; ?>" data-scroll-to="<?php echo $anchor3Slug; ?>" data-scroll-offset="118">
						<?php echo $anchor3; ?>
					</a>
				</li>
				<?php } ?>
				<?php if( get_field('anchor_active_4') ){ ?>
				<li>
					<?php
						$anchor4 = 'Related Solutions';
						$anchor4Slug = sanitize_title($anchor4);
						$ca4 = get_field('anchor_title_4');
						if( $ca4 ) {
							$anchor4 = $ca4;
							$anchor4Slug = sanitize_title($ca4);
						}
					?>
					<a href="#<?php echo $anchor4Slug; ?>" data-scroll-to="<?php echo $anchor4Slug; ?>" data-scroll-offset="118">
						<?php echo $anchor4; ?>
					</a>
				</li>
				<?php } ?>
			</ul>

			<?php $link = get_field('nav_cta'); if( $link ): ?>
			<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
				<span class="icon">
					<svg viewBox="0 0 18 20" width="18" height="20">
						<path d="M17,20H1c-0.3,0-0.5-0.1-0.7-0.3C0.1,19.5,0,19.3,0,19V1c0-0.3,0.1-0.5,0.3-0.7C0.5,0.1,0.7,0,1,0h16
							c0.3,0,0.5,0.1,0.7,0.3C17.9,0.5,18,0.7,18,1v18c0,0.3-0.1,0.5-0.3,0.7S17.3,20,17,20z M16,18V2H2v16H16z M5,5h8v2H5V5z M5,9h8v2H5
							V9z M5,13h5v2H5V13z"/>
					</svg>
				</span>
				<span><?php echo $link['title']; ?></span>
				<svg viewBox="0 0 20 13" width="20" height="13">
					<path fill="#51D87D" d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
				</svg>
			</a>
			<?php endif; ?>
		</div>
	</nav>

	<section class="js-page-section" id="<?php echo $anchor1Slug; ?>">
		<?php if( get_field('integrations_toggle') ) { ?>
		<div class="bg-light py-4">
			<div class="container py-5">
				<div class="row align-items-center">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<h3 class="h6 accent-left"><?php the_field('integrations_heading'); ?></h3>
						<div class="rte">
							<?php the_field('integrations_description'); ?>
						</div>
					</div>

					<div class="col-12 col-lg-9">
						<?php if( have_rows('logo_parade') ): ?>
						<ul class="logo-list">
					  	<?php $i=1; while ( have_rows('logo_parade') ) : the_row(); ?>
							<li>
								<?php if( get_sub_field('url') ): ?><a href="<?php the_sub_field('url'); ?>"><?php endif; ?>
								<?php $image = get_sub_field('logo'); ?>
								<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
								<?php if( get_sub_field('url') ): ?></a><?php endif; ?>
							</li>
					    <?php $i++; endwhile; ?>
						</ul>
					  <?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if( get_field('overview_toggle') ) { ?>
		<div class="py-4">
			<div class="container py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('overview_heading'); ?></h3>
						</div>
					</div>

					<div class="col-12 col-lg-9">
						<div class="d-xxlg-flex">
							<div class="col-12 col-lg-10 mb-4 px-0 pr-lg-4">
							<div class="rte">
								<?php the_field('overview_content'); ?>
							</div>
							<?php $link = get_field('overview_cta'); if( $link ): ?>
							<a class="btn" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
								<span><?php echo $link['title']; ?></span>
								<svg viewBox="0 0 20 13" width="20" height="13">
									<path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
								</svg>
							</a>
							<?php endif; ?>
							</div>
							<div class="col-12 col-lg-4 px-0">
								<?php $image = get_field('guide_image'); if( !empty($image) ): ?>
							  <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if( get_field('stats_toggle') ) { ?>
		<div class="bg-brand py-4">
			<div class="container py-5">
				<div class="row ">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0 d-none d-lg-block">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('stats_heading'); ?></h3>
						</div>
					</div>

					<div class="col-12 col-lg-9">
						<div class="rte pb-4">
							<?php the_field('stats_content'); ?>
						</div>

						<?php if( have_rows('stats') ): ?>
						<div class="stat-list">
							<?php $i=1; while ( have_rows('stats') ) : the_row(); ?>
								<div class="count-wrap">
									<p class="count__num"><span class="counter"><?php the_sub_field('stat'); ?></span><span><?php the_sub_field('descriptor'); ?></span><?php the_sub_field('symbol'); ?></p>
									<div class="rte rte--sm">
										<?php the_sub_field('description'); ?>
									</div>
								</div>
							<?php $i++; endwhile; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</section>

	<section class="js-page-section" id="<?php echo $anchor2Slug; ?>">
		<?php if( get_field('features_toggle') ) { ?>
		<div class="py-4">
			<div class="container container-flush-sm py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('features_heading'); ?></h3>
						</div>
					</div>

					<div class="col-12 col-lg-9">
						<?php $i=1; while ( have_rows('features') ) : the_row(); ?>
						<div class="accordion">
							<div class="accordion__hd">
								<h4><?php the_sub_field('feature'); ?></h4>
							</div>
							<div class="accordion__bd">
								<div class="rte rte--checklist">
									<?php the_sub_field('description'); ?>
								</div>
							</div>
						</div>
						<?php $i++; endwhile; ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if( get_field('additional_heading') ) { ?>
		<div class="py-4 bg-light">
			<div class="container py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('additional_heading'); ?></h3>
						</div>
					</div>

					<div class="col-12 col-lg-9">
						<div class="d-xxlg-flex">
							<div class="col-12 col-lg-5 mb-4 px-0 pr-lg-4">
							<div class="rte">
								<?php the_field('additional_content'); ?>
							</div>
							<?php $link = get_field('additional_cta'); if( $link ): ?>
							<a class="btn" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
								<span><?php echo $link['title']; ?></span>
								<svg viewBox="0 0 20 13" width="20" height="13">
									<path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
								</svg>
							</a>
							<?php endif; ?>
							</div>
							<div class="col-12 col-lg-7 px-0">
								<?php $image = get_field('additional_image'); if( !empty($image) ): ?>
								  <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
								<?php endif; ?>
							</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</section>

	<section class="js-page-section" id="<?php echo $anchor3Slug; ?>">
		<?php if( get_field('resources_toggle') ) { ?>
		<div class="py-4">
			<div class="container container-flush-right-sm py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('resources_heading'); ?></h3>
						</div>
					</div>
					<div class="col-12 col-lg-9">

						<?php if( have_rows('resource_list_expanded') ): ?>
							<div class="scroll-container">
								<div class="scroll-container__inner">
									<?php while ( have_rows('resource_list_expanded') ) : the_row();
										$ct = get_sub_field('content_type');
										$featured_post = get_sub_field('resource');
										if( $featured_post ):
											$permalink = get_permalink( $featured_post->ID );
	        						$title = get_the_title( $featured_post->ID );
											$image = get_the_post_thumbnail( $featured_post->ID );
										?>
										<div class="scroll-container__item">
											<a class="post-preview" href="<?php echo $permalink; ?>">
												<figure>
													<?php echo $image; ?>
												</figure>
												<span class="post-preview__cat"><?php echo $ct; ?></span>
												<h3><?php echo $title; ?></h3>
												<span class="icon-link">
													<span>Learn More</span>
													<svg viewBox="0 0 20 13" width="20" height="13">
														<path fill="#51D87D" d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
													</svg>
												</span>
											</a>
										</div>
										<?php endif; ?>
									<?php endwhile; ?>
								</div>
							</div>
						<?php else: //repeater ?>

							<?php $posts = get_field('resource_list'); if( $posts ): ?>
							<div class="scroll-container">
								<div class="scroll-container__inner">
									<?php foreach( $posts as $post): setup_postdata($post); ?>
										<div class="scroll-container__item">
											<a class="post-preview" href="<?php the_permalink(); ?>">
												<figure>
													<?php the_post_thumbnail(); ?>
												</figure>
												<span class="post-preview__cat">Blog</span>
												<h3><?php the_title(); ?></h3>

												<span class="icon-link">
													<span>Learn More</span>
													<svg viewBox="0 0 20 13" width="20" height="13">
														<path fill="#51D87D" d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
													</svg>
												</span>
											</a>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<?php wp_reset_postdata(); endif; ?>
							<?php endif; //repeater ?>

					</div>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if( get_field('testimonial') ): ?>
		<div class="py-4 bg-accent-light">
			<div class="container py-5">
				<blockquote class="testimonial">
			    <q><?php the_field('testimonial'); ?></q>
					<cite>
						<p><?php the_field('author'); ?></p>
						<span><?php the_field('author_credentials'); ?></span>
					</cite>
				</blockquote>
			</div>
		</div>
		<?php endif; ?>
	</section>

	<section class="bg-brand js-page-section" id="<?php echo $anchor4Slug; ?>">
		<?php if( get_field('related_toggle') ) { ?>
		<div class="py-4">
			<div class="container py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<h3 class="h6 accent-left"><?php the_field('related_solutions_heading'); ?></h3>
						<?php if( get_field('related_solutions_content') ): ?>
					  <div class="rte">
					    <?php the_field('related_solutions_content'); ?>
					  </div>
						<?php endif; ?>
					</div>

					<div class="col-12 col-lg-9">
						<?php $posts = get_field('related_solutions'); if( $posts ): ?>
							<div class="solutions-tiles">
							  <?php foreach( $posts as $post): setup_postdata($post); ?>
							    <a class="solutions-tiles__tile" href="<?php the_permalink(); ?>">
										<div>
											<h3><?php the_title(); ?></h3>
											<span>
												<?php if( get_field('page_banner_content') ):
														echo get_field('page_banner_content');
													else:
														the_excerpt();
													endif;
												?>
												</span>
											<svg viewBox="0 0 20 13" width="20" height="13">
												<path fill="#51D87D" d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
											</svg>
										</div>
									</a>
							  <?php endforeach; ?>
							</div>
							<?php wp_reset_postdata(); endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</section>
<?php
	endwhile;
get_footer();
