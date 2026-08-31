<?php
/**
 * Structured data (JSON-LD), social meta and crawler rules, SEO / GEO / AEO.
 *
 * If you run Rank Math or Yoast, they may also output schema/meta. Disable the
 * theme's Product schema with:
 *   add_filter( 'wildflower_output_product_schema', '__return_false' );
 * and the theme's social/meta tags with:
 *   add_filter( 'wildflower_output_social_meta', '__return_false' );
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
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

/**
 * The site logo/image URL for schema (custom logo → site icon → empty).
 *
 * @return string
 */
function wildflower_brand_image() {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( $src ) {
			return $src[0];
		}
	}
	$icon = get_site_icon_url( 512 );
	return $icon ? $icon : '';
}

/**
 * LocalBusiness (Florist) + Organization + WebSite, output in <head> everywhere.
 */
function wildflower_head_jsonld() {
	$home  = home_url( '/' );
	$brand = wildflower_brand();
	$image = wildflower_brand_image();

	$same_as = array_values( array_filter( array( $brand['instagram'], ! empty( $brand['facebook'] ) ? $brand['facebook'] : '' ) ) );

	$address = array(
		'@type'           => 'PostalAddress',
		'addressLocality' => 'Boston',
		'addressRegion'   => 'MA',
		'addressCountry'  => 'US',
	);
	if ( ! empty( $brand['street'] ) ) {
		$address['streetAddress'] = $brand['street'];
	}
	if ( ! empty( $brand['postal'] ) ) {
		$address['postalCode'] = $brand['postal'];
	}

	// areaServed as City entities (cleaner for local entity linking).
	$cities     = array( 'Boston', 'Cambridge', 'Somerville', 'Brookline', 'Newton', 'Medford', 'Arlington' );
	$area_served = array();
	foreach ( $cities as $c ) {
		$area_served[] = array( '@type' => 'City', 'name' => $c );
	}

	$business = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Florist',
		'@id'         => $home . '#business',
		'name'        => $brand['name'],
		'url'         => $home,
		'telephone'   => $brand['phone'],
		'description' => 'Farm-fresh bouquets and weekly flower subscriptions, hand-delivered same-day across Greater Boston.',
		'priceRange'  => '$$',
		'currenciesAccepted' => 'USD',
		'paymentAccepted'    => 'Credit Card, Debit Card',
		'address'     => $address,
		'areaServed'  => $area_served,
		'openingHoursSpecification' => array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
			'opens'     => '10:00',
			'closes'    => '18:00',
		),
	);
	if ( ! empty( $brand['legal_name'] ) ) {
		$business['legalName'] = $brand['legal_name'];
	}
	if ( ! empty( $brand['email'] ) ) {
		$business['email'] = $brand['email'];
	}
	if ( $image ) {
		$business['image'] = $image;
		$business['logo']  = $image;
	}
	if ( $same_as ) {
		$business['sameAs'] = $same_as;
	}
	if ( ! empty( $brand['lat'] ) && ! empty( $brand['lng'] ) ) {
		$business['geo'] = array( '@type' => 'GeoCoordinates', 'latitude' => $brand['lat'], 'longitude' => $brand['lng'] );
	}
	if ( ! empty( $brand['maps'] ) ) {
		$business['hasMap'] = $brand['maps'];
	}
	$business['contactPoint'] = array(
		'@type'       => 'ContactPoint',
		'contactType' => 'customer service',
		'telephone'   => $brand['phone'],
		'areaServed'  => 'US',
	);
	if ( ! empty( $brand['email'] ) ) {
		$business['contactPoint']['email'] = $brand['email'];
	}

	// WebSite + Sitelinks Search Box.
	$website = array(
		'@context'  => 'https://schema.org',
		'@type'     => 'WebSite',
		'@id'       => $home . '#website',
		'name'      => $brand['name'],
		'url'       => $home,
		'publisher' => array( '@id' => $home . '#business' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $home . '?s={search_term_string}',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	wildflower_print_jsonld( array( $business, $website ) );
}
add_action( 'wp_head', 'wildflower_head_jsonld', 5 );

/**
 * Product JSON-LD + product BreadcrumbList on single WooCommerce products.
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

	// Description ≥ ~150 chars: prefer the full description, fall back to short.
	$desc = wp_strip_all_tags( $product->get_description() );
	if ( strlen( $desc ) < 150 && $product->get_short_description() ) {
		$desc = trim( $desc . ' ' . wp_strip_all_tags( $product->get_short_description() ) );
	}

	// Images: featured + gallery.
	$images = array();
	if ( $product->get_image_id() ) {
		$images[] = wp_get_attachment_url( $product->get_image_id() );
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$url = wp_get_attachment_url( $gid );
		if ( $url ) {
			$images[] = $url;
		}
	}
	$images = array_values( array_filter( $images ) );

	$offer = array(
		'@type'           => 'Offer',
		'priceCurrency'   => get_woocommerce_currency(),
		'price'           => wc_get_price_to_display( $product ),
		'priceValidUntil' => gmdate( 'Y-12-31' ),
		'availability'    => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
		'itemCondition'   => 'https://schema.org/NewCondition',
		'url'             => get_permalink( $product->get_id() ),
		'shippingDetails' => array(
			'@type'               => 'OfferShippingDetails',
			'shippingDestination' => array( '@type' => 'DefinedRegion', 'addressRegion' => 'MA', 'addressCountry' => 'US' ),
			'deliveryTime'        => array(
				'@type'        => 'ShippingDeliveryTime',
				'handlingTime' => array( '@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 0, 'unitCode' => 'DAY' ),
				'transitTime'  => array( '@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'DAY' ),
			),
		),
		'hasMerchantReturnPolicy' => array(
			'@type'                => 'MerchantReturnPolicy',
			'applicableCountry'    => 'US',
			'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
			'merchantReturnDays'   => 1,
			'returnMethod'         => 'https://schema.org/ReturnByMail',
			'returnFees'           => 'https://schema.org/FreeReturn',
		),
	);

	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'description' => $desc,
		'sku'         => $product->get_sku() ? $product->get_sku() : (string) $product->get_id(),
		'image'       => $images,
		'brand'       => array( '@type' => 'Brand', 'name' => wildflower_brand()['name'] ),
		'offers'      => $offer,
	);

	// Real, on-page reviews only (this is what earns star snippets, and Google
	// requires the ratings to be visible on the page, WooCommerce shows them).
	if ( $product->get_review_count() > 0 && wc_review_ratings_enabled() ) {
		$data['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => (string) $product->get_average_rating(),
			'reviewCount' => (string) $product->get_review_count(),
		);
	}

	// Product breadcrumb: Home › (primary category) › Product.
	$crumbs = array(
		array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
	);
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	$pos   = 2;
	if ( $terms && ! is_wp_error( $terms ) ) {
		$term = $terms[0];
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			$crumbs[] = array( '@type' => 'ListItem', 'position' => $pos++, 'name' => $term->name, 'item' => $link );
		}
	}
	$crumbs[] = array( '@type' => 'ListItem', 'position' => $pos, 'name' => $product->get_name(), 'item' => get_permalink( $product->get_id() ) );

	wildflower_print_jsonld(
		array(
			$data,
			array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbs ),
		)
	);
}
add_action( 'wp_footer', 'wildflower_product_jsonld' );

/**
 * Article JSON-LD on single blog posts (E-E-A-T: author, dates, publisher).
 */
function wildflower_article_jsonld() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$post  = get_post();
	$brand = wildflower_brand();
	$image = wildflower_brand_image();
	if ( has_post_thumbnail( $post ) ) {
		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'large' );
		if ( $img ) {
			$image = $img[0];
		}
	}
	$home = home_url( '/' );

	$data = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => get_the_title( $post ),
		'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
		'datePublished'    => get_the_date( 'c', $post ),
		'dateModified'     => get_the_modified_date( 'c', $post ),
		'author'           => array( '@type' => 'Person', 'name' => ( function () use ( $post ) {
			$n = trim( (string) get_the_author_meta( 'display_name', $post->post_author ) );
			return ( '' === $n || false !== strpos( $n, '@' ) ) ? 'Marco Reyes' : $n;
		} )() ),
		'publisher'        => array( '@id' => $home . '#business' ),
		'mainEntityOfPage' => get_permalink( $post ),
	);
	if ( $image ) {
		$data['image'] = $image;
	}
	$crumbs = array(
		array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
		array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Journal', 'item' => home_url( '/journal/' ) ),
		array( '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title( $post ), 'item' => get_permalink( $post ) ),
	);
	wildflower_print_jsonld(
		array( $data, array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbs ) )
	);
}
add_action( 'wp_footer', 'wildflower_article_jsonld' );

/**
 * BreadcrumbList on product category / shop archives: Home › Category.
 */
function wildflower_archive_breadcrumb_jsonld() {
	if ( ! function_exists( 'is_product_category' ) || ! ( is_product_category() || ( function_exists( 'is_shop' ) && is_shop() ) ) ) {
		return;
	}
	$crumbs = array( array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ) );
	if ( is_product_category() ) {
		$term = get_queried_object();
		if ( $term && ! empty( $term->name ) ) {
			$link     = get_term_link( $term );
			$crumbs[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => $term->name, 'item' => is_wp_error( $link ) ? home_url( '/' ) : $link );
		}
	} elseif ( function_exists( 'wc_get_page_permalink' ) ) {
		$crumbs[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => wc_get_page_permalink( 'shop' ) );
	}
	wildflower_print_jsonld( array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbs ) );
}
add_action( 'wp_footer', 'wildflower_archive_breadcrumb_jsonld' );

/**
 * Give the main catalog and product-category archives concise search titles.
 * WordPress appends the site name using the configured title separator.
 *
 * @param array $parts Document-title parts.
 * @return array
 */
function wildflower_catalog_document_title( $parts ) {
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$parts['title'] = __( 'Flower Shop & Bouquet Delivery in Boston', 'wildflower' );
		return $parts;
	}

	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$parts['title'] = 'roses' === $term->slug
				? __( 'Rose Bouquets & Delivery in Boston', 'wildflower' )
				: sprintf(
					/* translators: %s: product-category name. */
					__( '%s, Flower Delivery in Boston', 'wildflower' ),
					$term->name
				);
		}
	}

	return $parts;
}
add_filter( 'document_title_parts', 'wildflower_catalog_document_title' );

/**
 * Meta description + Open Graph + Twitter Card tags.
 *
 * Only runs when no major SEO plugin is active (they output their own), and can
 * be disabled with the wildflower_output_social_meta filter.
 */
function wildflower_social_meta() {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
		return; // Yoast / Rank Math handle this.
	}
	if ( ! apply_filters( 'wildflower_output_social_meta', true ) ) {
		return;
	}

	$brand = wildflower_brand();
	$title = wp_get_document_title();
	$type  = 'website';
	$desc  = get_bloginfo( 'description' );
	$image = wildflower_brand_image();

	// Canonical-ish URL: permalink on singular, path (no query) elsewhere.
	$url = home_url( '/' );
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$path = strtok( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '?' );
		$url  = home_url( $path );
	}

	if ( is_singular() ) {
		$url  = get_permalink();
		$post = get_post();

		if ( function_exists( 'is_product' ) && is_product() ) {
			$type    = 'product';
			$product = wc_get_product( get_the_ID() );
			if ( $product ) {
				$short = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
				$desc  = wp_trim_words( wp_strip_all_tags( $short ), 30, '…' );
				if ( $product->get_image_id() ) {
					$img_src = wp_get_attachment_image_src( $product->get_image_id(), 'large' );
					if ( $img_src ) {
						$image = $img_src[0];
					}
				}
			}
		} else {
			$type = is_single() ? 'article' : 'website';
			if ( $post ) {
				$excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( $post->post_content );
				$desc    = wp_trim_words( $excerpt, 30, '…' );
			}
			if ( has_post_thumbnail() ) {
				$img_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
				if ( $img_src ) {
					$image = $img_src[0];
				}
			}
		}
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$url  = wc_get_page_permalink( 'shop' );
		$desc = __( 'Shop handcrafted bouquets, Ecuadorian roses and floral gifts with same-day delivery across Greater Boston.', 'wildflower' );
	} elseif ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$term_link = get_term_link( $term );
			if ( ! is_wp_error( $term_link ) ) {
				$url = $term_link;
			}

			$term_desc = wp_strip_all_tags( term_description( $term->term_id, $term->taxonomy ) );
			$desc      = $term_desc ? $term_desc : sprintf(
				/* translators: %s: product-category or product-attribute term name. */
				__( 'Shop %s from Wildflower, handcrafted for delivery across Greater Boston.', 'wildflower' ),
				$term->name
			);

			if ( 'product_cat' === $term->taxonomy ) {
				$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
				if ( $thumbnail_id ) {
					$img_src = wp_get_attachment_image_src( $thumbnail_id, 'large' );
					if ( $img_src ) {
						$image = $img_src[0];
					}
				}
			}
		}
	}

	$desc = trim( (string) $desc );
	if ( '' === $desc ) {
		$desc = 'Farm-fresh bouquets and same-day flower delivery across Greater Boston.';
	}
	if ( strlen( $desc ) > 160 ) {
		$desc = rtrim( mb_substr( $desc, 0, 157 ) ) . '…';
	}

	echo "\n";
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $brand['name'] ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'wildflower_social_meta', 6 );

/**
 * robots.txt: explicitly welcome AI answer-engine crawlers (we WANT citations)
 * and keep faceted-filter parameter URLs out of the crawl.
 *
 * @param string $output Existing robots.txt body.
 * @param bool   $public Whether the site is public.
 * @return string
 */
function wildflower_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$ai = array( 'GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-SearchBot', 'anthropic-ai', 'PerplexityBot', 'Perplexity-User', 'Google-Extended', 'Applebot-Extended', 'Bytespider', 'CCBot' );
	$lines = array( '', '# AI answer engines, allowed so the studio can be cited/recommended' );
	foreach ( $ai as $bot ) {
		$lines[] = 'User-agent: ' . $bot;
		$lines[] = 'Allow: /';
		$lines[] = '';
	}
	$lines[] = '# Keep filter/sort parameter URLs out of the crawl';
	$lines[] = 'User-agent: *';
	$lines[] = 'Disallow: /*?orderby=';
	$lines[] = 'Disallow: /*?min_price=';
	$lines[] = 'Disallow: /*?max_price=';
	$lines[] = 'Disallow: /*?filter_';
	$lines[] = 'Disallow: /*?add-to-cart=';
	$lines[] = 'Disallow: /cart/';
	$lines[] = 'Disallow: /checkout/';
	$lines[] = 'Disallow: /my-account/';

	return $output . implode( "\n", $lines ) . "\n";
}
add_filter( 'robots_txt', 'wildflower_robots_txt', 10, 2 );
