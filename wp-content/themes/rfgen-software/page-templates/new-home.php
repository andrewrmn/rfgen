<?php
/**
 * Template Name: New Homepage Page Template
 */
get_header();
?>

<?php if (have_rows('slider', get_the_ID())): ?>
    <div class="site-banner">
        <div id="carouselSlider" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php
                $irr = 1;
                while (have_rows('slider', get_the_ID())): the_row();
                    ?>
                    <div class="carousel-item <?php echo ($irr == 1) ? 'active' : ''; ?>">
                        <div class="banner-image-wrraper">
                            <div class="carousel-caption">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-8 col-lg-6">
                                            <h2><?php echo get_sub_field('title') ?></h2>
                                            <div class="d-none d-lg-block">
                                                <?php echo get_sub_field('content') ?>
                                                <?php if (get_sub_field('btn_link')): ?><a href="<?php echo get_sub_field('btn_link') ?>" class="banner-link"><?php echo get_sub_field('btn_label') ?></a><?php endif; ?>
                                            </div>
                                        </div></div>
                                </div>
                            </div>
                            <div class="site-banner-image"><?php echo wp_get_attachment_image(get_sub_field('image'), 'full'); ?></div>
                        </div>
                        <div class="d-block d-lg-none banner-content">
                            <?php echo get_sub_field('content') ?>
                            <?php if (get_sub_field('btn_link')): ?><a href="<?php echo get_sub_field('btn_link') ?>" class="banner-link"><?php echo get_sub_field('btn_label') ?></a><?php endif; ?>
                        </div>
                    </div>
                    <?php
                    $irr++;
                endwhile;
                ?>
            </div>
            <a class="carousel-control-prev" href="#carouselSlider" role="button" data-slide="prev"><span class="sr-only">Previous</span></a>
            <a class="carousel-control-next" href="#carouselSlider" role="button" data-slide="next"><span class="sr-only">Next</span></a>
        </div>
    </div>
<?php endif; ?>

<?php if (have_rows('erps')): ?>
    <div class="new-home-logos">
        <div class="container">
            <div class="logo-container row no-gutters">
                <?php
                while (have_rows('erps')): the_row();
                    $image = get_sub_field('image');
                    ?>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2"><a href="<?php the_sub_field('link'); ?>" class="d-block"><div class="table-div"><div class="table-cell"><?php echo wp_get_attachment_image($image, 'full'); ?></div></div></a></div>
                <?php endwhile; ?>
            </div>
        </div>
    </div><?php endif; ?>


<?php if (have_rows('counter_content')): ?>
    <div class="new-home-w1">
        <div class="container">
            <?php echo get_field('counter_section_title'); ?>
            <div class="row">
                <?php while (have_rows('counter_content')): the_row(); ?>
                    <div class="col-lg-4">
                        <h2><span class="counter"><?php the_sub_field('number'); ?></span><?php the_sub_field('unit'); ?></h2>
                        <p><?php the_sub_field('title'); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$trusted = new WP_Query(array('post_type' => 'trusted', 'posts_per_page' => '10', 'meta_query' => array(
        array(
            'key' => 'is_home_page',
            'value' => '1',
            'compare' => '=='
        ))
        ));
if ($trusted->have_posts()):
    ?>
    <div class="new-home-trusted-logos">
        <div class="container">
            <div class="mb-5"><?php echo get_field('trusted_section_title'); ?></div>
            <div class="owl-carousel owl-theme TrustedLogo">
                <?php
                while ($trusted->have_posts()): $trusted->the_post();
                    ?>
                    <div class="item"><div class="table-div"><div class="table-cell"><?php if( get_field('link') ): ?><a href="<?php echo get_field('link');?>"><?php endif; ?><?php the_post_thumbnail('full'); ?><?php if( get_field('link') ): ?></a><?php endif; ?></div></div></div>
                    <?php
                endwhile;
                wp_reset_query();
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (have_rows('chequred_section_content')): ?>
    <div class="new-home-solution">
        <div class="container">
            <div class="intro-content text-lg">
                <?php echo get_field('chequred_section_intro_content'); ?>
            </div>
            <div class="row">
                <?php while (have_rows('chequred_section_content')): the_row(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="box">
                            <div class="heading"><?php the_sub_field('title'); ?></div>
                            <div class="content">
                                <div class="pb-3 coleql_height">
                                    <?php the_sub_field('content'); ?></div>
                                <a href="<?php the_sub_field('link'); ?>" class="more">Learn More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (have_rows('industries')): ?>
    <div class="new-home-industry-list" style="background-image:url(<?php echo get_field('industries_section_bg'); ?>);">
        <div class="container">
            <?php echo get_field('industries_title'); ?>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo get_field('industries_intro_content'); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="row">
                        <?php while (have_rows('industries')): the_row(); ?>
                            <div class="col-sm-6 col-6 col-md-6 col-lg-4"><a href="<?php the_sub_field('link'); ?>" class="sol-link"><?php the_sub_field('title'); ?></a></div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (have_rows('image_block_content')): ?>
    <div class="new-home-more">
        <div class="container">
            <div class="title text-center"><?php echo get_field('about_section_title'); ?></div>
            <div class="row">
                <?php while (have_rows('image_block_content')): the_row(); ?>
                    <div class="col-lg-4"><a href="<?php the_sub_field('link'); ?>" class="link-btn"><?php the_sub_field('content'); ?></a></div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php
/*
 * Show case Studies  
 */
if (!empty(get_field('home_case_studies'))):
    $post_ids = get_field('home_case_studies');
    $casestudies = new WP_Query(array(
        'post_type' => 'media',
        'post__in' => $post_ids,
        'posts_per_page' => -1,
        'orderby' => 'post__in'
    ));
    if ($casestudies->have_posts()):
        ?>
        <div class="new-home-success-story">
            <div class="container">
                <div class="title">
                    <?php echo get_field('success_stories_intro_content'); ?>
                    <?php /* ?><h2>Client Success Stories</h2><?php */ ?>
                </div>
                <div class="owl-carousel owl-theme SuccessStroy">
                    <?php while ($casestudies->have_posts()): $casestudies->the_post(); ?>
                        <div class="item">
                            <a href="<?php the_permalink(); ?>" class="d-block">
                                <div class="embed-responsive embed-responsive-4by3">
                                    <?php
                                    $image = get_field('company_logo');
                                    $size = 'full'; // (thumbnail, medium, large, full or custom size)
                                    if ($image) {
                                        echo wp_get_attachment_image($image, $size);
                                    }
                                    ?>
                                </div>
                                <div class="content coleql_height">
                                    <h4><?php the_title(); ?></h4>
                                    <?php echo get_field('page_banner_content'); ?>	
                                </div>
                                <span class="link">Read More</span>
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
<?php endif; ?>


<?php if (!empty(get_field('bottom_cta_title')) || !empty(get_field('bottom_cta_content'))): ?>
    <div class="new-cta">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <?php echo get_field('bottom_cta_title'); ?>
                </div>
                <div class="col-lg-7 ml-auto">
                    <?php echo get_field('bottom_cta_content'); ?>
                    <?php if (get_field('bottom_cta_link')): ?><a href="<?php echo get_field('bottom_cta_link'); ?>" class="link-text link-text-blue"><?php echo get_field('bottom_cta_link_label'); ?><span class="icon"></span></a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php get_footer(); ?>
