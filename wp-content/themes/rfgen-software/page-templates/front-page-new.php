<?php
/**
 * Template Name: Front Page Template New
 */
get_header();
?>

<?php if (get_field('float_btn_link')): ?><a href="<?php echo get_field('float_btn_link'); ?>" class="btn btn-light float-btn"><?php echo get_field('float_btn_label'); ?></a><?php endif; ?>

<div class="banner row no-gutters">

<?php if (have_rows('slider', get_the_ID())): ?>
<div class="col-lg-8 col-xl-7">
<div class="banner">
<div id="carouselSlider" class="carousel slide" data-ride="carousel">
<div class="carousel-inner">
<?php
$irr = 1;
while (have_rows('slider', get_the_ID())): the_row();
?>
<div class="carousel-item <?php echo ($irr == 1) ? 'active' : ''; ?>">
<div class="carousel-caption">
<div class="row justify-content-end">
<div class="col-lg-6">
<?php echo get_sub_field('content') ?>
<?php if (get_sub_field('btn_link')): ?><a href="<?php echo get_sub_field('btn_link') ?>" class="btn btn-primary"><?php echo get_sub_field('btn_label') ?></a><?php endif; ?>
</div></div>
</div>
<div class="d-none d-md-block"><div class="banner-image"><?php echo wp_get_attachment_image(get_sub_field('image'), 'full'); ?></div></div>
<div class="d-block d-md-none"><div class="banner-image"><?php echo wp_get_attachment_image(get_sub_field('mobile_image'), 'full'); ?></div></div>
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
</div>
<?php endif; ?>

<?php if (have_rows('erps')): ?>
<div class="col-lg-4 col-xl-5 align-self-center">
<div class="banner-erp home-w1">
<div class="title"><?php echo get_field('erp_section_title'); ?></div>
<div class="row">
<?php
while (have_rows('erps')): the_row();
$image = get_sub_field('image');
?>
<div class="col-6 col-sm-6 col-md-4 col-lg-4 mt-4"><a href="<?php the_sub_field('link'); ?>" class="d-block box-content white-box-style coleql_height"><?php echo wp_get_attachment_image($image, 'full'); ?><h6><?php the_sub_field('title'); ?></h6></a></div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


</div>


<?php if( have_rows('counter_content') ): ?>
<div class="cta-grad cta-grad-dark home-top-cta">
<div class="container">
<div class="text-center"><?php echo get_field('counter_section_title');?></div>
<div class="row">
<?php while( have_rows('counter_content') ): the_row(); ?>
<div class="pt-4 col-md-4"><h6><?php the_sub_field('title'); ?></h6><h2><span class="counter"><?php the_sub_field('number'); ?></span><?php the_sub_field('unit'); ?></h2></div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('chequred_section_content') ): ?>
<div class="section">
<div class="container">
<div class="mb-5">
<?php echo get_field('chequred_section_intro_content');?>
</div>
<div class="chequred-content home-chequred">
<?php while( have_rows('chequred_section_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="row">
<div class="col-lg-6 full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
<div class="col-lg-6 align-self-center">
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('industries') ): ?>
<div class="section gradient-back iwt">
<div class="container">
<div class="mb-2"><?php echo get_field('industries_title');?></div>
<div class="row justify-content-center">
<?php while( have_rows('industries') ): the_row(); 
$image = get_sub_field('image');
$imageh = get_sub_field('image_hover');
$icon = get_sub_field('icon_type');
?>
<div class="col-lg-2 col-md-4 col-sm-4 col-6 mt-4">
<a class="d-block" href="<?php the_sub_field('link'); ?>">
<div class="icon">
<?php if($icon == 'Image'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php echo wp_get_attachment_image( $imageh, 'full' ); ?>
<?php endif; ?>
<?php if($icon == 'Font'): ?>
<?php the_sub_field('icon'); ?>
<?php endif; ?>
</div>
<?php the_sub_field('title'); ?>
</a>		
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<div class="trusted">
    <div class="container">
        <div class="mb-4 d-none d-lg-block"><?php echo get_field('trusted_section_title'); ?></div>
        <?php
        $trusteddesk = new WP_Query(array('post_type' => 'trusted', 'posts_per_page' => '400'));
        if ($trusteddesk->have_posts()):
            ?>
            <div class="owl-carousel owl-theme CarouselOwl d-none d-lg-block">
                <?php
                while ($trusteddesk->have_posts()): $trusteddesk->the_post();
                    ?>
                    <div class="item"><?php the_post_thumbnail('full'); ?></div>
                    <?php
                endwhile;
                wp_reset_query();
                ?>
            </div>
        <?php endif; ?>

        <?php
        $trusted = new WP_Query(array('post_type' => 'trusted', 'posts_per_page' => '4'));
        if ($trusted->have_posts()):
            ?>
            <div class="d-block d-lg-none">
                <div class="row text-center">
                    <?php
                    while ($trusted->have_posts()): $trusted->the_post();
                        ?>
                        <div class="col-3"><?php the_post_thumbnail('full'); ?> </div>
                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if( have_rows('image_block_content') ): ?>
<div class="section home-block-img">
<div class="container">
<div class="mb-4">
<?php echo get_field('image_block_intro_content');?>
</div>
<div class="row">
<?php while( have_rows('image_block_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="col-md-4 mt-4">
<div class="box-content-home">
<a href="<?php the_sub_field('link'); ?>"><div class="embed-responsive embed-responsive-4by3"><div class="full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div></div></a>
<div class="content">
<div class="pb-3 coleql_height">
<?php the_sub_field('content'); ?>
</div>
<?php if( get_sub_field('link') ): ?><a href="<?php the_sub_field('link'); ?>"><?php the_sub_field('link_label'); ?><i class="fas fa-arrow-right ml-2"></i></a><?php endif; ?>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('values') ): ?>
<div class="section gradient-back icv">
<div class="container">
<div class="mb-2 text-center mb-4"><?php echo get_field('values_title');?></div>
<div class="row">
<?php while( have_rows('values') ): the_row(); 
$image = get_sub_field('image');
$imageh = get_sub_field('image_hover');
$icon = get_sub_field('icon_type');
?>
<div class="col-lg-2 col-md-4 col-sm-4 col-6 mt-4">
<div class="box all-box coleql_height">
<div class="icon">
<?php if($icon == 'Image'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php echo wp_get_attachment_image( $imageh, 'full' ); ?>
<?php endif; ?>
<?php if($icon == 'Font'): ?>
<?php the_sub_field('icon'); ?>
<?php endif; ?>
<span style="background:<?php the_sub_field('backgound_color'); ?>;"><span style="background:<?php the_sub_field('foreground_color'); ?>;" class="inner"></span></span>
</div>
<?php the_sub_field('title'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php /*?><div class="section bg-cover home-w2" style="background-image:url(<?php echo get_field('full_width_section_bg'); ?>);">
<div class="container">
<?php echo get_field('full_width_section_content'); ?>
</div>
</div><?php */?>

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
<div class="section home-w3">
<div class="container">
<div class="success-intro row">
<div class="col-lg-8 col-md-9 mb-4">
<?php echo get_field('success_stories_intro_content'); ?>
</div>
<?php if (get_field('all_story_btn_link')): ?><div class="col-lg-4 col-md-3 align-self-center mb-4"><a href="<?php echo get_field('all_story_btn_link'); ?>" class="btn btn-primary"><?php echo get_field('all_story_btn_label'); ?></a></div><?php endif; ?>
</div>

<div class="success-story">
<?php while ($casestudies->have_posts()): $casestudies->the_post(); ?>
<div class="story-list">
<div class="row no-gutters">
<div class="col-md-4">
<a href="<?php the_permalink(); ?>"><div class="image"><?php 
$image = get_field('company_logo');
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?></div></a></div>
<div class="col-md-8 ml-auto align-self-center">
<h5><?php the_title(); ?></h5>
<?php //echo get_field('intro_content'); ?>
<?php echo get_field('page_banner_content'); ?>	
<a href="<?php the_permalink(); ?>" class="link">Learn More<i class="fas fa-arrow-right ml-2"></i></a>
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
<?php endif; ?>

<?php if( get_field('bottom_cta_content') ): ?>
<div class="cta-bg-content cta-bg-content-home section" style="background-image:url(<?php echo get_field('bottom_cta_section_bg');?>);">
<div class="container">
<div class="row">
<div class="col-lg-8 col-md-9">
<?php echo get_field('bottom_cta_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>


<?php /*?><div class="home-w4 row no-gutters">
<div class="col-lg-6"><div class="embed-responsive embed-responsive-16by9">
<?php echo get_field('bottom_section_video'); ?>
</div></div>
<div class="col-lg-6 align-self-center">
<div class="content">
<?php echo get_field('bottom_section_content'); ?>
</div>
</div>
</div><?php */?>


<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
