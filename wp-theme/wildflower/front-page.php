<?php
/**
 * Front page — the Wildflower homepage.
 *
 * Section order (see docs/STUDIO_PLAN.md):
 * hero → trust strip → occasions bento → bestsellers (3 big) → add-ons →
 * subscription → how it works → our story → delivery → reviews → gallery → CTA.
 *
 * @package Wildflower
 */

get_header();
$brand   = wildflower_brand();
$has_woo = class_exists( 'WooCommerce' );
$shop    = $has_woo ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$cats    = array();
$markup  = '';

if ( $has_woo ) {
	$cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 6,
			'exclude'    => array( get_option( 'default_product_cat' ) ),
		)
	);
	$cats = is_wp_error( $cats ) ? array() : $cats;

	// Six bestsellers — shown 3 at a time in a horizontal scroller.
	$markup = do_shortcode( '[products limit="6" columns="6" visibility="featured"]' );
	if ( false === strpos( $markup, '<li' ) ) {
		$markup = do_shortcode( '[products limit="6" columns="6" orderby="popularity"]' );
	}
	$markup = false === strpos( $markup, '<li' ) ? '' : $markup;
}

// Fall back to demo bouquets so the section is never empty (no Woo / no products).
if ( '' === $markup ) {
	$markup = wildflower_demo_products( 6 );
}
?>

<!-- 3 · HERO -->
<section class="hero">
	<span class="hero__glow" aria-hidden="true" data-parallax="90"></span>
	<div class="container--wide">
		<div class="hero__grid">
			<div class="hero__content">
				<span class="hero__badge"><span class="dot"></span> <?php printf( esc_html__( 'Fresh flowers · %s', 'wildflower' ), esc_html( $brand['city'] ) ); ?></span>
				<h1 class="hero__title kinetic">
					<?php echo wildflower_kinetic( 'Fresh flowers' ); ?><br>
					<span class="hero__title-accent"><?php echo wildflower_kinetic( 'today.' ); ?></span>
				</h1>
				<div class="hero__lead">
					<p><?php printf( esc_html__( 'Farm-fresh bouquets and weekly subscriptions, hand-delivered same-day across Greater Boston. Order by %s.', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></p>
					<div class="hero__cta">
						<a class="btn--primary btn--lg btn--magnetic" data-magnetic="0.25" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
						<a class="btn--outline btn--lg" href="#how"><?php esc_html_e( 'How it works', 'wildflower' ); ?></a>
					</div>
					<div class="hero__trust">
						<span class="hero__trust-item hero__rating"><span class="hero__stars">★★★★★</span> <strong>4.9</strong> <span class="hero__trust-sub"><?php esc_html_e( '820+ reviews', 'wildflower' ); ?></span></span>
						<span class="hero__trust-item"><?php esc_html_e( 'Same-day before 1 PM', 'wildflower' ); ?></span>
						<span class="hero__trust-item"><?php esc_html_e( 'Hand-tied daily', 'wildflower' ); ?></span>
					</div>
				</div>
			</div>
			<div class="hero__visual">
				<div class="hero__media media" data-hero-media>
					<?php wildflower_hero_visual(); ?>
				</div>
				<span class="hero__chip"><span class="hero__chip-star">★</span> 4.9 · <?php esc_html_e( 'Same-day', 'wildflower' ); ?></span>
				<div class="hero__float">
					<span class="hero__float-text">
						<span class="hero__float-name"><?php esc_html_e( 'Pink Peony Dream', 'wildflower' ); ?></span>
						<span class="hero__float-price">$49</span>
					</span>
					<span class="hero__float-plus" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
					</span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 4 · TRUST STRIP -->
<section class="trust-strip">
	<div class="container trust-strip__row">
		<?php
		$svg_open = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">';
		$trust    = array(
			array(
				$svg_open . '<path d="M3 7.5h10v8.5H3z"/><path d="M13 10.5h4l3.2 3v2.5H13z"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17.2" cy="17.5" r="1.6"/></svg>',
				__( 'Same-day delivery', 'wildflower' ),
				__( 'Order by 1 PM', 'wildflower' ),
			),
			array(
				$svg_open . '<path d="M12 21v-9"/><path d="M12 14C8.5 14 6 11 6 7c4 0 6 3 6 7z"/><path d="M12 12c3.2 0 5.5-2.2 5.5-5.5C14 6.5 12 8.6 12 12z"/></svg>',
				__( 'Farm-fresh', 'wildflower' ),
				__( 'Cut this week', 'wildflower' ),
			),
			array(
				$svg_open . '<path d="M3.5 12.5V5.5a2 2 0 0 1 2-2h7l8 8-9 9z"/><circle cx="8" cy="8" r="1.5"/></svg>',
				__( 'Honest pricing', 'wildflower' ),
				__( '$50–$130, no markups', 'wildflower' ),
			),
			array(
				$svg_open . '<path d="M12 20.5S4 16 4 9.8A4.3 4.3 0 0 1 12 7a4.3 4.3 0 0 1 8 2.8C20 16 12 20.5 12 20.5z"/></svg>',
				__( 'Loved in Boston', 'wildflower' ),
				__( '4.9★ · 820+ reviews', 'wildflower' ),
			),
		);
		foreach ( $trust as $t ) :
			?>
			<div class="trust-strip__item reveal">
				<span class="trust-strip__icon"><?php echo $t[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="trust-strip__text"><strong><?php echo esc_html( $t[1] ); ?></strong><em><?php echo esc_html( $t[2] ); ?></em></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<!-- 5 · SHOP BY OCCASION — ASYMMETRIC BENTO -->
<section class="section">
	<div class="container">
		<div class="section-head">
			<div style="max-width:32rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'For every moment', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Shop by occasion', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<a class="link-underline reveal" href="<?php echo esc_url( home_url( '/occasions/' ) ); ?>"><?php esc_html_e( 'All occasions', 'wildflower' ); ?></a>
		</div>
		<div class="bento">
			<?php
			$occasions = array(
				array( 'Birthday', 'Make their year bloom.', true ),
				array( 'Anniversary', 'Romance, distilled into stems.', false ),
				array( 'Sympathy', 'When words fall short.', false ),
				array( 'Just Because', 'The best reason there is.', false ),
				array( 'New Baby', 'A soft hello to someone small.', false ),
			);
			foreach ( $occasions as $oi => $o ) {
				$link = $has_woo ? add_query_arg( 'occasion', sanitize_title( $o[0] ), $shop ) : home_url( '/' );
				?>
				<a class="bento__tile reveal <?php echo $o[2] ? 'is-big' : ''; ?>" data-delay="<?php echo esc_attr( $oi * 90 ); ?>" href="<?php echo esc_url( $link ); ?>">
					<?php wildflower_media( null, 'large', $o[0], false ); ?>
					<span class="bento__overlay"></span>
					<span class="bento__caption">
						<h3><?php echo esc_html( $o[0] ); ?></h3>
						<p><?php echo esc_html( $o[1] ); ?></p>
					</span>
				</a>
				<?php
			}
			?>
		</div>
	</div>
</section>

<?php if ( $markup ) : ?>
<!-- 6 · BESTSELLERS — 3 shown, 6 total, horizontal scroll -->
<section class="section section--alt scroller" data-scroller>
	<div class="container">
		<div class="section-head">
			<div style="max-width:36rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'The line-up', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Bestsellers', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<div class="scroller__nav">
				<button class="scroller__arrow" data-scroll-prev aria-label="<?php esc_attr_e( 'Previous', 'wildflower' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
				<button class="scroller__arrow" data-scroll-next aria-label="<?php esc_attr_e( 'Next', 'wildflower' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></button>
				<a class="link-underline reveal" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'View all', 'wildflower' ); ?></a>
			</div>
		</div>
	</div>
	<div class="scroller__viewport">
		<div class="container products--scroll" data-scroller-track>
			<?php echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<span class="scroller__fade" aria-hidden="true"></span>
		<span class="scroller__hint" aria-hidden="true"><?php esc_html_e( 'Swipe for more', 'wildflower' ); ?> →</span>
	</div>
</section>
<?php endif; ?>

<!-- 7 · COMPLETE THE GIFT — ADD-ONS (dark) -->
<section class="section addons">
	<div class="container addons__inner">
		<div class="addons__head reveal">
			<p class="eyebrow" style="color:color-mix(in oklab,var(--primary-foreground) 70%,transparent);"><?php esc_html_e( 'The little extras', 'wildflower' ); ?></p>
			<h2 class="kinetic" style="margin-top:.5rem;color:var(--primary-foreground);"><?php echo wildflower_kinetic( __( 'Complete the gift', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p class="addons__lead"><?php esc_html_e( 'Make it unforgettable — add a little something at checkout.', 'wildflower' ); ?></p>
		</div>
		<div class="addons__grid">
			<?php
			$addons = array(
				array( 'Glass vase', '+ $18', 'M7 3h10l-1 5a5 5 0 0 1-8 0z' ),
				array( 'Soy candle', '+ $24', 'M12 3c2 2 2 4 0 6-2-2-2-4 0-6zM8 11h8v9H8z' ),
				array( 'Belgian truffles', '+ $16', 'M4 9h16l-2 11H6zM8 9a4 4 0 0 1 8 0' ),
				array( 'Handwritten card', 'Free', 'M3 5h18v14H3zM3 7l9 6 9-6' ),
			);
			foreach ( $addons as $ai => $a ) :
				?>
				<div class="addon reveal" data-delay="<?php echo esc_attr( $ai * 90 ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo esc_attr( $a[2] ); ?>"/></svg>
					<span class="addon__name"><?php echo esc_html( $a[0] ); ?></span>
					<span class="addon__price"><?php echo esc_html( $a[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<a class="btn--accent" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Browse add-ons', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
	</div>
</section>

<!-- 8 · DELIVERY — brief (full details on /delivery/) -->
<section class="section section--alt">
	<div class="container">
		<div class="delivery">
			<div class="delivery__body reveal">
				<p class="eyebrow" style="color:var(--accent);"><?php esc_html_e( 'Delivery', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Same-day across Boston', 'wildflower' ) ); // phpcs:ignore ?></h2>
				<p class="delivery__lead"><?php printf( esc_html__( 'Order by %s and we hand-deliver the same day across Boston, Cambridge, Somerville and the metro. Free delivery over $75.', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></p>
				<div class="delivery__facts">
					<div><strong><?php esc_html_e( 'Same-day', 'wildflower' ); ?></strong><span><?php esc_html_e( 'Order by 1 PM', 'wildflower' ); ?></span></div>
					<div><strong><?php esc_html_e( 'From $9', 'wildflower' ); ?></strong><span><?php esc_html_e( 'Free over $75', 'wildflower' ); ?></span></div>
					<div><strong><?php esc_html_e( 'Metro-wide', 'wildflower' ); ?></strong><span><?php esc_html_e( 'Within Rt-128', 'wildflower' ); ?></span></div>
				</div>
				<a class="link-underline" href="<?php echo esc_url( home_url( '/delivery/' ) ); ?>"><?php esc_html_e( 'See zones & full details', 'wildflower' ); ?> →</a>
			</div>
			<div class="delivery__map media" aria-hidden="true">
				<span class="delivery__pin"></span>
				<span class="media-fallback__label"><?php esc_html_e( 'Greater Boston', 'wildflower' ); ?></span>
			</div>
		</div>
	</div>
</section>

<!-- 9 · HOW IT WORKS (dark) -->
<section class="section how" id="how">
	<div class="container">
		<h2 class="kinetic" style="max-width:28rem;"><?php echo wildflower_kinetic( __( 'How it works', 'wildflower' ) ); // phpcs:ignore ?></h2>
		<div class="how__grid">
			<?php
			$steps = array(
				array( '01', 'Pick your bouquet', 'Browse the line-up or start a subscription. Choose a size — Petite, Classic or Grand.' ),
				array( '02', 'Tell us when & where', 'Add a delivery date, a time slot and a gift message. Same-day if you order by 1 PM.' ),
				array( '03', 'We hand-deliver it', 'Our local couriers bring it fresh to the door, anywhere across Greater Boston.' ),
			);
			foreach ( $steps as $si => $s ) {
				?>
				<div class="reveal" data-delay="<?php echo esc_attr( $si * 130 ); ?>">
					<p class="how__num"><?php echo esc_html( $s[0] ); ?></p>
					<h3><?php echo esc_html( $s[1] ); ?></h3>
					<p><?php echo esc_html( $s[2] ); ?></p>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>

<!-- 10 · SUBSCRIPTION -->
<section class="section">
	<div class="container">
		<div class="sub-teaser">
			<div class="media">
				<?php wildflower_media( null, 'large', 'Weekly ritual', true ); ?>
			</div>
			<div class="sub-teaser__body reveal">
				<p class="eyebrow" style="color:var(--accent);"><?php esc_html_e( 'The ritual', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.75rem;"><?php echo wildflower_kinetic( __( 'Fresh flowers, every week', 'wildflower' ) ); // phpcs:ignore ?></h2>
				<p style="margin-top:1.25rem;max-width:28rem;color:color-mix(in oklab,var(--foreground) 75%,transparent);line-height:1.6;"><?php esc_html_e( 'A standing order of seasonal blooms, chosen by our studio and delivered like clockwork. Pause, skip or cancel anytime — no strings, just stems.', 'wildflower' ); ?></p>
				<p class="sub-teaser__price"><?php esc_html_e( 'From', 'wildflower' ); ?> <span class="amt">$55</span> <span style="font-size:1rem;color:var(--muted-foreground);">/ delivery</span></p>
				<a class="btn--primary" style="margin-top:1.75rem;" href="<?php echo esc_url( home_url( '/subscriptions/' ) ); ?>"><?php esc_html_e( 'Explore subscriptions', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<!-- 11 · REVIEWS (matcha block) -->
<section class="section reviews">
	<div class="container">
		<div class="section-head">
			<div style="max-width:36rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'Loved across the city', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Notes from the neighborhood', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<span class="reviews__rating reveal">★ 4.9 · 820+ reviews</span>
		</div>
		<?php
		$reviews = array(
			array( 'The nicest flowers I’ve sent, and somehow the cheapest. My sister cried.', 'Maya R.', 'Back Bay' ),
			array( 'Subscription is the best $55 I spend each week. The studio has taste.', 'Daniel K.', 'Cambridge' ),
			array( 'Ordered at noon, delivered by 4. The bouquet looked exactly like the photo.', 'Priya S.', 'Somerville' ),
		);
		?>
		<div class="reviews-grid">
			<?php foreach ( $reviews as $ri => $r ) : ?>
				<figure class="quote-card reveal" data-delay="<?php echo esc_attr( $ri * 110 ); ?>">
					<span class="quote-card__stars">★★★★★</span>
					<blockquote>&ldquo;<?php echo esc_html( $r[0] ); ?>&rdquo;</blockquote>
					<figcaption><strong style="color:var(--foreground);"><?php echo esc_html( $r[1] ); ?></strong> · <?php echo esc_html( $r[2] ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- 12 · GALLERY -->
<section class="section">
	<div class="container">
		<div class="section-head">
			<div style="max-width:32rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'From the studio', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'The gallery', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<a class="link-underline reveal" href="<?php echo esc_url( $brand['instagram'] ); ?>"><?php echo esc_html( $brand['handle'] ); ?></a>
		</div>
		<div class="gallery-grid">
			<?php wildflower_gallery(); ?>
		</div>
	</div>
</section>

<!-- 13 · FINAL CTA (dark) -->
<section class="section" style="padding-top:0;">
	<div class="container">
		<div class="cta">
			<span class="cta__glow" aria-hidden="true" data-parallax="-70"></span>
			<p class="eyebrow" style="position:relative;color:color-mix(in oklab,var(--primary-foreground) 70%,transparent);margin-bottom:1.25rem;"><?php esc_html_e( 'No occasion required', 'wildflower' ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( 'Send something beautiful today.' ); ?></h2>
			<p><?php esc_html_e( 'Fresh, hand-tied and delivered across Boston in hours. The best flowers are the ones nobody expected.', 'wildflower' ); ?></p>
			<div class="cta__row">
				<a class="btn--accent btn--lg btn--pulse" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
				<a class="btn--lg" style="position:relative;background:transparent;border:1px solid color-mix(in oklab,var(--primary-foreground) 45%,transparent);color:var(--primary-foreground);" href="<?php echo esc_url( home_url( '/subscriptions/' ) ); ?>"><?php esc_html_e( 'Start a subscription', 'wildflower' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
