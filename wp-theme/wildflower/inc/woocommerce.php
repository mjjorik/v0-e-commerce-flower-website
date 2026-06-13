<?php
/**
 * WooCommerce integration & layout tweaks.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Replace default content wrappers with our container. */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

function wildflower_wc_wrapper_start() {
	echo '<div class="container" style="padding-block:2rem 5rem;">';
}
add_action( 'woocommerce_before_main_content', 'wildflower_wc_wrapper_start', 10 );

function wildflower_wc_wrapper_end() {
	echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'wildflower_wc_wrapper_end', 10 );

/* Remove the default sidebar — design is full-width grid. */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/* Loop: 3 columns, 12 per page. */
add_filter( 'loop_shop_columns', function () { return 3; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );

/* Related products: 3. */
add_filter(
	'woocommerce_output_related_products_args',
	function ( $args ) {
		$args['posts_per_page'] = 3;
		$args['columns']        = 3;
		return $args;
	}
);

/* Friendlier add-to-cart label in loops. */
add_filter(
	'woocommerce_product_add_to_cart_text',
	function ( $text, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			return __( 'Add to basket', 'wildflower' );
		}
		return $text;
	},
	10,
	2
);

/* Live-update the header cart count via Woo fragments. */
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		ob_start();
		?>
		<span class="cart-toggle__count" data-cart-count><?php echo esc_html( wildflower_cart_count() ); ?></span>
		<?php
		$fragments['span[data-cart-count]'] = ob_get_clean();
		return $fragments;
	}
);

/* Move the sale flash a touch and rename it. */
add_filter(
	'woocommerce_sale_flash',
	function () {
		return '<span class="badge badge--accent">' . esc_html__( 'Seasonal', 'wildflower' ) . '</span>';
	}
);
