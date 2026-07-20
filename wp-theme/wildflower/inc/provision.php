<?php
/**
 * Auto-provision required pages.
 *
 * The theme ships several page templates that expect a page to exist at a
 * specific slug (custom-order, faq, terms, privacy-policy). Rather than asking
 * the site owner to create those by hand, we create any that are missing the
 * next time an admin loads wp-admin. Idempotent and version-flagged, so it runs
 * once per version and never touches pages that already exist.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', 'wildflower_provision_pages' );
add_action( 'after_switch_theme', 'wildflower_provision_pages' );

/**
 * Create the theme's required pages if they do not already exist.
 */
function wildflower_provision_pages() {
	if ( 'v3' === get_option( 'wildflower_provisioned' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) && ! ( defined( 'DOING_CRON' ) && DOING_CRON ) && ! did_action( 'after_switch_theme' ) ) {
		return; // Only a capable admin (or theme activation) provisions.
	}

	$pages = array(
		'custom-order'   => array(
			'title'   => __( 'Custom Order', 'wildflower' ),
			'content' => '', // Rendered entirely by page-custom-order.php.
		),
		'faq'            => array(
			'title'   => __( 'FAQ', 'wildflower' ),
			'content' => '', // Rendered entirely by page-faq.php.
		),
		'terms'          => array(
			'title'   => __( 'Terms & Conditions', 'wildflower' ),
			'content' => wildflower_terms_starter(),
		),
		'privacy-policy' => array(
			'title'   => __( 'Privacy Policy', 'wildflower' ),
			'content' => wildflower_privacy_starter(),
		),
	);

	// City delivery landing pages (unique per-city content lives in
	// inc/delivery-cities.php; the City Delivery Page template renders them).
	if ( function_exists( 'wildflower_delivery_cities' ) ) {
		foreach ( wildflower_delivery_cities() as $city_slug => $city ) {
			$pages[ $city_slug ] = array(
				'title'    => sprintf( __( 'Flower Delivery in %s', 'wildflower' ), $city['name'] ),
				'content'  => '',
				'excerpt'  => isset( $city['metadesc'] ) ? $city['metadesc'] : '',
				'template' => 'template-city-delivery.php',
			);
		}
	}

	$ids = array();
	foreach ( $pages as $slug => $data ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing instanceof WP_Post ) {
			$ids[ $slug ] = (int) $existing->ID;
			// Ensure an existing page still gets its template (idempotent upgrade).
			if ( ! empty( $data['template'] ) && get_page_template_slug( $existing->ID ) !== $data['template'] ) {
				update_post_meta( $existing->ID, '_wp_page_template', $data['template'] );
			}
			continue;
		}
		$new_id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_name'      => $slug,
				'post_title'     => $data['title'],
				'post_content'   => $data['content'],
				'post_excerpt'   => isset( $data['excerpt'] ) ? $data['excerpt'] : '',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'page_template'  => isset( $data['template'] ) ? $data['template'] : '',
			)
		);
		if ( $new_id && ! is_wp_error( $new_id ) ) {
			$ids[ $slug ] = (int) $new_id;
			if ( ! empty( $data['template'] ) ) {
				update_post_meta( $new_id, '_wp_page_template', $data['template'] );
			}
		}
	}

	// Point WordPress at our privacy page if one is not already set.
	if ( empty( get_option( 'wp_page_for_privacy_policy' ) ) && ! empty( $ids['privacy-policy'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $ids['privacy-policy'] );
	}

	update_option( 'wildflower_provisioned', 'v3' );
}

/**
 * Starter Terms & Conditions copy. Plain, editable, brand-aware — the owner
 * should review it, but it is complete enough to publish immediately.
 *
 * @return string
 */
function wildflower_terms_starter() {
	$brand = wildflower_brand();
	$name  = $brand['name'];
	$email = $brand['email'];
	$city  = $brand['city'];

	$p = array(
		'<p><em>' . esc_html__( 'Last updated: on publication. Please review these terms and adjust them to your business before relying on them.', 'wildflower' ) . '</em></p>',
		'<h2>' . esc_html__( 'Ordering', 'wildflower' ) . '</h2>',
		'<p>' . sprintf( esc_html__( 'When you place an order with %1$s you are making an offer to purchase. We confirm orders by email. Prices are shown in US dollars and may change without notice, though changes never affect orders already confirmed.', 'wildflower' ), esc_html( $name ) ) . '</p>',
		'<h2>' . esc_html__( 'Substitutions', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'Flowers are seasonal and natural. If a specific stem or container is unavailable, we may substitute an item of equal or greater value in a similar style and color, keeping the overall look and feel of the arrangement.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'Delivery', 'wildflower' ) . '</h2>',
		'<p>' . sprintf( esc_html__( 'We deliver across %1$s and surrounding areas. Delivery fees are calculated by destination ZIP code and shown at checkout. We are not responsible for delays caused by incorrect addresses, no one being available to receive the flowers, or events outside our control.', 'wildflower' ), esc_html( $city ) ) . '</p>',
		'<h2>' . esc_html__( 'Freshness guarantee', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'We stand behind our flowers. If your arrangement wilts earlier than it should, contact us within three days with a photo and we will arrange a replacement or credit.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'Cancellations & refunds', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'Because arrangements are made fresh to order, we ask for changes or cancellations at least 24 hours before the delivery date. Once an order has been prepared or dispatched it can no longer be cancelled. Refunds are handled case by case.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'Contact', 'wildflower' ) . '</h2>',
		'<p>' . sprintf( esc_html__( 'Questions about these terms? Email %s.', 'wildflower' ), esc_html( $email ) ) . '</p>',
	);
	return implode( "\n\n", $p );
}

/**
 * Starter Privacy Policy copy. Covers a standard WooCommerce storefront; the
 * owner should review and complete it (especially third-party processors).
 *
 * @return string
 */
function wildflower_privacy_starter() {
	$brand = wildflower_brand();
	$name  = $brand['name'];
	$email = $brand['email'];

	$p = array(
		'<p><em>' . esc_html__( 'Last updated: on publication. This is a starter policy — please review it and confirm it reflects how your store actually handles data.', 'wildflower' ) . '</em></p>',
		'<p>' . sprintf( esc_html__( 'This policy explains how %1$s collects and uses your information when you visit our site or place an order.', 'wildflower' ), esc_html( $name ) ) . '</p>',
		'<h2>' . esc_html__( 'What we collect', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'When you order we collect your name, email, phone number, billing and delivery addresses, and the details of your order and any gift message. When you browse, we collect standard technical data such as your IP address, browser type and the pages you view.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'How we use it', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'We use your information to process and deliver orders, communicate with you about them, provide support, prevent fraud, and — only if you opt in — send occasional updates and offers. You can unsubscribe at any time.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'Cookies', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'We use cookies to keep your cart working, remember your preferences and understand how the site is used. You can control cookies in your browser settings.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'Sharing', 'wildflower' ) . '</h2>',
		'<p>' . esc_html__( 'We share data only with the services needed to run the store — for example our payment processor, delivery couriers and hosting provider — and never sell your personal information.', 'wildflower' ) . '</p>',
		'<h2>' . esc_html__( 'Your rights', 'wildflower' ) . '</h2>',
		'<p>' . sprintf( esc_html__( 'You may request a copy of the data we hold about you, ask us to correct it, or ask us to delete it. To make a request, email %s.', 'wildflower' ), esc_html( $email ) ) . '</p>',
	);
	return implode( "\n\n", $p );
}
