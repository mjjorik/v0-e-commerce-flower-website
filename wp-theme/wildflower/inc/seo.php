<?php
/**
 * Structured data (JSON-LD) for SEO / GEO / E-E-A-T.
 *
 * If you run Rank Math or Yoast, they may also output schema. You can disable
 * the theme's Product schema with:
 *   add_filter( 'wildflower_output_product_schema', '__return_false' );
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print a JSON-LD script block.
 *
 * @param array $data Schema.org structured data.
 */
function wildflower_print_jsonld( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
}

/**
 * LocalBusiness (Florist) + WebSite — output in <head> on every page.
 */
function wildflower_head_jsonld() {
	$home  = home_url( '/' );
	$brand = wildflower_brand();

	$business = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Florist',
		'@id'         => $home . '#business',
		'name'        => $brand['name'],
		'url'         => $home,
		'email'       => $brand['email'],
		'telephone'   => $brand['phone'],
		'description' => 'Farm-fresh bouquets and weekly flower subscriptions, hand-delivered same-day across Greater Boston.',
		'priceRange'  => '$$',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Boston',
			'addressRegion'   => 'MA',
			'addressCountry'  => 'US',
		),
		'areaServed'  => array( 'Boston', 'Cambridge', 'Somerville', 'Brookline', 'Newton', 'Medford', 'Arlington' ),
		'openingHoursSpecification' => array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
			'opens'     => '08:00',
			'closes'    => '16:00',
		),
		'sameAs'      => array( $brand['instagram'] ),
	);

	$website = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'@id'      => $home . '#website',
		'name'     => $brand['name'],
		'url'      => $home,
		'publisher' => array( '@id' => $home . '#business' ),
	);

	wildflower_print_jsonld( array( $business, $website ) );
}
add_action( 'wp_head', 'wildflower_head_jsonld', 5 );

/**
 * Product JSON-LD on single WooCommerce products.
 */
function wildflower_product_jsonld() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	/** This filter lets SEO plugins take over. */
	if ( ! apply_filters( 'wildflower_output_product_schema', true ) ) {
		return;
	}

	global $product;
	if ( ! is_object( $product ) ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! $product ) {
		return;
	}

	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'description' => wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ),
		'sku'         => $product->get_sku(),
		'image'       => wp_get_attachment_url( $product->get_image_id() ) ? array( wp_get_attachment_url( $product->get_image_id() ) ) : array(),
		'brand'       => array( '@type' => 'Brand', 'name' => wildflower_brand()['name'] ),
		'offers'      => array(
			'@type'         => 'Offer',
			'priceCurrency' => get_woocommerce_currency(),
			'price'         => wc_get_price_to_display( $product ),
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'url'           => get_permalink( $product->get_id() ),
		),
	);

	wildflower_print_jsonld( $data );
}
add_action( 'wp_footer', 'wildflower_product_jsonld' );
