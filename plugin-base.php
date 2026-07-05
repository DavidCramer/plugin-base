<?php
/**
 * Plugin Name: Plugin Base
 * Description: A base boilerplate for WordPress plugins.
 * Version: 1.0.0
 * Author: David Cramer
 * Text Domain: plugin-base
 * Requires PHP: 8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

const PLUGIN_BASE_VERSION = '1.0.0';
define( 'PLUGIN_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_BASE_BASENAME', plugin_basename( __FILE__ ) );

require_once PLUGIN_BASE_PATH . 'bootstrap.php';

register_activation_hook( __FILE__, [ 'PluginBase\\Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'PluginBase\\Plugin', 'deactivate' ] );
