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
	// Fonts.
	wp_enqueue_style(
		'wildflower-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..600;1,9..144,400..600&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);

	// Main stylesheet.
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
function wildflower_nav( $menu_class = 'site-header__menu' ) {
	$current = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$path    = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );
		$current = untrailingslashit( home_url( $path ) );
	}

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

	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $it ) {
		$active = ( untrailingslashit( $it[0] ) === $current ) ? ' current-menu-item' : '';
		echo '<li class="menu-item' . esc_attr( $active ) . '"><a href="' . esc_url( $it[0] ) . '">' . esc_html( $it[1] ) . '</a></li>';
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
 * Render a rich mosaic of clickable placeholder tiles (varied sizes — big,
 * tall and wide) that open in a lightbox. The size pattern has a total area
 * that's a multiple of 12, so it tiles flush on 2 / 3 / 4 columns with no gaps
 * (render-verified). Pass $sets to repeat the pattern (e.g. 2 for the full
 * gallery page). Swap the fallbacks for real images later.
 *
 * @param int $sets Number of pattern repetitions.
 */
function wildflower_gallery( $sets = 1 ) {
	$pattern = array( 'w2 h2', '', '', 'h2', 'w2', '', '', 'w2 h2', '', 'h2', 'w2', '', '', '' );
	$len     = count( $pattern );
	$i       = 0;
	for ( $s = 0; $s < $sets; $s++ ) {
		foreach ( $pattern as $span ) {
			$variant = ( $i % 5 ) + 1;
			$delay   = ( $i % $len ) * 55;
			echo '<button type="button" class="tile ' . esc_attr( $span ) . '" data-index="' . esc_attr( $i ) . '" data-delay="' . esc_attr( $delay ) . '" aria-label="' . esc_attr__( 'Open gallery image', 'wildflower' ) . '">';
			echo '<span class="media-fallback media-fallback--' . esc_attr( $variant ) . '" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
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
				'label'     => __( 'Hero image', 'wildflower' ),
				'section'   => 'wildflower_home',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'wildflower_customize' );

// Structured data (JSON-LD) and WooCommerce tweaks.
require get_template_directory() . '/inc/seo.php';
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

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
