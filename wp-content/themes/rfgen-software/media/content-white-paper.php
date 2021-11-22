<?php
/*
 * White Paper Content
 */
global $post;
if(isset($args['post_id']) && $args['post_id'] > 0){
	$post = get_post( $args['post_id'], OBJECT );
	setup_postdata( $post );
	$right_form = get_field('right_column_content');
?>
<div class="section">
	<div class="container">
		<div class="row">
			<?php if(!empty($right_form)){ ?>
			<div class="col-lg-7 col-md-8 clearfix">
				<?php the_content(); ?>
			</div>
			<div class="col-lg-5 col-md-4">
				<?php echo get_field('right_column_content'); ?>
			</div>
			<?php }else{ ?>
			<div class="col-lg-12 clearfix">
				<?php the_content(); ?>
			</div>
			<?php } ?>
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
<?php while (have_posts()) : the_post();
$right_form = get_field('right_column_content');
?>
    <div class="section">
        <div class="container">
            <div class="row">
				<?php if(!empty($right_form)){ ?>
                <div class="col-lg-7 col-md-8 clearfix">
                    <?php the_content(); ?>
                </div>
                <div class="col-lg-5 col-md-4">
                    <?php echo get_field('right_column_content'); ?>
                </div>
				<?php }else{ ?>
				<div class="col-lg-12 clearfix">
                    <?php the_content(); ?>
                </div>
				<?php } ?>
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

<?php
/*
 * Related Posts
 */
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

<div class="section">
    <div class="container">

    </div>
</div>
