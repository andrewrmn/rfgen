<?php
/**
 * The template for displaying all single media
 */
get_header();

$taxonomy = 'media_category';
$terms = wp_get_post_terms(get_the_ID(), $taxonomy);
$parents = array();
foreach ($terms as $term):
    if ($term->parent == 0)
        $parents[] = $term->term_id;
endforeach;

$parent = get_term_by('id', $parents[0], $taxonomy);
$template = get_field('templates', $parent);
?>

<?php get_template_part('content', 'banner'); ?>


<?php get_template_part('media/content', $template); ?>

<!--<div class="footer-cta">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>-->


<?php get_footer(); ?>

<?php
$count = count(get_field('right_section_content'));
if ($count % 2 != 0) {
?>
<script>
var set = jQuery('#rhtCont div.case-widget-col');
var length = set.length;
set.each(function(index, element) {
	if (index === (length - 1)) {
		jQuery(this).removeClass('col-lg-6 col-md-6');
		jQuery(this).addClass('col-lg-12 col-md-12');
	}
});
</script>
<?php
}

?>
