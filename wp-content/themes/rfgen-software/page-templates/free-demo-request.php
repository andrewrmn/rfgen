<?php
	/**
	 * Template Name: Free Demo Request Page Template
	 */
	
	get_header(); ?>
<?php get_template_part( 'content', 'banner' ); ?>

<div class="section">
	<div class="container">
		<?php
			// Start the Loop.
			while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/content', 'page' );
			endwhile; // End the loop.
			?>
	</div>
</div>
<?php if( have_rows('partners') ): ?>
<div class="section gradient-back partner-logo">
	<div class="container">
		<div class="mb-4"><?php echo get_field('partners_intro_content');?></div>
		<div class="owl-carousel owl-theme CarouselOwlLogo">
			<?php while( have_rows('partners') ): the_row(); 
				$image = get_sub_field('image');
				?>
			<div class="item text-center"><a href="<?php the_sub_field('link'); ?>" target="_blank" class="d-block"><?php echo wp_get_attachment_image( $image, 'full' ); ?></a></div>
			<?php endwhile; ?>     
		</div>
	</div>
</div>
<?php endif; ?>



<div class="contact-info-row row no-gutters">
	<?php if( have_rows('info_content') ): ?>
	<?php while( have_rows('info_content') ): the_row();?>
	<div class="col-sm-6 col-md-6 col-lg-3">
		<div class="content">
			<div class="media">
				<div class="icon"><?php the_sub_field('icon'); ?></div>
				<div class="media-body">
					<?php the_sub_field('content'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php endwhile; ?>
	<?php endif; ?>
	<?php if( have_rows('social') ): ?>
	<div class="col-sm-6 col-md-6 col-lg-3">
		<div class="content">
			<div class="media">
				<div class="icon"><?php echo get_field('social_icon');?></div>
				<div class="media-body">
					<h4><?php echo get_field('social_title');?></h4>
					<ul class="connect">
						<?php while( have_rows('social') ): the_row();?>
						<li><a href="<?php the_sub_field('link'); ?>" target="_blank"><?php the_sub_field('icon'); ?></a></li>
						<?php endwhile; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php if( get_field('map') ): ?>
<div class="section pb-0">
	<div class="container mb-5 text-center">
		<?php echo get_field('map_title');?>
	</div>
	<div class="map">
		<div class="map-inner"><?php echo get_field('map');?></div>
	</div>
</div>
<?php endif; ?>
<?php if( have_rows('testimonials') ): ?>
<div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_bg');?>);">
	<div class="container">
		<div class="testi-carousel">
			<div class="owl-carousel owl-theme CarouselOwl-1">
				<?php while( have_rows('testimonials') ): the_row(); ?>
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
<?php get_footer(); ?>