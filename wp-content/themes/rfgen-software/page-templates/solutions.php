<?php
/**
 * Template Name: Solutions Page Template
 */

get_header(); ?>
<?php if( get_field('float_btn_link') ): ?>
<a href="<?php echo get_field('float_btn_link');?>" class="floating-btn"><span class="media"><img src="<?php echo get_template_directory_uri(); ?>/images/wallet.svg" alt="wallet"><span class="media-body"><?php if (get_field('float_btn_label_top')): ?><?php echo get_field(' float_btn_label_top'); ?></small><?php endif; ?><?php echo get_field('float_btn_label');?></span></span></a>
<?php endif; ?>

<?php get_template_part( 'content', 'banner' ); ?>

<?php /*?><?php if( get_field('top_cta_section_content') ): ?>
<a href="<?php echo get_field('top_cta_section_btn_link');?>" class="d-block">
<div class="cta-bg-content section" style="background-image:url(<?php echo get_field('top_cta_section_bg');?>);">
<div class="container">
<div class="row justify-content-end">
<div class="col-lg-8 col-md-9">
<?php echo get_field('top_cta_section_content');?>
<?php if( get_field('top_cta_section_btn_link') ): ?><span class="btn btn-primary"><?php echo get_field('top_cta_section_label');?></span><?php endif; ?>
</div>
</div>
</div>
</div>
</a>
<?php endif; ?><?php */?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?>d-none<?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>

<div class="section pro-w1">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-12">
<?php
// Start the Loop.
while ( have_posts() ) :
the_post();
get_template_part( 'template-parts/content/content', 'page' );
endwhile; // End the loop.
?>
</div>
</div>
</div>
</div>


<div class="<?php if( get_field('show_benefit_section') == 'Show' ): ?>d-block<?php endif; ?><?php if( get_field('show_benefit_section') == 'Hide' ): ?>d-none<?php endif; ?>">
<?php
$benefits = new WP_Query(array('post_type' => 'benefits', 'posts_per_page' => '400'));
if ($benefits->have_posts()):
?>
<div class="section sol-w2 gradient-back">
<div class="container">
<div class="text-center mb-4"><?php echo get_field('benefit_section_title');?></div>
<div class="row">
<?php
while ($benefits->have_posts()): $benefits->the_post();
?>
<div class="col-md-6 col-lg-3 mt-4">
<div class="box all-box">
<div class="media"><div class="icon mr-3 cobalt"><?php the_post_thumbnail( 'full' ); ?></div><div class="media-body"><h5><?php echo get_field('numbers');?><?php echo get_field('unit');?><span><?php the_title();?></span></h5></div></div>
<div class="content coleql_height">
<?php the_content();?>
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
<?php endif; ?>
</div>


<?php if( get_field('first_cta_section_content') ): ?>
<div class="cta-grad cta-grad-dark">
<div class="container">
<div class="row">
<div class="col-md-8">
<?php echo get_field('first_cta_section_content');?>
</div>
<?php if( get_field('first_cta_section_btn_link') ): ?><div class="col-md-4 text-center align-self-center"><a href="<?php echo get_field('first_cta_section_btn_link');?>" class="btn btn-primary"><?php echo get_field('first_cta_section_btn_label');?></a></div><?php endif; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('chequred_content') ): ?>
<div class="section gradient-back chequred-content">
<div class="container">
<?php while( have_rows('chequred_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="row">
<div class="col-lg-6 text-center align-self-center"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
<div class="col-lg-6  align-self-center <?php echo (!empty($image)) ? 'col-lg-6' : 'col-lg-12'; ?>">
<?php the_sub_field('content'); ?>
<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
<?php endif; ?>


<?php if( get_field('second_cta_section_content') ): ?>
<div class="cta-grad cta-grad-dark">
<div class="container">
<div class="row">
<div class="col-md-8">
<?php echo get_field('second_cta_section_content');?>
</div>
<?php if( get_field('second_cta_section_btn_link') ): ?><div class="col-md-4 text-center align-self-center"><a href="<?php echo get_field('second_cta_section_btn_link');?>" class="btn btn-primary"><?php echo get_field('second_cta_section_btn_label');?></a></div><?php endif; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('box_content') ): ?>
<div class="section sol-w3">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10 mb-4 text-center">
<?php echo get_field('box_section_content');?>
</div>
</div>
<div class="row justify-content-center">
<?php while( have_rows('box_content') ): the_row(); 
$image = get_sub_field('icon');
$icon_check = get_sub_field('icon_check');
?>
<div class="col-lg-3 col-md-6 col-sm-6 mt-4">
<div  class="box all-box hyp-ct">
<?php if( get_sub_field('icon') ): ?>
<div class="icon cobalt">
<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
</div>
<?php endif; ?>
<div class="content coleql_height">
<?php the_sub_field('content'); ?>
</div>
<?php if(!empty(get_sub_field('btn_link'))){ ?>
<a href="<?php the_sub_field('btn_link'); ?>" class="link"><?php the_sub_field('btn_label'); ?><i class="fas fa-arrow-right ml-2"></i></a>
<?php } ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( get_field('third_cta_section_content') ): ?>
<a href="<?php echo get_field('third_cta_section_btn_link');?>" class="d-block">
<div class="cta-bg-content section" style="background-image:url(<?php echo get_field('third_cta_section_bg');?>);">
<div class="container">
<div class="row justify-content-end">
<div class="col-lg-8 col-md-9">
<?php echo get_field('third_cta_section_content');?>
<?php if( get_field('third_cta_section_btn_link') ): ?><span class="btn btn-primary"><?php echo get_field('third_cta_section_btn_label');?></span><?php endif; ?>
</div>
</div>
</div>
</div>
</a>
<?php endif; ?>



<?php if( have_rows('testimonials') ): ?>
<div class="page-testimonial section" style="background-image:url(<?php echo get_field('testimonial_section_bg');?>);">
<div class="container">
<div class="testi-carousel">
<div class="owl-carousel owl-theme CarouselOwl-1">
<?php while( have_rows('testimonials') ): the_row(); ?>
<div class="item">
<div class="media">
<img src="<?php echo get_template_directory_uri(); ?>/images/quote.png" alt="quote" class="mr-3">
<div class="media-body">
<blockquote><?php the_sub_field('quote'); ?></blockquote>
<cite>
<strong><?php the_sub_field('client'); ?></strong>
<?php the_sub_field('copmany'); ?>
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



<?php if (have_rows('scroll_section_item')): ?>
    <div class="section pro-scroll-section">
        <div class="container">
            <?php if (get_field('scroll_section_title')): ?><div class="mb-5"><?php echo get_field('scroll_section_title'); ?></div><?php endif; ?>
            <div class="owl-carousel owl-theme CarouselOwl-full">
                <?php
                while (have_rows('scroll_section_item')): the_row();
                    $image = get_sub_field('image');
                    ?>
                    <div class="item">
                        <div class="row">
                            <div class="col-lg-6 text-center"><?php echo wp_get_attachment_image($image, 'full'); ?></div>
                            <div class="col-lg-6">
                                <?php the_sub_field('content'); ?>
                                <?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
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
                            <div class="col-lg-6 full-img"><?php echo wp_get_attachment_image($image, 'full'); ?></div>
                            <div class="col-lg-6">
                                <?php the_sub_field('content'); ?>
                                <?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="btn btn-primary"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if( have_rows('bottom_box_content') ): ?>
<div class="section pro-w3">
<div class="container">
<div class="row justify-content-center text-center">
<div class="col-lg-10 mb-5">
<?php echo get_field('bottom_section_content');?>
</div>
</div>
<div class="icon-section">
<?php
// loop through the rows of data
// add a counter
$count = 0;
$group = 0;
while ( have_rows('bottom_box_content') ) : the_row(); 
// vars
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
</div><!-- #teachers -->
<?php 
}
$count++;
endwhile;
?>
</div>
</div>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('faqs') ): ?>
<div class="section gradient-back chequred-content c-accordion">
<div class="container">
<?php echo get_field('core_section_content'); ?>
<div class="custom-accordion accordionjs">
<?php while( have_rows('faqs') ): the_row(); ?>
<div class="csa">
<div><?php the_sub_field('title'); ?></div>
<div class="tab-container">
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('resources') ): ?>
<div class="section gradient-back">
<div class="container">
<div class="mb-4"><?php echo get_field('resources_section_title')?></div>
<div class="row post-landing-list">
<?php while( have_rows('resources') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="col-sm-6 col-md-6 col-lg-4 mt-4 mb-0">
<a href="<?php the_sub_field('link'); ?>" class="d-block mb-0">
<div class="post-box">
<div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div></div>
<div class="content">
<div class="coleql_height">
<h4><?php the_sub_field('title'); ?></h4>
<?php the_sub_field('content'); ?>
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


<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
