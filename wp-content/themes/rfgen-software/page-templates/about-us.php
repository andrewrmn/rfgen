<?php
/**
 * Template Name: About Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'cta' ); ?>
</div>
</div>

<?php if( get_field('first_section_content') ): ?>
<div class="cta-bg-content section" style="background-image:url(<?php echo get_field('first_section_bg');?>);">
<div class="container">
<div class="row justify-content-end">
<div class="col-lg-8 col-md-9">
<?php echo get_field('first_section_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if( get_field('second_section_content') ): ?>
<div class="section bg-cover" style="background-image:url(<?php echo get_field('second_section_bg');?>)">
<div class="container">
<div class="row">
<div class="col-lg-12">
<?php echo get_field('second_section_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if( get_field('third_section_content') ): ?>
<div class="section">
<div class="container">
<div class="row row-content">
<div class="col-lg-5 full-img">
<?php 
$image = get_field('third_section_image');
$size = 'full'; // (thumbnail, medium, large, full or custom size)
if( $image ) {
echo wp_get_attachment_image( $image, $size );
}?></div>
<div class="col-lg-7 align-self-center">
<?php echo get_field('third_section_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if( get_field('fourth_section_content') ): ?>
<div class="section bg-cover" style="background-image:url(<?php echo get_field('fourth_section_bg');?>)">
<div class="container">
<div class="row">
<div class="col-lg-6">
<?php echo get_field('fourth_section_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('fifth_section_box_content') ): ?>
<div class="section md-1">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-12 text-center mb-4">
<?php echo get_field('fifth_section_title');?>
</div>
</div>
<div class="row">
<?php while( have_rows('fifth_section_box_content') ): the_row(); 
$image = get_sub_field('icon');
$icon_check = get_sub_field('icon_check');
?>
<div class="col-md-6 col-lg-3 mt-4">
<div class="box-content all-box">
<div class="icon mb-3 cobalt">
<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
</div>
<div class="content">
<div class="pb-0 coleql_height">
<?php the_sub_field('content'); ?>
</div>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if( have_rows('sixth_section_box_content') ): ?>
<div class="section md-1 gradient-back">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-12 text-center mb-4">
<?php echo get_field('sixth_section_title');?>
</div>
</div>
<div class="row">
<?php while( have_rows('sixth_section_box_content') ): the_row(); 
$image = get_sub_field('icon');
$icon_check = get_sub_field('icon_check');
?>
<div class="col-md-6 col-lg-3 mt-4">
<div class="box-content all-box">
<div class="icon mb-3 cobalt">
<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
</div>
<div class="content">
<div class="pb-0 coleql_height">
<?php the_sub_field('content'); ?>
</div>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if( get_field('seventh_section_content') ): ?>
<div class="section">
<div class="container"><?php echo get_field('seventh_section_content');?></div>
</div>
<?php endif; ?>

<?php if( have_rows('eighth_section_content') ): ?>
<div class="section gradient-back">
<div class="container">
<div class="text-center mb-5"><?php echo get_field('eighth_section_title');?></div>
<div class="chequred-content">
<?php while( have_rows('eighth_section_content') ): the_row(); 
$image = get_sub_field('image');
?>
<div class="row">
<div class="col-lg-6 full-img"><?php echo wp_get_attachment_image( $image, 'full' ); ?></div>
<div class="col-lg-6  align-self-center <?php echo (!empty($image)) ? 'col-lg-6' : 'col-lg-12'; ?>">
<?php the_sub_field('content'); ?>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>

<?php if( get_field('nineth_section_content') ): ?>
<div class="cta-bg-content section" style="background-image:url(<?php echo get_field('nineth_section_bg');?>);">
<div class="container">
<div class="row justify-content-end">
<div class="col-lg-8 col-md-9">
<?php echo get_field('nineth_section_content');?>
</div>
</div>
</div>
</div>
<?php endif; ?>


<?php if( get_field('eleventh_section_content') ): ?>
<div class="section gradient-back">
<div class="container">
<?php echo get_field('eleventh_section_content');?>
</div>
</div>
<?php endif; ?>


<?php if( have_rows('twelveth_section_content') ): ?>
<div class="section">
<div class="container">
<div class="chequred-content">
<?php while( have_rows('twelveth_section_content') ): the_row(); 
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



<?php if( have_rows('thirteenth_section_box_content') ): ?>
<div class="section md-1 gradient-back">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-12 text-center mb-4">
<?php echo get_field('thirteenth_section_title');?>
</div>
</div>
<div class="row">
<?php while( have_rows('thirteenth_section_box_content') ): the_row(); 
$image = get_sub_field('icon');
$icon_check = get_sub_field('icon_check');
?>
<div class="col-md-6 col-lg-4 mt-4">
<div class="box-content all-box">
<div class="icon mb-3 cobalt">
<?php if($icon_check == 'Image Icon'): ?>
<?php echo wp_get_attachment_image( $image, 'full' ); ?>
<?php endif; ?>
<?php if($icon_check == 'Font Icon'): ?>
<?php the_sub_field('font_icon'); ?>
<?php endif; ?>
</div>
<div class="content">
<div class="pb-0 coleql_height">
<?php the_sub_field('content'); ?>
<?php if( get_sub_field('btn_link') ): ?><a href="<?php the_sub_field('btn_link'); ?>" class="link"><?php the_sub_field('btn_label'); ?></a><?php endif; ?>
</div>
</div>
</div>
</div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php endif; ?>


<?php if( get_field('tenth_section_content') ): ?>
<div class="cta-grad cta-grad-dark">
<div class="container">
<div class="row">
<div class="col-md-8">
<?php echo get_field('tenth_section_content');?>
</div>
<?php if( get_field('tenth_section_btn_link') ): ?><div class="col-md-4 text-center align-self-center"><a href="<?php echo get_field('tenth_section_btn_link');?>" class="btn btn-light"><?php echo get_field('tenth_section_btn_label');?></a></div><?php endif; ?>
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
