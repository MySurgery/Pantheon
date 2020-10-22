<?php
/**
 * WordPress Configuration for Pantheon
 *
 * Note that all $_ENV values are provided by Pantheon
 */

// SSL - if proxied from a load balancer
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS']          = 'on';
	$_SERVER['REQUEST_SCHEME'] = 'https';
}

// Set remote IP correctly when using Cloudflare
if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && '' !== $_SERVER['HTTP_CF_CONNECTING_IP'] ) {
	$_SERVER['REMOTE_ADDR'] = filter_var( $_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_SANITIZE_IP );
}

// Ensure a SERVER_NAME is defined for WP CLI
if ( defined( 'WP_CLI' ) && true === WP_CLI && ! isset( $_SERVER['SERVER_NAME'] ) ) {
	$_SERVER['SERVER_NAME'] = '';
}

// DB Settings
define( 'DB_NAME', $_ENV['DB_NAME'] );
define( 'DB_USER', $_ENV['DB_USER'] );
define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );
define( 'DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
$table_prefix = 'wp_';

// Salts and Hashes
define( 'AUTH_KEY', $_ENV['AUTH_KEY'] );
define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] );
define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] );
define( 'NONCE_KEY', $_ENV['NONCE_KEY'] );
define( 'AUTH_SALT', $_ENV['AUTH_SALT'] );
define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] );
define( 'NONCE_SALT', $_ENV['NONCE_SALT'] );

// Memory Limit
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// Debugging - log but do not display
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

// Disable Auto Updates
define( 'WP_AUTO_UPDATE_CORE', false );

// Security Enhancements
define( 'FORCE_SSL_ADMIN', true );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

// Caching
define( 'WP_CACHE', file_exists( __DIR__ . '/web/wp-content/advanced-cache.php' ) ? true : false );

// Include project specific constants
if ( file_exists( __DIR__ . '/web/wp-content/private/config.php' ) ) {
	define( 'LOAD_PRIVATE_CONFIG', true );
	require_once  __DIR__ . '/web/wp-content/private/config.php';
}

// Define WP Environment
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && false === getenv( 'WP_ENVIRONMENT_TYPE' ) ) {
	switch ( $_ENV['PANTHEON_ENVIRONMENT'] ) {
		case 'live': // Production environment. Uses main branch
			putenv( 'WP_ENVIRONMENT_TYPE=production' );
			break;
		case 'test': // Preview environment. Uses main branch
			putenv( 'WP_ENVIRONMENT_TYPE=staging' );
			break;
		default:     // Default to development
			putenv( 'WP_ENVIRONMENT_TYPE=development' );
			break;
	}
}

// Temp directory
define( 'WP_TEMP_DIR', $_SERVER['HOME'] . '/tmp' );

// Disable concatenate scripts
// CVE-2018-6389
// https://wpvulndb.com/vulnerabilities/9021
define( 'CONCATENATE_SCRIPTS', false );

// Define ABSPATH
defined( 'ABSPATH' ) || define( 'ABSPATH', __DIR__. '/' );

// Load WordPress
require_once ABSPATH . 'wp-settings.php';
