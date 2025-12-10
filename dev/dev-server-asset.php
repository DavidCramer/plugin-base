<?php
/**
 * Dev server asset loader.
 * Separates dev server assets from production assets.
 */

$port = file_get_contents( PLUGIN_BASE_PATH . '.dev-server-running' );

?>
	<script type="module">
      import RefreshRuntime from 'http://localhost:<?php echo $port; ?>/@react-refresh';

      RefreshRuntime.injectIntoGlobalHook(window);
      window.$RefreshReg$ = () => { };
      window.$RefreshSig$ = () => (type) => type;
      window.__vite_plugin_react_preamble_installed__ = true;
	</script>
<?php

// Vite client for HMR.
wp_enqueue_script_module( 'plugin-base-vite-client', 'http://localhost:' . $port . '/@vite/client', [], null );

// Main app.
wp_enqueue_script_module( 'plugin-base-admin', 'http://localhost:' . $port . '/admin/src/main.tsx', [], null );
