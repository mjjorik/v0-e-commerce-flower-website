<?php
/**
 * Header template.
 *
 * @package Wildflower
 */

$brand = wildflower_brand();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#FDFBF7">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sr-only" href="#main"><?php esc_html_e( 'Skip to content', 'wildflower' ); ?></a>

<div class="announce" data-announce>
	<div class="announce__inner">
		<?php
		/* translators: 1: city, 2: order cutoff time. */
		printf(
			esc_html__( 'Same-day delivery across %1$s — order by %2$s', 'wildflower' ),
			esc_html( $brand['city'] ),
			esc_html( $brand['cutoff'] )
		);
		?>
		<button class="announce__close" data-announce-close aria-label="<?php esc_attr_e( 'Dismiss', 'wildflower' ); ?>">&times;</button>
	</div>
</div>

<header class="site-header" data-site-header>
	<div class="container site-header__inner">
		<div style="display:flex;flex:1;align-items:center;">
			<button class="menu-toggle" data-menu-open aria-label="<?php esc_attr_e( 'Open menu', 'wildflower' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
			</button>
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => 'nav',
						'container_class' => 'site-header__nav',
						'menu_class'     => 'site-header__menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			}
			?>
		</div>

		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo esc_html( $brand['name'] );
			}
			?>
		</a>

		<div class="site-header__actions">
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a class="btn--primary btn--sm order-now" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Order Now', 'wildflower' ); ?></a>
				<a class="cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'View basket', 'wildflower' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
					<span class="cart-toggle__count" data-cart-count><?php echo esc_html( wildflower_cart_count() ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>

<div class="mobile-nav" data-mobile-nav>
	<div class="mobile-nav__top">
		<span class="mobile-nav__brand"><?php echo esc_html( $brand['name'] ); ?></span>
		<button data-menu-close aria-label="<?php esc_attr_e( 'Close menu', 'wildflower' ); ?>">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
		</button>
	</div>
	<div class="mobile-nav__links">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-nav__menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
		}
		?>
	</div>
	<div class="mobile-nav__foot">
		<p><?php echo esc_html( $brand['email'] ); ?></p>
		<p>
			<?php
			printf(
				esc_html__( 'Same-day across %1$s · order by %2$s', 'wildflower' ),
				esc_html( $brand['city'] ),
				esc_html( $brand['cutoff'] )
			);
			?>
		</p>
	</div>
</div>

<main id="main">
