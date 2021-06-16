<?php
/*
Plugin Name: Rather Simple WooCommerce Alternate Product Categories
Plugin URI:
Update URI: false
Description: A really simple WooCommerce alternate product categories widget and block.
Version: 2.0
WC tested up to: 4.2
Author: Oscar Ciutat
Author URI: http://oscarciutat.com/code/
Text Domain: rather-simple-woocommerce-alternate-product-categories
License: GPLv2 or later

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Rather_Simple_WooCommerce_Alternate_Product_Categories {

    /**
     * Plugin instance.
     *
     * @since 1.0
     *
     */
    protected static $instance = null;
    
    /**
     * Access this plugin’s working instance
     *
     * @since 1.0
     *
     */
    public static function get_instance() {
        
        if ( !self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;

    }
    
    /**
     * Used for regular plugin work.
     *
     * @since 1.0
     *
     */
    public function plugin_setup() {

        $this->includes();

        add_action( 'init', array( $this, 'load_language' ) );
        add_action( 'init', array( $this, 'register_block' ) );

    }
   
    /**
     * Constructor. Intentionally left empty and public.
     *
     * @since 1.0
     *
     */
    public function __construct() {}
    
    /**
     * Includes required core files used in admin and on the frontend.
     *
     * @since 1.0
     *
     */
    protected function includes() {
        //require_once 'include/rather-simple-woocommerce-alternate-product-categories-widget';
    }
    
    /**
     * Loads language
     *
     * @since 1.0
     *
     */
    public function load_language() {
        load_plugin_textdomain( 'rather-simple-woocommerce-alternate-product-categories', '', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Registers block
     *
     * @since 1.0
     *
     */
    public function register_block() {

        if ( ! function_exists( 'register_block_type' ) ) {
            // The block editor is not active.
            return;
        }

        $dir = dirname( __FILE__ );
        $script_asset_path = "$dir/build/index.asset.php";
        if ( ! file_exists( $script_asset_path ) ) {
            throw new Error(
                'You need to run `npm start` or `npm run build` for the block first.'
            );
        }
        $script_asset = require( $script_asset_path );
        
        wp_register_style(
            'rather-simple-woocommerce-alternate-product-categories-frontend',
            plugins_url( 'build/style-index.css', __FILE__ ),
            array(),
            filemtime( plugin_dir_path( __FILE__ ) . 'build/style-index.css' )
        );
        wp_register_script(
            'rather-simple-woocommerce-alternate-product-categories-block',
            plugins_url( 'build/index.js', __FILE__ ),
            $script_asset['dependencies'],
            filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' )
        );

        register_block_type( 'occ/alternate-product-categories', array(
            'style'           => 'rather-simple-woocommerce-alternate-product-categories-frontend',
            'editor_script'   => 'rather-simple-woocommerce-alternate-product-categories-block',
            'render_callback' => array( $this, 'render_block' ),
            'attributes' => array(
                'title' => array(
                    'type'    => 'string',
                ),
                'count' => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'dropdown' => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
            ),
        ) );

        wp_set_script_translations( 'rather-simple-woocommerce-alternate-product-categories-block', 'rather-simple-woocommerce-alternate-product-categories', plugin_dir_path( __FILE__ ) . 'languages' );

    }

    /**
     * render_block
     */
    public function render_block( $attr, $content ) {
        $html = '';

        $term_id = get_queried_object()->term_id;
        $term = get_term( $term_id, 'product_cat' );

        if ( $term && !is_wp_error( $term ) ) {
                    
            if ( $attr['dropdown'] ) {

                if ( $term->parent > 0 ) { 
                    $args = array(
                        'orderby'       => 'name', 
                        'order'         => 'ASC',
                        'hide_empty'    => true, 
                        'child_of'      => $term->parent,
                        'show_count'    => $attr['count'] ? 1 : 0,
                        'selected'      => $term ? $term->slug : ''
                    ); 
                
                } else {
                    $args = array(
                        'orderby'       => 'name', 
                        'order'         => 'ASC',
                        'hide_empty'    => true, 
                        'child_of'      => $term_id,
                        'show_count'    => $attr['count'] ? 1 : 0,
                        'selected'      => $term ? $term->slug : ''
                    );
                }
                
                $current_product_cat = isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '';
                $terms = get_terms( 'product_cat', $args );
                
                $output  = "<select name='product_cat' class='dropdown_product_cat'>";
                $output .= '<option value="" ' . selected( $term_id, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
                $output .= wc_walk_category_dropdown_tree( $terms, 0, $args );
                $output .= "</select>";

                $html .= $output;
                
                wc_enqueue_js( "
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
                " );
                
                
            } else {
                
                $html .= '<ul class="product-categories">';

                if ( $term->parent > 0 ) { 

                    $args = array(
                        'orderby'       => 'name', 
                        'order'         => 'ASC',
                        'hide_empty'    => true, 
                        'child_of'      => $term->parent,
                    ); 

                    $siblingcategories = get_terms( 'product_cat', $args );

                    foreach ( $siblingcategories as $siblingcategory ) { 
                        if ($siblingcategory->term_id == $term_id ) {
                            $html .= '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . ' current-cat">';
                        } else {
                            $html .= '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . '">';
                        }
                    
                        $html .= '<a href="' . get_term_link( $siblingcategory ) . '">' . $siblingcategory->name . '</a>';
                        if ( $attr['count'] ) {
                            $html .= ' <span class="count">(' .  $siblingcategory->count . ')</span>';
                        }
                        $html .= '</li>';
                    }

                } else { 
            
                    $args = array(
                        'orderby'    => 'name', 
                        'order'      => 'ASC',
                        'hide_empty' => true, 
                        'child_of'   => $term_id 
                    );

                    $subcategories = get_terms( 'product_cat', $args );

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
