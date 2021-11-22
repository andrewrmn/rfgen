<?php
/**
* The default template for displaying content Cta
*/
?>

<?php 
$is_cta = get_field('select_cta', $args['post_id']);
if( is_numeric($is_cta) ){ $ct_post = get_field('select_cta', $args['post_id']); ?>
<div class="cta-block" style="background-image:url(<?php echo get_field('cta_bg', $ct_post);?>)">
	<a href="<?php echo get_field('cta_link', $ct_post);?>" id="<?php echo get_field('cta_button_id', $ct_post);?>" class="d-block">
		<div class="row">
			<div class="col-md-8 col-lg-9 align-self-center">
				<div class="media">
					<?php 
						$image = get_field('cta_image', $ct_post);
						if( !empty( $image ) ): ?>
					<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
					<?php endif; ?>
					<div class="media-body">
						<?php echo get_field('cta_content', $ct_post); ?>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-lg-3 align-self-center text-center"><span class="btn btn-light"><?php echo get_field('cta_btn_label', $ct_post);?></span></div>
		</div>
	</a>
</div>
<?php } elseif( $is_cta != 'hide' && !empty(get_field('cta_link',3471)) ) { ?>
<div class="cta-block" style="background-image:url(<?php echo get_field('cta_bg', 3471);?>)">
	<a href="<?php echo get_field('cta_link',3471);?>" id="<?php echo get_field('cta_button_id', 3471);?>" class="d-block">
		<div class="row">
			<div class="col-md-8 col-lg-9 align-self-center">
				<div class="media">
					<?php 
						$image = get_field('cta_image',3471);
						if( !empty( $image ) ): ?>
					<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
					<?php endif; ?>
					<div class="media-body">
						<?php echo get_field('cta_content',3471);?>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-lg-3 align-self-center text-center"><span class="btn btn-light"><?php echo get_field('cta_btn_label',3471);?></span></div>
		</div>
	</a>
</div>
<?php } ?>