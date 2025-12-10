<?php
/**
 * Plugin Name: PluginBase
 * Description: A base boilerplate for WordPress plugins.
 * Version: 1.0.0
 * Author: David Cramer
 * Text Domain: plugin-base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin version.
 */
define( 'PLUGIN_BASE_VERSION', '1.0.0' );

/**
 * Plugin root directory path.
 */
define( 'PLUGIN_BASE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin root directory URL.
 */
define( 'PLUGIN_BASE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'PLUGIN_BASE_BASENAME', plugin_basename( __FILE__ ) );


require_once plugin_dir_path( __FILE__ ) . 'bootstrap.php';
