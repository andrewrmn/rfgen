
<?php
/**
 * The template for displaying all single media
 */
get_header();

$taxonomy = 'media_category';
$terms = wp_get_post_terms(get_the_ID(), $taxonomy);
$parents = array();
foreach ($terms as $term):
    if ($term->parent == 0)
        $parents[] = $term->term_id;
endforeach;

$parent = get_term_by('id', $parents[0], $taxonomy);
$template = get_field('templates', $parent);

?>

<?php if( $template == 'white-paper'  ): ?>

<div class="hero hero--left-content">
	<div class="container">
		<div class="hero__content">
      <div class="rte has-accent">
        <h1><?php echo get_field('page_title'); ?></h1>
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
		<div class="col-12 col-lg-6 pr-lg-4 pr-lg-6 mb-4 mb-lg-0 pl-0 clearfix">

			<div class="rte rte--no-ul-style">
        <?php the_content(); ?>

			</div>
		</div>

		<div class="col-12 col-lg-6 pr-0">
			<div class="lp-form-wrap-pull">
				<?php $image = get_field('form_image'); if( !empty($image) ): ?>
        <img width="168" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
        <?php endif; ?>
				<?php if( get_field('form') ){ ?>
				<div class="rte">
						<?php echo get_field('form'); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php
global $post;
$post_id = get_the_ID();
$cat_ids = array();
$categories = get_the_terms($post_id, 'media_category');

if (!empty($categories) && !is_wp_error($categories)):
    foreach ($categories as $category):
        array_push($cat_ids, $category->term_id);
    endforeach;
endif;

if (!empty(get_field('white_paper_count'))) {
    $count = get_field('white_paper_count');
} else {
    $count = 6;
}

$current_post_type = get_post_type($post_id);

$query_args = array(
    'post_type' => 'media',
    'post__not_in' => array($post_id),
    'posts_per_page' => $count,
    'tax_query' => array(
        array(
            'taxonomy' => 'media_category',
            'field' => 'term_id',
            'terms' => $cat_ids
        )
    )
);
$related_cats_post = new WP_Query($query_args);
if ($related_cats_post->have_posts()):
    ?>
    <div class="section gradient-back">
        <div class="container">
            <div class="text-center mb-5"><h2>Not What You Are Looking For? Also Explore:</h2></div>

            <div class="row post-landing-list">
                <?php while ($related_cats_post->have_posts()): $related_cats_post->the_post(); ?>
                    <div class="col-sm-6 col-md-6 col-lg-4">
                        <a href="<?php the_permalink(); ?>" class="d-block">
                            <div class="post-box">
                                <div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div></div>
                                <div class="content">
                                    <div class="pb-4 coleql_height">
                                        <h4><?php the_title(); ?></h4>
                                    </div>
                                    <strong>Learn More<i class="fas fa-arrow-right ml-2"></i></strong>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                endwhile;
                wp_reset_query();
                ?>
            </div>

        </div>
    </div>
<?php endif; ?>

<?php


else: ?>


<?php get_template_part('content', 'banner'); ?>


<?php get_template_part('media/content', $template); ?>

<!--<div class="footer-cta">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>-->


<?php get_footer(); ?>

<?php
$count = count(get_field('right_section_content'));
if ($count % 2 != 0) {
?>
<script>
var set = jQuery('#rhtCont div.case-widget-col');
var length = set.length;
set.each(function(index, element) {
	if (index === (length - 1)) {
		jQuery(this).removeClass('col-lg-6 col-md-6');
		jQuery(this).addClass('col-lg-12 col-md-12');
	}
});
</script>
<?php
}

?>

<?php endif;
?>



<?php get_footer(); ?>
