<?php
/**
 * Wildflower theme functions.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WILDFLOWER_VERSION', '1.0.0' );

/**
 * Theme setup.
 */
function wildflower_setup() {
	load_theme_textdomain( 'wildflower', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 40, 'width' => 200, 'flex-width' => true ) );

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'wildflower' ),
			'footer'  => __( 'Footer Menu', 'wildflower' ),
		)
	);
}
add_action( 'after_setup_theme', 'wildflower_setup' );

/**
 * Asset version helper — uses file mtime so browsers never serve a stale
 * cached CSS/JS after an update (a common "animations stopped working" cause).
 *
 * @param string $rel Path relative to the theme root, e.g. '/assets/js/main.js'.
 * @return string
 */
function wildflower_ver( $rel ) {
	$file = get_template_directory() . $rel;
	return file_exists( $file ) ? (string) filemtime( $file ) : WILDFLOWER_VERSION;
}

/**
 * Enqueue styles and scripts.
 */
function wildflower_assets() {
	// Fonts for the ACTIVE theme's pair are enqueued (handle: wildflower-fonts)
	// in inc/theme-switcher.php, so they swap with the theme.

	// Main stylesheet (theme CSS variables are added inline by the engine).
	wp_enqueue_style( 'wildflower-style', get_stylesheet_uri(), array( 'wildflower-fonts' ), wildflower_ver( '/style.css' ) );

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'wildflower-woo', get_template_directory_uri() . '/assets/css/woocommerce.css', array( 'wildflower-style' ), wildflower_ver( '/assets/css/woocommerce.css' ) );
	}

	// GSAP + ScrollTrigger (CDN) for the same motion as the prototype.
	wp_enqueue_script( 'gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );

	// Theme JS.
	wp_enqueue_script( 'wildflower-main', get_template_directory_uri() . '/assets/js/main.js', array( 'gsap', 'gsap-scrolltrigger' ), wildflower_ver( '/assets/js/main.js' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wildflower_assets' );

/**
 * Theme-controlled primary navigation (used by both the desktop bar and the
 * mobile burger). Order is fixed here so it's predictable everywhere:
 * Home, Shop, Subscriptions, Gallery, Journal, Occasions, Delivery, then
 * About and Contact last. Shop appears only when WooCommerce is active.
 *
 * @param string $menu_class Class for the <ul>.
 */
/**
 * The single source of truth for the primary navigation. Used by BOTH the
 * header and the footer so the two never drift apart. Each item is
 * array( url, label ).
 *
 * @return array
 */
function wildflower_nav_items() {
	$items = array( array( home_url( '/' ), __( 'Home', 'wildflower' ) ) );
	if ( class_exists( 'WooCommerce' ) && wc_get_page_permalink( 'shop' ) ) {
		$items[] = array( wc_get_page_permalink( 'shop' ), __( 'Shop', 'wildflower' ) );
	}
	$items[] = array( home_url( '/subscriptions/' ), __( 'Subscriptions', 'wildflower' ) );
	$items[] = array( home_url( '/gallery/' ), __( 'Gallery', 'wildflower' ) );
	$items[] = array( home_url( '/journal/' ), __( 'Journal', 'wildflower' ) );
	$items[] = array( home_url( '/occasions/' ), __( 'Occasions', 'wildflower' ) );
	$items[] = array( home_url( '/delivery/' ), __( 'Delivery', 'wildflower' ) );
	$items[] = array( home_url( '/about/' ), __( 'About', 'wildflower' ) );
	$items[] = array( home_url( '/contact/' ), __( 'Contact', 'wildflower' ) );

	/**
	 * Filter the primary nav items (header + footer share this list).
	 *
	 * @param array $items Array of array( url, label ).
	 */
	return apply_filters( 'wildflower_nav_items', $items );
}

/**
 * Resolve a WooCommerce product category to its archive URL by trying a list of
 * candidate slugs/names. Returns the real term link when the category exists;
 * otherwise a safe fallback to the shop filtered by the first candidate slug
 * (which shows all products until the category is created, never a 404, and
 * self-heals into the clean category URL once the term exists).
 *
 * @param array  $candidates Slugs/names to try, in order.
 * @param string $shop_url   Shop page URL (fallback base).
 * @return string
 */
function wildflower_resolve_product_cat( $candidates, $shop_url ) {
	$candidates = (array) $candidates;
	if ( class_exists( 'WooCommerce' ) && taxonomy_exists( 'product_cat' ) ) {
		foreach ( $candidates as $c ) {
			$term = get_term_by( 'slug', sanitize_title( $c ), 'product_cat' );
			if ( ! $term ) {
				$term = get_term_by( 'name', $c, 'product_cat' );
			}
			if ( $term && ! is_wp_error( $term ) ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					return $link;
				}
			}
		}
	}
	$slug = ! empty( $candidates ) ? sanitize_title( $candidates[0] ) : '';
	return $slug ? add_query_arg( 'product_cat', $slug, $shop_url ) : $shop_url;
}

/**
 * Curated "Shop" dropdown: the sections the studio sells, in a fixed order.
 *
 * Best sellers / New arrivals are the shop sorted by popularity / date (no
 * setup needed). Roses, Bouquets, Tin Can Bouquets and Gifts resolve to the
 * matching product category (created in WooCommerce → Products → Categories);
 * until a category exists the link falls back to the shop, so nothing 404s.
 * Custom order points at the dedicated /custom-order/ page.
 *
 * @return array Array of array( url, label ).
 */
function wildflower_shop_menu() {
	$shop = ( class_exists( 'WooCommerce' ) && wc_get_page_permalink( 'shop' ) ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	$items = array(
		array( add_query_arg( 'orderby', 'popularity', $shop ), __( 'Best sellers', 'wildflower' ) ),
		array( add_query_arg( 'orderby', 'date', $shop ), __( 'New arrivals', 'wildflower' ) ),
		array( wildflower_resolve_product_cat( array( 'roses', 'rose' ), $shop ), __( 'Roses', 'wildflower' ) ),
		array( wildflower_resolve_product_cat( array( 'bouquets', 'bouquet' ), $shop ), __( 'Bouquets', 'wildflower' ) ),
		array( wildflower_resolve_product_cat( array( 'tin-can-bouquets', 'tin can bouquets', 'tin-can', 'tin can' ), $shop ), __( 'Tin Can Bouquets', 'wildflower' ) ),
		array( wildflower_resolve_product_cat( array( 'gifts', 'gift', 'add-ons', 'addons' ), $shop ), __( 'Gifts', 'wildflower' ) ),
		array( home_url( '/custom-order/' ), __( 'Custom order', 'wildflower' ) ),
	);

	/**
	 * Filter the curated Shop dropdown items (header + footer share this list).
	 *
	 * @param array $items Array of array( url, label ).
	 */
	return apply_filters( 'wildflower_shop_menu', $items );
}

function wildflower_nav( $menu_class = 'site-header__menu' ) {
	$current = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$path    = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );
		$current = untrailingslashit( home_url( $path ) );
	}

	$items         = wildflower_nav_items();
	$shop_url      = ( class_exists( 'WooCommerce' ) && wc_get_page_permalink( 'shop' ) ) ? untrailingslashit( wc_get_page_permalink( 'shop' ) ) : '';
	$shop_cats     = $shop_url ? wildflower_shop_menu() : array();
	$is_mobile_nav = ( false !== strpos( $menu_class, 'mobile' ) );

	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $it ) {
		$active       = ( untrailingslashit( $it[0] ) === $current ) ? ' current-menu-item' : '';
		$is_shop      = ( '' !== $shop_url && untrailingslashit( $it[0] ) === $shop_url );
		$has_children = ( $is_shop && ! empty( $shop_cats ) );

		echo '<li class="menu-item' . esc_attr( $active ) . ( $has_children ? ' menu-item--has-children' : '' ) . '">';

		if ( $has_children ) {
			echo '<a href="' . esc_url( $it[0] ) . '" class="menu-item__link">' . esc_html( $it[1] );
			echo '<svg class="menu-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></a>';
			echo '<ul class="submenu">';
			foreach ( $shop_cats as $cat ) {
				echo '<li><a href="' . esc_url( $cat[0] ) . '">' . esc_html( $cat[1] ) . '</a></li>';
			}
			echo '<li class="submenu__all"><a href="' . esc_url( $it[0] ) . '">' . esc_html__( 'All flowers', 'wildflower' ) . '</a></li>';
			echo '</ul>';
		} else {
			echo '<a href="' . esc_url( $it[0] ) . '">' . esc_html( $it[1] ) . '</a>';
		}

		echo '</li>';
	}
	echo '</ul>';
}


/**
 * Brand info used in templates and structured data.
 *
 * @return array
 */
function wildflower_brand() {
	return array(
		'name'    => get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Wildflower',
		'city'    => 'Greater Boston',
		'cutoff'  => '1 PM',
		'email'   => 'hello@wildflower.boston',
		'phone'   => '(617) 555-0142',
		'handle'  => '@wildflower.boston',
		'instagram' => 'https://instagram.com/wildflower.boston',
		'facebook'  => 'https://facebook.com/wildflower.boston',
		// WhatsApp number in international format, digits only (e.g. 16175550142).
		// Defaults to the phone digits — set the real WhatsApp line here.
		'whatsapp'  => '',
		/*
		 * Real studio address / geo — used by the LocalBusiness schema, the
		 * footer/contact blocks and the map. Keep in sync with Google Business
		 * Profile byte-for-byte.
		 */
		'street'   => '267 N Beacon St',
		'locality' => 'Brighton',
		'postal'   => '02135',
		'address'  => '267 N Beacon St, Brighton, MA 02135',
		'lat'      => '42.3577',
		'lng'      => '-71.1426',
		'maps'     => 'https://www.google.com/maps/search/?api=1&query=267+N+Beacon+St+Brighton+MA+02135',
	);
}

/**
 * Render an image, or an elegant botanical fallback when there is none.
 *
 * @param int|null $attachment_id Attachment ID.
 * @param string   $size          Image size.
 * @param string   $alt           Alt text / fallback label.
 * @param bool     $show_label    Show the serif label in the fallback.
 */
function wildflower_media( $attachment_id = null, $size = 'large', $alt = '', $show_label = true ) {
	echo '<span class="media">';
	if ( $attachment_id ) {
		echo wp_get_attachment_image( $attachment_id, $size, false, array( 'alt' => esc_attr( $alt ), 'loading' => 'lazy' ) ); // phpcs:ignore
	} else {
		echo '<span class="media-fallback" aria-hidden="true">';
		echo wildflower_flower_svg();
		if ( $show_label && $alt ) {
			echo '<span class="media-fallback__label">' . esc_html( $alt ) . '</span>';
		}
		echo '</span>';
	}
	echo '</span>';
}

/**
 * Return the attachment IDs assigned to the homepage media slots.
 *
 * The IDs live in WordPress rather than the theme so a future deployment does
 * not hard-code environment-specific attachment IDs.
 *
 * @return array<string, mixed>
 */
function wildflower_home_media() {
	$media = get_option( 'wildflower_home_media', array() );
	return is_array( $media ) ? $media : array();
}

/**
 * Return one homepage media attachment ID.
 *
 * @param string $key Media slot key.
 * @return int
 */
function wildflower_home_media_id( $key ) {
	$media = wildflower_home_media();
	return isset( $media[ $key ] ) ? absint( $media[ $key ] ) : 0;
}

/**
 * Return media assignment for a non-homepage template.
 *
 * Assignments are stored in WordPress so a theme deployment never contains
 * environment-specific attachment IDs.
 *
 * @param string $page_slug Page slug used as the media group key.
 * @return array<string, mixed>
 */
function wildflower_page_media( $page_slug ) {
	$all_media = get_option( 'wildflower_page_media', array() );
	if ( ! is_array( $all_media ) || ! isset( $all_media[ $page_slug ] ) || ! is_array( $all_media[ $page_slug ] ) ) {
		return array();
	}
	return $all_media[ $page_slug ];
}

/**
 * Return one non-homepage media attachment ID.
 *
 * @param string $page_slug Page slug used as the media group key.
 * @param string $key       Media slot key.
 * @return int
 */
function wildflower_page_media_id( $page_slug, $key ) {
	$media = wildflower_page_media( $page_slug );
	return isset( $media[ $key ] ) ? absint( $media[ $key ] ) : 0;
}

/**
 * Map a media URL to a video MIME type by extension.
 *
 * @param string $url Media URL.
 * @return string
 */
function wildflower_video_mime( $url ) {
	$ext = strtolower( (string) pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$map = array(
		'mp4'  => 'video/mp4',
		'm4v'  => 'video/mp4',
		'webm' => 'video/webm',
		'ogv'  => 'video/ogg',
		'mov'  => 'video/quicktime',
	);
	return isset( $map[ $ext ] ) ? $map[ $ext ] : 'video/mp4';
}

/**
 * Render the hero visual. Prefers a video (uploaded attachment or URL), falls
 * back to the hero image, then to the elegant botanical placeholder — so the
 * hero always looks intentional whatever is (or isn't) configured.
 */
function wildflower_hero_visual() {
	$video_id  = (int) get_theme_mod( 'wildflower_hero_video', 0 );
	$video_url = trim( (string) get_theme_mod( 'wildflower_hero_video_url', '' ) );
	$poster_id = (int) get_theme_mod( 'wildflower_hero_image', 0 );

	$src = '';
	if ( $video_id ) {
		$src = (string) wp_get_attachment_url( $video_id );
	} elseif ( $video_url ) {
		$src = $video_url;
	}

	if ( $src ) {
		$poster = $poster_id ? wp_get_attachment_image_url( $poster_id, 'large' ) : '';
		echo '<span class="media">';
		printf(
			'<video class="hero__video" data-hero-video autoplay muted loop playsinline preload="metadata"%1$s><source src="%2$s" type="%3$s"></video>',
			$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
			esc_url( $src ),
			esc_attr( wildflower_video_mime( $src ) )
		);
		echo '</span>';
		return;
	}

	wildflower_media( $poster_id ? $poster_id : null, 'large', 'Wildflower', true );
}

/**
 * Demo bouquet cards for the homepage scroller when WooCommerce has no products
 * yet (so the section is never empty). Mirrors the Woo `[products]` markup so the
 * same styles apply; real products replace these automatically once they exist.
 *
 * @param int $count How many cards.
 * @return string
 */
function wildflower_demo_products( $count = 6 ) {
	$demo = array(
		array( 'Pink Peony Dream', '$49' ),
		array( 'Sunday Market', '$50' ),
		array( 'Wildfield', '$52' ),
		array( 'Garden Blush', '$58' ),
		array( 'Meadow Light', '$54' ),
		array( 'Rosa Bianca', '$60' ),
		array( 'Coastal Stems', '$48' ),
		array( 'Studio Choice', '$56' ),
	);
	$shop = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$out  = '<ul class="products demo-products">';
	for ( $i = 0; $i < $count; $i++ ) {
		$d    = $demo[ $i % count( $demo ) ];
		$out .= '<li class="product">';
		$out .= '<div class="product__media">';
		$out .= '<span class="media-fallback media-fallback--' . ( ( $i % 5 ) + 1 ) . '" aria-hidden="true">' . wildflower_flower_svg() . '</span>';
		$out .= '<a class="product__view" href="' . esc_url( $shop ) . '"><span>' . esc_html__( 'View bouquet', 'wildflower' ) . '</span></a>';
		$out .= '</div>';
		$out .= '<div class="product__body">';
		$out .= '<div class="product__info">';
		$out .= '<h2 class="woocommerce-loop-product__title">' . esc_html( $d[0] ) . '</h2>';
		$out .= '<span class="price">' . esc_html( $d[1] ) . '</span>';
		$out .= '</div>';
		$out .= '<a class="button add_to_cart_button" href="' . esc_url( $shop ) . '" aria-label="' . esc_attr( sprintf( __( 'View %s', 'wildflower' ), $d[0] ) ) . '"></a>';
		$out .= '</div>';
		$out .= '</li>';
	}
	$out .= '</ul>';
	return $out;
}

/**
 * Render the scroll-story visual: a video (uploaded or URL) that the homepage
 * zooms on scroll, or the elegant botanical placeholder when none is set.
 */
function wildflower_story_visual() {
	$video_id  = (int) get_theme_mod( 'wildflower_story_video', 0 );
	$video_url = trim( (string) get_theme_mod( 'wildflower_story_video_url', '' ) );
	$poster_id = (int) get_theme_mod( 'wildflower_story_image', 0 );

	$src = $video_id ? (string) wp_get_attachment_url( $video_id ) : $video_url;

	if ( $src ) {
		$poster = $poster_id ? wp_get_attachment_image_url( $poster_id, 'full' ) : '';
		printf(
			'<video class="vstory__video" data-hero-video autoplay muted loop playsinline preload="metadata"%1$s><source src="%2$s" type="%3$s"></video>',
			$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
			esc_url( $src ),
			esc_attr( wildflower_video_mime( $src ) )
		);
		return;
	}
	echo '<span class="media-fallback media-fallback--2" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
}

/**
 * Render a rich mosaic of clickable placeholder tiles (varied sizes — big,
 * tall and wide) that open in a lightbox. The size pattern has a total area
 * that's a multiple of 12, so it tiles flush on 2 / 3 / 4 columns with no gaps
 * (render-verified). Pass $sets to repeat the pattern (e.g. 2 for the full
 * gallery page). Swap the fallbacks for real images later.
 *
 * @param int $sets Number of pattern repetitions.
 */
function wildflower_gallery( $sets = 1, $pattern = null, $attachment_ids = array() ) {
	if ( null === $pattern ) {
		$pattern = array( 'w2 h2', '', '', 'h2', 'w2', '', '', 'w2 h2', '', 'h2', 'w2', '', '', '' );
	}
	$len     = count( $pattern );
	$i       = 0;
	for ( $s = 0; $s < $sets; $s++ ) {
		foreach ( $pattern as $span ) {
			$variant = ( $i % 5 ) + 1;
			$delay   = ( $i % $len ) * 55;
			$attachment_id = isset( $attachment_ids[ $i ] ) ? absint( $attachment_ids[ $i ] ) : 0;
			echo '<button type="button" class="tile ' . esc_attr( $span ) . '" data-index="' . esc_attr( $i ) . '" data-delay="' . esc_attr( $delay ) . '" aria-label="' . esc_attr__( 'Open gallery image', 'wildflower' ) . '">';
			if ( $attachment_id ) {
				echo wp_get_attachment_image( $attachment_id, 'large', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="media-fallback media-fallback--' . esc_attr( $variant ) . '" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
			}
			echo '</button>';
			$i++;
		}
	}
}

/**
 * Botanical line motif used in image fallbacks.
 *
 * @return string
 */
function wildflower_flower_svg() {
	return '<svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"><path d="M50 78 V40"/><path d="M50 52 C42 50 36 44 36 36 C44 38 50 44 50 52 Z"/><path d="M50 52 C58 50 64 44 64 36 C56 38 50 44 50 52 Z"/><ellipse cx="50" cy="28" rx="5.5" ry="10"/><ellipse cx="50" cy="28" rx="5.5" ry="10" transform="rotate(60 50 28)"/><ellipse cx="50" cy="28" rx="5.5" ry="10" transform="rotate(120 50 28)"/><circle cx="50" cy="28" r="3"/></svg>';
}

/**
 * Wrap a string into kinetic words (each word animates up on reveal).
 * Use inside an element with class "kinetic" (the JS staggers the words).
 *
 * @param string $text Plain text.
 * @return string HTML.
 */
function wildflower_kinetic( $text ) {
	$words = preg_split( '/\s+/', trim( $text ) );
	$out   = '';
	foreach ( $words as $word ) {
		$out .= '<span class="word"><span>' . esc_html( $word ) . '</span></span> ';
	}
	return trim( $out );
}

/**
 * Render one Journal post card from the current loop post (shared by the
 * Journal index and single.php related posts).
 *
 * @param bool $featured Render the large featured layout.
 */
function wildflower_post_card( $featured = false ) {
	$cat   = get_the_category();
	$cname = ! empty( $cat ) ? $cat[0]->name : __( 'Journal', 'wildflower' );
	$cls   = $featured ? 'journal-feature reveal' : 'post-card reveal';
	?>
	<article <?php post_class( $cls ); ?>>
		<a class="<?php echo $featured ? 'journal-feature__media' : 'post-card__media'; ?> media" href="<?php the_permalink(); ?>">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( $featured ? 'large' : 'medium_large' );
			} else {
				echo '<span class="media-fallback" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
			}
			?>
		</a>
		<div class="<?php echo $featured ? 'journal-feature__body' : 'post-card__body'; ?>">
			<span class="post-card__cat"><?php echo esc_html( $featured ? __( 'Featured', 'wildflower' ) : $cname ); ?></span>
			<?php if ( $featured ) : ?>
				<h2 class="journal-feature__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php else : ?>
				<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php endif; ?>
			<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $featured ? 32 : 18 ) ); ?></p>
			<span class="post-card__meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( wildflower_read_time() ); ?></span>
			<?php if ( $featured ) : ?><a class="link-underline post-card__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read story', 'wildflower' ); ?> →</a><?php endif; ?>
		</div>
	</article>
	<?php
}

/**
 * Estimated reading time for a post.
 *
 * @param int|null $post_id Post ID (defaults to current).
 * @return string e.g. "4 min read".
 */
function wildflower_read_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() );
	$words   = str_word_count( wp_strip_all_tags( (string) $content ) );
	$min     = max( 1, (int) ceil( $words / 200 ) );
	/* translators: %d: minutes. */
	return sprintf( _n( '%d min read', '%d min read', $min, 'wildflower' ), $min );
}

/**
 * Small inline arrow icon for buttons.
 *
 * @return string
 */
function wildflower_arrow() {
	return '<span class="btn-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>';
}

/**
 * Customizer: hero image.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function wildflower_customize( $wp_customize ) {
	$wp_customize->add_section(
		'wildflower_home',
		array(
			'title'    => __( 'Wildflower — Home', 'wildflower' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting( 'wildflower_hero_image', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'wildflower_hero_image',
			array(
				'label'       => __( 'Hero image', 'wildflower' ),
				'description' => __( 'Shown when no hero video is set, and used as the video poster.', 'wildflower' ),
				'section'     => 'wildflower_home',
				'mime_type'   => 'image',
			)
		)
	);

	// Hero video — takes precedence over the image when present.
	$wp_customize->add_setting( 'wildflower_hero_video', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'wildflower_hero_video',
			array(
				'label'       => __( 'Hero video', 'wildflower' ),
				'description' => __( 'MP4 or WebM. Plays muted & looped; falls back to the hero image.', 'wildflower' ),
				'section'     => 'wildflower_home',
				'mime_type'   => 'video',
			)
		)
	);

	$wp_customize->add_setting( 'wildflower_hero_video_url', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		'wildflower_hero_video_url',
		array(
			'label'       => __( 'Hero video URL (optional)', 'wildflower' ),
			'description' => __( 'External MP4/WebM URL — used if no video is uploaded above.', 'wildflower' ),
			'section'     => 'wildflower_home',
			'type'        => 'url',
		)
	);

	// Scroll-story video (the cinematic zoom section on the homepage).
	$wp_customize->add_setting( 'wildflower_story_video', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'wildflower_story_video',
			array(
				'label'       => __( 'Scroll-story video', 'wildflower' ),
				'description' => __( 'MP4/WebM for the full-width zoom section. Falls back to a placeholder.', 'wildflower' ),
				'section'     => 'wildflower_home',
				'mime_type'   => 'video',
			)
		)
	);
	$wp_customize->add_setting( 'wildflower_story_video_url', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		'wildflower_story_video_url',
		array(
			'label'       => __( 'Scroll-story video URL (optional)', 'wildflower' ),
			'section'     => 'wildflower_home',
			'type'        => 'url',
		)
	);
	$wp_customize->add_setting( 'wildflower_story_image', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'wildflower_story_image',
			array(
				'label'     => __( 'Scroll-story poster image', 'wildflower' ),
				'section'   => 'wildflower_home',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'wildflower_customize' );

// Colour theme switcher ("the pult") — data-theme + REST API.
require get_template_directory() . '/inc/theme-switcher.php';

// Structured data (JSON-LD) and WooCommerce tweaks.
require get_template_directory() . '/inc/seo.php';
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
	require get_template_directory() . '/inc/shop-filters.php';
}

// Local delivery city landing-page data (used by the template + provisioning).
require get_template_directory() . '/inc/delivery-cities.php';

// Auto-create required pages (custom-order, faq, terms, privacy-policy, cities).
require get_template_directory() . '/inc/provision.php';

/**
 * Cart item count for the header (Woo aware, with graceful fallback).
 *
 * @return int
 */
function wildflower_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return WC()->cart->get_cart_contents_count();
	}
	return 0;
}
