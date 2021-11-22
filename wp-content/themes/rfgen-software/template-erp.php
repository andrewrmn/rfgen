<?php
/**
 * Template Name: ERP
 */

get_header();
	while ( have_posts() ) : the_post(); ?>


	<div class="hero-split hero-split--white">
		<div class="container">
			<div class="hero-split__content">

				<div class="hero-split__intro">
						<div>
							<?php $image = get_field('hero__logo'); if( !empty($image) ): ?>
						  <img class="mb-4" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
							<?php endif; ?>

							<?php $tag1 = get_field('sub_heading_tag'); ?>
							<?php if(get_field('sub-heading')){ ?><<?php echo $tag1; ?> class="has-accent sub-heading"><?php the_field('sub-heading'); ?></<?php echo $tag1; ?>><?php } ?>
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
						$anchor1 = 'Benefits';
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
						$anchor2 = 'Success Stories';
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
						$anchor3 = 'Features';
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
						$anchor4 = 'Resources';
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

			<?php $link = get_field('data_sheet'); if( $link ): ?>
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

	<section class="js-page-section bg-brand" id="<?php echo $anchor1Slug; ?>">
		<?php if( get_field('benefits_toggle') ) { ?>
		<div class="py-4">
			<div class="container container-flush-right-sm py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('benefits_heading'); ?></h3>
						</div>
					</div>
					<div class="col-12 col-lg-9">

						<?php if( get_field('benefits_content') ): ?>
						  <div class="rte mb-5">
						    <?php the_field('benefits_content'); ?>
						  </div>
						<?php endif; ?>

						<div class="row">
							<?php $i = 1; while ( have_rows('benefits') ) : the_row(); ?>
							<div class="col-12 col-md-4 mb-5">
								<?php if( get_sub_field('content') ): ?>
								  <div class="rte">
								    <?php the_sub_field('content'); ?>
								  </div>
								<?php endif; ?>
							  <div class="icon-link mt-4" data-scroll-to="feature-<?php echo $i; ?>" data-scroll-offset="112">
									<span><?php the_sub_field('scroll_to_text'); ?></span>
									<svg width="20" height="20" viewBox="0 0 20 20">
										<path d="M10.8331 16.81L15.3031 12.34L16.4814 13.5183L9.99978 20L3.51811 13.5183L4.69645 12.34L9.16645 16.81V0L10.8331 0V16.81Z" fill="#51D87D"/>
									</svg>
								</div>
							</div>
							<?php $i++; endwhile; ?>
						</div>


						<?php if( get_field('benefits_footer_text') ): ?>
						  <div class="rte mt-3">
						    <?php the_field('benefits_footer_text'); ?>
						  </div>
						<?php endif; ?>

						<?php $link = get_field('data_sheet'); if( $link ): ?>
							<a class="btn btn--outline mt-4" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
								<svg width="18" height="20" viewBox="0 0 18 20">
									<path d="M17 20H1C0.734784 20 0.48043 19.8946 0.292893 19.7071C0.105357 19.5196 0 19.2652 0 19V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H17C17.2652 0 17.5196 0.105357 17.7071 0.292893C17.8946 0.48043 18 0.734784 18 1V19C18 19.2652 17.8946 19.5196 17.7071 19.7071C17.5196 19.8946 17.2652 20 17 20ZM16 18V2H2V18H16ZM5 5H13V7H5V5ZM5 9H13V11H5V9ZM5 13H10V15H5V13Z" />
								</svg>
								<span><?php echo $link['title']; ?></span>
							</a>
						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</section>

	<section class="js-page-section" id="<?php echo $anchor2Slug; ?>">
		<?php if( get_field('ss_toggle') ) { ?>
		<div class="py-4">
			<div class="container container-flush-right-sm py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('ss_heading'); ?></h3>
						</div>
					</div>
					<div class="col-12 col-lg-9">

						<?php if( get_field('ss_content') ): ?>
						  <div class="rte mb-5">
						    <?php the_field('ss_content'); ?>
						  </div>
						<?php endif; ?>

						<?php $posts = get_field('success_stories'); if( $posts ): ?>
							<div class="row-scroll-wrap">
								<div class="row row--scroll">
								  <?php foreach( $posts as $post): setup_postdata($post); ?>
								    <div class="col-4">

											<a class="post-preview" href="<?php the_permalink(); ?>">
												<figure>
													<?php the_post_thumbnail(); ?>
												</figure>
												<span class="post-preview__cat">Case Studies</span>
								      	<h3><?php the_title(); ?></h3>

												<span class="icon-link">
													<span>Learn More</span>
													<svg viewBox="0 0 20 13" width="20" height="13">
														<path fill="#51D87D" d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
													</svg>
												</a>
											</a>
								    </div>
								  <?php endforeach; ?>
								</div>
							</div>
							<?php wp_reset_postdata(); endif; ?>
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

	<section class="js-page-section" id="<?php echo $anchor3Slug; ?>">
		<?php if( get_field('features_toggle') ) { ?>
		<div class="bg-light py-4">
			<div class="container py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<h3 class="h6 accent-left"><?php the_field('features_heading'); ?></h3>
					</div>

					<div class="col-12 col-lg-9">
						<?php if( get_field('features_content') ): ?>
						  <div class="rte mb-5">
						    <?php the_field('features_content'); ?>
						  </div>
						<?php endif; ?>

						<?php if( have_rows('features') ): ?>

					  	<?php $i=1; while ( have_rows('features') ) : the_row(); ?>
							<div class="media-object <?php if ($i % 2 == 0){ ?>media-object--flip<?php } ?>" id="feature-<?php echo $i; ?>">
								<figure class="media-object__media">
									<?php $image = get_sub_field('image'); if( !empty($image) ): ?>
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
									<?php endif; ?>
								</figure>
								<div class="media-object__content">
									<?php if( get_sub_field('content') ): ?>
									<div class="rte rte--checklist mb-4">
										<?php the_sub_field('content'); ?>
									</div>
									<?php endif; ?>

									<?php if( have_rows('two_col_blocks') ): ?>
									<div class="row d-sm-flex flex-wrap">
										<?php while ( have_rows('two_col_blocks') ) : the_row(); ?>
										<div class="col-12 col-sm-6 pr-md-3 mb-4">
											<div class="rte">
												<?php the_sub_field('block'); ?>
											</div>
										</div>
										<?php endwhile; ?>
									</div>
									<?php endif; ?>

									<div class="d-md-flex align-items-center">
										<?php $link = get_sub_field('cta_1'); if( $link ): ?>
										<div class="mr-4 mb-4">
											<a class="btn btn--sm" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
												<span><?php echo $link['title']; ?></span>
												<svg viewBox="0 0 20 13" width="20" height="13">
													<path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
												</svg>
											</a>
										</div>
										<?php endif; ?>

										<?php $link = get_sub_field('cta_2'); if( $link ): ?>
										<div class="mb-3">
											<a class="icon-link" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
												<span><?php echo $link['title']; ?></span>
												<svg viewBox="0 0 20 13" width="20" height="13">
													<path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
												</svg>
											</a>
										</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
					    <?php $i++; endwhile; ?>

					  <?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</section>

	<section class="bg-brand js-page-section" id="<?php echo $anchor4Slug; ?>">
		<?php if( get_field('resources_toggle') ) { ?>
		<div class="py-4">
			<div class="container container-flush-right-sm py-5">
				<div class="row">
					<div class="col-12 col-lg-3 mb-4 mb-lg-0">
						<div class="accent-left">
							<h3 class="h6"><?php the_field('resources_heading'); ?></h3>
						</div>
					</div>
					<div class="col-12 col-lg-9 row-scroll-wrapper">

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
	</section>

<?php
	endwhile;
get_footer();
