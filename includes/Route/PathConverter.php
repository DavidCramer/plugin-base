<?php
/**
 * Converts ':param'-style endpoint paths into named-capture regex paths
 * suitable for register_rest_route(). Registration-time only.
 *
 * @package PluginBase
 */

namespace PluginBase\Route;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PathConverter {

	/**
	 * Convert e.g. 'user/:id/location' into '/user/(?P<id>[A-Za-z0-9_-]+)/location'.
	 */
	public static function convert( string $path ): string {
		$path = '/' . trim( $path, '/' );

		return preg_replace( '/:([a-zA-Z_][a-zA-Z0-9_]*)/', '(?P<$1>[A-Za-z0-9_-]+)', $path );
	}
}
