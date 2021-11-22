<?php
/**
 * Template Name: Landing Page - 2021
 */

get_header();
	while ( have_posts() ) : the_post(); ?>


	<div class="hero">
		<div class="container">
			<div class="hero__content text-center">
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

  <?php if( get_field('alert') ){ ?>
  <div class="lp-alert">
    <div class="container py-3">
      <?php the_field('alert'); ?>
    </div>
  </div>
  <?php } ?>


  <section class="wrapper-full bg-light py-6">
    <div class="container">
      <div class="media-object media-object--split media-object--flip media-object--center">
        <figure class="media-object__media">
          <?php $media = get_field('media_type');
            $image = get_field('content_image'); if( !empty($image) && $media == 'Image' ): ?>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
          <?php endif; ?>

          <?php $video = get_field('content_video'); if( !empty($video) && $media == 'Video' ): ?>
          <div class="video-wrapper">
            <?php echo $video; ?>
          </div>
          <?php endif; ?>
        </figure>

        <div class="media-object__content">
          <?php if( get_field('content_left') ): ?>
          <div class="rte">
            <?php the_field('content_left'); ?>
          </div>
          <?php endif; ?>

          <?php $link = get_field('content_cta'); if( $link ): ?>
          <div class="mt-4">
            <a class="btn btn--sm" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
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
  </section>

	<?php if( have_rows('columns') ):

		$count = count(get_field('columns'));

		$col = 'col-12';
		if( $count == '2' ) {
			$col = 'col-12 col-md-6 mb-4 pb-4 mb-md-0 pb-md-0';
		}
		if( $count == '3' ) {
			$col = 'col-12 col-md-4 mb-4 pb-4 mb-md-0 pb-md-0';
		}
		if( $count == '4' ) {
			$col = 'col-12 col-md-3 mb-4 pb-4';
		}
		if( $count > '4' ) {
			$col = 'col-12 col-md-4 mb-4 pb-4';
		}

	?>
  <section class="container py-6">
    <?php if(get_field('3_columns_heading')) { ?>
    <div class="rte mb-4 pb-4">
      <?php the_field('3_columns_heading'); ?>
    </div>
    <?php } ?>
		<div class="row">
		<?php $i=1; while ( have_rows('columns') ) : the_row(); ?>
			<div class="<?php echo $col; ?>">
        <div class="rte rte--tight">
          <?php the_sub_field('text'); ?>
        </div>
      </div>
		<?php $i++; endwhile; ?>
		</div>
  </section>
	<?php endif; ?>


	<?php if( get_the_content() ) { ?>
		<section class="container py-6">
	    <?php the_content(); ?>
	  </section>
	<?php } ?>

  <section class="wrapper-full bg-brand py-6">
    <div class="container">
      <div class="media-object media-object--split">
        <figure class="media-object__media">
          <?php
            $image = get_field('banner_image'); if( !empty($image) ): ?>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
          <?php endif; ?>
        </figure>

        <div class="media-object__content">
          <?php if( get_field('banner_content') ): ?>
          <div class="rte">
            <?php the_field('banner_content'); ?>
          </div>
          <?php endif; ?>

          <?php $link = get_field('banner_cta'); if( $link ): ?>
          <div class="mt-4">
            <a class="btn btn--sm" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
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
  </section>

  <?php $posts = get_field('resource_list'); if( $posts ): ?>
  <section class="container py-6">
    <?php if( get_field('resources_heading') ){ ?>
    <div class="rte mb-4 pb-4">
      <?php the_field('resources_heading'); ?>
    </div>
    <?php } ?>
    <div class="row">
      <?php foreach( $posts as $post): setup_postdata($post); ?>
      <div class="col-12 col-md-4 mb-4 pb-4 mb-md-0 pb-md-0">
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
  </section>
  <?php wp_reset_postdata(); endif; ?>

  <?php $posts = get_field('solutions'); if( $posts ): ?>
  <section class="wrapper-full bg-brand py-6">
    <div class="container py-4">
      <?php if( get_field('solutions_heading') ){ ?>
      <div class="rte mb-4 pb-4">
        <?php the_field('solutions_heading'); ?>
      </div>
      <?php } ?>
      <div class="solutions-tiles">
        <?php foreach( $posts as $post): setup_postdata($post); ?>
        <a class="solutions-tiles__tile solutions-tiles__tile--show" href="<?php the_permalink(); ?>">
          <div>
            <h3><?php the_title(); ?></h3>
            <span><?php the_excerpt(); ?></span>
            <svg viewBox="0 0 20 13" width="20" height="13">
              <path fill="#51D87D" d="M16.8,5.7l-4.5-4.5L13.5,0L20,6.5L13.5,13l-1.2-1.2l4.5-4.5H0V5.7H16.8z"/>
            </svg>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php wp_reset_postdata(); endif; ?>


<?php
	endwhile;
get_footer();
