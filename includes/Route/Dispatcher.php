<?php
/**
 * Resolves and serves the configured success/fail JSON for a matched
 * mock endpoint + method.
 *
 * @package PluginBase
 */

namespace PluginBase\Route;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dispatcher {

	/**
	 * Handle a matched mock route request.
	 *
	 * @param \WP_REST_Request $request       The incoming request.
	 * @param array             $endpoint      The endpoint config (path, enabled, ...).
	 * @param array             $method_config The matched method's config (auth, success, fail).
	 */
	public static function handle( \WP_REST_Request $request, array $endpoint, array $method_config ): \WP_REST_Response {
		$body_config = empty( $endpoint['enabled'] ) ? ( $method_config['fail'] ?? [] ) : ( $method_config['success'] ?? [] );

		$status  = (int) ( $body_config['status'] ?? 200 );
		$decoded = json_decode( (string) ( $body_config['body'] ?? 'null' ), true );

		if ( is_array( $decoded ) ) {
			$params = $request->get_url_params();
			if ( ! empty( $params ) ) {
				$decoded = ResponseFilter::apply( $decoded, $params );
			}
		}

		return new \WP_REST_Response( $decoded, $status );
	}
}
