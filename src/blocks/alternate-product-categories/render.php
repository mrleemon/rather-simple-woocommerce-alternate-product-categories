<?php
	$block_attributes = get_block_wrapper_attributes()
?>
<div
	<?php echo $block_attributes; ?>
	<?php
	echo wp_interactivity_data_wp_context(
		array(
			'homeURL' => home_url( '/' ),
		)
	);
	?>
	data-wp-interactive="rswapc-store"
>

<?php
$term_id = get_queried_object()->term_id;
$term    = get_term( $term_id, 'product_cat' );

if ( $term && ! is_wp_error( $term ) ) {

	if ( $attributes['dropdown'] ) {

		if ( $term->parent > 0 ) {
			$cat_args = array(
				'taxonomy'   => 'product_cat',
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => true,
				'child_of'   => $term->parent,
				'show_count' => $attributes['count'] ? 1 : 0,
				'selected'   => $term ? $term->slug : '',
			);
		} else {
			$cat_args = array(
				'taxonomy'   => 'product_cat',
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => true,
				'child_of'   => $term_id,
				'show_count' => $attributes['count'] ? 1 : 0,
				'selected'   => $term ? $term->slug : '',
			);
		}

		$current_product_cat = isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '';
		$terms               = get_terms( $cat_args );

		echo "<select data-wp-on--change='actions.redirect' name='product_cat' class='dropdown_product_cat'>";
		echo '<option value="" ' . selected( $term_id, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
		echo wc_walk_category_dropdown_tree( $terms, 0, $cat_args );
		echo '</select>';

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
				echo '<li class="cat-item cat-item-' . esc_attr( $siblingcategory->term_id );
				echo $siblingcategory->term_id === $term_id ? ' current-cat">' : '">';
				echo '<a href="' . esc_url( get_term_link( $siblingcategory ) ) . '">' . $siblingcategory->name . '</a>';
				if ( $attributes['count'] ) {
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
				if ( $attributes['count'] ) {
					echo ' <span class="count">(' . $subcategory->count . ')</span>';
				}
				echo '</li>';
			}
		}

		echo '</ul>';

	}
}
?>
</div>