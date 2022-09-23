<?php
require_once(__DIR__ . '/local.php');

//==project
define('CANONICAL_DOMAIN', 'tobymackenzie.com');
define('SITE_DOMAIN', (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : CANONICAL_DOMAIN));
define('PROJECT_PATH', realpath(__DIR__ . '/..'));
define('WEBROOT_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http') . '://' . SITE_DOMAIN);

//==wordpress
$table_prefix = 'wp_';
define('ADMIN_COOKIE_PATH', '/');
define('DB_CHARSET', 'utf8');
// define('DB_COLLATE', '');
if(!defined('WP_DEBUG')){
	define('WP_DEBUG', false);
}
define('WP_CONTENT_DIR', PROJECT_PATH . '/www/_content');
define('WP_CONTENT_URL', WEBROOT_URL . '/_content');
define('WP_HOME', WEBROOT_URL);
define('WP_SITEURL', WEBROOT_URL . '/_wp');
//-# must still enable permalinks </_wp/wp-admin/options-permalink.php> for things to work properly
