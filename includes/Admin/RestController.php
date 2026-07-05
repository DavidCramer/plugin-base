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

	const NAMESPACE = 'plugin-base/v1';

	/**
	 * Hook into rest_api_init.
	 */
	public function init(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function get_endpoint_definitions() {
		$endpoints = [
			'items'               => [
				'get' => [
					'callback' => [ $this, 'list_items' ],
				],
				'post'    => [
					'callback' => [ $this, 'create_item' ],
					'args'     => [
						'slug' => [
							'required'          => false,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'data' => [
							'required'          => true,
						]
					]
				]
			],
			'items/(?P<slug>[a-z0-9-]+)'              => [
				'get' => [
					'callback' => [ $this, 'get_item' ],
					'args'     => [
						'slug' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
					],
				],
				'put' => [
					'callback' => [ $this, 'update_item' ],
					'args'     => [
						'slug'  => [
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
					'callback' => [ $this, 'delete_item' ],
					'args'     => [
						'slug'  => [
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
		return apply_filters( 'archetype_rest_endpoints', $endpoints );
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
	 */
	public function check_permission(): bool {
		return current_user_can( apply_filters( 'plugin_base_manage_capability', 'manage_options' ) );
	}

	/**
	 * List items.
	 * @return \WP_REST_Response
	 */
	public function list_items(): \WP_REST_Response {
		return new \WP_REST_Response( ConfigStore::list_summaries() );
	}

	public function create_item( \WP_REST_Request $request ) {
		$slug = $request->get_param( 'slug' );
		$data = $request->get_param( 'data' );

		$slug = ConfigStore::create( $slug, $data );

		if ( false === $slug ) {
			return new \WP_Error( 'plugin_base_item_exists', __( 'That item already exists.', 'plugin-base' ), [ 'status' => 409 ] );
		}

		return new \WP_REST_Response( ['slug' => $slug, 'data' => $data], 201 );
	}

	public function get_item( \WP_REST_Request $request ) {
		$slug = (string) $request->get_param( 'slug' );
		$data       = ConfigStore::get( $slug );

		if ( null === $data ) {
			return new \WP_Error( 'plugin_base_not_found', __( 'Item not found.', 'plugin-base' ), [ 'status' => 404 ] );
		}

		return new \WP_REST_Response( ['slug' => $slug, 'data' => $data] );
	}

	public function update_item( \WP_REST_Request $request ) {
		$slug = (string) $request->get_param( 'slug' );
		$existing   = ConfigStore::get( $slug );

		if ( null === $existing ) {
			return new \WP_Error( 'plugin_base_not_found', __( 'Item not found.', 'plugin-base' ), [ 'status' => 404 ] );
		}

		$data = $request->get_param( 'data' );

		ConfigStore::update( $slug, $data );

		return new \WP_REST_Response( ['slug' => $slug, 'data' => $data] );
	}

	public function delete_item( \WP_REST_Request $request ) {
		$deleted = ConfigStore::delete( (string) $request->get_param( 'slug' ) );

		if ( ! $deleted ) {
			return new \WP_Error( 'plugin_base_not_found', __( 'Item not found.', 'plugin-base' ), [ 'status' => 404 ] );
		}

		return new \WP_REST_Response( null, 204 );
	}
}
