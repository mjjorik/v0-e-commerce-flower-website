<?php
/**
 * Footer template.
 *
 * @package Wildflower
 */

$brand = wildflower_brand();
?>
</main><!-- #main -->

<footer class="site-footer">
	<div class="container site-footer__main">
		<h2 class="site-footer__statement"><?php esc_html_e( 'Flowers for every day, not just occasions.', 'wildflower' ); ?></h2>

		<?php
		/*
		 * Footer navigation is built from the SAME source as the header
		 * (wildflower_nav_items), split into two balanced columns so every page
		 * in the header also appears in the footer. Add WooCommerce account
		 * links so the footer is complete.
		 */
		$wf_nav_items  = function_exists( 'wildflower_nav_items' ) ? wildflower_nav_items() : array();
		$wf_info_slugs = array( '/journal/', '/delivery/', '/about/', '/contact/' );
		$wf_explore    = array();
		$wf_company    = array();
		foreach ( $wf_nav_items as $wf_it ) {
			$wf_is_info = false;
			foreach ( $wf_info_slugs as $wf_slug ) {
				if ( false !== strpos( trailingslashit( $wf_it[0] ), $wf_slug ) ) {
					$wf_is_info = true;
					break;
				}
			}
			if ( $wf_is_info ) {
				$wf_company[] = $wf_it;
			} else {
				$wf_explore[] = $wf_it;
			}
		}
		?>
		<div class="footer-cols">
			<div>
				<h3><?php esc_html_e( 'Shop', 'wildflower' ); ?></h3>
				<ul class="footer-menu">
					<?php
					$wf_shop_menu = function_exists( 'wildflower_shop_menu' ) ? wildflower_shop_menu() : array();
					foreach ( $wf_shop_menu as $wf_sit ) :
						?>
						<li><a href="<?php echo esc_url( $wf_sit[0] ); ?>"><?php echo esc_html( $wf_sit[1] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Explore', 'wildflower' ); ?></h3>
				<ul class="footer-menu">
					<?php foreach ( $wf_explore as $wf_it ) : ?>
						<li><a href="<?php echo esc_url( $wf_it[0] ); ?>"><?php echo esc_html( $wf_it[1] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Company', 'wildflower' ); ?></h3>
				<ul class="footer-menu">
					<?php foreach ( $wf_company as $wf_it ) : ?>
						<li><a href="<?php echo esc_url( $wf_it[0] ); ?>"><?php echo esc_html( $wf_it[1] ); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'wildflower' ); ?></a></li>
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My account', 'wildflower' ); ?></a></li>
						<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Cart', 'wildflower' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Studio', 'wildflower' ); ?></h3>
				<p class="footer-contact" style="margin-top:1rem;font-size:.9rem;">
					<?php echo esc_html( $brand['email'] ); ?><br>
					<?php echo esc_html( $brand['phone'] ); ?>
				</p>
				<div class="footer-social" aria-label="<?php esc_attr_e( 'Follow us', 'wildflower' ); ?>">
					<a href="<?php echo esc_url( $brand['instagram'] ); ?>" rel="noopener" target="_blank" aria-label="Instagram">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.4" cy="6.6" r="1.1" fill="currentColor" stroke="none"/></svg>
					</a>
					<?php if ( ! empty( $brand['facebook'] ) ) : ?>
						<a href="<?php echo esc_url( $brand['facebook'] ); ?>" rel="noopener" target="_blank" aria-label="Facebook">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H17V3.6c-.29-.04-1.28-.13-2.43-.13-2.4 0-4.05 1.47-4.05 4.16v2.32H7.8V13h2.72v8h2.98z"/></svg>
						</a>
					<?php endif; ?>
				</div>
				<p style="margin-top:.7rem;">
					<a class="link-underline" href="<?php echo esc_url( $brand['instagram'] ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $brand['handle'] ); ?></a>
				</p>
			</div>

			<div>
				<h3><?php esc_html_e( 'Newsletter', 'wildflower' ); ?></h3>
				<p style="margin-top:1rem;font-size:.9rem;">
					<?php esc_html_e( 'Seasonal blooms, delivery news, the occasional secret sale.', 'wildflower' ); ?>
				</p>
				<form class="newsletter-form" onsubmit="return false;">
					<input type="email" placeholder="<?php esc_attr_e( 'Your email', 'wildflower' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'wildflower' ); ?>">
					<button type="submit" aria-label="<?php esc_attr_e( 'Subscribe', 'wildflower' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
					</button>
				</form>
			</div>
		</div>

		<?php
		$wf_footer_cities = function_exists( 'wildflower_delivery_cities' ) ? wildflower_delivery_cities() : array();
		if ( $wf_footer_cities ) :
			?>
			<nav class="footer-areas" aria-label="<?php esc_attr_e( 'Delivery areas', 'wildflower' ); ?>">
				<span class="footer-areas__label"><?php esc_html_e( 'Same-day delivery:', 'wildflower' ); ?></span>
				<?php foreach ( $wf_footer_cities as $wf_fslug => $wf_fc ) : ?>
					<a href="<?php echo esc_url( home_url( '/' . $wf_fslug . '/' ) ); ?>"><?php echo esc_html( $wf_fc['name'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $brand['name'] ); ?>. <?php printf( esc_html__( 'Made in %s.', 'wildflower' ), esc_html( $brand['city'] ) ); ?></p>
			<nav class="footer-legal" aria-label="<?php esc_attr_e( 'Legal', 'wildflower' ); ?>">
				<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'wildflower' ); ?></a>
				<a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'wildflower' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'wildflower' ); ?></a>
			</nav>
			<p><?php esc_html_e( 'Delivering across Boston, Cambridge, Somerville, Brookline & beyond.', 'wildflower' ); ?></p>
		</div>
	</div>
</footer>

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<!-- Mobile sticky action bar — appears after the hero (phones only) -->
<div class="mobile-bar" data-mobile-bar aria-hidden="true">
	<div class="mobile-bar__info">
		<span class="mobile-bar__label"><?php esc_html_e( 'Bouquets', 'wildflower' ); ?></span>
		<span class="mobile-bar__price"><?php esc_html_e( 'From $50 · same-day', 'wildflower' ); ?></span>
	</div>
	<a class="btn--accent btn--sm" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop now', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
</div>
<?php endif; ?>

<?php
/* Floating WhatsApp button (bottom-right). Uses the WhatsApp number if set,
   otherwise the studio phone digits — set brand['whatsapp'] to the real line. */
$wf_wa = ! empty( $brand['whatsapp'] ) ? preg_replace( '/[^0-9]/', '', $brand['whatsapp'] ) : preg_replace( '/[^0-9]/', '', $brand['phone'] );
if ( strlen( $wf_wa ) === 10 ) {
	$wf_wa = '1' . $wf_wa; // US country code.
}
if ( $wf_wa ) :
	?>
	<a class="wa-float" href="https://wa.me/<?php echo esc_attr( $wf_wa ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'wildflower' ); ?>">
		<svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.15-1.77-.87-2-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.67-2.08-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.53.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5l-.57-.01c-.2 0-.52.07-.8.37-.27.3-1.05 1.02-1.05 2.5 0 1.47 1.08 2.9 1.23 3.1.15.2 2.12 3.24 5.13 4.54.72.31 1.27.5 1.7.63.72.23 1.37.2 1.88.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35zM12 2a10 10 0 0 0-8.53 15.26L2 22l4.85-1.27A10 10 0 1 0 12 2z"/></svg>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
