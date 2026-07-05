<?php
/**
 * Handles plugin activation / database installation.
 *
 * @package PluginBase
 */

namespace PluginBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Install {

	/**
	 * Current database schema version.
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Create/upgrade the database table. Safe to call repeatedly (dbDelta is idempotent).
	 */
	public static function install(): void {
		global $wpdb;

		$table           = $wpdb->prefix . 'plugin_base_config';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table (
			id         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			slug varchar(191) NOT NULL,
			data       JSON NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY slug (slug)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'plugin_base_db_version', self::DB_VERSION );
	}

	/**
	 * Install/upgrade the table if the stored schema version is out of date.
	 */
	public static function maybe_upgrade(): void {
		if ( get_option( 'plugin_base_db_version' ) !== self::DB_VERSION ) {
			self::install();
		}
	}
}
