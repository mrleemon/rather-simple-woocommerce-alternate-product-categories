<?php
/**
 * Rather_Simple_WooCommerce_Alternate_Product_Categories_Widget class
 *
 * @package rather_simple_woocommerce_alternate_product_categories
 */

/**
 * Core class used to implement the widget.
 *
 * @see WP_Widget
 */
class Rather_Simple_WooCommerce_Alternate_Product_Categories_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'             => 'woocommerce widget_alternate_product_categories',
			'description'           => __( 'A really simple WooCommerce alternate product categories widget', 'rather-simple-woocommerce-alternate-product-categories' ),
			'show_instance_in_rest' => true,
		);
		parent::__construct( 'rswapc', __( 'WooCommerce Alternate Product Categories', 'rather-simple-woocommerce-alternate-product-categories' ), $widget_ops );
	}

	/**
	 * Output widget.
	 *
	 * @param array $args     Display arguments.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$title    = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		$dropdown = ! empty( $instance['dropdown'] ) ? true : false;
		$count    = ! empty( $instance['count'] ) ? true : false;

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$term_id = get_queried_object()->term_id;
		$term    = get_term( $term_id, 'product_cat' );

		if ( $term && ! is_wp_error( $term ) ) {

			if ( $dropdown ) {

				if ( $term->parent > 0 ) {
					$cat_args = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term->parent,
						'show_count' => $count ? 1 : 0,
						'selected'   => $term ? $term->slug : '',
					);

				} else {
					$cat_args = array(
						'taxonomy'   => 'product_cat',
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term_id,
						'show_count' => $count ? 1 : 0,
						'selected'   => $term ? $term->slug : '',
					);
				}

				$current_product_cat = isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '';
				$terms               = get_terms( $cat_args );

				$output  = "<select name='product_cat' class='dropdown_product_cat'>";
				$output .= '<option value="" ' . selected( $term_id, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
				$output .= wc_walk_category_dropdown_tree( $terms, 0, $cat_args );
				$output .= '</select>';

				echo $output;

				wc_enqueue_js(
					"
                    var dropdown = document.querySelector( '.dropdown_product_cat' );
					if ( dropdown ) {
						dropdown.addEventListener( 'change', function() {
                        	if ( this.value !== '' ) {
                            	var this_page = '';
                            	var home_url  = '" . esc_js( home_url( '/' ) ) . "';
                            	if ( home_url.indexOf( '?' ) > 0 ) {
                                	this_page = home_url + '&product_cat=' + this.value;
                            	} else {
	                                this_page = home_url + '?product_cat=' + this.value;
    	                        }
        	                    location.href = this_page;
            	            }
                    	});
					}
                "
				);

			} else {

				echo '<ul class="product-categories">';

				if ( $term->parent > 0 ) {

					$cat_args = array(
						'taxonomy'   => 'product_cat',
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term->parent,
					);

					$siblingcategories = get_terms( $cat_args );

					foreach ( $siblingcategories as $siblingcategory ) {
						if ( $siblingcategory->term_id === $term_id ) {
							echo '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . ' current-cat">';
						} else {
							echo '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id ) . '">';
						}

						echo '<a href="' . esc_url( get_term_link( $siblingcategory ) ) . '">' . $siblingcategory->name . '</a>';
						if ( $count ) {
							echo ' <span class="count">(' . $siblingcategory->count . ')</span>';
						}
						echo '</li>';
					}
				} else {

					$cat_args = array(
						'taxonomy'   => 'product_cat',
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'child_of'   => $term_id,
					);

					$subcategories = get_terms( $cat_args );

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

		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['count']    = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['dropdown'] = ! empty( $new_instance['dropdown'] ) ? 1 : 0;
		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		$count    = isset( $instance['count'] ) ? (bool) $instance['count'] : false;

		?>
			<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'rather-simple-woocommerce-alternate-product-categories' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
			<input class="checkbox" type="checkbox" <?php checked( $dropdown ); ?> id="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown' ) ); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>"><?php _e( 'Show as dropdown', 'rather-simple-woocommerce-alternate-product-categories' ); ?></label>
			</p>
			<p>
			<input class="checkbox" type="checkbox" <?php checked( $count ); ?> id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Show product counts', 'rather-simple-woocommerce-alternate-product-categories' ); ?></label>
			</p>
		<?php
	}
}

add_action(
	'widgets_init',
	function () {
		return register_widget( 'Rather_Simple_WooCommerce_Alternate_Product_Categories_Widget' );
	}
);
