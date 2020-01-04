<?php
/*
Plugin Name: Rather Simple WooCommerce Alternate Product Categories
Plugin URI:
Description: A really simple WooCommerce alternate product categories widget.
Version: 1.0
WC tested up to: 3.6.5
Author: Oscar Ciutat
Author URI: http://oscarciutat.com/code/
Text Domain: rswapc-widget
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

class Rather_Simple_WooCommerce_Alternate_Product_Categories extends WP_Widget {
    
    /**
     * Constructor.
     */
    function __construct() {
        load_plugin_textdomain( 'rswapc-widget', '', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        $widget_ops = array(
            'classname' => 'woocommerce widget_alternate_product_categories',
            'description' => __( 'A really simple WooCommerce alternate product categories widget', 'rswapc-widget' )
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent::__construct( 'rswapc', __( 'WooCommerce Alternate Product Categories', 'rswapc-widget' ), $widget_ops, $control_ops );
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args
     * @param array $instance
     */
    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
        $count = $instance['count'] ? true : false;
        $dropdown = $instance['dropdown'] ? true : false;
        
        echo $before_widget;
        
        if ( !empty( $title ) ) { 
            echo $before_title . $title . $after_title;
        };
        
        $term_id = get_queried_object()->term_id;
        $term = get_term( $term_id, 'product_cat' );

        if ( $term && !is_wp_error( $term ) ) {
                    
            if ( $dropdown ) {

                if ( $term->parent > 0 ) { 
                    $args = array(
                        'orderby'       => 'name', 
                        'order'         => 'ASC',
                        'hide_empty'    => true, 
                        'child_of'      => $term->parent,
                        'show_count'    => $count ? 1 : 0,
                        'selected'      => $term ? $term->slug : ''
                    ); 
                
                } else {
                    $args = array(
                        'orderby'       => 'name', 
                        'order'         => 'ASC',
                        'hide_empty'    => true, 
                        'child_of'      => $term_id,
                        'show_count'    => $count ? 1 : 0,
                        'selected'      => $term ? $term->slug : ''
                    );
                }
                
                $current_product_cat = isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '';
                $terms = get_terms( 'product_cat', $args );
                
                $output  = "<select name='product_cat' class='dropdown_product_cat'>";
                $output .= '<option value="" ' . selected( $term_id, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
                $output .= wc_walk_category_dropdown_tree( $terms, 0, $args );
                $output .= "</select>";

                echo $output;
                
                wc_enqueue_js( "
                    jQuery( '.dropdown_product_cat' ).change( function() {
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
                
                echo '<ul class="product-categories">';

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
                            echo '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . ' current-cat">';
                        } else {
                            echo '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . '">';
                        }
                    
                        echo '<a href="' . get_term_link( $siblingcategory ) . '">' . $siblingcategory->name . '</a>';
                        if ( $count ) {
                            echo ' <span class="count">(' .  $siblingcategory->count . ')</span>';
                        }
                        echo '</li>';
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
                        echo '<li class="cat-item cat-item-' . esc_attr( $subcategory->term_id ) . '"><a href="' . esc_url( get_term_link( $subcategory ) ) . '">' . $subcategory->name . '</a>';
                        if ( $count ) {
                            echo ' <span class="count">(' . $subcategory->count . ')</span>';
                        }
                        echo '</li>';
                    }

                }
            
                echo '</ul>';
                
            }

        }

        echo $after_widget;
    }

    /**
     * Updates a particular instance of a widget.
     *
     * @see    WP_Widget->update
     * @param  array $new_instance
     * @param  array $old_instance
     * @return array
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['count'] = $new_instance['count'];
        $instance['dropdown'] = $new_instance['dropdown'];
        return $instance;
    }

    /**
     * Outputs the settings update form.
     *
     * @see   WP_Widget->form
     * @param array $instance
     */
     function form( $instance ) {
        $instance = wp_parse_args( ( array ) $instance, array( 'title' => '', 'count' => 'off', 'dropdown' => 'off' ) );
        $title = strip_tags( $instance['title'] );
        $count = $instance['count'];
        $dropdown = $instance['dropdown'];
        
        ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'rswapc-widget' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
            <input class="checkbox" type="checkbox" <?php checked( $dropdown, 'on' ); ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" /> 
            <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Show as dropdown', 'rswapc-widget' ); ?></label>
            </p>
            <p>
            <input class="checkbox" type="checkbox" <?php checked( $count, 'on' ); ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" /> 
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show product counts', 'rswapc-widget' ); ?></label>
            </p>
        <?php
    }
}

add_action( 'widgets_init', function() { return register_widget( 'Rather_Simple_WooCommerce_Alternate_Product_Categories' ); } );