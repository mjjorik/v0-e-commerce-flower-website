<?php
/**
 * 404 template.
 *
 * @package Wildflower
 */

get_header();
?>
<div class="container" style="min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding-block:6rem;">
	<p class="eyebrow"><?php esc_html_e( '404', 'wildflower' ); ?></p>
	<h1 style="font-size:clamp(2.5rem,6vw,4rem);margin-top:.75rem;"><?php esc_html_e( 'This page wilted.', 'wildflower' ); ?></h1>
	<p class="muted" style="margin-top:1rem;max-width:28rem;"><?php esc_html_e( 'The page you’re after isn’t here — but our flowers are very much alive.', 'wildflower' ); ?></p>
	<a class="btn--primary" style="margin-top:2rem;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
</div>
<?php
get_footer();
