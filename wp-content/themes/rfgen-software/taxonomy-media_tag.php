<?php
/*
 * Media Taxonomy Page
 */
get_header();
?>


<?php get_template_part('content', 'banner'); ?>
<!--<div class="top-cta">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>-->

<div class="section resources-w1 pb-0">
    <div class="container">
        <div class="row post-landing-list">
            <?php while (have_posts()): the_post(); ?>
			<?php if(get_field('show_in_front_page') != 1){ continue; }?>
                <div class="col-sm-6 col-md-6 col-lg-4">
                    <div class="d-block">
                        <div class="post-box">
							<a href="<?php the_permalink(); ?>"><div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div></div></a>
                            <div class="content">
                                <div class="pb-4 coleql_height">
                                    <h4><?php the_title(); ?></h4>
                                    <?php the_excerpt(); ?>
                                    
									<?php
                                    $object_terms = wp_get_object_terms(get_the_ID(), 'media_tag', array('fields' => 'names'));
                                    if ($object_terms):
									?><ul><li><?php echo implode('</li><li>', $object_terms); ?></li></ul><?php endif; ?>
									
									
                                </div>
                                <a href="<?php the_permalink(); ?>" class="link">Learn More<i class="fas fa-arrow-right ml-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!--<div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>-->
<?php
get_footer();
