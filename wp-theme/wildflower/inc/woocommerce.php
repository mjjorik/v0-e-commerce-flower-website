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

/**
 * Whether the current request is a browsable WooCommerce catalog archive.
 *
 * @return bool
 */
function wildflower_is_catalog_archive() {
	return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
}

/**
 * Build the archive navigation from the same curated list as the Shop menu.
 *
 * @return array<int,array{url:string,label:string,is_all:bool}>
 */
function wildflower_catalog_navigation_items() {
	if ( ! function_exists( 'wc_get_page_permalink' ) ) {
		return array();
	}

	$shop_url = wc_get_page_permalink( 'shop' );
	if ( ! $shop_url ) {
		return array();
	}

	$items = array(
		array(
			'url'    => $shop_url,
			'label'  => __( 'All flowers', 'wildflower' ),
			'is_all' => true,
		),
	);

	if ( function_exists( 'wildflower_shop_menu' ) ) {
		foreach ( wildflower_shop_menu() as $item ) {
			if ( empty( $item[0] ) || empty( $item[1] ) ) {
				continue;
			}
			$items[] = array(
				'url'    => (string) $item[0],
				'label'  => (string) $item[1],
				'is_all' => false,
			);
		}
	}

	return $items;
}

/**
 * Determine whether a catalog-navigation link represents the current view.
 *
 * @param string $target_url Navigation target.
 * @param bool   $is_all     Whether this is the unfiltered Shop link.
 * @return bool
 */
function wildflower_catalog_navigation_is_active( $target_url, $is_all ) {
	$target_query = array();
	$target_parts = wp_parse_url( $target_url );
	if ( ! empty( $target_parts['query'] ) ) {
		parse_str( $target_parts['query'], $target_query );
	}

	$current_orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_cat     = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! empty( $target_query['orderby'] ) ) {
		return is_shop() && sanitize_key( $target_query['orderby'] ) === $current_orderby;
	}

	if ( ! empty( $target_query['product_cat'] ) ) {
		$target_cat = sanitize_title( $target_query['product_cat'] );
		return ( function_exists( 'is_product_category' ) && is_product_category( $target_cat ) ) || $target_cat === $current_cat;
	}

	if ( is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$current_url = get_term_link( $term );
			if ( ! is_wp_error( $current_url ) ) {
				$current_path = untrailingslashit( (string) wp_parse_url( $current_url, PHP_URL_PATH ) );
				$target_path  = untrailingslashit( (string) wp_parse_url( $target_url, PHP_URL_PATH ) );
				return '' !== $current_path && $current_path === $target_path;
			}
		}
	}

	if ( ! $is_all || ! is_shop() || '' !== $current_cat ) {
		return false;
	}

	// Price/name sorting still belongs to All flowers. Only the two curated
	// sort links take over its active state.
	return ! in_array( $current_orderby, array( 'popularity', 'date' ), true );
}

/**
 * Render the shared horizontal catalog navigation.
 *
 * @param string $position Either top or bottom.
 */
function wildflower_catalog_navigation( $position ) {
	if ( ! wildflower_is_catalog_archive() ) {
		return;
	}

	$items = wildflower_catalog_navigation_items();
	if ( empty( $items ) ) {
		return;
	}

	$is_bottom = 'bottom' === $position;
	$title     = $is_bottom ? __( 'Continue browsing', 'wildflower' ) : __( 'Browse the shop', 'wildflower' );
	$label     = $is_bottom ? __( 'Shop sections, repeated', 'wildflower' ) : __( 'Shop sections', 'wildflower' );
	?>
	<nav class="wf-shop-sections wf-shop-sections--<?php echo esc_attr( $position ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
		<div class="wf-shop-sections__head">
			<span class="wf-shop-sections__title"><?php echo esc_html( $title ); ?></span>
			<span class="wf-shop-sections__hint"><?php esc_html_e( 'Swipe to explore', 'wildflower' ); ?></span>
		</div>
		<div class="wf-shop-sections__viewport">
			<div class="wf-shop-sections__track">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $active = wildflower_catalog_navigation_is_active( $item['url'], $item['is_all'] ); ?>
					<a class="wf-shop-sections__link<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
						<span class="wf-shop-sections__index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<span><?php echo esc_html( $item['label'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</nav>
	<?php
}

function wildflower_catalog_navigation_top() {
	wildflower_catalog_navigation( 'top' );
}
add_action( 'woocommerce_shop_loop_header', 'wildflower_catalog_navigation_top', 20 );

function wildflower_catalog_navigation_bottom() {
	wildflower_catalog_navigation( 'bottom' );
}
add_action( 'woocommerce_after_main_content', 'wildflower_catalog_navigation_bottom', 5 );

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

// Open the media wrapper. It is a <div> (not an <a>) because it holds the
// photo slider's arrow <button>s, and an <a> may not contain buttons. The
// data-href lets JS open the product on a tap/click (main.js), while the
// product title below is a real <a> for keyboard, SEO and no-JS access.
add_action(
	'woocommerce_before_shop_loop_item',
	function () {
		echo '<div class="product__media" data-href="' . esc_url( get_permalink() ) . '">';
	},
	10
);

// After thumbnail + sale flash: a visual "View bouquet" hover label (not a
// link — navigation is handled by the media tap and the title link), then
// close media and open the text body (title + price on the left).
add_action(
	'woocommerce_before_shop_loop_item_title',
	function () {
		echo '<span class="product__view" aria-hidden="true"><span>' . esc_html__( 'View bouquet', 'wildflower' ) . '</span></span>';
		echo '</div><div class="product__body"><div class="product__info">';
	},
	20
);

// Make the loop title a real link to the product (accessible + crawlable).
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action(
	'woocommerce_shop_loop_item_title',
	function () {
		echo '<h2 class="woocommerce-loop-product__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>';
	},
	10
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

/* ------------------------------------------------------------------
   Per-card photo slider: replace the single loop thumbnail with a
   swipeable gallery (featured image + product gallery images). Flip
   with arrows on desktop, native swipe on touch. Falls back to a
   single image (no chrome) or the placeholder when there's <2 images.
   ------------------------------------------------------------------ */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'wildflower_loop_product_gallery', 10 );

function wildflower_loop_product_gallery() {
	global $product;
	if ( ! $product ) {
		return;
	}

	$ids      = array();
	$featured = $product->get_image_id();
	if ( $featured ) {
		$ids[] = (int) $featured;
	}
	foreach ( (array) $product->get_gallery_image_ids() as $gid ) {
		$gid = (int) $gid;
		if ( $gid && ! in_array( $gid, $ids, true ) ) {
			$ids[] = $gid;
		}
	}

	// No images → theme flower placeholder.
	if ( empty( $ids ) ) {
		echo '<span class="media-fallback" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
		return;
	}

	// One image → plain, no slider chrome.
	if ( count( $ids ) < 2 ) {
		echo wp_get_attachment_image( $ids[0], 'woocommerce_thumbnail', false, array( 'class' => 'product__img', 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore
		return;
	}

	// Multiple images → slider.
	$stroke = 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
	echo '<div class="card-slider" data-card-slider>';
	echo '<div class="card-slider__track" data-card-slider-track>';
	foreach ( $ids as $i => $id ) {
		echo '<div class="card-slider__slide">';
		echo wp_get_attachment_image( $id, 'woocommerce_thumbnail', false, array( 'class' => 'product__img', 'loading' => 0 === $i ? 'eager' : 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore
		echo '</div>';
	}
	echo '</div>';
	echo '<button type="button" class="card-slider__arrow card-slider__arrow--prev" data-card-prev aria-label="' . esc_attr__( 'Previous photo', 'wildflower' ) . '"><svg width="18" height="18" viewBox="0 0 24 24" ' . $stroke . '><path d="M15 18l-6-6 6-6"/></svg></button>'; // phpcs:ignore
	echo '<button type="button" class="card-slider__arrow card-slider__arrow--next" data-card-next aria-label="' . esc_attr__( 'Next photo', 'wildflower' ) . '"><svg width="18" height="18" viewBox="0 0 24 24" ' . $stroke . '><path d="M9 6l6 6-6 6"/></svg></button>'; // phpcs:ignore
	echo '<div class="card-slider__dots" aria-hidden="true">';
	foreach ( $ids as $i => $id ) {
		echo '<span class="card-slider__dot' . ( 0 === $i ? ' is-active' : '' ) . '"></span>';
	}
	echo '</div>';
	echo '</div>';
}

/* Friendlier add-to-cart label in loops. */
add_filter(
	'woocommerce_product_add_to_cart_text',
	function ( $text, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			return __( 'Add to cart', 'wildflower' );
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

/* Checkout: a gift-card message field (in addition to order notes). */
add_action(
	'woocommerce_after_order_notes',
	function ( $checkout ) {
		echo '<div class="wf-card-message">';
		woocommerce_form_field(
			'wildflower_card_message',
			array(
				'type'        => 'textarea',
				'class'       => array( 'form-row-wide' ),
				'label'       => __( 'Message on the gift card', 'wildflower' ),
				'placeholder' => __( 'What should we handwrite on the card? (optional)', 'wildflower' ),
				'required'    => false,
			),
			$checkout->get_value( 'wildflower_card_message' )
		);
		echo '</div>';
	}
);
add_action(
	'woocommerce_checkout_create_order',
	function ( $order ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the checkout nonce.
		if ( ! empty( $_POST['wildflower_card_message'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$order->update_meta_data( '_wildflower_card_message', sanitize_textarea_field( wp_unslash( $_POST['wildflower_card_message'] ) ) );
		}
	}
);
add_action(
	'woocommerce_admin_order_data_after_billing_address',
	function ( $order ) {
		$msg = $order->get_meta( '_wildflower_card_message' );
		if ( $msg ) {
			echo '<p><strong>' . esc_html__( 'Gift card message:', 'wildflower' ) . '</strong><br>' . nl2br( esc_html( $msg ) ) . '</p>';
		}
	}
);
add_filter(
	'woocommerce_email_order_meta_fields',
	function ( $fields, $sent_to_admin, $order ) {
		$msg = $order->get_meta( '_wildflower_card_message' );
		if ( $msg ) {
			$fields['wildflower_card_message'] = array(
				'label' => __( 'Gift card message', 'wildflower' ),
				'value' => $msg,
			);
		}
		return $fields;
	},
	10,
	3
);

/* Remove product reviews entirely — no Reviews tab, no star rating. */
add_filter(
	'woocommerce_product_tabs',
	function ( $tabs ) {
		unset( $tabs['reviews'] );
		return $tabs;
	},
	98
);
add_filter( 'woocommerce_product_review_comment_form_args', '__return_empty_array' );
add_filter( 'woocommerce_product_get_rating_html', '__return_empty_string' );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );

/* Move the sale flash a touch and rename it. */
add_filter(
	'woocommerce_sale_flash',
	function () {
		return '<span class="badge badge--accent">' . esc_html__( 'Seasonal', 'wildflower' ) . '</span>';
	}
);
