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
 * Ensure a "Shop" link is always present at the start of the primary menu
 * when WooCommerce is active — so it shows in both the desktop nav and the
 * mobile burger without manual menu editing.
 *
 * @param string   $items HTML list items.
 * @param stdClass $args  Menu args.
 * @return string
 */
function wildflower_inject_shop( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $items;
	}
	$shop_url = wc_get_page_permalink( 'shop' );
	if ( ! $shop_url || false !== stripos( $items, '>' . __( 'Shop', 'wildflower' ) . '<' ) ) {
		return $items;
	}
	$shop = '<li class="menu-item menu-item--shop"><a href="' . esc_url( $shop_url ) . '">' . esc_html__( 'Shop', 'wildflower' ) . '</a></li>';
	return $shop . $items;
}
add_filter( 'wp_nav_menu_items', 'wildflower_inject_shop', 10, 2 );

/**
 * Default menu used when no menu is assigned to a location — guarantees the
 * theme has navigation (incl. Shop) out of the box.
 *
 * @param array $args Menu args.
 */
function wildflower_default_menu( $args ) {
	$location = isset( $args['theme_location'] ) ? $args['theme_location'] : '';
	if ( 'primary' !== $location ) {
		return;
	}
	$class = isset( $args['menu_class'] ) ? $args['menu_class'] : '';
	$links = array();
	if ( class_exists( 'WooCommerce' ) && wc_get_page_permalink( 'shop' ) ) {
		$links[ wc_get_page_permalink( 'shop' ) ] = __( 'Shop', 'wildflower' );
	}
	$links[ home_url( '/subscriptions/' ) ] = __( 'Subscriptions', 'wildflower' );
	$links[ home_url( '/occasions/' ) ]     = __( 'Occasions', 'wildflower' );
	$links[ home_url( '/gallery/' ) ]        = __( 'Gallery', 'wildflower' );
	$links[ home_url( '/journal/' ) ]        = __( 'Journal', 'wildflower' );
	$links[ home_url( '/delivery/' ) ]       = __( 'Delivery', 'wildflower' );
	$links[ home_url( '/about/' ) ]          = __( 'About', 'wildflower' );

	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $url => $label ) {
		echo '<li class="menu-item"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
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
 * Render a mosaic of placeholder gallery tiles that assemble in on scroll.
 * Varied sizes + colourful botanical gradients. Swap for real images later.
 *
 * @param int  $count       Number of tiles.
 * @param bool $with_spans  Use varied tile sizes (mosaic) vs uniform.
 */
function wildflower_gallery( $count = 9, $with_spans = true ) {
	$spans = $with_spans
		? array( 'c2 r2', '', '', 'c2', '', 'r2', '', 'c2', '', 'r2', '', '', 'c2', '', '' )
		: array();
	for ( $i = 0; $i < $count; $i++ ) {
		$variant = ( $i % 5 ) + 1;
		$span    = isset( $spans[ $i ] ) ? $spans[ $i ] : '';
		echo '<span class="tile ' . esc_attr( $span ) . '" data-delay="' . esc_attr( $i * 70 ) . '">';
		echo '<span class="media-fallback media-fallback--' . esc_attr( $variant ) . '" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
		echo '</span>';
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
