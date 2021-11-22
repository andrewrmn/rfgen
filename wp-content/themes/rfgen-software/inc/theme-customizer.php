<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Mega Menu custom Code
 */

function wp_custom_nav_menu($theme_location) {
    if (($theme_location) && ($locations = get_nav_menu_locations()) && isset($locations[$theme_location])) {

        $menu_list = '';

        $menu = get_term($locations[$theme_location], 'nav_menu');
        $menu_items = wp_get_nav_menu_items($menu->term_id);

        $menu_list .= '<ul class="menu">' . "\n";

        foreach ($menu_items as $menu_item) {
            if ($menu_item->menu_item_parent == 0) {

                $parent = $menu_item->ID;

                $menu_array = array();
                foreach ($menu_items as $submenu) {
                    if ($submenu->menu_item_parent == $parent) {
                        $bool = true;
                        if (in_array('mega-menu', $menu_item->classes)) {
                            $menu_array[] = '<li class="col-md-6">';
                            $menu_array[] = '<ul><li>';
                            $menu_array[] = '<a href="' . $submenu->url . '">' . $submenu->title . '</a>';
                            $menu_array[] = '<ul>';

                            foreach ($menu_items as $subbmenu) {
                                if ($subbmenu->menu_item_parent == $submenu->ID) {
                                    $menu_array[] = '<li class="' . implode(' ', $subbmenu->classes) . '"><a href="' . $subbmenu->url . '">' . $subbmenu->title . '</a></li>';
                                }
                            }

                            $menu_array[] = '</ul>';
                            $menu_array[] = '</li></ul>';
                            $menu_array[] = '</li>' . "\n";
                        } else {
                            $menu_array[] = '<li><a href="' . $submenu->url . '">' . $submenu->title . '</a></li>' . "\n";
                        }
                    }
                }
                if ($bool == true && count($menu_array) > 0) {

                    if (in_array('mega-menu', $menu_item->classes)) {

                        $menu_classes = implode(" ", $menu_item->classes);
                        $post_id = get_field('post_link', $menu_item);

                        $menu_list .= '<li>' . "\n";
                        $menu_list .= '<a href="' . $menu_item->url . '" class="parent">' . $menu_item->title . '</a>' . "\n";
                        $menu_list .= '<ul class="mega-menu"><li class="container">';
                        $menu_list .= '<ul class="row">' . "\n";
                        $menu_list .= '<li class="col-lg-5 col-xl-5">';
                        if (!empty($post_id)) {
                            $categories = get_the_category($post_id);
                            $category_list = join(', ', wp_list_pluck($categories, 'name'));
                            $menu_list .= '<ul class="block-menu">
                                                <div class="full-img mb-4">' . get_the_post_thumbnail($post_id, 'full') . '</div>
                                                <h6>' . wp_kses_post($category_list) . '</h6>
                                                <h5>' . get_the_title($post_id) . '</h5>
                                                <a href="' . get_the_permalink($post_id) . '">Read more</a>
                                            </ul>';
                        }
                        $menu_list .= '</li>' . "\n";
                        $menu_list .= '<li class="col-lg-7 col-xl-6 ml-auto"><ul class="row">' . "\n";
                        $menu_list .= implode("\n", $menu_array);
                        $menu_list .= '</ul></li>' . "\n";
                        $menu_list .= '</ul>' . "\n";
                        $menu_list .= '</li></ul>' . "\n";
                        $menu_list .= '</li>' . "\n";
                    } else {
                        $menu_list .= '<li class="dropmenu">' . "\n";
                        $menu_list .= '<a href="' . $menu_item->url . '" class="parent">' . $menu_item->title . '</a>' . "\n";

                        $menu_list .= '<ul>' . "\n";
                        $menu_list .= implode("\n", $menu_array);
                        $menu_list .= '</ul>' . "\n";
                        $menu_list .= '</li>' . "\n";
                    }
                } else {

                    $menu_list .= '<li>' . "\n";
                    $menu_list .= '<a href="' . $menu_item->url . '">' . $menu_item->title . '</a>' . "\n";
                    $menu_list .= '</li>' . "\n";
                }
            }
        }

        $menu_list .= '</ul>' . "\n";
        $menu_list .= '' . "\n";
    } else {
        $menu_list = '<!-- no menu defined in location "' . $theme_location . '" -->';
    }

    echo $menu_list;
}
