<?php
	$block_attributes = get_block_wrapper_attributes()
?>
<div
	<?php echo wp_kses_data( $block_attributes ); ?>
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
$term_id = isset( get_queried_object()->term_id ) ? get_queried_object()->term_id : 0;
$term    = get_term( $term_id, 'product_cat' );

if ( $term && ! is_wp_error( $term ) ) {

	$parent_id = ( $term->parent > 0 ) ? $term->parent : $term_id;

	$cat_args = array(
		'taxonomy'   => 'product_cat',
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => true,
		'child_of'   => $parent_id,
	);

	$terms = get_terms( $cat_args );

	if ( $attributes['dropdown'] ) {

		$options = array(
			'show_count' => $attributes['count'] ? 1 : 0,
			'selected'   => $term ? $term->slug : '',
		);

		echo '<select data-wp-on--change="actions.redirect" name="product_cat" class="dropdown_product_cat">';
		echo '<option value="" ' . selected( $term_id, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
		echo wc_walk_category_dropdown_tree( $terms, 0, $options );
		echo '</select>';

	} else {

		echo '<ul class="product-categories">';
		foreach ( $terms as $term ) {
			echo '<li class="cat-item cat-item-' . esc_attr( $term->term_id ) . ( $term->term_id === $term_id ? ' current-cat' : '' ) . '">';
			echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . $term->name . '</a>';
			if ( $attributes['count'] ) {
				echo ' <span class="count">(' . $term->count . ')</span>';
			}
			echo '</li>';
		}
		echo '</ul>';

	}
}
?>
</div>