<!-- Start: v4 -->
<?php
	$menus = $this->get_menus(1);
  if(empty($menus)) return 0;

  $menu_on_top = ($this->GetOption('menu_on_top')) ? "d-md-none" : "";
?>
<div class="sidebar">
  <div class="ap-side">
    <a href="#" class="wlm-brand d-none d-sm-block <?php echo $menu_on_top; ?>" id="wlm3-hamburger-toggle">
      <img src="<?php echo $this->pluginURL3; ?>/ui/images/WishListMember-logomark-16px-wp.svg" style="min-width: 24px">
      <span class="logo-text"><?php _e( 'WishList Member', 'wishlist-member' ); ?></span>
    </a>
    <ul id="wlm3-sidebar" class="nav nav-sidebar flex-column">
      <?php
        foreach($menus AS $menu) {
          if($menu['legacy']) continue;
          $icon = sprintf('<i class="wlm-icons pull-left">%s</i>', empty($menu['icon']) ? 'settings' : $menu['icon']);
          $link = $this->get_menu_link($menu['key'], 1);
          $active = $this->is_menu_active($link, 1) ? ' active' : '';
          $devonly = $GLOBALS['wlm_globalrev'] ? '' : 'd-none';

          printf('<li class="item nav-item %s"><a data-title="%s" class="%s nav-link" href="%s">%s<span>%s</span></a></li>', (empty($menu['devonly']) ? '' : $devonly), $this->format_title($menu['title']), $active, $link, $icon, $menu['name']);
        }
      ?>
      <li class="item nav-item toggle-sidebar">
        <a class="nav-link" href="">
          <i class="wlm-icons md-26 left">keyboard_arrow_left</i>
          <i class="wlm-icons md-26 right">keyboard_arrow_right</i>
          <span><?php _e( 'Collapse menu', 'wishlist-member' ); ?></span>
        </a>
      </li>
    </ul>    
  </div>
</div>
<?php return count($menus); ?>

