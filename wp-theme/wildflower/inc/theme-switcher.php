<?php
/**
 * Theme switcher ("the pult").
 *
 * Stores the active colour theme as a single site option and applies it to the
 * <html> element server-side, so every visitor sees the published theme with no
 * flash of the wrong palette. A small REST API lets the Studio remote read the
 * available palettes and (for admins) publish a new one instantly.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WILDFLOWER_THEME_OPTION  = 'wildflower_theme';
const WILDFLOWER_THEME_DEFAULT = 'slate';

/**
 * The curated palettes. This is the single source of truth — it must mirror the
 * [data-theme="…"] blocks in style.css. The remote builds its buttons from here,
 * so adding a theme is: add the CSS block + an entry here.
 *
 * @return array<string,array<string,string>>
 */
function wildflower_theme_palettes() {
	return array(
		'slate' => array(
			'label'   => __( 'Slate', 'wildflower' ),
			'desc'    => __( 'Cool, editorial, interior-brand calm. Stays out of the flowers’ way.', 'wildflower' ),
			'primary' => '#44546a',
			'accent'  => '#b56a52',
		),
		'evergreen' => array(
			'label'   => __( 'Evergreen', 'wildflower' ),
			'desc'    => __( 'Deep, near-black green. Luxe hospitality mood.', 'wildflower' ),
			'primary' => '#1c4a40',
			'accent'  => '#b56a52',
		),
		'aubergine' => array(
			'label'   => __( 'Aubergine', 'wildflower' ),
			'desc'    => __( 'Dark plum. Romantic, rich, classic florist warmth.', 'wildflower' ),
			'primary' => '#4b304a',
			'accent'  => '#b56a52',
		),
		'copper' => array(
			'label'   => __( 'Copper', 'wildflower' ),
			'desc'    => __( 'Warm tonal brown-copper. Friendly and inviting.', 'wildflower' ),
			'primary' => '#7a4a38',
			'accent'  => '#c77e63',
		),
	);
}

/**
 * Sanitize a theme id against the allow-list of curated palettes.
 *
 * @param string $theme Candidate id.
 * @return string A valid theme id (falls back to the default).
 */
function wildflower_sanitize_theme( $theme ) {
	$theme = is_string( $theme ) ? sanitize_key( $theme ) : '';
	return array_key_exists( $theme, wildflower_theme_palettes() ) ? $theme : WILDFLOWER_THEME_DEFAULT;
}

/**
 * The published theme (what visitors get).
 *
 * @return string
 */
function wildflower_published_theme() {
	return wildflower_sanitize_theme( get_option( WILDFLOWER_THEME_OPTION, WILDFLOWER_THEME_DEFAULT ) );
}

/**
 * The theme to actually render. Admins can preview any theme without publishing
 * via ?wf_preview=<id>; everyone else always gets the published theme.
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
 * REST API: read palettes + current theme, and (admins) publish a new one.
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
							'theme'     => wildflower_published_theme(),
							'default'   => WILDFLOWER_THEME_DEFAULT,
							'palettes'  => wildflower_theme_palettes(),
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
