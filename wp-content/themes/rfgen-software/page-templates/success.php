<?php
/**
 * Template Name: Success Page Template
 */
get_header();
$page_id - get_the_ID();
?>


<?php get_template_part('content', 'banner'); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>

<div class="section">
    <div class="container">
        <?php
// Start the Loop.
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content/content', 'page');
        endwhile; // End the loop.
        wp_reset_query();
        ?>
    </div>
</div>

<?php
/*
 * Show Case Studies
 */

$case_studies = get_field('case_studies', $page_id);
if (!empty($case_studies)):
    $media = new WP_Query(array('post_type' => 'media', 'posts_per_page' => -1, 'post__in' => $case_studies));
    ?>
    <div class="section resources-w1 gradient-back">
        <div class="container">
            <div class="text-center mb-5"><h2>You May Also Like:</h2></div>
            <div class="row post-landing-list">
                <?php while ($media->have_posts()): $media->the_post(); ?>
                    <div class="col-sm-6 col-md-6 col-lg-4">
                        <a href="<?php the_permalink(); ?>" class="d-block">
                            <div class="post-box">
                                <div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            the_post_thumbnail('full');
                                        } else {
                                            ?>
                                            <img src="<?php bloginfo('template_directory'); ?>/images/default-image.jpg" alt="<?php the_title(); ?>" />
                                        <?php } ?> 
                                    </div></div>
                                <div class="content">
                                    <div class="pb-4 coleql_height">
                                        <h4><?php the_title(); ?></h4>
                                        <?php the_excerpt(); ?>
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


<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>

<?php get_footer(); ?>
