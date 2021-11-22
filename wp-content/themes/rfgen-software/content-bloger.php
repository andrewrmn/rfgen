<li class="col-sm-6 col-md-4 mb-4 <?php if ( get_field('hide_form_list', $args['post__id']) == true ) { ?> d-none <?php } else { ?> d-block <?php } ?>">
    <div class="post-items">
        <a href="<?php echo get_permalink($args['post__id']); ?>" rel="bookmark">
            <div class="embed-responsive embed-responsive-4by3">
                <div class="full-img">
                    <?php if (get_the_post_thumbnail($args['post__id'])): ?>
                        <?php echo get_the_post_thumbnail($args['post__id'], 'full'); ?>
                    <?php else : ?>
                        <img src="<?php bloginfo('template_directory'); ?>/images/default-image.jpg" alt="<?php echo get_the_title($args['post__id']); ?>" />
                    <?php endif; ?> 
                </div>
            </div>
        </a>
        <div class="content content-blog coleql_height">
            <ol>
                <li><?php echo get_the_date('', $args['post__id']); ?></li>
                <?php /* ?><li>4 min read</li><?php */ ?>
            </ol>
            <h5><a href="<?php echo get_permalink($args['post__id']); ?>" rel="bookmark"><?php echo get_the_title($args['post__id']); ?></a></h5>
        </div>
    </div>
</li>