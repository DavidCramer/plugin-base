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
	 * Dev mode flag.
	 *
	 * @var bool|integer
	 */
	private $dev_mode = false;



	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->dev_mode = file_exists( PLUGIN_BASE_PATH . '.dev-server-running' );
	}

	/**
	 * Initialize the plugin.
	 */
	public static function init() {
		$self = new self();

		// Initialize API.
		Api::init();

		// Admin-specific hooks.
		if ( is_admin() ) {
			add_action( 'admin_menu', [ $self, 'add_settings_page' ] );
			add_action( 'admin_enqueue_scripts', [ $self, 'enqueue_admin_assets' ] );
		}

	}

	/**
	 * Register the settings page.
	 */
	public function add_settings_page() {
		add_options_page(
				'Plugin Base',
				'Plugin Base',
				'manage_options',
				'plugin-base',
				[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Render the settings page HTML.
	 */
	public function render_settings_page() {
		$appData = [
				'apiUrl'  => rest_url( Api::NAMESPACE ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'version' => PLUGIN_BASE_VERSION,
				'devMode' => ! empty( $this->dev_mode ),
		];
		?>
		<div id="plugin-base-root" class="wrap">
			<div id="root" data-config="<?php echo esc_attr( wp_json_encode( $appData ) ); ?>"></div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin assets (CSS/JS).
	 *
	 * @param string $hook The current admin page hook.
	 *
	 * @since 0.1.0
	 *
	 */
	public function enqueue_admin_assets( string $hook ): void {
		// Only load on our admin pages.
		if ( strpos( $hook, 'plugin-base' ) === false ) {
			return;
		}


		// In development mode, load from Vite dev server.
		if ( $this->dev_mode ) {
			require_once PLUGIN_BASE_PATH . 'dev/dev-server-asset.php';

			return;
		}

		$asset_path    = PLUGIN_BASE_URL . 'admin/build/js/main.js';
		$manifest_path = PLUGIN_BASE_PATH . 'admin/build/.vite/manifest.json';
		if ( ! file_exists( $manifest_path ) ) {
			wp_die( __( 'The plugin-base build is missing. Please run the build process.', 'plugin-base' ) );
		}
		$manifest = json_decode( file_get_contents( $manifest_path ), true );
		if ( ! isset( $manifest['admin/src/main.tsx'] ) ) {
			wp_die( __( 'The plugin-base build is invalid. Please run the build process again.', 'plugin-base' ) );
		}
		$js_path  = PLUGIN_BASE_URL . 'admin/build/' . $manifest['admin/src/main.tsx']['file'];
		$css_path = isset( $manifest['admin/src/main.tsx']['css'][0] )
			? PLUGIN_BASE_URL . 'admin/build/' . $manifest['admin/src/main.tsx']['css'][0]
			: '';

		// Production mode - load built assets.
		// Note: In production, we'll need to parse the manifest to get hashed filenames.
		wp_enqueue_script(
				'plugin-base-admin',
				$js_path,
				[],
				PLUGIN_BASE_VERSION,
				true
		);

		// Enqueue CSS.
		if ( $css_path ) {
			wp_enqueue_style(
					'plugin-base-admin',
					$css_path,
					[],
					PLUGIN_BASE_VERSION
			);
		}


	}
}
