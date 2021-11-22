<?php
# Database Configuration
define( 'DB_NAME', 'rfgen_db' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'localhost' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         'Wdx.q4~+3^WI6kFh-lT,lC%Gh#ueth&{_su6qFzKC~cun0$3Mn?=h*hi)6/B/+23');
define('SECURE_AUTH_KEY',  '2H?#NTJ0n%W3i@h[.@-?I4?|q:Hf$5j%#Sb2ym_#.}sb:cgIUT|Vv;N1Lq/0L&rN');
define('LOGGED_IN_KEY',    '6qT-;6V.Av|?vTBBB=G+>O-qGkle<ovU.A|*{7.h2qT)^x[yheaEMb^<-rr Yz=v');
define('NONCE_KEY',        '$X4$3C.cGn:@2qfDz QFwqel|g mQ-GX3W.$XQ}98n2f+r9+jE/!th{(z(wfV}DM');
define('AUTH_SALT',        's?Y;D;-fI,4Sv(.T!UI#qwi_v}(+I$ 7>JU)jv0|;0xT0gcSncrV6bkK4n9ch`l!');
define('SECURE_AUTH_SALT', 'K=N}wQ/oaf#V_<&/Iq1*i)/d8YA=}F$]Hyw({le)dIQJAjL;LK{fHmw2t!USQkgN');
define('LOGGED_IN_SALT',   'jw^<]nEXp_eCprXDFon$+&O9 iH~f`LaJpe5 zTkrvXOcG*Q=M<Su+7D17b6MS}z');
define('NONCE_SALT',       '_(3Q!y~:Jj-[7,zJs|+Vni{FYwQW@,M+|+!mXX/JsSXSJZOj(UNzU2xY@0|-;XS{');

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
# Localized Language Stuff

define( 'WP_CACHE', FALSE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'rfgendev' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'WPE_APIKEY', '58086dbd743b08843692a917261ff397691d5536' );

define( 'WPE_CLUSTER_ID', '100151' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'rfgendev.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-100151', );

$wpe_special_ips=array ( 0 => '173.255.117.27', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );

define( 'WPE_SFTP_ENDPOINT', '' );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings






# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', __DIR__ . '/');
require_once(ABSPATH . 'wp-settings.php');
