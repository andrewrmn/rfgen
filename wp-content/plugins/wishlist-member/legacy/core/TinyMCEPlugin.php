<?php

/**
 * WishList Member TinyMCE Plugin
 *
 * @author Fel Jun Palawan <feljunpalawan@gmail.com>
 */
if ( ! class_exists( 'WLMTinyMCEPluginOnly' ) ) {

	class WLMTinyMCEPluginOnly {

		public $codes             = array();
		public $use_legacy        = false;
		public $wlmtinymceplugin4 = null;
		public $role_access       = array();

		function __construct( $role_access = false ) {
			global $tinymce_version;
			$this->use_legacy  = version_compare( $tinymce_version, '400', '<' );
			$this->role_access = $role_access;
			if ( $this->use_legacy ) {
				add_action( 'init', array( &$this, 'TMCE_InsertButton' ) );
			} else {
				$this->wlmtinymceplugin4              = new WLMTinyMCEPluginOnly_4();
				$this->wlmtinymceplugin4->codes       = $this->codes;
				$this->wlmtinymceplugin4->role_access = $this->role_access;
			}
		}

		function TMCE_InsertButton() {
			if ( is_array( $this->role_access ) ) {
				$user   = wp_get_current_user();
				$access = array_intersect( $this->role_access, (array) $user->roles );
				if ( count( $access ) <= 0 ) {
					return false; // only roles with access can use this
				}
			}
			// for users who can edit only
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return false;
			}
			// for rich editing only
			if ( get_user_option( 'rich_editing' ) == 'true' && is_admin() ) {
				add_filter( 'mce_external_plugins', array( &$this, 'TNMCE_RegisterPlugin' ) );
				add_filter( 'tiny_mce_before_init', array( &$this, 'TNMCE_RegisterButton' ) );
			}

			if ( wlm_arrval( $_GET, 'page' ) != $WishListMemberInstance->MenuID ) {
				wp_enqueue_style( 'media-views' );
				wp_enqueue_style( 'wishlist_member_admin_chosen_css', $WishListMemberInstance->pluginURL . '/css/chosen.css', array(), $WishListMemberInstance->Version );
				wp_enqueue_script( 'wishlist_member_admin_chosen_js', $WishListMemberInstance->pluginURL . '/js/chosen.jquery.js', array( 'jquery' ), $WishListMemberInstance->Version );
				wp_enqueue_style( 'wishlist-member-tinymce-lightbox-css', $WishListMemberInstance->pluginURL . '/css/tinymce_lightbox.css', array(), $WishListMemberInstance->Version );
				wp_enqueue_script( 'wishlistmember-shortcoder-inserter-lightbox-js', $WishListMemberInstance->pluginURL . '/js/tinymce_lightbox.js', array( 'jquery' ), $WishListMemberInstance->Version );
				wp_localize_script( 'wishlistmember-shortcoder-inserter-lightbox-js', 'wlmtnmcelbox_vars', array() );
			}
		}

		function TNMCE_RegisterButton( $in ) {
			// where would you like to put the new dropdown?
			$advance_button_place = 1; // 1,2,3,4
			$key                  = 'theme_advanced_buttons' . $advance_button_place;
			$holder               = explode( ',', $in[ $key ] );
			$holder[]             = 'wlmonly_shortcodes'; // add our plugin on the menu
			$in[ $key ]           = implode( ',', $holder );
			return $in;
		}

		function TNMCE_RegisterPlugin( $plugin_array ) {
			$pagenow                            = $GLOBALS['pagenow'];
			$page                               = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$p                                  = $page != '' ? "?page={$page}&WLMOnlyTNMCEPlugin=1" : '?WLMOnlyTNMCEPlugin=1';
			$url                                = admin_url() . $pagenow . $p;
			$plugin_array['wlmonly_shortcodes'] = $url;
			return $plugin_array;
		}

		function TNMCE_GetSpecialCodeSub( $specialcodes, $sclevel_count ) {
			$code_js = '';
			foreach ( $specialcodes as $ind => $scode ) {
				if ( count( $ind ) == count( $ind, COUNT_RECURSIVE ) ) {
						$code_js .= "sub3 = sub2.addMenu({title : '{$ind}'})\n";
					foreach ( $scode as $parent => $code ) {
						$title    = $code['title'];
						$value    = $code['value'];
						$code_js .= "sub3.add({title : '{$title}', onclick : function() {\n";
						$code_js .= "  tinyMCE.activeEditor.execCommand('mceInsertContent', false, '{$value}');\n";
						$code_js .= "}});\n";
					}
				} else {
					foreach ( $scode as $parent => $code ) {
						$title    = $code['title'];
						$value    = $code['value'];
						$code_js .= "sub3.add({title : '{$title}', onclick : function() {\n";
						$code_js .= "  tinyMCE.activeEditor.execCommand('mceInsertContent', false, '{$value}');\n";
						$code_js .= "}});\n";
					}
				}
			}
			return $code_js;
		}

		function TNMCE_GenerateSpecialCode( $specialcodes, $sclevel_count, $level = 2 ) {
			$code_js = '';
			$sub     = $level <= 2 ? '' : $level - 1;
			foreach ( $specialcodes as $index => $scodes ) {
				if ( count( $scodes ) > 0 ) {
					if ( ! is_integer( $index ) ) {
						if ( $sclevel_count != 1 ) {
							$code_js .= "sub{$level} = sub{$sub}.addMenu({title : '{$index}'})\n";
							$level++;
						}
						$code_js .= $this->TNMCE_GenerateSpecialCode( $scodes, $sclevel_count, $level );
					} else {
						$title   = $scodes['title'];
						$value   = $scodes['value'];
						$value2  = substr_replace( $value, '/', 1, 0 );
						$type    = isset( $scodes['type'] ) ? $scodes['type'] : '';
						$js_func = isset( $scodes['jsfunc'] ) ? $scodes['jsfunc'] : false;
						if ( $js_func ) {
							$code_js .= "sub{$sub}.add({title : '{$title}', onclick : {$js_func} });\n";
						} elseif ( $type == 'merge' ) {
							$code_js .= "sub{$sub}.add({title : '{$title}', onclick : function() {\n               ";
							$code_js .= "  var t = tinyMCE.activeEditor.selection.getContent();\n				   ";
							$code_js .= "  tinyMCE.activeEditor.selection.setContent('{$value}' +t +'{$value2}');\n";
							$code_js .= "  if(t == ''){\n														   ";
							$code_js .= "  		tinyMCE.activeEditor.focus();\n                                    ";
							$code_js .= "  }\n																	   ";
							$code_js .= "}});\n																   	   ";
						} else {
							$code_js .= "sub{$sub}.add({title : '{$title}', onclick : function() {\n";
							$code_js .= "  tinyMCE.activeEditor.execCommand('mceInsertContent', false, '{$value}');\n";
							$code_js .= "}});\n";
						}
					}
				}
			}
			return $code_js;
		}

		function TNMCE_GeneratePlugin( $ptitle, $icon_path, $plugin_name, $max_width ) {
			$pagenow = $GLOBALS['pagenow'];
			header( 'Content-type: text/javascript' );
			$shortcodes = '';
			foreach ( $this->codes as $WLPShortcodes ) {
				$code_js       = '';
				$sclevel_count = 0;
				// if for post only, skip if not in post section
				if ( isset( $WLPShortcodes['wponly'] ) && $pagenow != 'post.php' && $pagenow != 'post-new.php' ) {
					break;
				}
				$sclevel_count += isset( $WLPShortcodes['shortcode'] ) && count( $WLPShortcodes['shortcode'] ) > 0 ? 1 : 0;
				$sclevel_count += isset( $WLPShortcodes['mergecode'] ) && count( $WLPShortcodes['mergecode'] ) > 0 ? 1 : 0;
				$sclevel_count += isset( $WLPShortcodes['special'] ) && count( $WLPShortcodes['special'] ) > 0 ? count( $WLPShortcodes['special'] ) : 0;

				// for shortcodes
				if ( isset( $WLPShortcodes['shortcode'] ) && count( $WLPShortcodes['shortcode'] ) > 0 ) {
					$shortcode = $WLPShortcodes['shortcode'][0];
					$stitle    = isset( $shortcode['replace_title'] ) && $shortcode['replace_title'] != '' ? $shortcode['replace_title'] : 'Shortcodes';
					if ( $sclevel_count != 1 ) {
						$code_js .= "sub2 = sub.addMenu({title : '{$stitle}' })\n";
					}
					foreach ( $WLPShortcodes['shortcode'] as $index => $scode ) {
						$title = $scode['title'];
						$value = $scode['value'];
						if ( $sclevel_count != 1 ) {
							$short_func = "sub2.add({title : '{$title}', onclick : function() {
								tinyMCE.activeEditor.execCommand('mceInsertContent', false, '{$value}');
							}});";
						} else {
							$short_func = "sub.add({title : '{$title}', onclick : function() {
								tinyMCE.activeEditor.execCommand('mceInsertContent', false, '{$value}');
							}});";
						}
						$code_js .= $short_func;
					}
				}
				// for mergecodes
				if ( isset( $WLPShortcodes['mergecode'] ) && count( $WLPShortcodes['mergecode'] ) > 0 ) {
					if ( $sclevel_count != 1 ) {
						$code_js .= "sub2 = sub.addMenu({title : 'Mergecodes'})\n";
					}
					foreach ( $WLPShortcodes['mergecode'] as $index => $scode ) {
						$title  = $scode['title'];
						$value  = $scode['value'];
						$scode2 = substr_replace( $value, '/', 1, 0 );
						if ( $sclevel_count != 1 ) {
							$merge_func = "sub2.add({title : '{$title}', onclick : function() {
								var t = tinyMCE.activeEditor.selection.getContent();
								tinyMCE.activeEditor.selection.setContent('{$value}' +t +'{$scode2}');
								if(t == ''){
									tinyMCE.activeEditor.focus();
								}
							}});";
						} else {
							$merge_func = "sub.add({title : '{$title}', onclick : function() {
								var t = tinyMCE.activeEditor.selection.getContent();
								tinyMCE.activeEditor.selection.setContent('{$value}' +t +'{$scode2}');
								if(t == ''){
									tinyMCE.activeEditor.focus();
								}
							}});";
						}
						$code_js .= $merge_func;
					}
				}

				// for special codes
				if ( isset( $WLPShortcodes['special'] ) && count( $WLPShortcodes['special'] ) > 0 ) {
					$code_js .= $this->TNMCE_GenerateSpecialCode( $WLPShortcodes['special'], $sclevel_count );
				}

				if ( $WLPShortcodes['name'] ) {
					if ( $code_js != '' ) {
						$shortcodes .= "sub = m.addMenu({title : '{$WLPShortcodes['name']}'})\n" . $code_js;
					} else {
						if ( $WLPShortcodes['jsfunction'] ) {
							$shortcodes .= "sub = m.add({title : '{$WLPShortcodes['name']}', onclick:{$WLPShortcodes['jsfunction']}})\n";
						} else {
							$shortcodes .= "sub = m.add({title : '{$WLPShortcodes['name']}'})\n";
						}
					}
				}
			}
			if ( $shortcodes == '' ) {
				return;
			}
			echo "
				tinymce.create('tinymce.plugins.{$plugin_name}', {
	        createControl: function(n, cm) {
            switch (n) {
              case '{$plugin_name}':
                var c = cm.createMenuButton('{$plugin_name}', {
                  title : '{$ptitle}',
                  image : '{$icon_path}',
                  icons : false
                });

                c.onRenderMenu.add(function(c, m) {
                  var sub;
                  m.settings['max_width'] = {$max_width};
                  //add our shortcodes
                  {$shortcodes}
                });

                // Return the new menu button instance
                return c;
            }
            return null;
	        }
				});
				// Register plugin with a short name
				tinymce.PluginManager.add('{$plugin_name}', tinymce.plugins.{$plugin_name});
			";
		}

		function TNMCE_PluginJS() {
			global $WishListMemberInstance;
			$icon_path   = $WishListMemberInstance->pluginURL . '/images/WishList-Icon-Blue-16.png';
			$title       = 'WishList Member';
			$plugin_name = 'wlmonly_shortcodes';
			$max_width   = 600;
			// generate WLM Tinymce Plugin
			if ( isset( $_GET['WLMOnlyTNMCEPlugin'] ) && wlm_arrval( $_GET, 'WLMOnlyTNMCEPlugin' ) == '1' ) {
				$this->TNMCE_GeneratePlugin( $title, $icon_path, $plugin_name, $max_width );
				exit( 0 );
			} elseif ( isset( $_GET['WLMTNMCEPlugin4Only'] ) && $_GET['WLMTNMCEPlugin4Only'] == '1' ) {
				if ( ! $this->use_legacy && ! is_null( $this->wlmtinymceplugin4 ) ) {
					$this->wlmtinymceplugin4->TNMCE_GeneratePlugin( $title, $icon_path, $plugin_name, $max_width );
					exit( 0 );
				}
			}
		}

		/**
		 * Function to be called to register your shortcodes on your plugin
		 *
		 * @param string $name The name of your shortcode that will appear on the menu
		 * @param array  $shortcodes (optional) Multi-dimensional array of shortcodes eg. array('title'=>'[wlm_fname]','value'=>'[wlm_fname]')
		 * @param array  $mergecodes (optional) Multi-dimensional array of shortcodes eg. array('title'=>'[wlm_ismember]','value'=>'[wlm_ismember]')
		 * @param int    $wponly (optional) Specify if your shortcode appear on post only
		 * @param string $jsfunc (optional) Applicable only when $shortcode and $mergecode were empty. The js function to be called when the $name is click
		 */
		function RegisterShortcodes( $name, $shortcodes = array(), $mergecodes = array(), $wponly = 0, $jsfunc = null, $specialcodes = array() ) {
			$code         = array();
			$code['name'] = $name;
			if ( $wponly == 1 ) {
				$code['wponly'] = 1;
			}
			if ( ! is_null( $jsfunc ) ) {
				$code['jsfunction'] = $jsfunc;
			}
			if ( is_array( $shortcodes ) && count( $shortcodes ) > 0 ) {
				$code['shortcode'] = $shortcodes;
			}
			if ( is_array( $mergecodes ) && count( $mergecodes ) > 0 ) {
				$code['mergecode'] = $mergecodes;
			}
			if ( is_array( $specialcodes ) && count( $specialcodes ) > 0 ) {
				$code['special'] = $specialcodes;
			}
			if ( $this->use_legacy ) {
				$this->codes[] = $code;
			} else {
				$this->wlmtinymceplugin4->codes[] = $code;
			}
		}
	}
}

if ( ! class_exists( 'WLMTinyMCEPluginOnly_4' ) ) {

	class WLMTinyMCEPluginOnly_4 {

		public $codes       = array();
		private $css_path   = '';
		public $role_access = array();

		function __construct() {
			// specify the css file
			$this->css_path = '/css/tinymce4_plugin.css';
			add_action( 'init', array( &$this, 'TMCE_InsertButton' ) );
		}

		function TMCE_InsertButton() {
			global $WishListMemberInstance;
			if ( is_array( $this->role_access ) ) {
				$user   = wp_get_current_user();
				$access = array_intersect( $this->role_access, (array) $user->roles );
				if ( count( $access ) <= 0 ) {
					return false; // only roles with access can use this
				}
			}
			// for users who can edit only
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return false;
			}
			// for rich editing only
			if ( get_user_option( 'rich_editing' ) == 'true' && is_admin() ) {
				add_filter( 'mce_external_plugins', array( &$this, 'TNMCE_RegisterPlugin' ) );
				add_filter( 'mce_buttons', array( &$this, 'TNMCE_RegisterButton' ) );
				wp_enqueue_style( 'wlmtinymcepluginonly_4_css', $WishListMemberInstance->pluginURL . $this->css_path, array(), '1.0' );

				if ( wlm_arrval( $_GET, 'page' ) != $WishListMemberInstance->MenuID ) {
					wp_enqueue_style( 'media-views' );
					wp_enqueue_style( 'wishlist_member_admin_chosen_css', $WishListMemberInstance->pluginURL . '/css/chosen.css', array(), $WishListMemberInstance->Version );
					wp_enqueue_script( 'wishlist_member_admin_chosen_js', $WishListMemberInstance->pluginURL . '/js/chosen.jquery.js', array( 'jquery' ), $WishListMemberInstance->Version );
					wp_enqueue_style( 'wishlist-member-tinymce-lightbox-css', $WishListMemberInstance->pluginURL . '/css/tinymce_lightbox.css', array(), $WishListMemberInstance->Version );
					wp_enqueue_script( 'wishlistmember-shortcoder-inserter-lightbox-js', $WishListMemberInstance->pluginURL . '/js/tinymce_lightbox.js', array( 'jquery' ), $WishListMemberInstance->Version );
					wp_localize_script( 'wishlistmember-shortcoder-inserter-lightbox-js', 'wlmtnmcelbox_vars', array() );
				}
			}
		}

		function TNMCE_RegisterPlugin( $plugin_array ) {
			$pagenow = $GLOBALS['pagenow'];

			$params = array();
			if ( isset( $_GET['post_type'] ) && ! empty( $_GET['post_type'] ) ) {
				$params[] = "post_type={$_GET['post_type']}";
			}
			if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ) {
				$params[] = "page={$_GET['page']}";
			}
			$params[] = 'WLMTNMCEPlugin4Only=1';

			$p = implode( '&', $params );
			$p = ! empty( $p ) ? '?' . $p : $p;

			$url                                = admin_url( 'admin.php' ) . $p;
			$plugin_array['wlmonly_shortcodes'] = $url;
			return $plugin_array;
		}

		function TNMCE_RegisterButton( $buttons ) {
			array_push( $buttons, 'wlmonly_shortcodes' );
			return $buttons;
		}

		function TNMCE_GeneratePlugin( $ptitle, $icon_path, $plugin_name, $max_width ) {
			$data = array(
				'title' => $ptitle,
				'type' => 'menubutton',
				'icon' => 'icon wlmshortcode-own-icon',
			) + wishlistmember_instance()->wlmshortcode->_render_tinymce_shortcode_menu();
			
			$data = stripslashes( str_replace( array( '"onclick":"', '"}'), array( '"onclick":', '}' ), json_encode( $data ) ) );
			
			header( 'Content-type: text/javascript' );
			ob_start();
			echo "/*JS SCRIPT FOR WLM TINYMCE4 PLUGIN MENU*/
		    if ( typeof wlmtnmcelbox_vars !== 'undefined' && wlmtnmcelbox_vars ) {
			    tinymce.PluginManager.add('{$plugin_name}', function( editor, url ) {
		        editor.addButton( '{$plugin_name}', {$data});
			    });
		    }";
			echo ob_get_clean();
		}
	}
}

