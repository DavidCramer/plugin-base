<?php
/**
 * API Class
 *
 * @package PluginBase
 */

namespace PluginBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle REST API requests.
 */
class Api {

	/**
	 * API Namespace.
	 */
	const NAMESPACE = 'plugin-base/v1';

	/**
	 * Settings Option Key.
	 */
	const SETTINGS_KEY = 'plugin_base_settings';

	/**
	 * Initialize API.
	 */
	public static function init() {
		$self = new self();
		add_action( 'rest_api_init', [ $self, 'register_routes' ] );
	}

	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		register_rest_route( self::NAMESPACE, '/settings', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_settings' ],
				'permission_callback' => [ $this, 'check_permission' ],
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_settings' ],
				'permission_callback' => [ $this, 'check_permission' ],
			],
		] );
	}

	/**
	 * Check permissions.
	 *
	 * @return bool
	 */
	public function check_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get settings.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_settings() {
		$settings = get_option( self::SETTINGS_KEY, [] );
		
		// Ensure default structure if needed
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}

		return rest_ensure_response( $settings );
	}

	/**
	 * Update settings.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	public function update_settings( \WP_REST_Request $request ) {
		$params = $request->get_json_params();
		
		if ( ! is_array( $params ) ) {
			return new \WP_Error( 'invalid_data', 'Invalid data provided', [ 'status' => 400 ] );
		}

		// Merge with existing settings to prevent overwriting keys not passed
		$existing = get_option( self::SETTINGS_KEY, [] );
		if ( ! is_array( $existing ) ) {
			$existing = [];
		}
		
		// Sanitize values? For now we trust admin input but in real app we should sanitize
		$new_settings = array_merge( $existing, $params );

		update_option( self::SETTINGS_KEY, $new_settings );

		return rest_ensure_response( $new_settings );
	}
}
