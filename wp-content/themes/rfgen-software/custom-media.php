<?php
/**
 * The template for displaying all single media
 */
get_header();

global $post;
$sub_cat = get_term_by('slug', get_query_var( 'cat_b' ), 'media_category');
$post_parent = 0;
if(isset($sub_cat) || empty($sub_cat)){
	$arg_s = array('name' => get_query_var( 'cat_b' ), 'post_type' => 'media', 'post_status' => 'publish', 'numberposts' => 1);
	$top_posts = get_posts($arg_s);
	$args = array();
	if(isset($top_posts[0]->ID) && $top_posts[0]->ID > 0){
		$post_parent = $top_posts[0]->ID;
	}
}
$arg_s = array('name' => get_query_var( 'post_slug' ), 'post_type' => 'media', 'post_parent' => $post_parent, 'post_status' => 'publish', 'numberposts' => 1);
$my_posts = get_posts($arg_s);
$args = array();
if(isset($my_posts[0]->ID) && $my_posts[0]->ID > 0){
	echo '<input type="hidden" value="'.$my_posts[0]->ID.'" id="edit_it" />';
	$post = get_post( $my_posts[0]->ID, OBJECT );
	setup_postdata( $post );
	$args['post_id'] = $my_posts[0]->ID;
}

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

<?php get_template_part('content', 'mr', $args); ?>

<?php get_template_part('media/content', $template, $args); ?>

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
