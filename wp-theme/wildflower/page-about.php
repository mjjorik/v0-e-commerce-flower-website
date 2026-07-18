<?php
/**
 * About page (auto-applies to the page with slug "about").
 *
 * Story / values / team — strong E-E-A-T (real local studio, founder, florists).
 * Wildflower system (theme + pult aware). AboutPage + Organization + Person +
 * BreadcrumbList JSON-LD.
 *
 * @package Wildflower
 */

get_header();
$brand = wildflower_brand();
$shop  = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$media = wildflower_page_media( 'about' );

$values = array(
	array( 'M12 21v-9M12 14C8.5 14 6 11 6 7c4 0 6 3 6 7zM12 12c3.2 0 5.5-2.2 5.5-5.5C14 6.5 12 8.6 12 12z', __( 'Seasonal & local', 'wildflower' ), __( 'Cut this week from New England growers — what’s freshest, not what ships cheapest.', 'wildflower' ) ),
	array( 'M5 13l4 4L19 7', __( 'Hand-tied daily', 'wildflower' ), __( 'Every bouquet is arranged by a real florist the morning it’s delivered.', 'wildflower' ) ),
	array( 'M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6', __( 'Honest pricing', 'wildflower' ), __( 'Beautiful flowers from $50 to $130 — the kind you can send on a Tuesday.', 'wildflower' ) ),
	array( 'M20 6 9 17l-5-5', __( 'Freshness guarantee', 'wildflower' ), __( 'If your flowers wilt early, we replace them. No fuss, just stems.', 'wildflower' ) ),
);

$team = array(
	array( __( 'Andrea Bell', 'wildflower' ), __( 'Founder & Lead Florist', 'wildflower' ) ),
	array( __( 'Marco Reyes', 'wildflower' ), __( 'Studio Designer', 'wildflower' ) ),
	array( __( 'Sana Okafor', 'wildflower' ), __( 'Seasonal Buyer', 'wildflower' ) ),
	array( __( 'Tom Whitfield', 'wildflower' ), __( 'Delivery Lead', 'wildflower' ) ),
);
?>

<!-- ABOUT: HERO -->
<section class="hero">
	<span class="hero__glow" aria-hidden="true" data-parallax="90"></span>
	<div class="container--wide">
		<div class="hero__grid">
			<div class="hero__content">
				<span class="hero__badge"><span class="dot"></span> <?php esc_html_e( 'Our story', 'wildflower' ); ?></span>
				<h1 class="kinetic">
					<?php echo wildflower_kinetic( 'A real florist,' ); ?><br>
					<span class="italic"><?php echo wildflower_kinetic( 'around the corner.' ); ?></span>
				</h1>
				<div class="hero__lead">
					<p><?php esc_html_e( 'We’re a small Boston studio — not a warehouse. Since 2015 we’ve hand-tied farm-fresh bouquets and delivered them same-day across the city, one doorstep at a time.', 'wildflower' ); ?></p>
					<div class="hero__cta">
						<a class="btn--primary btn--lg btn--magnetic" data-magnetic="0.25" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
						<a class="btn--outline btn--lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Visit the studio', 'wildflower' ); ?></a>
					</div>
				</div>
			</div>
			<div class="hero__visual">
				<div class="hero__media media"><?php wildflower_media( isset( $media['hero'] ) ? absint( $media['hero'] ) : 0, 'large', __( 'Wild Flower Boston studio bouquet', 'wildflower' ), true ); ?></div>
			</div>
		</div>
	</div>
</section>

<!-- STORY + STATS -->
<section class="section">
	<div class="container">
		<div class="story">
			<div class="story__media media"><?php wildflower_media( isset( $media['story'] ) ? absint( $media['story'] ) : 0, 'large', __( 'Wild Flower florist arranging seasonal flowers', 'wildflower' ), true ); ?></div>
			<div class="story__body reveal">
				<p class="eyebrow" style="color:var(--accent);"><?php esc_html_e( 'Since 2015', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.75rem;"><?php echo wildflower_kinetic( __( 'Flowers, the way a neighbor would do it', 'wildflower' ) ); // phpcs:ignore ?></h2>
				<p class="story__text"><?php esc_html_e( 'It started with a market stall and a stubborn belief: that beautiful flowers shouldn’t cost a fortune or feel like a warehouse afterthought. Today our studio hand-ties every order from stems cut this week, and a local courier brings it to the door — fresh, upright, and on time.', 'wildflower' ); ?></p>
				<div class="story__stats">
					<div><strong>2015</strong><span><?php esc_html_e( 'Studio founded', 'wildflower' ); ?></span></div>
					<div><strong>40k+</strong><span><?php esc_html_e( 'Bouquets delivered', 'wildflower' ); ?></span></div>
					<div><strong>4.9★</strong><span><?php esc_html_e( '820+ reviews', 'wildflower' ); ?></span></div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- VALUES -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'What we stand for', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Our promise', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<ul class="incl-grid reveal">
			<?php foreach ( $values as $v ) : ?>
				<li class="incl">
					<span class="incl__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo esc_attr( $v[0] ); ?>"/></svg></span>
					<h3><?php echo esc_html( $v[1] ); ?></h3>
					<p><?php echo esc_html( $v[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<!-- TEAM (E-E-A-T) -->
<section class="section">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'The hands behind it', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Meet the studio', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<ul class="team-grid">
			<?php foreach ( $team as $ti => $m ) : ?>
				<li class="team-member reveal" data-delay="<?php echo esc_attr( $ti * 80 ); ?>">
					<span class="team-member__photo"><span class="media-fallback media-fallback--<?php echo esc_attr( ( $ti % 5 ) + 1 ); ?>" aria-hidden="true"><?php echo wildflower_flower_svg(); // phpcs:ignore ?></span></span>
					<h3 class="team-member__name"><?php echo esc_html( $m[0] ); ?></h3>
					<p class="team-member__role"><?php echo esc_html( $m[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<!-- CTA -->
<section class="section" style="padding-top:0;">
	<div class="container">
		<div class="cta">
			<span class="cta__glow" aria-hidden="true" data-parallax="-70"></span>
			<p class="eyebrow" style="position:relative;margin-bottom:1.25rem;"><?php esc_html_e( 'Made in Boston', 'wildflower' ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( __( 'Let’s put flowers on someone’s table.', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p><?php esc_html_e( 'Fresh, hand-tied and delivered same-day across Greater Boston.', 'wildflower' ); ?></p>
			<div class="cta__row">
				<a class="btn--accent btn--lg btn--pulse" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<?php
$home = home_url( '/' );
$members = array();
foreach ( $team as $m ) {
	$members[] = array( '@type' => 'Person', 'name' => $m[0], 'jobTitle' => $m[1], 'worksFor' => array( '@id' => $home . '#business' ) );
}
wildflower_print_jsonld(
	array(
		array(
			'@context' => 'https://schema.org',
			'@type'    => 'AboutPage',
			'name'     => 'About ' . $brand['name'],
			'url'      => get_permalink(),
			'about'    => array( '@id' => $home . '#business' ),
		),
		array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Organization',
			'@id'           => $home . '#business',
			'name'          => $brand['name'],
			'foundingDate'  => '2015',
			'founder'       => array( '@type' => 'Person', 'name' => $team[0][0] ),
			'employee'      => $members,
			'areaServed'    => 'Greater Boston',
		),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'About', 'item' => get_permalink() ),
			),
		),
	)
);

get_footer();
