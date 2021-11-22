<?php
/**
 * Template Name: Products Download Page Template
 */
get_header();
?>
<?php get_template_part('content', 'banner'); ?>

<?php
$selvers = get_the_terms(get_the_ID(), 'versions');
foreach ($selvers as $selver):
    if ($selver->parent == 0)
        $parent = $selver->term_id;
endforeach;

$versions = new WP_Query(array(
    'post_type' => 'product_download',
    'tax_query' => array(
        array(
            'taxonomy' => 'versions',
            'field' => 'term_id',
            'terms' => $parent
        )
    )
        ));
?>

<div class="section">
    <div class="container">
        <div class="row" id="proDown">

            <div class="col-lg-6">
                <div class="download-box">
                    <div class="head">
                        <div class="row">
                            <div class="col-7 align-self-center relDate">
                                <?php
                                while ($versions->have_posts()): $versions->the_post();
                                    if ($i == 0):
                                        ?>
                                        <h4><?php the_field('release_label'); ?></h4>
                                        <p>Release Date: <?php the_field('release_date'); ?></p>
                                        <?php
                                    endif;
                                    $i++;
                                endwhile;
                                wp_reset_query();
                                ?>
                            </div>
                            <div class="col-5 align-self-center">
                                <select class="custom-select" onchange="getversion(this)" id="se11">
                                    <?php while ($versions->have_posts()): $versions->the_post(); ?>
                                        <option value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
                                        <?php
                                    endwhile;
                                    wp_reset_query();
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="content coleql_height" id="relFeature">
                        <?php
                        while ($versions->have_posts()): $versions->the_post();
                            if ($j == 0):
                                while (have_rows('product_features')): the_row();
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-6 col-12 align-self-center"><div class="title"><span class="icon"><?php the_sub_field('feature_icon'); ?></span><?php the_sub_field('feature_label'); ?></div></div>
                                        <?php
                                        $count = count(get_sub_field('feature_link'));
                                        while (have_rows('feature_link')): the_row();
                                            ?>
                                            <div class="<?php echo ($count == 1) ? 'col-sm-6 col-12' : 'col-sm-3 col-6'; ?> align-self-center"><a class="btn btn-link btn-block" href="<?php the_sub_field('button_link'); ?>"><?php the_sub_field('button_label'); ?></a></div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php
                                endwhile;
                            endif;
                            $j++;
                        endwhile;
                        wp_reset_query();
                        ?>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="download-box">
                    <div class="head">
                        <div class="row">
                            <div class="col-7 align-self-center relDate">
                                <?php
                                while ($versions->have_posts()): $versions->the_post();
                                    if ($k == 0):
                                        ?>
                                        <h4><?php the_field('release_label'); ?></h4>
                                        <p>Release Date: <?php the_field('release_date'); ?></p>
                                        <?php
                                    endif;
                                    $k++;
                                endwhile;
                                wp_reset_query();
                                ?>
                            </div>
                            <div class="col-5 align-self-center">
                                <select class="custom-select" onchange="getversion(this)" id="sel2">
                                    <?php while ($versions->have_posts()): $versions->the_post(); ?>
                                        <option value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
                                        <?php
                                    endwhile;
                                    wp_reset_query();
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="content coleql_height" id="relNote">
                        <?php
                        while ($versions->have_posts()): $versions->the_post();
                            if ($l == 0):
                                the_field('release_notes');
                            endif;
                            $l++;
                        endwhile;
                        wp_reset_query();
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="footer-cta">
    <div class="container">
        <?php get_template_part('content', 'ctabottom'); ?>
    </div>
</div>


<?php get_footer(); ?>

<script>
    function getversion(sel) {
        //console.log(sel.value);
        jQuery('#sel1 option[value=' + sel.value + ']').prop('selected', true);
        jQuery('#sel2 option[value=' + sel.value + ']').prop('selected', true);

        jQuery('#proDown').waitMe({
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
                action: 'filter_version',
                post_id: sel.value,
            },
            success: function (res) {
                //console.log(res);
                jQuery('.relDate').html(res.reldate);
                jQuery('#relFeature').html(res.relfeature);
                jQuery('#relNote').html(res.relnote);
                jQuery('#proDown').waitMe("hide");
            }
        });
    }
</script>