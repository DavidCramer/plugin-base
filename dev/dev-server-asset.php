<?php
/**
 * Dev server asset loader.
 * Loads the admin app from the running Vite dev server instead of the
 * production manifest, with React Fast Refresh wired up manually since
 * this page isn't served by Vite itself.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$port = trim( (string) file_get_contents( PLUGIN_BASE_PATH . '.dev-server-running' ) );

?>
	<script type="module">
		import RefreshRuntime from 'http://localhost:<?php echo esc_js( $port ); ?>/@react-refresh';

		RefreshRuntime.injectIntoGlobalHook(window);
		window.$RefreshReg$ = () => { };
		window.$RefreshSig$ = () => (type) => type;
		window.__vite_plugin_react_preamble_installed__ = true;
	</script>
<?php

// Vite client for HMR.
wp_enqueue_script_module( 'plugin-base-vite-client', 'http://localhost:' . $port . '/@vite/client', [], null );

// Main app.
wp_enqueue_script_module( 'plugin-base-app', 'http://localhost:' . $port . '/src/main.tsx', [], null, [ 'in_footer' => true ] );
