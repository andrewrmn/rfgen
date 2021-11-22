<?php
/*
 * All Media Content
 */
global $post;
if(isset($args['post_id']) && $args['post_id'] > 0){
	$post = get_post( $args['post_id'], OBJECT );
	setup_postdata( $post );
?>
<div class="section">
	<div class="container">
		<?php the_content(); ?>

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
    <div class="section">
        <div class="container">
            <?php the_content(); ?>

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
