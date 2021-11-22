<?php
$wl = $_GET['wl'];
$wl =explode( "/", $wl );
$content_type = $wl[1];
$content_comment = false;
$custom_post_type = $wl[1];

$enabled_types = (array) $this->GetOption('protected_custom_post_types');
$enabled_custom_post_types = in_array( $custom_post_type, $enabled_types ) ? 1 : 0;

include( $this->pluginDir3 ."/ui/admin_screens/content_protection/post_page_files/content.php");
?>