<?php
/**
 * Loads the React admin app: from the Vite dev server (with HMR) when it's
 * running, otherwise from the built production manifest.
 *
 * @package PluginBase
 */

namespace PluginBase\Admin;

use PluginBase\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	private Plugin $plugin;
	private Menu $menu;

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->menu   = $this->plugin->get_component('menu');
	}

	/**
	 * Hook into admin_enqueue_scripts.
	 */
	public function init(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Enqueue the app's JS/CSS, only on PluginBase's own admin page.
	 */
	public function enqueue( string $hook ): void {
		if ( $hook !== $this->menu->get_hook() ) {
			return;
		}

		$dev_mode = $this->plugin->is_dev_mode();

		if ( $dev_mode && file_exists( PLUGIN_BASE_PATH . 'dev/dev-server-asset.php' ) ) {
			require PLUGIN_BASE_PATH . 'dev/dev-server-asset.php';
			return;
		}

		$this->enqueue_production();
	}

	/**
	 * Load the built app from build/manifest.json.
	 */
	private function enqueue_production(): void {
		$manifest_path = PLUGIN_BASE_PATH . 'build/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			wp_die( esc_html__( 'The PluginBase build is missing. Run `npm run build` in the plugin directory.', 'plugin-base' ) );
		}

		$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );

		if ( ! isset( $manifest['src/main.tsx'] ) ) {
			wp_die( esc_html__( 'The PluginBase build is invalid. Run `npm run build` again.', 'plugin-base' ) );
		}

		$entry = $manifest['src/main.tsx'];

		wp_register_script_module( 'plugin-base-app', PLUGIN_BASE_URL . 'build/' . $entry['file'], [], PLUGIN_BASE_VERSION );
		wp_enqueue_script_module( 'plugin-base-app' );

		if ( ! empty( $entry['css'][0] ) ) {
			wp_register_style( 'plugin-base-app', PLUGIN_BASE_URL . 'build/' . $entry['css'][0], [], PLUGIN_BASE_VERSION );
			wp_enqueue_style( 'plugin-base-app' );
		}
	}
}
