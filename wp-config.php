<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 * @package WordPress
 */

$wpdomain = $_SERVER['SERVER_NAME'];
if ($wpdomain == "cds.local") {
	$dbConfigFile = dirname(__FILE__) . '/local-config.php';
	if (!file_exists($dbConfigFile))
	    die('Set up database config in local-config.php');
} elseif ($wpdomain == "stage.cds-global.com" || $wpdomain == "uk.stage.cds-global.com") {
	$dbConfigFile = dirname(__FILE__) . '/stage-config.php';
	if (!file_exists($dbConfigFile))
	    die('Set up database config in stage-config.php');
} else {
	$dbConfigFile == dirname(__FILE__) . '/prod-config.php';
	if (!file_exists($dbConfigFile))
	    die('Set up database config in prod-config.php');
}
include($dbConfigFile);

/** Custom content directory */
define('WP_CONTENT_DIR', dirname(__FILE__) . '/content');
if (isset($_SERVER['HTTP_HOST']))
    define('WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FS_METHOD', 'direct');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 */
define('AUTH_KEY',         'W)v|T<RUufTKkL.|qwL<_pXDy.3fU/#;JoF%0+-!#A*;M~>UMa~!eK|ion$V=K;l');
define('SECURE_AUTH_KEY',  'Jq@?sPmQ cp|8S;)F-`TecJ<)Z+fz)?jnQLZ(@RSZV$QpV=|O7_t x:A7+(Y6~j ');
define('LOGGED_IN_KEY',    'NAE92jm6Gg9+cRO=)jI!`N%D$$?aalgvr)L+{Y@+[9Si0Z1-{8H9a;MEG*KeUibr');
define('NONCE_KEY',        'k?<il:?}-{@n@5A]:AV!**Sel_a$bp.ooX!>bQMl5pw(wp1}=KOxUcds*z.#Dd}|');
define('AUTH_SALT',        'h_tKH-p|G|X!Z=KBdtveMpck|3Ko;I?UrR:Xk7l<bv+QU6D~mYG/9)AbZ?(,qO-s');
define('SECURE_AUTH_SALT', '*{3E|W:W0wFaa[d8w6W,Yo t*G mJ;|G&WHDYhLO7,@C!3T-NV>+)_EV[meBd7H)');
define('LOGGED_IN_SALT',   'j5Z)tS^ddZ1zCf,k1-2?iWVz.ejw>w#,9]5?F&<YinM6B#lKdZ)|oEbN7T@V-MkI');
define('NONCE_SALT',       '3^]~Te,DOAYaWA7DQFLtsecf~kB[-d0:dIln+K}eXdyQlP<>g0QfJRUJo!Ta991a');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );

define('MULTISITE', true);
define('SUNRISE', 'on');
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', $wpdomain);
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define('WP_DEFAULT_THEME', 'cds');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/wordpress/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
