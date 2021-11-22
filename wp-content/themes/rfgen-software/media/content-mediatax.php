<li class="col-sm-6 col-md-4 mb-4">
    <div class="post-items d-click">
        <a href="<?php the_permalink(); ?>" class="d-block"><div class="embed-responsive embed-responsive-4by3"><div class="full-img">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('full');
                    } else {
                        ?>
                        <img src="<?php bloginfo('template_directory'); ?>/images/default-image.jpg" alt="<?php the_title(); ?>" />
                    <?php } ?> 
                </div></div></a>
        <div class="content">
            <div class="pb-4 coleql_height">
                <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
            </div>
            <?php
$categories = get_the_terms($post->ID, "media_category");
if (!empty($categories)) {
echo '<small class="d-block">';
//foreach ($categories as $value) {
echo '<span>' . esc_html($categories[0]->name) . '</span>';
//}
echo '</small>';
}
?>
            <ul>
                <li><a href="<?php the_permalink(); ?>">Learn More<i class="fas fa-arrow-right ml-2"></i></a></li>
                        <?php if (have_rows('files')): ?>
                            <?php while (have_rows('files')): the_row(); ?>
                                <?php if (!empty(get_sub_field('link'))): ?>
                            <li><a class="dw-fls" href="<?php the_sub_field('link') ?>" <?php echo (get_sub_field('type') == 1) ? 'data-fancybox' : ''; ?>><?php the_sub_field('label') ?>  <?php the_sub_field('icon') ?></a></li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</li>