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

<?php wp_footer(); ?>
</body>
</html>
