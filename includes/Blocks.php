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
		add_filter( 'block_categories_all', [ $this, 'register_block_category' ] );
	}

	/**
	 * Register custom blocks.
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$manifest = include PLUGIN_BASE_PATH . 'blocks/blocks-manifest.php';
		$keys     = array_keys( $manifest );
		foreach ( $keys as $block ) {
			register_block_type( PLUGIN_BASE_PATH . 'blocks/' . $block );
		}
	}

	/**
	 * Register block category.
	 *
	 * @param array $categories The existing categories.
	 *
	 * @return array The modified categories.
	 */
	public function register_block_category( $categories = [] ) {

		$pg_category = [
			'slug'  => 'plugin-base',
			'title' => __( 'Plugin Base', 'plugin-base' ),
		];

		// Add the custom category to the beginning of the categories array.
		array_unshift( $categories, $pg_category );

		return $categories;
	}

}
