<?php
/**
 * PluginBase Bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// PSR-4 autoloader for the 'PluginBase' namespace mapped to the 'includes' directory.
spl_autoload_register( function ( $class ) {
	$prefix   = 'PluginBase\\';
	$base_dir = PLUGIN_BASE_PATH . 'includes/';

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

add_action( 'plugins_loaded', [ 'PluginBase\\Plugin', 'init' ] );
