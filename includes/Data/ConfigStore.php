<?php
/**
 * CRUD storage for namespace configuration blobs.
 *
 * @package PluginBase
 */

namespace PluginBase\Data;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ConfigStore {

	const CACHE_GROUP = 'plugin_base_config';

	/**
	 * Get the underlying table name.
	 */
	private static function table(): string {
		global $wpdb;

		return $wpdb->prefix . 'plugin_base_config';
	}

	/**
	 * Derive the storage key for a item string.
	 */
	public static function key_for_item( string|null $item ): string {
		if ( empty( $item ) ) {
			$item = uniqid( 'plugin_base' );
		}

		return sanitize_title( $item );
	}

	/**
	 * Fetch a single item's data by its config key.
	 *
	 * @return array|null Decoded data, or null if not found.
	 */
	public static function get( string $slug ): ?array {
		$slug   = self::key_for_item( $slug );
		$cached = wp_cache_get( $slug, self::CACHE_GROUP );
		if ( false !== $cached ) {
			return $cached;
		}

		global $wpdb;
		$row = $wpdb->get_var(
			$wpdb->prepare( 'SELECT data FROM ' . self::table() . ' WHERE slug = %s LIMIT 1', $slug )
		);

		if ( null === $row ) {
			return null;
		}

		$data = json_decode( $row, true );
		wp_cache_set( $slug, $data, self::CACHE_GROUP, HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Fetch every item's data, keyed by slug.
	 *
	 * @return array<string, array>
	 */
	public static function get_all(): array {
		global $wpdb;
		$rows = $wpdb->get_results( 'SELECT slug, data FROM ' . self::table(), ARRAY_A );

		$items = [];
		foreach ( (array) $rows as $row ) {
			$decoded = json_decode( $row['data'], true );
			if ( is_array( $decoded ) ) {
				$items[ $row['slug'] ] = $decoded;
			}
		}

		return $items;
	}

	/**
	 * List lightweight summaries of every item, for the item list screen.
	 *
	 * @return array<int, array{slug: string}>
	 */
	public static function list_summaries(): array {
		$summaries = [];
		foreach ( self::get_all() as $slug => $data ) {
			$summaries[] = [
				'slug' => $slug,
			];
		}

		return $summaries;
	}

	/**
	 * Insert a new item. Fails if the derived key already exists.
	 *
	 * @return string|false The new slug, or false if the key already exists.
	 */
	public static function create( string $slug, array $data ) {
		$slug = self::key_for_item( $slug );

		if ( null !== self::get( $slug ) ) {
			return false;
		}

		global $wpdb;
		$result = $wpdb->insert(
			self::table(),
			[
				'slug' => $slug,
				'data' => wp_json_encode( $data ),
			],
			[ '%s', '%s' ]
		);

		return false !== $result ? $slug : false;
	}

	/**
	 * Insert or update a item's data.
	 */
	public static function update( string $slug, array $data ): bool {
		$slug = self::key_for_item( $slug );
		wp_cache_delete( $slug, self::CACHE_GROUP );

		global $wpdb;
		$result = $wpdb->query(
			$wpdb->prepare(
				'INSERT INTO ' . self::table() . ' (slug, data)
				VALUES (%s, %s)
				ON DUPLICATE KEY UPDATE data = VALUES(data), updated_at = CURRENT_TIMESTAMP',
				$slug,
				wp_json_encode( $data )
			)
		);

		return false !== $result;
	}

	/**
	 * Delete a item.
	 */
	public static function delete( string $slug ): bool {
		$slug = self::key_for_item( $slug );
		wp_cache_delete( $slug, self::CACHE_GROUP );

		global $wpdb;
		$result = $wpdb->delete( self::table(), [ 'slug' => $slug ], [ '%s' ] );

		return false !== $result && $result > 0;
	}
}
