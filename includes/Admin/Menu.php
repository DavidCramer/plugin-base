<?php
/**
 * Registers the PluginBase admin page and its React mount point.
 *
 * @package PluginBase
 */

namespace PluginBase\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Menu {

	/**
	 * The admin page's hook suffix, once registered.
	 *
	 * @var string
	 */
	private string $hook = '';

	/**
	 * Hook into admin_menu.
	 */
	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register' ] );
	}

	/**
	 * Register the admin page.
	 */
	public function register(): void {
		$this->hook = add_menu_page(
			__( 'Plugin Base', 'plugin-base' ),
			__( 'Plugin Base', 'plugin-base' ),
			apply_filters( 'plugin_base_manage_capability', 'manage_options' ),
			'plugin-base',
			[ $this, 'render' ],
			'dashicons-admin-plugins'
		);
	}

	/**
	 * The hook suffix of the registered admin page, used to gate asset loading.
	 */
	public function get_hook(): string {
		return $this->hook;
	}

	/**
	 * Render the React mount point plus the data payload it reads on boot.
	 */
	public function render(): void {
		$data = [
			'apiBase' => esc_url_raw( rest_url( 'plugin-base/v1' ) ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'version' => PLUGIN_BASE_VERSION,
		];
		?>
		<div id="plugin-base-root" class="plugin-base-app"></div>
		<script type="application/json" id="plugin-base-data"><?php echo wp_json_encode( $data ); ?></script>
		<?php
	}
}
