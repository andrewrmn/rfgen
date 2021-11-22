<?php
/**
 * Template Name: New Solutions Page Template
 */
get_header();
?>

<?php if (!empty(get_field('banner_title')) || !empty(get_field('banner_content'))): ?>
    <div class="inner-page-banner">
        <div class="inner-page-banner-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-8">
                        <?php echo get_field('banner_title'); ?>
                        <div class="d-none d-lg-block">
                            <?php echo get_field('banner_content'); ?>
                            <?php if (get_field('primary_btn_link')): ?><a href="<?php echo get_field('primary_btn_link'); ?>" class="button-custom button-primary mr-3"><?php echo get_field('primary_btn_label'); ?></a> <?php endif; ?>
                            <?php if (get_field('secondary_btn_link')): ?><a href="<?php echo get_field('secondary_btn_link'); ?>" class="button-custom button-secondary"><?php echo get_field('secondary_btn_label'); ?></a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
			
			
			<?php if ( get_field( 'banner_image' ) ): ?>
 <div class="inner-banner-image">
			<?php 
$image = get_field('banner_image');
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
    echo wp_get_attachment_image( $image, $size );
}?>
			</div>

<?php else:?>

<div class="inner-banner-image no-img"></div>
<?php endif;?>

            
        </div>
        <div class="d-block d-lg-none inner-banner-content-sm">
            <?php echo get_field('banner_content'); ?>
            <?php if (get_field('primary_btn_link')): ?><a href="<?php echo get_field('primary_btn_link'); ?>" class="button-custom button-primary"><?php echo get_field('primary_btn_label'); ?></a> <?php endif; ?>
            <?php if (get_field('secondary_btn_link')): ?><a href="<?php echo get_field('secondary_btn_link'); ?>" class="button-custom button-secondary"><?php echo get_field('secondary_btn_label'); ?></a><?php endif; ?>
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
    </div>
<?php endif; ?>

<?php
/*
 * Trusted Section
 */
$term_id = get_field('trusted');
if (!empty($term_id)):
    $trusted = new WP_Query(array('post_type' => 'trusted', 'posts_per_page' => '6', 'tax_query' => array(
            array(
                'taxonomy' => 'trustedcat',
                'field' => 'term_id',
                'terms' => $term_id
            )
        )
    ));
    if ($trusted->have_posts()):
        ?>
        <div class="new-home-trusted-logos">
            <div class="container">
                <div class="mb-5"><?php echo get_field('trusted_heading'); ?></div>
                <div class="owl-carousel owl-theme TrustedLogo">
                    <?php while ($trusted->have_posts()): $trusted->the_post(); ?>
                        <div class="item"><div class="table-div"><div class="table-cell"><div class="item"><?php if( get_field('link') ): ?><a href="<?php echo get_field('link');?>"><?php endif; ?><?php the_post_thumbnail('full'); ?><?php if( get_field('link') ): ?></a><?php endif; ?> </div></div></div></div>
                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>
                </div>
            </div>
        </div>
        <?php
    endif;
endif;
?>


<div class="new-section-md solution-mid-content">
    <div class="container text-lg intro-content">
        <?php
        /* Start the Loop */
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content/content', 'page');
        endwhile; // End of the loop.
        ?>
    </div>
</div>



<?php
$benefits = new WP_Query(array('post_type' => 'benefits', 'posts_per_page' => '400'));
if ($benefits->have_posts()):
    ?>
    <div class="new-home-w1 mt-0">
        <div class="container">
            <?php echo get_field('benefit_section_title'); ?>
            <div class="row">
                <?php
                while ($benefits->have_posts()): $benefits->the_post();
                    ?>
                    <div class="col-lg-3">
                        <h2><span class="counter"><?php echo get_field('numbers'); ?></span><?php echo get_field('unit'); ?></h2>
                        <?php the_content(); ?>
                    </div>
                    <?php
                endwhile;
                wp_reset_query();
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (have_rows('testimonials')): ?>
    <div class="page-quote new-section-md">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <?php while (have_rows('testimonials')): the_row(); ?>
                        <div class="quote">
                            <div class="content">
                                <blockquote><?php the_sub_field('quote'); ?></blockquote>
                                <cite>
                                    <strong><?php the_sub_field('client'); ?></strong><?php the_sub_field('copmany'); ?>
                                </cite>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (have_rows('scroll_section_item', get_the_ID())): ?>
    <div class="new-section core-feature">
        <div class="container">
            <?php echo get_field('scroll_section_title'); ?>
            <div class="row tabs-area">
                <div class="col-lg-6">
                    <ul class="nav nav-tabs" role="tablist">
                        <?php
                        $irr = 1;
                        while (have_rows('scroll_section_item', get_the_ID())): the_row();
                            ?>
                            <li class="nav-item"><a class="nav-link  <?php echo ($irr == 1) ? 'active' : ''; ?> " id="tab-<?php echo $irr; ?>" data-toggle="tab" href="#tab<?php echo $irr; ?>" role="tab" aria-controls="tab<?php echo $irr; ?>" aria-selected="<?php echo ($irr == 1) ? 'true' : 'false'; ?>"><span><?php the_sub_field('title'); ?></span></a></li>
                            <?php
                            $irr++;
                        endwhile;
                        ?>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="tab-content">
                        <?php
                        $ir = 1;
                        while (have_rows('scroll_section_item', get_the_ID())): the_row();
                            ?>
                            <div class="tab-pane fade <?php echo ($ir == 1) ? 'show active' : ''; ?>" id="tab<?php echo $ir; ?>" role="tabpanel" aria-labelledby="tab-<?php echo $ir; ?>">
                                <?php the_sub_field('content'); ?>
                            </div>
                            <?php
                            $ir++;
                        endwhile;
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (get_field('third_cta_section_content')): ?>
    <div class="solution-ebook d-block d-lg-none">
        <div class="media">
            <div class="thumb">
                <?php
                $image = get_field('third_cta_section_bg');
                $size = 'full'; // (thumbnail, medium, large, full or custom size)
                if ($image) {
                    echo wp_get_attachment_image($image, $size);
                }
                ?>
            </div>
            <div class="media-body">
                <?php echo get_field('third_cta_section_content'); ?>
                <a href="<?php echo get_field('third_cta_section_btn_link'); ?>" class="button-custom button-primary"><?php echo get_field('third_cta_section_btn_label'); ?></a>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="new-section">
    <div class="container">

        <?php if (get_field('third_cta_section_content')): ?>
            <div class="solution-ebook d-none d-lg-block">
                <div class="media">
                    <div class="thumb"><img src="<?php echo get_template_directory_uri(); ?>/images/new/image-24.png" alt=""></div>
                    <div class="media-body">
                        <h6>Free e-book</h6>
                        <h4>Where's My Inventory?</h4>
                        <p>Download this free eBook to learn how mobile barcoding software can help you find your inventory in real-time, every time, all the time. Cut costs. Increase productivity, efficiency, throughput and profit.</p>
                        <a href="<?php echo get_field('third_cta_section_btn_link'); ?>" class="button-custom button-primary">Download the eBook</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <?php if (have_rows('resources')): ?>
            <div class="solution-resources">
                <h6>Helpful resources</h6>
                <div class="owl-carousel owl-theme SuccessStroy">
                    <?php
                    while (have_rows('resources')): the_row();
                        $image = get_sub_field('image');
                        ?>
                        <div class="item">
                            <a href="<?php the_sub_field('link'); ?>" class="d-block">
                                <div class="embed-responsive embed-responsive-4by3"><div class="full-img"><?php echo wp_get_attachment_image($image, 'full'); ?></div></div>
                                <div class="content pb-4">
                                    <ul>
                                        <li><?php the_sub_field('post_type'); ?></li>
                                    </ul>
                                    <h5><span><?php the_sub_field('title'); ?></span></h5>
                                </div>
                                <span class="link">Read More</span>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php endif; ?>


<?php if (get_field('first_cta_section_content')): ?>
    <div class="full-cta new-section-md">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-lg">
                    <?php echo get_field('first_cta_section_content'); ?>
                    <?php if (get_field('first_cta_section_btn_link')): ?><a href="<?php echo get_field('first_cta_section_btn_link'); ?>" class="button-custom button-primary"><?php echo get_field('first_cta_section_btn_label'); ?></a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if (have_rows('box_content')): ?>
    <div class="new-home-solution">
        <div class="container">
            <div class="intro-content text-lg">
                <?php echo get_field('box_section_content'); ?>
            </div>
            <div class="row">
                <?php while (have_rows('box_content')): the_row(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="box">
                            <div class="heading"><?php the_sub_field('title'); ?></div>
                            <div class="content">
                                <div class="pb-3 coleql_height">
                                    <?php the_sub_field('content'); ?>
                                </div>
                                <a href="<?php the_sub_field('btn_link'); ?>" class="more"><?php the_sub_field('btn_label'); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php get_footer(); ?>
