<?php
/**
 * Theming engine + owner remote ("the pult").
 *
 * A *theme* here is a complete look, not a single colour. Each theme defines:
 *   • two accent colours (both change per theme, nothing is hardcoded),
 *   • a dark "statement" colour with single-hue gradient stops,
 *   • a typography pair (heading + body),
 *   • a button corner radius.
 *
 * Everything is defined ONCE in wildflower_themes() / wildflower_font_pairs().
 * The CSS variables for every theme are generated from that source, and the
 * remote builds its UI from the same source, so adding a theme is a one-entry
 * change with no copy-paste. The published theme is stored as a site option and
 * applied server-side via <html data-theme="…"> (no flash). Admins can preview
 * any theme without publishing via ?wf_preview=<id>.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WILDFLOWER_THEME_OPTION  = 'wildflower_theme';
const WILDFLOWER_THEME_DEFAULT = 'forest';

/**
 * Typography pairs. `google` is the query part of a fonts.googleapis.com URL.
 *
 * @return array<string,array<string,string>>
 */
function wildflower_font_pairs() {
	return array(
		'editorial' => array(
			'label'   => 'Editorial',
			'heading' => "'Fraunces', Georgia, serif",
			'body'    => "'Inter', ui-sans-serif, system-ui, sans-serif",
			'google'  => 'family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Inter:wght@400;500;600',
		),
		'modern' => array(
			'label'   => 'Modern',
			'heading' => "'Space Grotesk', ui-sans-serif, system-ui, sans-serif",
			'body'    => "'Inter', ui-sans-serif, system-ui, sans-serif",
			'google'  => 'family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600',
		),
		'classic' => array(
			'label'   => 'Classic',
			'heading' => "'Cormorant Garamond', Georgia, serif",
			'body'    => "'Inter', ui-sans-serif, system-ui, sans-serif",
			'google'  => 'family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@400;500;600',
		),
		'grotesk' => array(
			'label'   => 'Grotesk',
			'heading' => "'Schibsted Grotesk', ui-sans-serif, system-ui, sans-serif",
			'body'    => "'Inter', ui-sans-serif, system-ui, sans-serif",
			'google'  => 'family=Schibsted+Grotesk:ital,wght@0,400..800;1,400..700&family=Inter:wght@400;500;600',
		),
	);
}

/**
 * The curated themes, the single source of truth.
 *
 * Keys: label, desc, primary (+ -2 lighter / -3 darker gradient stops),
 * accent (main: prices, links, dot, cart), accent2 (headline italics &
 * highlights), font (key into wildflower_font_pairs), radius (button corner).
 *
 * @return array<string,array<string,string>>
 */
function wildflower_themes() {
	return array(
		'forest' => array(
			'label'  => 'Forest',
			'desc'   => 'Ivory + ink + matcha. Restrained monochrome, the flowers are the colour.',
			'primary' => '#45533a', 'primary2' => '#56653f', 'primary3' => '#2f3a25',
			'accent'  => '#5e7042', 'accent2'  => '#6f8350', 'ondark' => '#d4c193',
			'font'    => 'editorial', 'radius' => '3px',
		),
		'slate' => array(
			'label'  => 'Slate',
			'desc'   => 'Ivory + deep slate-blue. Cool, architectural, calm.',
			'primary' => '#3a4658', 'primary2' => '#4a586c', 'primary3' => '#2c3645',
			'accent'  => '#44546a', 'accent2'  => '#5a6a82', 'ondark' => '#d6c6a3',
			'font'    => 'editorial', 'radius' => '2px',
		),
		'evergreen' => array(
			'label'  => 'Evergreen',
			'desc'   => 'Near-black evergreen. Deep, hushed, luxe.',
			'primary' => '#1f3f36', 'primary2' => '#2c574a', 'primary3' => '#15302a',
			'accent'  => '#2f5a4d', 'accent2'  => '#3f6f60', 'ondark' => '#d4c193',
			'font'    => 'classic', 'radius' => '4px',
		),
		'aubergine' => array(
			'label'  => 'Aubergine',
			'desc'   => 'Dark plum. Romantic and opulent.',
			'primary' => '#43304a', 'primary2' => '#553f5c', 'primary3' => '#322338',
			'accent'  => '#6e4a6b', 'accent2'  => '#855f82', 'ondark' => '#e2c6c1',
			'font'    => 'classic', 'radius' => '4px',
		),
		'harbor' => array(
			'label'  => 'Harbor',
			'desc'   => 'Deep teal. Coastal, calm, fresh.',
			'primary' => '#1f4f54', 'primary2' => '#2c696f', 'primary3' => '#163b3f',
			'accent'  => '#2f7079', 'accent2'  => '#4a8a8a', 'ondark' => '#d9c59c',
			'font'    => 'grotesk', 'radius' => '2px',
		),
		'noir' => array(
			'label'  => 'Noir',
			'desc'   => 'Ivory + near-black. Gallery-grade minimal.',
			'primary' => '#29251f', 'primary2' => '#3a352d', 'primary3' => '#1c1915',
			'accent'  => '#4f4a40', 'accent2'  => '#6e685c', 'ondark' => '#d8c49a',
			'font'    => 'modern', 'radius' => '0',
		),
		'bordeaux' => array(
			'label'  => 'Bordeaux',
			'desc'   => 'Deep wine. Romantic, warm, elegant.',
			'primary' => '#4f2630', 'primary2' => '#6a3340', 'primary3' => '#3a1b23',
			'accent'  => '#7a3a47', 'accent2'  => '#9a5560', 'ondark' => '#e6c7a6',
			'font'    => 'classic', 'radius' => '3px',
		),
		'midnight' => array(
			'label'  => 'Midnight',
			'desc'   => 'Deep navy. Architectural and timeless.',
			'primary' => '#222b40', 'primary2' => '#313f5e', 'primary3' => '#19202f',
			'accent'  => '#34406a', 'accent2'  => '#4a5a86', 'ondark' => '#d6c49c',
			'font'    => 'grotesk', 'radius' => '0',
		),
		'stone' => array(
			'label'  => 'Stone',
			'desc'   => 'Cool slate-grey. Quiet, modern neutral.',
			'primary' => '#43474a', 'primary2' => '#555a5e', 'primary3' => '#33373a',
			'accent'  => '#565e63', 'accent2'  => '#6f767b', 'ondark' => '#d4c193',
			'font'    => 'editorial', 'radius' => '4px',
		),
		'blush' => array(
			'label'  => 'Blush',
			'desc'   => 'Ivory + deep mauve-rose. Soft, romantic, floral.',
			'primary' => '#6b3a48', 'primary2' => '#854a59', 'primary3' => '#4f2a35',
			'accent'  => '#8a4f5e', 'accent2'  => '#a86b78', 'ondark' => '#e8cda8',
			'font'    => 'classic', 'radius' => '5px',
		),
	);
}

/**
 * Sanitize a theme id against the allow-list.
 *
 * @param string $theme Candidate id.
 * @return string Valid id (falls back to default).
 */
function wildflower_sanitize_theme( $theme ) {
	$theme = is_string( $theme ) ? sanitize_key( $theme ) : '';
	return array_key_exists( $theme, wildflower_themes() ) ? $theme : WILDFLOWER_THEME_DEFAULT;
}

/**
 * The published theme (what every visitor gets).
 *
 * @return string
 */
function wildflower_published_theme() {
	return wildflower_sanitize_theme( get_option( WILDFLOWER_THEME_OPTION, WILDFLOWER_THEME_DEFAULT ) );
}

/**
 * The theme to render. Admins can preview any theme via ?wf_preview=<id>.
 *
 * @return string
 */
function wildflower_active_theme() {
	if ( isset( $_GET['wf_preview'] ) && current_user_can( 'edit_theme_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return wildflower_sanitize_theme( wp_unslash( $_GET['wf_preview'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	return wildflower_published_theme();
}

/**
 * Resolve a theme's font pair (with a safe fallback).
 *
 * @param string $theme Theme id.
 * @return array<string,string>
 */
function wildflower_theme_font( $theme ) {
	$themes = wildflower_themes();
	$pairs  = wildflower_font_pairs();
	$key    = isset( $themes[ $theme ]['font'] ) ? $themes[ $theme ]['font'] : 'editorial';
	return isset( $pairs[ $key ] ) ? $pairs[ $key ] : reset( $pairs );
}

/**
 * Pick a readable text colour (dark or light) for a given accent background,
 * using WCAG relative luminance, so accent strips/badges stay legible across
 * every theme (a light gold and a dark wine both get the right text).
 *
 * @param string $hex Background colour.
 * @return string Ink or cream.
 */
function wildflower_readable_on( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return '#f7f4ee';
	}
	$lin = function ( $c ) {
		$c = $c / 255;
		return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
	};
	$l = 0.2126 * $lin( hexdec( substr( $hex, 0, 2 ) ) )
		+ 0.7152 * $lin( hexdec( substr( $hex, 2, 2 ) ) )
		+ 0.0722 * $lin( hexdec( substr( $hex, 4, 2 ) ) );
	return $l > 0.42 ? '#1b1916' : '#f7f4ee';
}

/**
 * Build the CSS variable block for one theme.
 *
 * @param array<string,string> $t Theme.
 * @return string CSS declarations (no selector).
 */
function wildflower_theme_vars( $t ) {
	$font   = wildflower_theme_font_by_key( isset( $t['font'] ) ? $t['font'] : 'editorial' );
	$ondark = isset( $t['ondark'] ) ? $t['ondark'] : '#d4c193'; // warm champagne fallback.
	return sprintf(
		'--primary:%1$s;--primary-2:%2$s;--primary-3:%3$s;--accent:%4$s;--accent-2:%5$s;--accent-foreground:%9$s;--accent-on-dark:%10$s;--ring:%4$s;--radius-btn:%6$s;--font-serif:%7$s;--font-sans:%8$s;',
		$t['primary'],
		$t['primary2'],
		$t['primary3'],
		$t['accent'],
		$t['accent2'],
		$t['radius'],
		$font['heading'],
		$font['body'],
		wildflower_readable_on( $t['accent'] ),
		$ondark
	);
}

/**
 * Font pair by key (helper that won't recurse into theme lookup).
 *
 * @param string $key Pair id.
 * @return array<string,string>
 */
function wildflower_theme_font_by_key( $key ) {
	$pairs = wildflower_font_pairs();
	return isset( $pairs[ $key ] ) ? $pairs[ $key ] : reset( $pairs );
}

/**
 * Generate and attach the CSS for every theme (single source of truth).
 */
function wildflower_generate_theme_css() {
	$css = '';
	foreach ( wildflower_themes() as $id => $t ) {
		$css .= '[data-theme="' . $id . '"]{' . wildflower_theme_vars( $t ) . '}';
	}
	// Default values on :root so the site is correct even before data-theme.
	$themes = wildflower_themes();
	$def    = isset( $themes[ WILDFLOWER_THEME_DEFAULT ] ) ? $themes[ WILDFLOWER_THEME_DEFAULT ] : reset( $themes );
	$css   .= ':root{' . wildflower_theme_vars( $def ) . '}';
	wp_add_inline_style( 'wildflower-style', $css );
}
add_action( 'wp_enqueue_scripts', 'wildflower_generate_theme_css', 20 );

/**
 * Load only the active theme's font pair (front-end + previews).
 */
function wildflower_enqueue_theme_fonts() {
	$font = wildflower_theme_font( wildflower_active_theme() );
	wp_enqueue_style(
		'wildflower-fonts',
		'https://fonts.googleapis.com/css2?' . $font['google'] . '&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'wildflower_enqueue_theme_fonts', 5 );

/**
 * Put data-theme on the <html> element (header.php prints language_attributes()).
 *
 * @param string $output Existing attribute string.
 * @return string
 */
function wildflower_html_theme_attr( $output ) {
	return trim( $output . ' data-theme="' . esc_attr( wildflower_active_theme() ) . '"' );
}
add_filter( 'language_attributes', 'wildflower_html_theme_attr' );

/**
 * Compact theme list for the remote (id, label, desc, swatches, font label).
 *
 * @return array<string,array<string,string>>
 */
function wildflower_themes_for_remote() {
	$pairs = wildflower_font_pairs();
	$out   = array();
	foreach ( wildflower_themes() as $id => $t ) {
		$out[ $id ] = array(
			'label'   => $t['label'],
			'desc'    => $t['desc'],
			'primary' => $t['primary'],
			'accent'  => $t['accent'],
			'accent2' => $t['accent2'],
			'font'    => isset( $pairs[ $t['font'] ] ) ? $pairs[ $t['font'] ]['label'] : ucfirst( $t['font'] ),
			'radius'  => $t['radius'],
		);
	}
	return $out;
}

/**
 * REST API: read themes + state, and (admins) publish a new one.
 */
function wildflower_register_theme_rest() {
	register_rest_route(
		'wildflower/v1',
		'/theme',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => function () {
					return rest_ensure_response(
						array(
							'theme'    => wildflower_published_theme(),
							'default'  => WILDFLOWER_THEME_DEFAULT,
							'palettes' => wildflower_themes_for_remote(),
						)
					);
				},
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
				'args'                => array(
					'theme' => array( 'required' => true, 'type' => 'string' ),
				),
				'callback'            => function ( WP_REST_Request $req ) {
					$theme = wildflower_sanitize_theme( $req->get_param( 'theme' ) );
					update_option( WILDFLOWER_THEME_OPTION, $theme );
					return rest_ensure_response( array( 'ok' => true, 'theme' => $theme ) );
				},
			),
		)
	);
}
add_action( 'rest_api_init', 'wildflower_register_theme_rest' );
