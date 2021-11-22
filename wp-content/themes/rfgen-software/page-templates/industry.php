<?php
/**
 * Template Name: Industry Page Template
 */
get_header();
?>
<?php if (get_field('float_btn_link')): ?><a href="<?php echo get_field('float_btn_link'); ?>" class="btn btn-light float-btn"><small>
<?php if (get_field('float_btn_label_top')): ?><?php echo get_field(' float_btn_label_top'); ?></small><?php endif; ?>
<?php echo get_field('float_btn_label'); ?></a><?php endif; ?>

<?php get_template_part('content', 'banner'); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>


<div class="section indus-w1">
    <div class="container">
        <?php
        // Start the Loop.
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content/content', 'page');
        endwhile; // End the loop.
        ?>
    </div>
</div>

<?php if( get_field('content_area') ): ?>
<div class="section gradient-back indus-w1">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <?php echo get_field('content_area'); ?>
            </div>
            <div class="col-lg-6 full-img">
                <?php
                $image = get_field('content_section_image');
                $size = 'full'; // (thumbnail, medium, large, full or custom size)
                if ($image) {
                    echo wp_get_attachment_image($image, $size);
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if( have_rows('chequred_content') ): ?>
<div class="section chequred-content">
<div class="container">
<?php while( have_rows('chequred_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="row">
<div class="col-lg-6 text-center full-img align-self-center"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
<div class="col-lg-6  align-self-center <?php echo (!empty($image)) ? 'col-lg-6' : 'col-lg-12'; ?>">
<?php the_sub_field('content'); ?>
<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-dark"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
<?php endif; ?>

<?php /*?>

<?php */?>

<?php if (have_rows('testimonials')): ?>
    <div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_section_bg'); ?>);">
        <div class="container">
            <div class="testi-carousel">
                <div class="owl-carousel owl-theme CarouselOwl-1">
                    <?php while (have_rows('testimonials')): the_row(); ?>
                        <div class="item">
                            <div class="media">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/quote.png" alt="" class="mr-3">
                                <div class="media-body">
                                    <blockquote><?php the_sub_field('quote'); ?></blockquote>
                                    <cite>
                                        <strong><?php the_sub_field('client'); ?></strong>
                                        <?php the_sub_field('company'); ?>
                                    </cite>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
if (!empty(get_field('case_study_tag'))):
    $term_ids = get_field('case_study_tag');
    $case_studies = new WP_Query(array(
        'post_type' => 'media',
		'post__in' => $term_ids,
        /*'tax_query' => array(
			'relation' => 'AND',
            array(
                'taxonomy' => 'media_tag',
                'field' => 'term_id',
                'terms' => $term_ids
            ),
			array(
                'taxonomy' => 'media_category',
                'field' => 'slug',
                'terms' => 'success-stories'
            )
        ),*/
        'posts_per_page' => 3,
        //'orderby' => 'rand'
    ));
    if ($case_studies->have_posts()):
        ?>
        <div class="section pro-w6">
            <div class="container">
                <div class="text-center mb-4">
                    <?php echo get_field('case_studies_title'); ?>
                </div>
                <div class="row justify-content-center">
                    <?php while ($case_studies->have_posts()): $case_studies->the_post(); ?>
                        <div class="col-6 col-sm-6 col-md-3 mt-4"><a href="<?php the_permalink(); ?>" class="embed-responsive embed-responsive-1by1"><?php echo wp_get_attachment_image(get_field('company_logo', get_the_ID()), 'full'); ?></a></div>
                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (get_field('cta_section_content')): ?>
    <div class="cta-grad cta-grad-dark">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <?php echo get_field('cta_section_content'); ?>
                </div>
                <?php if (get_field('cta_btn_link')): ?><div class="col-md-4 text-center align-self-center"><a href="<?php echo get_field('cta_btn_link'); ?>" class="btn btn-primary"><?php echo get_field('cta_btn_label'); ?></a></div><?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (have_rows('benefits')): ?>
    <div class="section sol-w3">
        <div class="container">
            <div class="text-center mb-4"><?php echo get_field('benefits_section_title'); ?></div>
            <div class="row">
                <?php
                while (have_rows('benefits')): the_row();
                    $image = get_sub_field('icon');
					$icon_check = get_sub_field('icon_check');
                    ?>
                    <div class="col-md-6 col-lg-3 mt-4">
                     <?php if (get_sub_field('btn_link')): ?><a href="<?php the_sub_field('btn_link'); ?>" class="link"><?php endif; ?>
                        <div class="box all-box">
                            <div class="icon mr-3 cobalt">
							<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
                            </div>
                            <div class="content coleql_height">
                                <?php the_sub_field('title'); ?>
                                <?php the_sub_field('content'); ?>
                            </div>
                            <?php if (get_sub_field('btn_link')): ?><span class="link"><?php the_sub_field('btn_label'); ?><i class="fas fa-arrow-right ml-2"></i></span><?php endif; ?>
                        </div>
                        <?php if (get_sub_field('btn_link')): ?></a><?php endif; ?>
                    </div>
                <?php endwhile; ?>
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
