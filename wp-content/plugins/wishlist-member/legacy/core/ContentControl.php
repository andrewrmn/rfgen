<?php

/**
 * Content Control Class for WishList Member
 * @author Fel Jun Palawan <fjpalawan@gmail.com>
 * @package wishlistmember
 *
 * @version $Rev: 4837 $
 * $LastChangedBy: feljun $
 * $LastChangedDate: 2018-08-06 23:16:59 +0800 (Mon, 06 Aug 2018) $
 */
if (!defined('ABSPATH')) die();

require_once(dirname(__FILE__) . '/content-control/scheduler.php');
require_once(dirname(__FILE__) . '/content-control/archiver.php');
require_once(dirname(__FILE__) . '/content-control/manager.php');

if (!class_exists('WLM3_ContentControl')) {
	/**
	 * WishList Member Level Class
	 * @package wishlistmember
	 * @subpackage classes
	 */
	class WLM3_ContentControl {

		var $scheduler = null;
		var $archiver  = null;
		var $manager = null;
		var $old_contentcontrol_active = false;

		function __construct($that) {
			if ( is_plugin_active( 'wishlist-content-control/wishlist-content-control.php' )  || isset($WishListContentControl) ) {
				$this->old_contentcontrol_active = true;
				return;
			}

			if ( $that->GetOption('enable_content_scheduler') ) $this->scheduler = new WLM3_ContentScheduler();
			if ( $that->GetOption('enable_content_archiver') ) $this->archiver = new WLM3_ContentArchiver();
			if ( $that->GetOption('enable_content_manager') ) $this->manager = new WLM3_ContentManager();
		}

		function activate() {
			if ( $this->scheduler ) $this->scheduler->Activate();
		}

		function load_hooks() {
			if ( $this->scheduler ) $this->scheduler->load_hooks();
			if ( $this->archiver ) $this->archiver->load_hooks();
			if ( $this->manager ) $this->manager->load_hooks();
		}
	}
}
?>