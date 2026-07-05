<?php
/**
 * Per-method auth checks for mock endpoints: WordPress nonce, header API key,
 * or bearer token. String-match only, as specified — no elaborate scheme.
 *
 * @package PluginBase
 */

namespace PluginBase\Route;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Auth {

	/**
	 * Check a request against a method's configured auth mode.
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 * @param array             $auth    The method's auth config: { mode, header?, value? }.
	 */
	public static function check( \WP_REST_Request $request, array $auth ): bool {
		$mode = $auth['mode'] ?? 'nonce';

		switch ( $mode ) {
			case 'api_key':
				return self::check_api_key( $request, $auth );

			case 'bearer':
				return self::check_bearer( $request, $auth );

			case 'public':
				return true;

			case 'nonce':
			default:
				return self::check_nonce( $request );
		}
	}

	/**
	 * WordPress nonce, verified via wp_verify_nonce() (not a naive string compare).
	 */
	private static function check_nonce( \WP_REST_Request $request ): bool {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( empty( $nonce ) ) {
			$nonce = $request->get_param( '_wpnonce' );
		}

		return ! empty( $nonce ) && false !== wp_verify_nonce( (string) $nonce, 'wp_rest' );
	}

	/**
	 * Header API key: simple constant-time string match against the configured value.
	 */
	private static function check_api_key( \WP_REST_Request $request, array $auth ): bool {
		$header   = $auth['header'] ?? 'X-PluginBase-Api-Key';
		$expected = (string) ( $auth['value'] ?? '' );
		$provided = (string) ( $request->get_header( $header ) ?? '' );

		return '' !== $expected && hash_equals( $expected, $provided );
	}

	/**
	 * Bearer token: simple constant-time string match against the configured value.
	 */
	private static function check_bearer( \WP_REST_Request $request, array $auth ): bool {
		$expected      = (string) ( $auth['value'] ?? '' );
		$authorization = (string) ( $request->get_header( 'authorization' ) ?? '' );

		if ( '' === $expected || ! preg_match( '/^Bearer\s+(.+)$/i', $authorization, $matches ) ) {
			return false;
		}

		return hash_equals( $expected, trim( $matches[1] ) );
	}
}
