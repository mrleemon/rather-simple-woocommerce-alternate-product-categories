<?php
/**
 * Plugin Name: Rather Simple WooCommerce Alternate Product Categories
 * Plugin URI:
 * Update URI: false
 * Version: 2.0
 * Requires at least: 5.8
 * Requires PHP: 7.0
 * WC tested up to: 7.1
 * Author: Oscar Ciutat
 * Author URI: http://oscarciutat.com/code/
 * Text Domain: rather-simple-woocommerce-alternate-product-categories
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
 * @package rather_simple_wooCommerce_alternate_product_categories
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

		add_action( 'init', array( $this, 'load_language' ) );
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
	 * Loads language
	 */
	public function load_language() {
		load_plugin_textdomain( 'rather-simple-woocommerce-alternate-product-categories', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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

		// Register the block by passing the location of block.json to register_block_type.
		register_block_type(
			__DIR__ . '/build/blocks/alternate-product-categories',
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);

		// Load translations.
		$script_handle = generate_block_asset_handle( 'occ/alternate-product-categories', 'editorScript' );
		wp_set_script_translations( $script_handle, 'rather-simple-woocommerce-alternate-product-categories', plugin_dir_path( __FILE__ ) . 'languages' );

	}

	/**
	 * Render block.
	 *
	 * @param array $attr     The block attributes.
	 * @param array $content  The content.
	 */
	public function render_block( $attr, $content ) {
		$html    = '';
		$term_id = get_queried_object()->term_id;
		$term    = get_term( $term_id, 'product_cat' );

		if ( $term && ! is_wp_error( $term ) ) {

			if ( $attr['dropdown'] ) {

				if ( $term->parent > 0 ) {
					$cat_args = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term->parent,
						'show_count' => $attr['count'] ? 1 : 0,
						'selected'   => $term ? $term->slug : '',
					);

				} else {
					$cat_args = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term_id,
						'show_count' => $attr['count'] ? 1 : 0,
						'selected'   => $term ? $term->slug : '',
					);
				}

				$current_product_cat = isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '';
				$terms               = get_terms( 'product_cat', $cat_args );

				$output  = "<select name='product_cat' class='dropdown_product_cat'>";
				$output .= '<option value="" ' . selected( $term_id, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
				$output .= wc_walk_category_dropdown_tree( $terms, 0, $cat_args );
				$output .= '</select>';

				$html .= $output;

				wc_enqueue_js(
					"
                    jQuery( '.dropdown_product_cat' ).on( 'change', function() {
                        if ( jQuery( this ).val() != '' ) {
                            var this_page = '';
                            var home_url  = '" . esc_js( home_url( '/' ) ) . "';
                            if ( home_url.indexOf( '?' ) > 0 ) {
                                this_page = home_url + '&product_cat=' + jQuery( this ).val();
                            } else {
                                this_page = home_url + '?product_cat=' + jQuery( this ).val();
                            }
                            location.href = this_page;
                        }
                    });
                "
				);

			} else {

				$html .= '<ul class="product-categories">';

				if ( $term->parent > 0 ) {

					$cat_args = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term->parent,
					);

					$siblingcategories = get_terms( 'product_cat', $cat_args );

					foreach ( $siblingcategories as $siblingcategory ) {
						if ( $siblingcategory->term_id === $term_id ) {
							$html .= '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . ' current-cat">';
						} else {
							$html .= '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . '">';
						}

						$html .= '<a href="' . esc_url( get_term_link( $siblingcategory ) ) . '">' . $siblingcategory->name . '</a>';
						if ( $attr['count'] ) {
							$html .= ' <span class="count">(' . $siblingcategory->count . ')</span>';
						}
						$html .= '</li>';
					}
				} else {

					$cat_args = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term_id,
					);

					$subcategories = get_terms( 'product_cat', $cat_args );

					foreach ( $subcategories as $subcategory ) {
						$html .= '<li class="cat-item cat-item-' . esc_attr( $subcategory->term_id ) . '"><a href="' . esc_url( get_term_link( $subcategory ) ) . '">' . $subcategory->name . '</a>';
						if ( $attr['count'] ) {
							$html .= ' <span class="count">(' . $subcategory->count . ')</span>';
						}
						$html .= '</li>';
					}
				}

				$html .= '</ul>';

			}
		}

		return $html;
	}

}

add_action( 'plugins_loaded', array( Rather_Simple_WooCommerce_Alternate_Product_Categories::get_instance(), 'plugin_setup' ) );
