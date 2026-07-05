<?php
/**
 * Simple key-match filtering of mock response bodies against captured
 * ':param' path values (e.g. /user/:id/location -> filter by "id").
 * Deliberately not a query engine — see plan for the exact rules.
 *
 * @package PluginBase
 */

namespace PluginBase\Route;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ResponseFilter {

	/**
	 * Apply captured path params to a decoded response body.
	 *
	 * @param mixed $decoded The json_decode()'d response body (assoc arrays).
	 * @param array $params  Captured path params, e.g. [ 'id' => '1' ].
	 *
	 * @return mixed The filtered body, or the original body if no filtering applies.
	 */
	public static function apply( $decoded, array $params ) {
		if ( empty( $params ) ) {
			return $decoded;
		}

		if ( self::is_list( $decoded ) ) {
			return self::filter_list( $decoded, $params );
		}

		if ( is_array( $decoded ) ) {
			foreach ( $decoded as $key => $value ) {
				if ( self::is_list( $value ) ) {
					$decoded[ $key ] = self::filter_list( $value, $params );

					// Only the first top-level array-valued key is treated as the wrapped list.
					return $decoded;
				}
			}
		}

		return $decoded;
	}

	/**
	 * Filter a list of items by whichever params match a key present on the items.
	 */
	private static function filter_list( array $list, array $params ): array {
		$active_params = [];

		foreach ( $params as $name => $value ) {
			foreach ( $list as $item ) {
				if ( is_array( $item ) && array_key_exists( $name, $item ) ) {
					$active_params[ $name ] = $value;
					break;
				}
			}
		}

		if ( empty( $active_params ) ) {
			return $list;
		}

		return array_values(
			array_filter(
				$list,
				function ( $item ) use ( $active_params ) {
					if ( ! is_array( $item ) ) {
						return false;
					}

					foreach ( $active_params as $name => $value ) {
						if ( ! array_key_exists( $name, $item ) || (string) $item[ $name ] !== (string) $value ) {
							return false;
						}
					}

					return true;
				}
			)
		);
	}

	/**
	 * Whether a value is a JSON-array-shaped (sequential, non-empty) PHP array.
	 */
	private static function is_list( $value ): bool {
		if ( ! is_array( $value ) || empty( $value ) ) {
			return false;
		}

		return array_keys( $value ) === range( 0, count( $value ) - 1 );
	}
}
