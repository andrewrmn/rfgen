<?php
/**
 * Template Name: Media Page Template
 */
get_header();
?>


<?php get_template_part('content', 'banner'); ?>

<div class="top-cta <?php $cta = get_field( 'select_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>


<div class="section resources-w1">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="page-widget">
                    <h5>Search Media</h5>
                    <div class="search-item-form">
                        <div class="header-search">
                            <form method="post" class="search-form" id="mediaSearch">
                                <input type="text" class="search-field form-control" placeholder="Search…" name="search_key" title="Search for:">
                                <input type="hidden" name="action" value="media_search" />
                                <input type="submit" value="Search">
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                /*
                 * Media Taxonomy
                 */
                $media_terms = get_terms(array('taxonomy' => 'media_category', 'parent' => 0, 'hide_empty' => true));
                if (!empty($media_terms)):
                    $termids = array();
                    foreach ($media_terms as $media_term):
                        $termids[] = $media_term->term_id;
                    endforeach;
                    ?>
                    <div class="page-widget">
                        <h5 class="d-none d-lg-block">Filter</h5>
                        <a class="btn btn-primary btn-block d-block d-lg-none  dropdown-toggle" data-toggle="collapse" href="#collapseLink" role="button" aria-expanded="false" aria-controls="collapseLink"><strong>Choose Filter</strong></a>
                        <div class="sidebar-links">
                            <div class="collapse" id="collapseLink">
                                <div class="card card-body">
                                    <ul>
                                        <li><a class="cat-list_item" href="javascript:void(0);" data-id="<?php echo implode(',', $termids); ?>">All</a></li>
                                        <?php foreach ($media_terms as $media_term): ?>
                                            <li><a class="cat-list_item" href="javascript:void(0);" data-id="<?php echo $media_term->term_id; ?>"><?php echo $media_term->name; ?></a></li>
                                        <?php endforeach; ?>
										<!--<li><a class="cat-list_item" href="<?php the_permalink('7096'); ?>" data-id="<?php echo $media_term->term_id; ?>">
Product Datasheets
</a></li>-->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <?php
			$media = new WP_Query(array('post_type' => 'media', 'posts_per_page' => 12, 'orderby' => 'publish_date', 'order' => 'DESC', 'meta_query' => array(
    array(
      'key' => 'show_in_front_page',
      'value' => '1',
      'compare' => '==' // not really needed, this is the default
    )
  )));
            if ($media->have_posts()):
                ?>
                <div class="col-lg-9">
					<ul class="row list" id="mediaList" data-off="<?php echo count($media->posts); ?>">
                        <?php
                        while ($media->have_posts()): $media->the_post();
                            get_template_part('media/content', 'mediatax');
                        endwhile;
                        wp_reset_query();
                        ?>
                    </ul>
                    <div class="text-center"><button  type="button" id="btn" class="btn btn-primary" style="display:block;">Load More</button></div>
                </div>
			<?php else: ?>
			<div class="col-lg-9"><p class="lead text-center mt-5">No record found!</p></div>
            <?php endif; ?>


        </div>
    </div>
</div>



<div class="footer-cta <?php $cta = get_field( 'bottom_cta' ); if( $cta == 'hide' ): ?> d-none <?php endif; ?>">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>


<?php get_footer(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.js"></script>
<script>
    jQuery("#mediaSearch").validate({
        rules: {
            search_key: {required: true},
        },
        messages: {
            search_key: {required: "Please enter search value."},
        },
        errorClass: 'text-danger',
        errorElement: 'em',
        highlight: function (element) {
            jQuery(element).closest('.form-control').addClass('is-invalid');
        },
        unhighlight: function (element) {
            jQuery(element).closest('.form-control').removeClass('is-invalid');
        },
        errorPlacement: function (error, element) {
            //element.after(error);
            jQuery('.header-search').after(error);
        },
        submitHandler: function (form) {

            jQuery('#mediaList').waitMe({
                effect: 'roundBounce',
                text: '',
                bg: 'rgba(255, 255, 255, 0.7)',
                color: '#000',
                maxSize: '',
                waitTime: '-1',
                textPos: 'vertical',
                fontSize: '',
                source: '',
                onClose: function () {}
            });

            var form1 = jQuery('#mediaSearch')[0];
            var formData = new FormData(form1);
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                processData: false,
                contentType: false,
				dataType: 'json',
                data: formData,
                success: function (res) {
                    jQuery('#mediaList').html(res.blogs);
					jQuery('#mediaList').attr('data-off', res.rows);
                    jQuery('#mediaList').waitMe("hide");
                }
            });
            return false;
        }
    });

    jQuery('.cat-list_item').on('click', function () {
        jQuery('.cat-list_item').removeClass('active');
        jQuery(this).addClass('active');
		jQuery('#btn').show();
        jQuery('#mediaList').waitMe({
            effect: 'roundBounce',
            text: '',
            bg: 'rgba(255, 255, 255, 0.7)',
            color: '#000',
            maxSize: '',
            waitTime: '-1',
            textPos: 'vertical',
            fontSize: '',
            source: '',
            onClose: function () {}
        });

        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: {
                action: 'filter_media',
                term_id: jQuery(this).data('id'),
            },
            success: function (res) {
				jQuery('#mediaList').html(res.blogs);
				jQuery('#mediaList').attr('data-off', res.rows);
                jQuery('#mediaList').waitMe("hide");
            }
        });
    });

    jQuery('#btn').click(function(){
		var obj = jQuery(this);
		var offset = jQuery('#mediaList').attr('data-off');
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
			dataType: 'json',
            data: {action: 'filter_media', term_id: jQuery('.cat-list_item.active').data('id'), offset: offset},
			beforeSend: function() {
				$(obj).text('Please wait...');
				$(obj).attr('disabled', true);
			},
            success: function (res) {
                jQuery('#mediaList').append(res.blogs);
				jQuery('#mediaList').attr('data-off', parseInt(offset) + parseInt(res.rows));
				if(parseInt(res.rows) == 0){
					$(obj).hide();
				}
            },
			complete: function() {
				$(obj).text('Load More');
				$(obj).removeAttr('disabled');
			}
        });
	});
</script>