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

/**
 * Bump this on any deploy whose output should reach visitors immediately.
 *
 * It re-runs provisioning (repo-owned pages are rewritten) and purges the
 * host's full-page cache, which otherwise serves the previous build without
 * ever reaching PHP.
 */
const WILDFLOWER_BUILD = 'v11';

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
	if ( WILDFLOWER_BUILD === get_option( 'wildflower_provisioned' ) ) {
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
		// The two legal pages are repo-owned: `sync` re-writes them on every
		// version bump so the published copy always matches the source, the way
		// journal posts already work. Every template links to them from the
		// footer, so a missing or stale one is a site-wide problem.
		'terms'          => array(
			'title'   => __( 'Terms & Conditions', 'wildflower' ),
			'content' => wildflower_terms_starter(),
			'sync'    => true,
		),
		'privacy-policy' => array(
			'title'   => __( 'Privacy Policy', 'wildflower' ),
			'content' => wildflower_privacy_starter(),
			'sync'    => true,
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
			// Repo-owned pages: push the current copy over the stored one, and
			// restore anything that was trashed (a trashed page 404s).
			if ( ! empty( $data['sync'] ) ) {
				wp_update_post(
					array(
						'ID'           => $existing->ID,
						'post_title'   => $data['title'],
						'post_content' => $data['content'],
						'post_status'  => 'publish',
					)
				);
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

	// Journal posts, created from code so they appear with the deploy, no
	// manual import. Also trash the default "Hello World" post so the grid
	// stays a clean feature + 6.
	if ( function_exists( 'wildflower_journal_articles' ) ) {
		$admins    = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
		$author_id = ! empty( $admins ) ? (int) $admins[0] : 1;
		foreach ( wildflower_journal_articles() as $art ) {
			$existing = get_page_by_path( $art['slug'], OBJECT, 'post' );
			if ( $existing instanceof WP_Post ) {
				// Keep the repo as the source of truth: re-sync the title, body and
				// excerpt so content edits (e.g. copy fixes) reach live posts too.
				wp_update_post(
					array(
						'ID'           => $existing->ID,
						'post_title'   => $art['title'],
						'post_content' => $art['content'],
						'post_excerpt' => $art['excerpt'],
					)
				);
				$post_id = (int) $existing->ID;
			} else {
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
			}
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

	update_option( 'wildflower_provisioned', WILDFLOWER_BUILD );
	delete_transient( 'wildflower_provisioning' );

	wildflower_purge_page_cache();
}

/**
 * Drop the host's full-page cache after provisioning.
 *
 * Without this the work above is invisible: LiteSpeed keeps serving the copy it
 * made before the page existed. That is how a freshly created /privacy-policy/
 * carried on returning 404, and how the XML sitemap carried on advertising URLs
 * that had already been dropped from it.
 *
 * Every call is a no-op when the matching cache is not installed.
 */
function wildflower_purge_page_cache() {
	/*
	 * The host runs LiteSpeed, and only the server-level purge reaches every
	 * object: the plugin's purge-all emits a tagged header
	 * (`X-LiteSpeed-Purge: public,<blog>_`) that left a 15-hour-old
	 * /wp-sitemap.xml being served long after the sitemap had changed.
	 *
	 * `*` is the documented purge-everything token, so send it directly, and do
	 * NOT fire the plugin action alongside it: the plugin writes its own value
	 * into the same header later in the request and ours would be replaced. The
	 * action stays as the fallback for when output has already started and the
	 * header can no longer be set.
	 */
	if ( headers_sent() ) {
		do_action( 'litespeed_purge_all' );
	} else {
		wildflower_send_purge_header();
		// The plugin writes its own value into this header from a shutdown
		// handler, so claim it back as late as the request allows.
		add_action( 'shutdown', 'wildflower_send_purge_header', PHP_INT_MAX );
	}

	if ( function_exists( 'wp_cache_clear_cache' ) ) {
		wp_cache_clear_cache(); // WP Super Cache.
	}
	if ( function_exists( 'w3tc_flush_all' ) ) {
		w3tc_flush_all();
	}
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}
}

/**
 * Ask the LiteSpeed server to drop every cached object for this site.
 */
function wildflower_send_purge_header() {
	if ( ! headers_sent() ) {
		header( 'X-LiteSpeed-Purge: *' );
	}
}

/**
 * Purge the full-page cache once per deployed build.
 *
 * Pages are served from LiteSpeed without touching PHP, so a deploy that
 * changes what a template outputs stays invisible until the cache is dropped.
 * Keyed on the build signature: once per deploy, never on an ordinary request.
 */
function wildflower_purge_cache_on_deploy() {
	$signature = WILDFLOWER_BUILD;
	if ( get_option( 'wildflower_cache_purged_for' ) === $signature ) {
		return;
	}

	update_option( 'wildflower_cache_purged_for', $signature );
	wildflower_purge_page_cache();
}
add_action( 'init', 'wildflower_purge_cache_on_deploy', 21 );

/**
 * Create the shop categories that actually receive products and file existing
 * products into them, so the Shop menu links (and the Home → Shop → Roses
 * breadcrumb) resolve to real category archives instead of falling back to the
 * shop. A category with nothing in it is never created.
 *
 * Products are matched by name, "tin can" → Tin Can Bouquets, "rose" → Roses,
 * "gift" → Gifts, everything else → Bouquets. Only products that are still
 * uncategorised are touched, so any category picked by hand in WooCommerce wins.
 */
function wildflower_provision_product_categories() {
	if ( ! class_exists( 'WooCommerce' ) || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$labels = array(
		'roses'            => __( 'Roses', 'wildflower' ),
		'bouquets'         => __( 'Bouquets', 'wildflower' ),
		'tin-can-bouquets' => __( 'Tin Can Bouquets', 'wildflower' ),
		'gifts'            => __( 'Gifts', 'wildflower' ),
	);

	$default_cat = (int) get_option( 'default_product_cat' );
	$products    = get_posts(
		array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields'      => 'ids',
		)
	);

	/*
	 * Work out where each uncategorised product belongs BEFORE creating any
	 * terms. Creating the full list up front left "Gifts" and "Tin Can
	 * Bouquets" empty on the live site: an empty archive renders nothing but
	 * "No products were found", which is a dead link in the Shop menu and a
	 * thin page for Google.
	 */
	$plan = array();
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
		$plan[ $product_id ] = $slug;
	}

	if ( empty( $plan ) ) {
		return;
	}

	$cat_ids = array();
	foreach ( array_unique( $plan ) as $slug ) {
		$existing = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $existing instanceof WP_Term ) {
			$cat_ids[ $slug ] = (int) $existing->term_id;
			continue;
		}
		$created = wp_insert_term( $labels[ $slug ], 'product_cat', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $created ) && ! empty( $created['term_id'] ) ) {
			$cat_ids[ $slug ] = (int) $created['term_id'];
		}
	}

	foreach ( $plan as $product_id => $slug ) {
		if ( ! empty( $cat_ids[ $slug ] ) ) {
			// Replace (drops "Uncategorised") so the product leaves the default bucket.
			wp_set_object_terms( $product_id, array( $cat_ids[ $slug ] ), 'product_cat', false );
		}
	}
}

/**
 * Terms of Service copy, kept in the repo so the page can never go missing.
 *
 * Wildflower and Boston Flowers are the same Massachusetts LLC trading under two
 * brands from one studio, so the legal substance (entity, address, governing
 * law, refunds, delivery and access policy) is deliberately identical to
 * boston-flowers.com/legal/. Only the brand name, the contact mailbox and the
 * delivery rates differ, because the two brands sit at different price levels.
 *
 * @return string
 */
function wildflower_terms_starter() {
	$brand      = wildflower_brand();
	$name       = $brand['name'];
	$legal_name = $brand['legal_name'];
	$address    = $brand['address'];
	$phone      = $brand['phone'];
	$email      = $brand['email'];
	$cutoff     = $brand['cutoff'];
	$care_url   = home_url( '/how-to-make-cut-flowers-last-longer/' );

	$p = array();

	$p[] = '<p><em>' . sprintf(
		/* translators: 1: brand name, 2: studio address. */
		esc_html__( 'Effective September 1, 2026 · %1$s · %2$s', 'wildflower' ),
		esc_html( $name ),
		esc_html( $address )
	) . '</em></p>';

	$p[] = '<h2>' . esc_html__( 'Orders & availability', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: same-day cutoff time, e.g. "1 PM". */
		esc_html__( 'All orders are subject to flower availability. Placing an order is a request, not a guaranteed reservation. We confirm availability by email, phone or WhatsApp. Same-day delivery requires order placement by %s Eastern Time. We reserve the right to decline any order at our discretion.', 'wildflower' ),
		esc_html( $cutoff )
	) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Pricing & delivery fees', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'All prices are listed in US dollars. Delivery fees are charged separately and are calculated from the delivery ZIP code.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'Rates start at $19 for Boston and nearby neighborhoods and $25 for Greater Boston, then step up by distance ($35, $40, $45 and above) for extended-distance destinations. Spend $85 or more and delivery is a flat $15. Extended-distance deliveries may require manager approval, custom routing, and an individual quote.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'The applicable delivery fee is displayed at checkout after a valid delivery address and ZIP code have been entered. Studio pickup, where offered, is free.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'Delivery availability and pricing depend on the exact destination, access requirements, requested delivery time, and route availability. Addresses outside our configured delivery area may require confirmation and a custom delivery quote.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'Delivery fees are non-refundable once the order has been dispatched.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'No-return policy — perishable goods', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'All sales are final. Fresh flowers are perishable agricultural products with a natural and limited lifespan. Every arrangement is assembled to order from live flowers and is non-returnable and non-exchangeable after delivery.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'Custom orders (monogrammed pieces, grand compositions, event florals) are non-refundable once production has commenced, as materials are sourced specifically for your order.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'This policy is compliant with Massachusetts law, which does not require businesses to accept returns or refunds when the policy is clearly disclosed prior to purchase (M.G.L. c. 93A). By completing a purchase, you acknowledge and accept these terms.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Cancellations', 'wildflower' ) . '</h2>';
	$p[] = '<ul>';
	$p[] = '<li><strong>' . esc_html__( 'Standard orders:', 'wildflower' ) . '</strong> ' . esc_html__( 'cancelled up to 48 hours before scheduled delivery are eligible for a full refund. Cancellations made less than 48 hours before delivery are not eligible for a refund.', 'wildflower' ) . '</li>';
	$p[] = '<li><strong>' . esc_html__( 'Same-day delivery orders:', 'wildflower' ) . '</strong> ' . esc_html__( 'these orders are not eligible for cancellation or refund once placed, as flowers are purchased and preparation begins immediately.', 'wildflower' ) . '</li>';
	$p[] = '<li><strong>' . esc_html__( 'Custom orders, grand arrangements, and event florals:', 'wildflower' ) . '</strong> ' . esc_html__( 'full refund available if cancelled within 3 hours of placing the order. After this window, these orders may be rescheduled to a new date or exchanged for store credit — cash refunds are not available.', 'wildflower' ) . '</li>';
	$p[] = '</ul>';

	$p[] = '<h2>' . esc_html__( 'Damaged on arrival & post-delivery care', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'If your arrangement arrives in a condition substantially different from what was photographed before dispatch, contact us within 2 hours of delivery and provide clear photographs of the issue.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: brand name. */
		esc_html__( '%s will review the claim and may, at its sole discretion, offer a replacement, store credit, or partial refund. Claims submitted more than 2 hours after delivery will not be considered. We can only address issues that existed prior to or at the time of delivery.', 'wildflower' ),
		esc_html( $name )
	) . '</p>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: brand name. */
		esc_html__( '%s is not responsible for deterioration, damage, or loss occurring after delivery due to improper care, insufficient water, direct sunlight, excessive heat or cold, pets, accidental damage, mishandling, third-party actions, theft, or environmental conditions.', 'wildflower' ),
		esc_html( $name )
	) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Flower care & lifespan', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: URL of the flower care guide. */
		wp_kses_post( __( 'Expected lifespan under proper care: roses 5–10 days, peonies 4–7 days, tulips 3–6 days. We are not responsible for premature wilting caused by improper care after delivery (incorrect water, direct sunlight, heat, or cold exposure). Full care instructions are in our <a href="%s">flower care guide</a>.', 'wildflower' ) ),
		esc_url( $care_url )
	) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Substitutions', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'In rare cases of unavailability, we may substitute flowers of equal or greater value while preserving the overall style and color palette. You will be notified before dispatch. If you decline the substitution, you may reschedule your delivery date, or cancel according to the terms above (full refund only within 3 hours of placing a custom order; otherwise reschedule or store credit).', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Delivery & access policy', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: brand name. */
		esc_html__( 'To ensure the safe arrival of your order, %s may use photographs, GPS records, courier logs, communication records, timestamps, and other internal delivery records to document successful fulfillment.', 'wildflower' ),
		esc_html( $name )
	) . '</p>';
	$p[] = '<p>' . esc_html__( 'Customers are responsible for providing accurate delivery information, including recipient name, address, apartment or suite number, access instructions, and contact information.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . esc_html__( 'If delivery cannot be completed because of an incorrect or incomplete address, missing apartment, suite, gate or access details, an invalid phone number, recipient unavailability, an inability to reach the recipient or sender, or restricted building access, the order may not be eligible for refund and additional delivery fees may apply for redelivery.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: brand name. */
		esc_html__( 'For deliveries to apartment buildings, gated communities, offices, hospitals, hotels, campuses, and other restricted-access properties, %s may, at its sole discretion, leave the arrangement in a reasonably safe location, leave it with a concierge, receptionist, front desk, security personnel, mailroom staff or other authorized building representative, or return it to the studio for a future delivery attempt.', 'wildflower' ),
		esc_html( $name )
	) . '</p>';
	$p[] = '<p>' . esc_html__( 'If the sender or recipient requests contactless delivery, leave-at-door delivery, or delivery to a reception desk, concierge, mailroom, lobby, package room or other unattended location, our responsibility ends once delivery has been completed and documented.', 'wildflower' ) . '</p>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: brand name. */
		esc_html__( '%s is not responsible for loss, theft, weather exposure, animal activity, vandalism, third-party actions, or other events occurring after documented delivery. If an order is reported missing after documented delivery has been completed according to the delivery instructions provided, we reserve the right to deny refunds, credits, replacements, or compensation.', 'wildflower' ),
		esc_html( $name )
	) . '</p>';
	$p[] = '<p>' . esc_html__( 'Delivery records maintained by the studio are considered evidence of successful fulfillment of the order. Perishable products that cannot be delivered due to recipient refusal, recipient unavailability, inability to access the recipient, or failure to obtain access to the delivery location are not eligible for refund once delivery has been attempted.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Limitation of liability', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'Our liability is limited to the value of the original order. We are not liable for indirect, incidental, or consequential damages including event disruption, emotional distress, or loss of enjoyment.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Business entity', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: 1: brand name, 2: registered LLC name. */
		esc_html__( '%1$s is operated by %2$s, a Massachusetts limited liability company. All references to “%1$s”, “we”, “our”, and “us” refer to %2$s unless otherwise specified.', 'wildflower' ),
		esc_html( $name ),
		esc_html( $legal_name )
	) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Governing law', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'These terms are governed by the laws of the Commonwealth of Massachusetts. Disputes shall be resolved in the courts of Suffolk County, MA. If any provision is found unenforceable, the remaining provisions stay in full effect.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Contact', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: 1: brand name, 2: studio address, 3: phone number, 4: studio email. */
		esc_html__( '%1$s · %2$s · %3$s · %4$s', 'wildflower' ),
		esc_html( $name ),
		esc_html( $address ),
		esc_html( $phone ),
		esc_html( $email )
	) . '</p>';

	return implode( "\n\n", $p );
}

/**
 * Privacy Policy copy, mirroring boston-flowers.com/legal/ for the same LLC.
 *
 * Kept in the repo (rather than typed into wp-admin) so the page is recreated
 * automatically: the footer links to it from every template, and a missing page
 * meant a site-wide 404 on a legal link.
 *
 * @return string
 */
function wildflower_privacy_starter() {
	$brand      = wildflower_brand();
	$name       = $brand['name'];
	$legal_name = $brand['legal_name'];
	$address    = $brand['address'];
	$phone      = $brand['phone'];
	$email      = $brand['email'];

	$p = array();

	$p[] = '<p><em>' . sprintf(
		/* translators: 1: brand name, 2: studio address. */
		esc_html__( 'Effective September 1, 2026 · %1$s · %2$s', 'wildflower' ),
		esc_html( $name ),
		esc_html( $address )
	) . '</em></p>';

	$p[] = '<h2>' . esc_html__( 'Information we collect', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'When you place an order or contact us, we collect what you provide: name, delivery address, phone number, and email, together with the details of your order and any gift message. Payment data is processed by our payment provider — we do not store card numbers. We may also collect standard analytics data (IP address, browser type, pages visited).', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'How we use your information', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'We use your data solely to fulfill your order, confirm delivery, send a pre-delivery photo of your arrangement, and respond to inquiries. We do not sell, rent, or share your personal information with third parties for marketing.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Payment processing', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: brand name. */
		esc_html__( 'We accept all major credit and debit cards through our secure checkout, and corporate accounts can be invoiced by arrangement. Each processor operates under its own privacy policy. %s does not access or store full card details.', 'wildflower' ),
		esc_html( $name )
	) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Communications', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'By placing an order you consent to order-related messages via email, WhatsApp, SMS, or phone. We will not send unrelated marketing communications without your explicit consent, and every newsletter carries an unsubscribe link.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Data retention', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: %s: studio email address. */
		esc_html__( 'Order records are retained for up to 3 years for business and tax purposes. To request a copy of your data, a correction, or deletion, email %s.', 'wildflower' ),
		esc_html( $email )
	) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Cookies', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . esc_html__( 'We use essential cookies (cart, session management) and may use analytics cookies. You can disable cookies in your browser settings at any time.', 'wildflower' ) . '</p>';

	$p[] = '<h2>' . esc_html__( 'Business entity & contact', 'wildflower' ) . '</h2>';
	$p[] = '<p>' . sprintf(
		/* translators: 1: brand name, 2: registered LLC name. */
		esc_html__( '%1$s is operated by %2$s, a Massachusetts limited liability company.', 'wildflower' ),
		esc_html( $name ),
		esc_html( $legal_name )
	) . '</p>';
	$p[] = '<p>' . sprintf(
		/* translators: 1: brand name, 2: studio address, 3: phone number, 4: studio email. */
		esc_html__( '%1$s · %2$s · %3$s · %4$s', 'wildflower' ),
		esc_html( $name ),
		esc_html( $address ),
		esc_html( $phone ),
		esc_html( $email )
	) . '</p>';

	return implode( "\n\n", $p );
}
