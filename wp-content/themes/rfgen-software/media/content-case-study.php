<?php
/*
 * Case Study Content
 */
 
global $post;
if(isset($args['post_id']) && $args['post_id'] > 0){
	$post = get_post( $args['post_id'], OBJECT );
	setup_postdata( $post );
?>
<?php if (have_rows('testimonials')): ?>
	<div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_bg'); ?>);">
		<div class="container">
			<div class="testi-carousel">
				<div class="owl-carousel owl-theme CarouselOwl-1">
					<?php while (have_rows('testimonials')): the_row(); ?>
						<div class="item">
							<div class="media">
								<img src="<?php echo get_template_directory_uri(); ?>/images/quote.png" alt="" class="mr-3">
								<div class="media-body">
									<blockquote><?php the_sub_field('quote'); ?></blockquote>
									<cite>
										<strong><?php the_sub_field('client'); ?></strong>
										<?php the_sub_field('company'); ?>
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

<div class="section">
	<div class="container">
			<?php if (have_rows('right_section_content')): ?>
		<div class="row" id="rhtCont">
				<?php $i = 0; while (have_rows('right_section_content')): the_row(); ?>
					<div class="col-lg-6 col-md-6 case-widget-col mb-4 pb-4 <?php echo (0 == $i % 2) ? 'even-col' : 'odd-col'; ?>">   
						<div class="case-widget coleql_height">
							<?php the_sub_field('content'); ?>  
						</div>
					</div>
				<?php $i++; endwhile; ?>
		</div>
			<?php endif; ?>

			<div class="row">
			<div class="col-lg-12 col-md-12 mb-4 pb-4">
				<?php the_content(); ?>
			</div>

			<?php if (get_field('company_logo')): ?>
				<div class="col-lg-5 col-md-6 align-self-center">
					<div class="case-company-logo full-img">
						<?php
						$image = get_field('company_logo');
						$size = 'full'; // (thumbnail, medium, large, full or custom size)
						if ($image) {
							echo wp_get_attachment_image($image, $size);
						}
						?>
					</div>
				</div>
			<?php endif; ?>
			<?php if (get_field('intro_content')): ?>
				<div class="col-lg-7 col-md-6 align-self-center">
					<?php echo get_field('intro_content'); ?>
				</div>
			<?php endif; ?>
		</div>

		<ul class="list-inline mt-4">
			<li class="d-none"><a href="<?php the_permalink(); ?>">Learn More<i class="fas fa-arrow-right ml-2"></i></a></li>
					<?php if (have_rows('files')): ?>
						<?php while (have_rows('files')): the_row(); ?>
							<?php if (!empty(get_sub_field('link'))): ?>
						<li class="list-inline-item"><a class="btn btn-primary" href="<?php the_sub_field('link') ?>" <?php echo (get_sub_field('type') == 1) ? 'data-fancybox' : ''; ?>><?php the_sub_field('label') ?>  <?php the_sub_field('icon') ?></a></li>
					<?php endif; ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</ul>

	</div>
</div>
<?php } else { ?>
<?php while (have_posts()) : the_post(); ?>

    <?php if (have_rows('testimonials')): ?>
        <div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_bg'); ?>);">
            <div class="container">
                <div class="testi-carousel">
                    <div class="owl-carousel owl-theme CarouselOwl-1">
                        <?php while (have_rows('testimonials')): the_row(); ?>
                            <div class="item">
                                <div class="media">
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/quote.png" alt="" class="mr-3">
                                    <div class="media-body">
                                        <blockquote><?php the_sub_field('quote'); ?></blockquote>
                                        <cite>
                                            <strong><?php the_sub_field('client'); ?></strong>
                                            <?php the_sub_field('company'); ?>
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

    <div class="section">
        <div class="container">
                <?php if (have_rows('right_section_content')): ?>
			<div class="row" id="rhtCont">
                    <?php $i = 0; while (have_rows('right_section_content')): the_row(); ?>
                        <div class="col-lg-6 col-md-6 case-widget-col mb-4 pb-4 <?php echo (0 == $i % 2) ? 'even-col' : 'odd-col'; ?>">   
                            <div class="case-widget coleql_height">
                                <?php the_sub_field('content'); ?>  
                            </div>
                        </div>
                    <?php $i++; endwhile; ?>
			</div>
                <?php endif; ?>

				<div class="row">
                <div class="col-lg-12 col-md-12 mb-4 pb-4">
                    <?php the_content(); ?>
                </div>

                <?php if (get_field('company_logo')): ?>
                    <div class="col-lg-5 col-md-6 align-self-center">
                        <div class="case-company-logo full-img">
                            <?php
                            $image = get_field('company_logo');
                            $size = 'full'; // (thumbnail, medium, large, full or custom size)
                            if ($image) {
                                echo wp_get_attachment_image($image, $size);
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (get_field('intro_content')): ?>
                    <div class="col-lg-7 col-md-6 align-self-center">
                        <?php echo get_field('intro_content'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <ul class="list-inline mt-4">
                <li class="d-none"><a href="<?php the_permalink(); ?>">Learn More<i class="fas fa-arrow-right ml-2"></i></a></li>
                        <?php if (have_rows('files')): ?>
                            <?php while (have_rows('files')): the_row(); ?>
                                <?php if (!empty(get_sub_field('link'))): ?>
                            <li class="list-inline-item"><a class="btn btn-primary" href="<?php the_sub_field('link') ?>" <?php echo (get_sub_field('type') == 1) ? 'data-fancybox' : ''; ?>><?php the_sub_field('label') ?>  <?php the_sub_field('icon') ?></a></li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>

        </div>
    </div>

<?php endwhile; } ?>