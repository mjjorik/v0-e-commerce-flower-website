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
	<script>document.documentElement.className += ' js';</script>
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
			esc_html__( 'Same-day delivery across %1$s, order by %2$s', 'wildflower' ),
			esc_html( $brand['city'] ),
			esc_html( $brand['cutoff'] )
		);
		?>
		<button class="announce__close" data-announce-close aria-label="<?php esc_attr_e( 'Dismiss', 'wildflower' ); ?>">&times;</button>
	</div>
</div>

<header class="site-header" data-site-header>
	<div class="container site-header__inner">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
				echo wp_kses_post(
					wp_get_attachment_image(
						$custom_logo_id,
						'full',
						false,
						array(
							'class' => 'custom-logo',
							'alt'   => get_bloginfo( 'name' ),
						)
					)
				);
			} else {
				echo esc_html( $brand['name'] );
			}
			?>
		</a>

		<nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'wildflower' ); ?>">
			<?php wildflower_nav( 'site-header__menu' ); ?>
		</nav>

		<div class="site-header__actions">
			<button class="header-icon" data-search-toggle aria-label="<?php esc_attr_e( 'Search', 'wildflower' ); ?>" aria-expanded="false">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			</button>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a class="header-icon" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'Account', 'wildflower' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
				</a>
				<a class="header-icon cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'View cart', 'wildflower' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
					<span class="cart-toggle__count" data-cart-count><?php echo esc_html( wildflower_cart_count() ); ?></span>
				</a>
			<?php endif; ?>
			<button class="menu-toggle header-icon" data-menu-open aria-label="<?php esc_attr_e( 'Open menu', 'wildflower' ); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
			</button>
		</div>
	</div>
	<div class="header-search" data-search-panel>
		<form class="header-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search bouquets…', 'wildflower' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'wildflower' ); ?>" data-search-input>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button type="submit" class="btn--primary btn--sm"><?php esc_html_e( 'Search', 'wildflower' ); ?></button>
		</form>
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
		<?php wildflower_nav( 'mobile-nav__menu' ); ?>
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
