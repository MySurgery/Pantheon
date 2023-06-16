<?php
/**
 * WordPress Configuration for Pantheon
 *
 * Note that all $_ENV values are provided by Pantheon
 */

// Redirect all requests to HTTPS.
// Note: This may require adjustment for non-UNIX systems.
if ( isset( $_SERVER['HTTPS'] ) && 'off' === $_SERVER['HTTPS'] ) {
	$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( "Location: $redirect" );
	exit();
}

if ( ! function_exists( 'wp_define' ) ) {
	/**
	 * WP Define
	 * This allows for constants to be set in the private config with safe defaults set here.
	 * Note: This silently "fails" where a key is already defined.
	 *
	 * @param string $key   The key to be used for this constant.
	 * @param mixed  $value The value to be set for this constant.
	 *
	 * @return void
	 */
	function wp_define( string $key, mixed $value ) {
		if ( defined( $key ) ) {
			return;
		}

		define( $key, $value );
	}
}

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

// Include project specific constants
if ( file_exists( __DIR__ . '/web/wp-content/private/config.php' ) ) {
	wp_define( 'LOAD_PRIVATE_CONFIG', true );
	require_once  __DIR__ . '/web/wp-content/private/config.php';
}

// DB Settings
wp_define( 'DB_NAME', $_ENV['DB_NAME'] );
wp_define( 'DB_USER', $_ENV['DB_USER'] );
wp_define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );
wp_define( 'DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] );
wp_define( 'DB_CHARSET', 'utf8mb4' );
wp_define( 'DB_COLLATE', '' );
$table_prefix = 'wp_';

// Salts and Hashes
wp_define( 'AUTH_KEY', $_ENV['AUTH_KEY'] );
wp_define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] );
wp_define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] );
wp_define( 'NONCE_KEY', $_ENV['NONCE_KEY'] );
wp_define( 'AUTH_SALT', $_ENV['AUTH_SALT'] );
wp_define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
wp_define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] );
wp_define( 'NONCE_SALT', $_ENV['NONCE_SALT'] );

// Multisite
// Define multisite config in wp-content/private/config.php

// Cookies
wp_define( 'ADMIN_COOKIE_PATH', '/' );
wp_define( 'COOKIE_DOMAIN', '' );
wp_define( 'COOKIE_PATH', '' );
wp_define( 'SITECOOKIEPATH', '' );

// Memory Limit
wp_define( 'WP_MEMORY_LIMIT', '256M' );
wp_define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// Debugging - log but do not display
wp_define( 'WP_DEBUG', true );
wp_define( 'WP_DEBUG_LOG', true );
wp_define( 'WP_DEBUG_DISPLAY', false );

// Disable Auto Updates
wp_define( 'WP_AUTO_UPDATE_CORE', false );
wp_define( 'AUTOMATIC_UPDATER_DISABLED', true );

// Security Enhancements
wp_define( 'FORCE_SSL_ADMIN', true );
wp_define( 'DISALLOW_FILE_EDIT', true );
wp_define( 'DISALLOW_FILE_MODS', true );

// Caching
wp_define( 'WP_CACHE', file_exists( __DIR__ . '/web/wp-content/object-cache.php' ) );
wp_define( 'WP_CACHE_KEY_SALT', AUTH_KEY . NONCE_SALT );


// Local Dev
wp_define( 'WP_LOCAL_DEV', false );

// Environment
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	switch ( $_ENV['PANTHEON_ENVIRONMENT'] ) {
		case 'live': // Production environment (uses main branch)
			wp_define( 'WP_ENVIRONMENT_TYPE', 'production' );
			wp_define( 'WP_ENVIRONMENT_NAME', 'production' );
			break;
		case 'test': // Preview environment (uses main branch)
			wp_define( 'WP_ENVIRONMENT_TYPE', 'staging' );
			wp_define( 'WP_ENVIRONMENT_NAME', 'test' );
			break;
		case 'dev': // Preprod environment
			wp_define( 'WP_ENVIRONMENT_TYPE', 'develop' );
			wp_define( 'WP_ENVIRONMENT_NAME', 'dev' );
			break;
		default:     // Default to development
			wp_define( 'WP_ENVIRONMENT_TYPE', 'develop' );
			wp_define( 'WP_ENVIRONMENT_NAME', 'unknown' );
			break;
	}
}
// Temp directory
wp_define( 'WP_TEMP_DIR', $_SERVER['HOME'] . '/tmp' );

// Compression
wp_define( 'COMPRESS_CSS', true );
wp_define( 'COMPRESS_SCRIPTS', true );
wp_define( 'ENFORCE_GZIP', true );

// Disable concatenate scripts
// CVE-2018-6389
// https://wpvulndb.com/vulnerabilities/9021
wp_define( 'CONCATENATE_SCRIPTS', false );

// Define ABSPATH
defined( 'ABSPATH' ) || wp_define( 'ABSPATH', __DIR__. '/' );

// Load WordPress
require_once ABSPATH . 'wp-settings.php';
