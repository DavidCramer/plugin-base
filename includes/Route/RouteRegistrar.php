<?php
/**
 * Registers every configured mock endpoint/method as a real REST route
 * on boot. Each callback has its config bound in at registration time —
 * no request-time re-scanning of the full config.
 *
 * @package PluginBase
 */

namespace PluginBase\Route;

use PluginBase\Data\ConfigStore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RouteRegistrar {

	/**
	 * Hook into rest_api_init.
	 */
	public function init(): void {
		add_action( 'rest_api_init', [ $this, 'register' ] );
	}

	/**
	 * Register all configured namespaces/endpoints/methods.
	 */
	public function register(): void {
		foreach ( ConfigStore::get_all() as $data ) {
			$namespace = trim( (string) ( $data['namespace'] ?? '' ), '/' );

			if ( '' === $namespace ) {
				continue;
			}

			foreach ( (array) ( $data['endpoints'] ?? [] ) as $endpoint ) {
				$this->register_endpoint( $namespace, $endpoint );
			}
		}
	}

	/**
	 * Register every enabled method of a single endpoint.
	 */
	private function register_endpoint( string $namespace, array $endpoint ): void {
		if ( empty( $endpoint['path'] ) ) {
			return;
		}

		$path = PathConverter::convert( $endpoint['path'] );

		foreach ( (array) ( $endpoint['methods'] ?? [] ) as $method => $method_config ) {
			if ( empty( $method_config['enabled'] ) ) {
				continue;
			}

			register_rest_route(
				$namespace,
				$path,
				[
					'methods'             => strtoupper( $method ),
					'callback'            => function ( \WP_REST_Request $request ) use ( $endpoint, $method_config ) {
						return Dispatcher::handle( $request, $endpoint, $method_config );
					},
					'permission_callback' => function ( \WP_REST_Request $request ) use ( $method_config ) {
						return Auth::check( $request, $method_config['auth'] ?? [] );
					},
				]
			);
		}
	}
}
