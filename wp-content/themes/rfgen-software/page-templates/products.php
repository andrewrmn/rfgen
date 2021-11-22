<?php
/**
 * Template Name: Products Page Template
 */
get_header();
$page_id = get_the_ID();
?>
<style>.transist{transition: all 0.5s ease;-webkit-transition: all 0.5s ease;-moz-transition: all 0.5s ease}</style>
<?php if (get_field('float_btn_link')): ?>
    <a href="<?php echo get_field('float_btn_link'); ?>" class="floating-btn"><span class="media"><img src="<?php echo get_template_directory_uri(); ?>/images/wallet.svg" alt="wallet"><span class="media-body"><?php if (get_field('float_btn_label_top')): ?><?php echo get_field(' float_btn_label_top'); ?></small><?php endif; ?><?php echo get_field('float_btn_label'); ?></span></span></a>
<?php endif; ?>

<?php get_template_part('content', 'banner'); ?>

<div class="top-cta <?php if (get_field('show_top_cta') == 'Show'): ?>d-block<?php endif; ?><?php if (get_field('show_top_cta') == 'Hide'): ?>d-none<?php endif; ?>">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>


<div class="section pro-w1">
    <div class="container">
        <?php
        while (have_posts()) : the_post();
            get_template_part('template-parts/content/content', 'page');
        endwhile;
        wp_reset_query();
        ?>
		
		<?php if( have_rows('box_listing_content') ): ?>
<div class="row post-landing-list">
<?php while( have_rows('box_listing_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="col-sm-2 col-md-2 col-6 col-lg-2 mb-0 mt-4">
<a href="<?php the_sub_field('link'); ?>" class="d-block">
<div class="post-box">
<div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div></div>
<div class="content">
<div class="pb-4 coleql_height">
<h4><?php the_sub_field('title'); ?></h4>
<?php the_sub_field('content'); ?>
</div>
<small><strong>Learn More<i class="fas fa-arrow-right ml-2"></i></strong></small>
</div>
</div>
</a>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>
		
    </div>
</div>



<div class="<?php if (get_field('show_performance_section') == 'Show'): ?>d-block<?php endif; ?><?php if (get_field('show_performance_section') == 'Hide'): ?>d-none<?php endif; ?>">
    <?php
    $benefits = new WP_Query(array('post_type' => 'benefits', 'posts_per_page' => '400'));
    if ($benefits->have_posts()):
        ?>
        <div class="section pro-w2 gradient-back">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-4">
                        <?php echo get_field('performance_content'); ?>
                    </div>
                    <div class="col-lg-8 col-md-12">
                        <div class="row">
                            <?php
                            while ($benefits->have_posts()): $benefits->the_post();
                                ?>
                                <div class="col-md-6 mb-4">
                                    <div class="box all-box">
                                        <div class="media"><div class="icon mr-3 see-glass"><?php the_post_thumbnail('full'); ?></div><div class="media-body"><h5><?php echo get_field('numbers'); ?><?php echo get_field('unit');?><span><?php the_title(); ?></span></h5></div></div>
                                        <div class="content coleql_height">
                                            <?php the_content(); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_query();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (have_rows('features')): ?>
<div class="section pro-w3">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 mb-5">
                <?php echo get_field('feature_section_content'); ?>
            </div>
        </div>

        <div class="icon-section">
            <?php
                $count = 0;
                $group = 0;
                while (have_rows('features')) : the_row();
                    $content = get_sub_field('content');
                    $image = get_sub_field('icon');
                    $imageh = get_sub_field('icon_hover');
                    if ($count % 3 == 0) {
                        $group++;
                        ?>
                        <div id="sol-<?php echo $group; ?>" class="row no-gutters cf group-<?php echo $group; ?>">
                            <?php
                        }
                        ?>
                        <div class="col-md-4">
                            <div class="content">
                                <div class="icon"><img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" /><img src="<?php echo $imageh['url']; ?>" alt="<?php echo $imageh['alt'] ?>" /></div>
                                <?php echo $content; ?>
                            </div>
                        </div>
                        <?php
                        if ($count % 3 == 2) {
                            ?>
                        </div>
                        <?php
                    }
                    $count++;
                endwhile;
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (have_rows('links')): ?>
<?php /*?><?php if( get_field('section_link') ): ?><a href="<?php echo get_field('section_link'); ?>" class="d-block"><?php endif; ?><?php */?>
<a href="<?php echo get_field( 'section_link'); ?>" class="d-block">
    <div class="section pro-w4" style="background-image:url(<?php echo get_field('link_section_bg'); ?>);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center mb-3">
                    <?php echo get_field('link_section_content'); ?>
                </div>
            </div>

            <div class="row justify-content-center">
                <?php
                while (have_rows('links')): the_row();
                    $image = get_sub_field('icon');
                    ?>
                    <div class="col-md-6 col-lg-4 mt-4"><div class="media transist"><?php echo wp_get_attachment_image($image, 'full'); ?><span class="media-body"><?php the_sub_field('label'); ?></span></div></div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</a>    
<?php /*?><?php if( get_field('section_link') ): ?></a>   <?php endif; ?> <?php */?>
<?php endif; ?>


<?php if (have_rows('fifty_icon_section')): ?>
    <div class="section pro-w5">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <?php echo get_field('fifty_section_content'); ?>
                </div>
                <div class="col-lg-7">
                    <?php
                    while (have_rows('fifty_icon_section')): the_row();
                        $image = get_sub_field('icon');
                        ?>
                        <div class="media">
                            <?php echo wp_get_attachment_image($image, 'full'); ?>
                            <div class="media-body">
                                <?php the_sub_field('content'); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



<?php if (have_rows('testimonials')): ?>
    <div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_section_bg'); ?>);">
        <div class="container">
            <div class="testi-carousel">
                <div class="owl-carousel owl-theme CarouselOwl-1">
                    <?php while (have_rows('testimonials')): the_row(); ?>
                        <div class="item">
                            <div class="media">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/quote.png" alt="quote" class="mr-3">
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
if (!empty(get_field('choose_casestudy'))):
    $term_ids = get_field('choose_casestudy');
    $case_studies = new WP_Query(array(
        'post_type' => 'media',
		'post__in' => $term_ids,
        'posts_per_page' => 100,
        'orderby' => 'post__in'
    ));
        /*'tax_query' => array(
            array(
                'taxonomy' => 'media_tag',
                'field' => 'term_id',
                'terms' => $term_ids
            )
        ),*/
    if ($case_studies->have_posts()):
        ?>
        <div class="section pro-w6">
            <div class="container">
                <div class="text-center mb-4">
                    <?php echo get_field('case_study_section_content'); ?>
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


<?php if (have_rows('scroll_section_content')): ?>
    <div class="section pro-scroll-section gradient-back">
        <div class="container">
            <?php if (get_field('scroll_section_intro_content')): ?><div class="mb-5"><?php echo get_field('scroll_section_intro_content'); ?></div><?php endif; ?>
            <div class="owl-carousel owl-theme CarouselOwl-full">
                <?php
                while (have_rows('scroll_section_content')): the_row();
                    $image = get_sub_field('image');
                    ?>
                    <div class="item">
                        <div class="row">
                            <div class="col-lg-6 text-center"><?php echo wp_get_attachment_image($image, 'full'); ?></div>
                            <div class="col-lg-6">
                                <?php the_sub_field('content'); ?>
                                <?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-dark"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php /*?>
<?php if( have_rows('video_list') ): ?>
<div class="section pro-w7 d-none">
<div class="container">
<div class="text-center mb-5">
<?php echo get_field('video_list_section_intro');?>
</div>
<div class="owl-carousel owl-theme CarouselOwl-More">
<?php while( have_rows('video_list') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="item"><div class="content"><a href="<?php the_sub_field('video_link'); ?>" class="d-block"><div class="full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div><div class="title coleql_height"><?php the_sub_field('caption'); ?></div></a></div></div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>
<?php */?>

<?php if( have_rows('video_list') ): ?>
<div class="section pro-w7">
<div class="container">
<div class="mb-4"><?php if( get_field('video_list_section_title') ): ?><?php echo get_field('video_list_section_title');?><?php endif; ?></div>
<div class="row post-landing-list">
<?php while( have_rows('video_list') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="col-sm-6 col-md-6 col-lg-4 mt-4">
<a href="<?php the_sub_field('link'); ?>" class="d-block">
<div class="post-box">
<div class="embed-responsive embed-responsive-16by9"><div class="full-img nobg"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div></div>
<div class="content">
<div class="pb-0 coleql_height">
<h5><?php the_sub_field('title'); ?></h5>
</div>
</div>
</div>
</a>
</div>
<?php endwhile; ?>
</div>
	</div>
	</div>
<?php endif; ?>

<?php if (get_field('cta_section_content')): ?>
    <a href="<?php echo get_field('cta_section_btn_link'); ?>">
        <div class="cta-bg-content section" style="background-image:url(<?php echo get_field('cta_section_bg'); ?>);">
            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-lg-8 col-md-9">
                        <?php echo get_field('cta_section_content'); ?>
                        <?php if (get_field('cta_section_btn_link')): ?><span class="btn btn-primary"><?php echo get_field('cta_section_btn_label'); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php
/*
 * Show Posts by Category
 */
$cat_ids = get_field('blog_cat', $page_id);
//print_r($cat_ids);
if (!empty($cat_ids)):
    $the_query = new WP_Query(array(
        'post_type' => 'post',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $cat_ids,
            )
        ),
        'posts_per_page' => 12,
        'orderby' => 'rand'
    ));
    //echo "Last SQL-Query: {$the_query->request}";
    if ($the_query->have_posts()):
        ?>
        <div class="section pro-w7 pb-0">
            <div class="container">
                <div class="text-center mb-5">
                    <?php echo get_field('blog_section_title'); ?>
                </div>
                <div class="owl-carousel owl-theme CarouselOwl-More">
                    <?php while ($the_query->have_posts()): $the_query->the_post(); ?>
                        <div class="item"><div class="content"><a href="<?php the_permalink(); ?>/" class="d-block"><div class="full-img"><?php the_post_thumbnail('full'); ?></div><div class="title coleql_height"><?php the_title(); ?></div></a></div></div>
                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php /*?><div class="footer-cta">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div><?php */?>


<?php get_footer(); ?>
