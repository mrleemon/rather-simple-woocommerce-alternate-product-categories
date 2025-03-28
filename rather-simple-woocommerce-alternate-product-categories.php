<?php
/**
 * Plugin Name: Rather Simple WooCommerce Alternate Product Categories
 * Plugin URI:
 * Update URI: false
 * Version: 2.0
 * Requires at least: 6.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC tested up to: 7.1
 * Author: Oscar Ciutat
 * Author URI: http://oscarciutat.com/code/
 * Text Domain: rather-simple-woocommerce-alternate-product-categories
 * Domain Path: /languages
 * Description: A really simple WooCommerce alternate product categories widget and block.
 * License: GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package rather_simple_woocommerce_alternate_product_categories
 */

/**
 * Core class used to implement the plugin.
 */
class Rather_Simple_WooCommerce_Alternate_Product_Categories {

	/**
	 * Plugin instance.
	 *
	 * @var object $instance
	 */
	protected static $instance = null;

	/**
	 * Access this plugin’s working instance.
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Used for regular plugin work.
	 */
	public function plugin_setup() {

		$this->includes();

		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_wchpos_compatibility' ) );
	}

	/**
	 * Constructor. Intentionally left empty and public.
	 */
	public function __construct() {}

	/**
	 * Includes required core files used in admin and on the frontend.
	 */
	protected function includes() {
		require_once 'include/class-rather-simple-woocommerce-alternate-product-categories-widget.php';
	}

	/**
	 * Declare WooCommerce High-Performance Order Storage compatibility
	 */
	public function declare_wchpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

	/**
	 * Registers block
	 *
	 * @throws Error If block is not built.
	 */
	public function register_block() {

		if ( ! function_exists( 'register_block_type' ) ) {
			// The block editor is not active.
			return;
		}

		// Register the block.
		register_block_type( __DIR__ . '/build/blocks/alternate-product-categories' );

		// Load translations.
		$script_handle = generate_block_asset_handle( 'occ/alternate-product-categories', 'editorScript' );
		wp_set_script_translations( $script_handle, 'rather-simple-woocommerce-alternate-product-categories', plugin_dir_path( __FILE__ ) . 'languages' );
	}
}

add_action( 'plugins_loaded', array( Rather_Simple_WooCommerce_Alternate_Product_Categories::get_instance(), 'plugin_setup' ) );
