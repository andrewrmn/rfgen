<?php
/*
 * Media Taxonomy Page
 */
get_header();

$curr_term = get_queried_object();

$medias = new WP_Query(array('post_type' => 'media', 'posts_per_page' => -1, 'tax_query' => array(
    array(
    'taxonomy' => 'media_category',
    'field' => 'term_id',
    'terms' => $curr_term->term_id
     )
  ),
		'meta_query' => array(
    array(
      'key' => 'show_in_front_page',
      'value' => '1',
      'compare' => '==' // not really needed, this is the default
    )
  )
							));
?>


<?php get_template_part('content', 'banner'); ?>
<!--<div class="top-cta">
    <div class="container">
        <?php get_template_part('content', 'cta'); ?>
    </div>
</div>-->

<div class="section resources-w1 pb-0">
    <div class="container">
        <div class="row post-landing-list">
            <?php while ($medias->have_posts()): $medias->the_post(); ?>
                <div class="col-sm-6 col-md-6 col-lg-4">
                    <div class="d-block">
                        <a class="post-box" style="color: #231f20" href="<?php the_permalink(); ?>">
							<div class="embed-responsive embed-responsive-4by3"><div class="full-img nobg"><?php the_post_thumbnail('full'); ?></div></div>
                            <div class="content">
                                <div class="pb-4 coleql_height">
                                    <h4><?php the_title(); ?></h4>
                                    <?php //the_excerpt(); ?>
									<?php echo get_field('page_banner_content'); ?>	
                                    <?php //echo get_the_term_list(get_the_ID(), 'media_tag', '<ul><li>', ',</li><li>', '</li></ul>'); ?>
                                    <?php
                                    $object_terms = wp_get_object_terms(get_the_ID(), 'media_tag', array('fields' => 'names'));
                                    if ($object_terms):
                                        ?><ul><li><?php echo implode('</li><li>', $object_terms); ?></li></ul><?php endif; ?>
                                        
                                        
         <?php /*?><ul>                               <?php
$currentcat = get_queried_object();
$currentcatID = $currentcat->term_id;
$terms = get_terms(array(
'taxonomy' => 'media_tag',
'hide_empty' => false,
));
?>
<?php foreach ($terms as $term): ?>
<li><a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_attr($term->name); ?></a></li>
<?php endforeach; ?>
</ul><?php */?>
                                        
                                </div>
                                <a href="<?php the_permalink(); ?>" class="link">Learn More<i class="fas fa-arrow-right ml-2"></i></a>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!--<div class="footer-cta">
<div class="container">
<?php get_template_part( 'content', 'ctabottom' ); ?>
</div>
</div>-->
<?php
get_footer();
