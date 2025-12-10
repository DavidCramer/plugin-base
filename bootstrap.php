<?php
/**
 * Bootstrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Simple SPL Autoloader for 'PluginBase' namespace mapped to 'includes' directory
spl_autoload_register( function ( $class ) {
	$prefix   = 'PluginBase\\';
	$base_dir = plugin_dir_path( __FILE__ ) . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Initialize the Plugin
if ( class_exists( 'PluginBase\\Plugin' ) ) {
	PluginBase\Plugin::init();
}
