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
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My account', 'wildflower' ); ?></a></li>
						<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Basket', 'wildflower' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Studio', 'wildflower' ); ?></h3>
				<p class="footer-contact" style="margin-top:1rem;font-size:.9rem;">
					<?php echo esc_html( $brand['email'] ); ?><br>
					<?php echo esc_html( $brand['phone'] ); ?>
				</p>
				<p style="margin-top:.75rem;">
					<a class="link-underline" href="<?php echo esc_url( $brand['instagram'] ); ?>" rel="noopener"><?php echo esc_html( $brand['handle'] ); ?></a>
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
