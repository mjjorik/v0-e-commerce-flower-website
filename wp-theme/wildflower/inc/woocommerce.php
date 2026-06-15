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

/* Cross-sell / upsell headings — framed as add-on selling. */
add_filter( 'woocommerce_product_upsells_products_heading', function () { return __( 'Complete the gift', 'wildflower' ); } );
add_filter( 'woocommerce_product_related_products_heading', function () { return __( 'You may also like', 'wildflower' ); } );
add_filter( 'woocommerce_cross_sells_columns', function () { return 3; } );
add_filter( 'woocommerce_cross_sells_total', function () { return 3; } );

/* ============================================================
   Product card — rebuild the loop into an editorial card:
   image with a circular "+" add button in the corner and a
   hover gradient revealing "View bouquet"; title + price below.
   Works on the shop archive and the homepage [products] scroller.
   ============================================================ */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Open the media wrapper (no enclosing <a>, so the add button can live inside).
add_action(
	'woocommerce_before_shop_loop_item',
	function () {
		echo '<div class="product__media">';
	},
	10
);

// After thumbnail + sale flash: full-cover hover link, then close media and
// open the text body (title + price on the left).
add_action(
	'woocommerce_before_shop_loop_item_title',
	function () {
		echo '<a class="product__view" href="' . esc_url( get_permalink() ) . '"><span>' . esc_html__( 'View bouquet', 'wildflower' ) . '</span></a>';
		echo '</div><div class="product__body"><div class="product__info">';
	},
	20
);

// Close the info, then the circular add button sits to the RIGHT of name/price.
add_action(
	'woocommerce_after_shop_loop_item_title',
	function () {
		echo '</div>';
		woocommerce_template_loop_add_to_cart();
		echo '</div>';
	},
	20
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
