<?php
/**
 * Template Name: Landing Page w/ Form - 2021
 */

get_header();
	while ( have_posts() ) : the_post(); ?>


	<div class="hero hero--left-content">
		<div class="container">
			<div class="hero__content">
        <div class="rte has-accent">
        	<?php the_field('intro_content'); ?>
        </div>
        <?php $link = get_field('cta'); if( $link ): ?>
        <a class="btn mt-4" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
          <span><?php echo $link['title']; ?></span>
          <svg viewBox="0 0 20 13" width="20" height="13">
            <path d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
          </svg>
        </a>
        <?php endif; ?>
			</div>
		</div>
    <?php if(has_post_thumbnail()){ ?>
		<figure class="hero__media">
			<?php the_post_thumbnail(); ?>
		</figure>
    <?php } ?>
	</div>

	<div class="container py-6">
		<div class="d-lg-flex">
			<div class="col-12 col-lg-6 pr-lg-4 pr-lg-6 mb-4 mb-lg-0 pl-0">
				<div class="rte">
					<?php the_content(); ?>
				</div>
			</div>
			<div class="col-12 col-lg-6 pr-0">
				<div class="lp-form-wrap-pull">
					<?php $image = get_field('form_image'); if( !empty($image) ): ?>
          <img width="168" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
          <?php endif; ?>
					<?php if( get_field('iframe_embed') ){ ?>
					<div class="rte">
							<?php the_field('iframe_embed'); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>


	<?php $posts = get_field('resources'); if( $posts ): ?>
  <section class="container pb-6">
    <?php if( get_field('resource_content') ){ ?>
    <div class="rte mb-4 pb-4">
      <?php the_field('resource_content'); ?>
    </div>
    <?php } ?>
    <div class="row">
      <?php foreach( $posts as $post): setup_postdata($post); ?>
      <div class="col-12 col-md-4 mb-4 pb-4 mb-md-0 pb-md-0">
        <a class="post-preview" href="<?php the_permalink(); ?>">
          <figure>
            <?php the_post_thumbnail(); ?>
          </figure>
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
  </section>
  <?php wp_reset_postdata(); endif; ?>


<?php
	endwhile;
get_footer();
