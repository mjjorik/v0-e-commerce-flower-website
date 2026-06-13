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

		<div class="footer-cols">
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div>
					<h3><?php esc_html_e( 'Explore', 'wildflower' ); ?></h3>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</div>
			<?php endif; ?>

			<div>
				<h3><?php esc_html_e( 'Help', 'wildflower' ); ?></h3>
				<ul class="footer-menu">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'wildflower' ); ?></a></li>
						<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Basket', 'wildflower' ); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'wildflower' ); ?></a></li>
				</ul>
			</div>

			<div>
				<h3><?php esc_html_e( 'Studio', 'wildflower' ); ?></h3>
				<p style="margin-top:1rem;font-size:.9rem;color:color-mix(in oklab,var(--foreground) 80%,transparent);">
					<?php echo esc_html( $brand['email'] ); ?><br>
					<?php echo esc_html( $brand['phone'] ); ?>
				</p>
				<p style="margin-top:.75rem;">
					<a class="link-underline" href="<?php echo esc_url( $brand['instagram'] ); ?>" rel="noopener"><?php echo esc_html( $brand['handle'] ); ?></a>
				</p>
			</div>

			<div>
				<h3><?php esc_html_e( 'Newsletter', 'wildflower' ); ?></h3>
				<p style="margin-top:1rem;font-size:.9rem;color:color-mix(in oklab,var(--foreground) 80%,transparent);">
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

<?php wp_footer(); ?>
</body>
</html>
