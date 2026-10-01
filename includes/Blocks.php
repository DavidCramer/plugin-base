<?php
/**
 * Main Blocks class.
 *
 * @package PluginBase
 */

namespace PluginBase;

/**
 * Blocks class.
 */
class Blocks {

	/**
	 * Holds the Plugin instance.
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin The Plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}


	/**
	 * Init class.
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Register custom blocks.
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$manifest = include SJC_CONTENT_TYPES_PATH . 'blocks/blocks-manifest.php';
		$keys     = array_keys( $manifest );
		foreach ( $keys as $block ) {
			register_block_type( SJC_CONTENT_TYPES_PATH . 'blocks/' . $block );
		}
	}
}
