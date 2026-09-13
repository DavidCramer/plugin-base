<?php
/**
 * Admin-facing REST API (plugin-base/v1) used by the React app to CRUD
 * item/endpoint config. Separate from the dynamically-registered mock
 * endpoints themselves, which live under each user-defined item.
 *
 * @package PluginBase
 */

namespace PluginBase\Admin;

use PluginBase\Data\ConfigStore;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RestController {

	/**
	 * The namespace for the REST API.
	 */
	const NAMESPACE = 'plugin-base/v1';

	/**
	 * Holds the singular item type name for the REST API.
	 * @var string
	 */
	const ITEM_TYPE_SINGLE = 'item';

	/**
	 * Holds the plural item type name for the REST API.
	 * @var string
	 */
	const ITEM_TYPE_PLURAL = 'items';


	/**
	 * Hook into rest_api_init.
	 */
	public function init(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function get_endpoint_definitions() {
		$endpoints = [
			self::ITEM_TYPE_PLURAL                           => [
				'get'  => [
					'callback' => [ $this, 'list' ],
				],
				'post' => [
					'callback' => [ $this, 'create' ],
					'args'     => [
						'slug' => [
							'required'          => false,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'data' => [
							'required' => true,
						]
					]
				]
			],
			self::ITEM_TYPE_PLURAL . '/(?P<slug>[a-z0-9-]+)' => [
				'get'    => [
					'callback' => [ $this, 'get' ],
					'args'     => [
						'slug' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
					],
				],
				'put'    => [
					'callback' => [ $this, 'update' ],
					'args'     => [
						'slug' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'data' => [
							'required' => true,
						],
					],
				],
				'delete' => [
					'callback' => [ $this, 'delete' ],
					'args'     => [
						'slug' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
					]
				]
			],
		];

		/**
		 * Filter the endpoint definitions.
		 *
		 * @param array $endpoints The endpoint definitions.
		 */
		return apply_filters( 'plugin_base_rest_endpoints', $endpoints );
	}

	/**
	 * Register the admin CRUD routes.
	 *
	 * @return void
	 */
	public function register_routes() {

		$endpoints = $this->get_endpoint_definitions();
		$methods   = [
			'get'    => WP_REST_Server::READABLE,
			'post'   => WP_REST_Server::CREATABLE,
			'put'    => WP_REST_Server::EDITABLE,
			'patch'  => WP_REST_Server::EDITABLE,
			'delete' => WP_REST_Server::DELETABLE,
		];
		foreach ( $endpoints as $endpoint => $routes ) {

			foreach ( $routes as $method => $config ) {
				$defaultArgs = [
					'methods'             => $methods[ strtolower( $method ) ],
					'permission_callback' => [ $this, 'check_permission' ],
				];

				$args = wp_parse_args( $config, $defaultArgs );

				register_rest_route( self::NAMESPACE, '/' . $endpoint, $args );
			}

		}
	}

	/**
	 * Only users who can manage_options (filterable) may use this API.
	 *
	 * @param \WP_REST_Request $request The request object.
	 *
	 * @return bool True if the user has permission, false otherwise.
	 */
	public function check_permission( \WP_REST_Request $request ): bool {
		/**
		 * Filter the capability required to manage plugin-base items.
		 *
		 * @param string $permission The capability required to manage plugin-base items.
		 */
		$permission = apply_filters( 'plugin_base_manage_capability', 'manage_options' );
		$can        = current_user_can( $permission );


		/**
		 * Filter the result of the permission check.
		 *
		 * @param bool             $can        Whether the user has permission to manage plugin-base items.
		 * @param string           $permission The capability required to manage plugin-base items.
		 * @param \WP_REST_Request $request    The request object.
		 *
		 * @return bool True if the user has permission, false otherwise.
		 */
		return apply_filters( 'plugin_base_check_rest_permission', $can, $permission, $request );
	}

	/**
	 * List items.
	 * @return \WP_REST_Response
	 */
	public function list(): \WP_REST_Response {
		return new \WP_REST_Response( ConfigStore::list_summaries() );
	}

	public function create( \WP_REST_Request $request ) {
		$slug = $request->get_param( 'slug' );
		$data = $request->get_param( 'data' );

		$slug = ConfigStore::create( $slug, $data );

		if ( false === $slug ) {
			$error_code = 'plugin_base_' . self::ITEM_TYPE_SINGLE . '_exists';
			$message    = sprintf( __( 'That %s already exists.', 'plugin-base' ), self::ITEM_TYPE_SINGLE );
			return new \WP_Error( $error_code, $message, [ 'status' => 409 ] );
		}

		return new \WP_REST_Response( [ 'slug' => $slug, 'data' => $data ], 201 );
	}

	public function get( \WP_REST_Request $request ) {
		$slug = (string) $request->get_param( 'slug' );
		$data = ConfigStore::get( $slug );

		if ( null === $data ) {
			$error_code = 'plugin_base_' . self::ITEM_TYPE_SINGLE . '_not_found';
			$message    = sprintf( __( 'That %s does not exist.', 'plugin-base' ), self::ITEM_TYPE_SINGLE );
			return new \WP_Error( $error_code, $message, [ 'status' => 404 ] );
		}

		return new \WP_REST_Response( [ 'slug' => $slug, 'data' => $data ] );
	}

	public function update( \WP_REST_Request $request ) {
		$slug     = (string) $request->get_param( 'slug' );
		$existing = ConfigStore::get( $slug );

		if ( null === $existing ) {
			$error_code = 'plugin_base_' . self::ITEM_TYPE_SINGLE . '_not_found';
			$message    = sprintf( __( 'That %s does not exist.', 'plugin-base' ), self::ITEM_TYPE_SINGLE );
			return new \WP_Error( $error_code, $message, [ 'status' => 404 ] );
		}

		$data = $request->get_param( 'data' );

		ConfigStore::update( $slug, $data );

		return new \WP_REST_Response( [ 'slug' => $slug, 'data' => $data ] );
	}

	public function delete( \WP_REST_Request $request ) {
		$deleted = ConfigStore::delete( (string) $request->get_param( 'slug' ) );

		if ( ! $deleted ) {
			$error_code = 'plugin_base_' . self::ITEM_TYPE_SINGLE . '_not_found';
			$message    = sprintf( __( 'That %s does not exist.', 'plugin-base' ), self::ITEM_TYPE_SINGLE );
			return new \WP_Error( $error_code, $message, [ 'status' => 404 ] );
		}

		return new \WP_REST_Response( null, 204 );
	}
}
