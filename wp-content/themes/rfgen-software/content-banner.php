<?php
/**
 * The default template for displaying content Banner
 */
?>

<?php

function cpotheme_page_title() {
    global $post;
    if (isset($post->ID)) {
        $current_id = $post->ID;
    } else {
        $current_id = false;
    }
    $title_tag = 'h1';
    echo '<' . $title_tag . '>';
    if (is_category() || is_tag() || is_tax()) {
        echo single_tag_title('', true);
    } elseif (is_author()) {
        the_author();
    } elseif (is_date()) {
        _e('Archive', 'brilliance');
    } elseif (is_404()) {
        echo __('Page Not Found', 'brilliance');
    } elseif (is_search()) {
        echo __('Search Results for', 'brilliance') . ' "' . get_search_query() . '"';
    } else {
        echo single_post_title('', false);
    }
    echo '</' . $title_tag . '>';
}
?>
<?php
global $wp_query;
if (is_category() || is_tag() || is_tax()) {
    $term = $wp_query->get_queried_object();
    $backimage = wp_get_attachment_image(get_field('page_banner_image', $term), 'full');
    $pagetile = get_field('page_title', $term);
    $pagecont = get_field('page_banner_content', $term);

    $modal_content = get_field('modal_content', $term);
    $white_btn_label = get_field('white_btn_label', $term);
    $red_btn_label = get_field('red_btn_label', $term);
	$white_btn_link = get_field('white_btn_link', $term);
	$red_btn_link = get_field('red_btn_link', $term);
	$white_check = get_field('white_check', $term);
	$red_check = get_field('red_check', $term);
    $red_btn_modal_content = get_field('red_btn_modal_content', $term);
    $banner_video_image = get_field('banner_video_image', $term);
    $banner_video = get_field('banner_video', $term);
    $modal_content = get_field('modal_content', $term);
    $modal_title = get_field('modal_title', $term);
    $red_btn_modal_content = get_field('red_btn_modal_content', $term);
    $red_btn_modal_title = get_field('red_btn_modal_title', $term);
} elseif (is_archive() || is_date()) {
    $pageid = get_option('page_for_posts');
    $backimage = wp_get_attachment_image(get_field('page_banner_image', $pageid), 'full');
    $pagetile = get_field('page_title', $pageid);
    $pagecont = get_field('page_banner_content', $pageid);

    $modal_content = get_field('modal_content', $pageid);
    $white_btn_label = get_field('white_btn_label', $pageid);
    $red_btn_label = get_field('red_btn_label', $pageid);
	$white_btn_link = get_field('white_btn_link', $pageid);
	$red_btn_link = get_field('red_btn_link', $pageid);
	$white_check = get_field('white_check', $pageid);
	$red_check = get_field('red_check', $pageid);
    $red_btn_modal_content = get_field('red_btn_modal_content', $pageid);
    $banner_video_image = get_field('banner_video_image', $pageid);
    $banner_video = get_field('banner_video', $pageid);
    $modal_content = get_field('modal_content', $pageid);
    $modal_title = get_field('modal_title', $pageid);
    $red_btn_modal_content = get_field('red_btn_modal_content', $pageid);
    $red_btn_modal_title = get_field('red_btn_modal_title', $pageid);
} else {
    if (is_home()) {
        $pageid = get_option('page_for_posts');
    } else {
        $pageid = get_the_ID();
    }
    $backimage = wp_get_attachment_image(get_field('page_banner_image', $pageid), 'full');
    $pagetile = get_field('page_title', $pageid);
    $pagecont = get_field('page_banner_content', $pageid);

    $modal_content = get_field('modal_content', $pageid);
    $white_btn_label = get_field('white_btn_label', $pageid);
    $red_btn_label = get_field('red_btn_label', $pageid);
	$white_btn_link = get_field('white_btn_link', $pageid);
	$red_btn_link = get_field('red_btn_link', $pageid);
	$white_check = get_field('white_check', $pageid);
	$red_check = get_field('red_check', $pageid);
    $red_btn_modal_content = get_field('red_btn_modal_content', $pageid);
    $banner_video_image = get_field('banner_video_image', $pageid);
    $banner_video = get_field('banner_video', $pageid);
    $modal_content = get_field('modal_content', $pageid);
    $modal_title = get_field('modal_title', $pageid);
    $red_btn_modal_content = get_field('red_btn_modal_content', $pageid);
    $red_btn_modal_title = get_field('red_btn_modal_title', $pageid);
}
?>

<div class="page-banner">
    <div class="container">
        <div class="row">
            <div class="<?php echo (!empty($banner_video_image)) ? 'col-lg-6' : 'col-lg-12'; ?> align-self-center">
                <?php
                if (empty($pagetile)) {
                    cpotheme_page_title();
                } else {
                    echo '<h1>' . $pagetile . '</h1>';
                }
                ?>
                <?php echo $pagecont; ?>
				<?php if($white_check == 'Modal'): ?>
					<?php if (!empty($modal_content)): ?><a href="#" data-toggle="modal" data-target="#ModalFrom" class="btn btn-light"><?php echo $white_btn_label; ?></a><?php endif; ?>
				<?php endif; ?>
				<?php if($red_check == 'Modal'): ?>
					<?php if (!empty($red_btn_modal_content)): ?><a  href="#" data-toggle="modal" data-target="#ModalFrom-2" class="btn btn-primary"><?php echo $red_btn_label; ?></a><?php endif; ?>
				<?php endif; ?>
				<?php if($white_check == 'Link'): ?>
					<?php if (!empty($white_btn_link)): ?><a href="<?php echo $white_btn_link; ?>" target="_blank" class="btn btn-light"><?php echo $white_btn_label; ?></a><?php endif; ?>
				<?php endif; ?>
				<?php if($red_check == 'Link'): ?>
					<?php if (!empty($red_btn_link)): ?><a  href="<?php echo $red_btn_link; ?>" target="_blank" class="btn btn-primary"><?php echo $red_btn_label; ?></a><?php endif; ?>
				<?php endif; ?>
				
                <?php /* ?><?php if( get_field('modal_content') ): ?><a href="#" class="btn btn-light" data-toggle="modal" data-target="#ModalFrom"><?php echo get_field('modal_btn_label');?></a><?php endif; ?>
                <?php if (!empty($modal_content)){ ?>
				<?php if ($pageid == 4345){ ?>
				<button type="button" class="btn btn-light to-quote"><?php echo $white_btn_label; ?></button>
				<?php } else { ?>
				<a href="#" data-toggle="modal" data-target="#ModalFrom" class="btn btn-light"><?php echo $white_btn_label; ?></a>
				<?php } } ?>
                <?php if (!empty($red_btn_modal_content)): ?><a  href="#" data-toggle="modal" data-target="#ModalFrom-2" class="btn btn-primary"><?php echo $red_btn_label; ?></a><?php endif; ?><?php */ ?>
            </div>
            <?php if (!empty($banner_video_image)): ?>
                <div class="col-lg-6">
                    <?php if (!empty($banner_video)): ?><a class="play-div d-block" data-fancybox href="<?php echo $banner_video; ?>"><?php endif; ?>
                        <?php if (!empty($banner_video)): ?><span class="play-btn"></span><?php endif; ?>
                        <div class="embed-responsive embed-responsive-16by9">
                            <div class="full-img">
                                <?php
                                $image = $banner_video_image;
                                $size = 'full'; // (thumbnail, medium, large, full or custom size)
                                if ($image) {
                                    echo wp_get_attachment_image($image, $size);
                                }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($banner_video)): ?></a><?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="page-banner-image">
        <?php echo $backimage; ?>
    </div>
</div>

<?php if (!empty($modal_content)): ?>
    <!-- Modal -->
    <div class="modal fade" id="ModalFrom" tabindex="-1" role="dialog" aria-labelledby="ModalFrom" aria-hidden="true">
        <div class="modal-dialog modal-form modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalFrom"><?php echo $modal_title; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $modal_content; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($red_btn_modal_content)): ?>
    <!-- Modal -->
    <div class="modal fade" id="ModalFrom-2" tabindex="-1" role="dialog" aria-labelledby="ModalFrom-2" aria-hidden="true">
        <div class="modal-dialog modal-form modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalFrom"><?php echo $red_btn_modal_title; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $red_btn_modal_content; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>