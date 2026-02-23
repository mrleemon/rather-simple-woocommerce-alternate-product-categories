<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block          The instance of the WP_Block class that represents the block being rendered.
 *
 * @package rather_simple_woocommerce_alternate_product_categories
 */

?>
<div
	<?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>
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

$queried_object = get_queried_object();
$term_id        = 0;
$parent_id      = 0;

if ( $queried_object instanceof WP_Term ) {
	$term_id   = $queried_object->term_id;
	$parent_id = ( $queried_object->parent > 0 ) ? $queried_object->parent : $term_id;
}

$cat_args = array(
	'taxonomy'   => 'product_cat',
	'orderby'    => 'name',
	'order'      => 'ASC',
	'hide_empty' => true,
	'child_of'   => $parent_id,
);

$terms = get_terms( $cat_args );

if ( ! empty( $terms ) ) {
	if ( $attributes['dropdown'] ) {
		$options = array(
			'hierarchical' => 1,
			'show_count'   => $attributes['count'] ? 1 : 0,
			'selected'     => $queried_object ? $queried_object->slug : '',
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