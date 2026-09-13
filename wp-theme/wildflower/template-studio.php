<?php
/**
 * Template Name: Wildflower Studio (Remote)
 *
 * A standalone, installable (PWA) "pult" for the site owner. Big buttons switch
 * the colour theme; a live preview shows the change before you Publish it to all
 * visitors. Admin-only, everyone else sees a sign-in prompt.
 *
 * To use: create a Page (e.g. "Studio"), set its template to "Wildflower Studio
 * (Remote)", then open it on your phone/desktop and "Add to Home Screen".
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tpl   = get_template_directory();
$uri   = get_template_directory_uri();
$can   = current_user_can( 'edit_theme_options' );
$cssv  = file_exists( $tpl . '/assets/css/studio.css' ) ? filemtime( $tpl . '/assets/css/studio.css' ) : '1';
$jsv   = file_exists( $tpl . '/assets/js/studio.js' ) ? filemtime( $tpl . '/assets/js/studio.js' ) : '1';

$boot = array(
	'restUrl'     => esc_url_raw( rest_url( 'wildflower/v1/theme' ) ),
	'nonce'       => wp_create_nonce( 'wp_rest' ),
	'published'   => wildflower_published_theme(),
	'default'     => WILDFLOWER_THEME_DEFAULT,
	'palettes'    => wildflower_theme_palettes(),
	'previewBase' => esc_url_raw( home_url( '/' ) ),
	'canEdit'     => (bool) $can,
);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#2b2724">
	<title><?php esc_html_e( 'Wildflower Studio', 'wildflower' ); ?></title>
	<link rel="manifest" href="<?php echo esc_url( $uri . '/assets/studio/manifest.webmanifest' ); ?>">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..600;1,9..144,400..600&family=Inter:wght@400;500;600&display=swap">
	<link rel="stylesheet" href="<?php echo esc_url( $uri . '/assets/css/studio.css?v=' . $cssv ); ?>">
</head>
<body class="studio">
<?php if ( ! $can ) : ?>
	<main class="studio-gate">
		<div class="studio-gate__card">
			<p class="studio-eyebrow"><?php esc_html_e( 'Wildflower Studio', 'wildflower' ); ?></p>
			<h1><?php esc_html_e( 'The remote is for the studio.', 'wildflower' ); ?></h1>
			<p class="studio-muted"><?php esc_html_e( 'Sign in as an administrator to switch the site’s look.', 'wildflower' ); ?></p>
			<a class="studio-btn" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign in', 'wildflower' ); ?></a>
		</div>
	</main>
<?php else : ?>
	<div class="studio-app" data-studio>
		<aside class="studio-panel">
			<header class="studio-head">
				<p class="studio-eyebrow"><?php esc_html_e( 'Wildflower Studio', 'wildflower' ); ?></p>
				<h1><?php esc_html_e( 'Site remote', 'wildflower' ); ?></h1>
				<p class="studio-muted"><?php esc_html_e( 'Pick a mood. Preview it live. Publish when it feels right.', 'wildflower' ); ?></p>
			</header>

			<div class="studio-presets" data-presets><!-- buttons injected by studio.js --></div>

			<footer class="studio-foot">
				<div class="studio-status" data-status>
					<span class="studio-status__dot"></span>
					<span data-status-text><?php esc_html_e( 'Loading…', 'wildflower' ); ?></span>
				</div>
				<button class="studio-btn studio-btn--publish" data-publish disabled>
					<?php esc_html_e( 'Publish to live', 'wildflower' ); ?>
				</button>
				<p class="studio-hint" data-hint></p>
			</footer>
		</aside>

		<main class="studio-stage">
			<div class="studio-frame" data-frame-wrap>
				<div class="studio-frame__bar">
					<span class="studio-dots"><i></i><i></i><i></i></span>
					<span class="studio-frame__url" data-frame-url></span>
					<button class="studio-device" data-device aria-label="<?php esc_attr_e( 'Toggle device', 'wildflower' ); ?>"><?php esc_html_e( 'Mobile', 'wildflower' ); ?></button>
				</div>
				<iframe class="studio-frame__view" data-frame title="<?php esc_attr_e( 'Live preview', 'wildflower' ); ?>"></iframe>
			</div>
		</main>
	</div>

	<script>window.WF_STUDIO = <?php echo wp_json_encode( $boot ); ?>;</script>
	<script src="<?php echo esc_url( $uri . '/assets/js/studio.js?v=' . $jsv ); ?>"></script>
	<script>
		if ('serviceWorker' in navigator) {
			navigator.serviceWorker.register('<?php echo esc_url( $uri . '/assets/studio/sw.js' ); ?>').catch(function(){});
		}
	</script>
<?php endif; ?>
</body>
</html>
