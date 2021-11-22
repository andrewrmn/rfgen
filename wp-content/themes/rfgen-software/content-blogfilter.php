<?php
/**
 * The default template for displaying content filter
 */
$parents = array(2, 3);
$taxonomy = 'category';

$obj_id = get_queried_object_id();
$current_url = get_permalink($obj_id);
?>

<form method="post" id="blogFilter">
    <input type="hidden" name="action" value="filter_blog" />
    <div class="row mb-4 blog-sorting">
        <div class="col-12 col-sm-12 col-md-12 col-lg-3 mb-3 align-self-center"><p> Filter by Category </p></div>
        <?php
        foreach ($parents as $parent):
            $parenttax = get_term_by('id', $parent, $taxonomy);
            $terms = get_terms(array('taxonomy' => $taxonomy, 'parent' => $parent, 'hide_empty' => true));
            ?>
            <div class="col-4 col-sm-4 col-md-4 col-lg-3 mb-3">
                <select name="term_id[]" class="form-control filter-control">
                    <option value=""><?php echo $parenttax->name; ?></option>
                    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo $term->term_id; ?>">- <?php echo $term->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>
        <div class="col-4 col-sm-4 col-md-4 col-lg-3 mb-3">
            <input type="text" class="form-control" placeholder="Search" name="search_key" value="<?php echo get_search_query(); ?>"/>
            <input type="submit" value="Search" disabled>
        </div>
    </div>
</form>
