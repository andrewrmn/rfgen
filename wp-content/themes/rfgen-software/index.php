<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */
get_header();
?>
<?php get_template_part('content', 'banner'); ?>

<?php /*?><div class="top-cta">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div><?php */?>

<section id="primary" class="content-area section">
    <main id="main" class="site-main container">

        <?php get_template_part('content', 'blogfilter'); ?>

        <?php
		$_none = array(array('key' => 'hide_form_list', 'value' => true, 'compare' => '!='));
        $blogs = new WP_Query(array('post_type' => 'post', 'meta_query' => $_none, 'posts_per_page' => 9, 'orderby' => 'publish_date', 'order' => 'DESC'));
        if ($blogs->have_posts()):
            ?>
			<p class="hide_msg" style="display: none">Sorry, we couldn't find what you were looking for, you may find one of these articles pertinent.</p>
            <ul class="row list" id="blogList" data-off="<?php echo count($blogs->posts); ?>">
                <?php
                while ($blogs->have_posts()) : $blogs->the_post();
                    get_template_part('content', 'blogs');
                endwhile;
                wp_reset_query();
                ?>
            </ul>
            <div class="text-center"><button  type="button" id="btn" class="btn btn-primary">Load More</button></div>
        <?php endif; ?>
    </main>
</section>

<?php /*?><div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div><?php */?>

<?php get_footer(); ?>

<script>
    jQuery('#blogFilter select, #blogFilter input[name="search_key"]').on('change keyup', function () {
		jQuery('#btn').show();
        jQuery('#blogList').waitMe({
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

        var form = jQuery('#blogFilter')[0];
        var formData = new FormData(form);

        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            processData: false,
            contentType: false,
			dataType: 'json',
            data: formData,
            success: function (res) {
                jQuery('#blogList').html(res.blogs);
				jQuery('#blogList').attr('data-off', res.rows);
                jQuery('#blogList').waitMe("hide");
				if(res.tops == 1){
					jQuery(".hide_msg").show();
				}
				else{
					jQuery(".hide_msg").hide();
				}
				if(parseInt(res.rows) == 0){
					jQuery('#btn').hide();
				}
            }
        });
    });
	jQuery('#btn').click(function(){
		var obj = jQuery(this);
		var form = jQuery('#blogFilter')[0];
        var formData = new FormData(form);
		var offset = jQuery('#blogList').attr('data-off');
		var exlude = [];
		jQuery('#blogList > li').each(function(){
			exlude.push(jQuery(this).attr('data-post'));
		});
		formData.append('exlude', exlude);
		formData.append('offset', offset);
		formData.append('load', 1);
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            processData: false,
            contentType: false,
			dataType: 'json',
            data: formData,
			beforeSend: function() {
				$(obj).text('Please wait...');
				$(obj).attr('disabled', true);
			},
            success: function (res) {
                jQuery('#blogList').append(res.blogs);
				jQuery('#blogList').attr('data-off', parseInt(offset) + parseInt(res.rows));
				if(parseInt(res.rows) == 0){
					$(obj).hide();
				}
				if(res.tops == 1){
					jQuery(".hide_msg").show();
				}
				else{
					jQuery(".hide_msg").hide();
				}
            },
			complete: function() {
				$(obj).text('Load More');
				$(obj).removeAttr('disabled');
			}
        });
	});
</script>
