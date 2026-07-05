<?php
/**
 * Main plugin class.
 *
 * @package PluginBase
 */

namespace PluginBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Dev mode flag. False, or the Vite dev server port as a string.
	 *
	 * @var bool|string
	 */
	private bool|string $dev_mode = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( file_exists( PLUGIN_BASE_PATH . '.dev-server-running' ) ) {
			$this->dev_mode = trim( file_get_contents( PLUGIN_BASE_PATH . '.dev-server-running' ) );
		}
	}

	/**
	 * Initialize the plugin. Hooked to plugins_loaded.
	 */
	public static function init(): void {
		if ( null !== self::$instance ) {
			return;
		}

		self::$instance = new self();
		self::$instance->boot();
	}

	/**
	 * Wire up the plugin's components.
	 */
	private function boot(): void {
		( new Route\RouteRegistrar() )->init();
		( new Admin\RestController() )->init();

		if ( is_admin() ) {
			$menu = new Admin\Menu();
			$menu->init();

			( new Admin\Assets( $this, $menu ) )->init();
		}
	}

	/**
	 * Whether the Vite dev server is running.
	 *
	 * @return bool|string False, or the dev server port.
	 */
	public function is_dev_mode(): bool|string {
		return $this->dev_mode;
	}

	/**
	 * Plugin activation.
	 */
	public static function activate(): void {
		Install::install();
	}

	/**
	 * Plugin deactivation.
	 */
	public static function deactivate(): void {
		// Config data is intentionally preserved on deactivation.
	}
}
