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

add_action( 'init', 'wildflower_provision_pages', 20 );
add_action( 'after_switch_theme', 'wildflower_provision_pages' );

/**
 * Create the theme's required pages + journal posts if they don't exist.
 *
 * Runs on a normal front-end request (not just wp-admin), so a repo deploy
 * self-provisions with no manual import and no dashboard visit. Version-flagged
 * so it runs once per version, with a short lock to avoid concurrent double-runs.
 */
function wildflower_provision_pages() {
	if ( 'v6' === get_option( 'wildflower_provisioned' ) ) {
		return;
	}
	if ( get_transient( 'wildflower_provisioning' ) ) {
		return; // Another request is already provisioning.
	}
	set_transient( 'wildflower_provisioning', 1, 2 * MINUTE_IN_SECONDS );

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

	// Journal posts — created from code so they appear with the deploy, no
	// manual import. Also trash the default "Hello World" post so the grid
	// stays a clean feature + 6.
	if ( function_exists( 'wildflower_journal_articles' ) ) {
		$admins    = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
		$author_id = ! empty( $admins ) ? (int) $admins[0] : 1;
		foreach ( wildflower_journal_articles() as $art ) {
			if ( get_page_by_path( $art['slug'], OBJECT, 'post' ) instanceof WP_Post ) {
				continue;
			}
			$post_id = wp_insert_post(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'post_author'    => $author_id,
					'post_name'      => $art['slug'],
					'post_title'     => $art['title'],
					'post_content'   => $art['content'],
					'post_excerpt'   => $art['excerpt'],
					'post_date'      => $art['date'],
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $art['category'] ) ) {
				$term = term_exists( $art['category'], 'category' );
				if ( ! $term ) {
					$term = wp_insert_term( $art['category'], 'category' );
				}
				if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) ) {
					wp_set_post_terms( $post_id, array( (int) $term['term_id'] ), 'category' );
				}
			}
		}
	}

	// Remove the default "Hello World" starter post if it is still around.
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hello instanceof WP_Post && 'trash' !== $hello->post_status ) {
		wp_trash_post( $hello->ID );
	}

	// WooCommerce shop sections (Roses / Bouquets / …) + auto-file products.
	wildflower_provision_product_categories();

	update_option( 'wildflower_provisioned', 'v6' );
	delete_transient( 'wildflower_provisioning' );
}

/**
 * Create the curated WooCommerce shop categories and file existing products into
 * them so the Shop menu links (and the Home → Shop → Roses breadcrumb) resolve to
 * real category archives instead of falling back to the shop.
 *
 * Products are matched by name — "tin can" → Tin Can Bouquets, "rose" → Roses,
 * "gift" → Gifts, everything else → Bouquets. Only products that are still
 * uncategorised are touched, so any category picked by hand in WooCommerce wins.
 */
function wildflower_provision_product_categories() {
	if ( ! class_exists( 'WooCommerce' ) || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$categories = array(
		'roses'            => __( 'Roses', 'wildflower' ),
		'bouquets'         => __( 'Bouquets', 'wildflower' ),
		'tin-can-bouquets' => __( 'Tin Can Bouquets', 'wildflower' ),
		'gifts'            => __( 'Gifts', 'wildflower' ),
	);

	$cat_ids = array();
	foreach ( $categories as $slug => $name ) {
		$existing = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $existing instanceof WP_Term ) {
			$cat_ids[ $slug ] = (int) $existing->term_id;
			continue;
		}
		$created = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $created ) && ! empty( $created['term_id'] ) ) {
			$cat_ids[ $slug ] = (int) $created['term_id'];
		}
	}
	if ( empty( $cat_ids ) ) {
		return;
	}

	$default_cat = (int) get_option( 'default_product_cat' );
	$products    = get_posts(
		array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields'      => 'ids',
		)
	);
	foreach ( $products as $product_id ) {
		$current = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $current ) ) {
			continue;
		}
		// Respect any real (non-default) category chosen in WooCommerce.
		if ( array_diff( $current, array( $default_cat ) ) ) {
			continue;
		}

		$title = strtolower( (string) get_the_title( $product_id ) );
		if ( false !== strpos( $title, 'tin can' ) || false !== strpos( $title, 'tin-can' ) ) {
			$slug = 'tin-can-bouquets';
		} elseif ( false !== strpos( $title, 'rose' ) ) {
			$slug = 'roses';
		} elseif ( false !== strpos( $title, 'gift' ) ) {
			$slug = 'gifts';
		} else {
			$slug = 'bouquets';
		}

		if ( ! empty( $cat_ids[ $slug ] ) ) {
			// Replace (drops "Uncategorised") so the product leaves the default bucket.
			wp_set_object_terms( $product_id, array( $cat_ids[ $slug ] ), 'product_cat', false );
		}
	}
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
