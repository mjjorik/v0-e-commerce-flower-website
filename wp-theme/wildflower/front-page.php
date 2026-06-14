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

	// Three hero products — featured first, then best-selling.
	$markup = do_shortcode( '[products limit="3" columns="3" visibility="featured"]' );
	if ( false === strpos( $markup, '<li' ) ) {
		$markup = do_shortcode( '[products limit="3" columns="3" orderby="popularity"]' );
	}
	$markup = false === strpos( $markup, '<li' ) ? '' : $markup;
}
?>

<!-- 3 · HERO -->
<section class="hero">
	<span class="hero__glow" aria-hidden="true" data-parallax="90"></span>
	<div class="container--wide">
		<div class="hero__grid">
			<div class="hero__content">
				<span class="hero__badge"><span class="dot"></span> <?php printf( esc_html__( 'Fresh flowers · %s', 'wildflower' ), esc_html( $brand['city'] ) ); ?></span>
				<h1 class="kinetic">
					<?php echo wildflower_kinetic( 'Beautiful flowers.' ); ?><br>
					<span class="italic"><?php echo wildflower_kinetic( 'Honest' ); ?></span> <?php echo wildflower_kinetic( 'prices.' ); ?>
				</h1>
				<div class="hero__lead">
					<p><?php printf( esc_html__( 'Farm-fresh bouquets and weekly subscriptions, hand-delivered same-day across Greater Boston. Order by %s.', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></p>
					<div class="hero__cta">
						<a class="btn--primary btn--lg btn--magnetic" data-magnetic="0.25" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
						<a class="btn--outline btn--lg" href="#how"><?php esc_html_e( 'How it works', 'wildflower' ); ?></a>
					</div>
					<p class="hero__trust"><span class="hero__stars">★★★★★</span> <strong>4.9</strong> · <?php esc_html_e( '820+ reviews · Same-day before 1 PM · Hand-tied daily', 'wildflower' ); ?></p>
				</div>
			</div>
			<div class="hero__visual">
				<div class="hero__media media" data-hero-media>
					<?php wildflower_hero_visual(); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 4 · TRUST STRIP -->
<section class="trust-strip">
	<div class="container trust-strip__row">
		<?php
		$trust = array(
			array( 'M5 13l4 4L19 7', __( 'Same-day delivery', 'wildflower' ), __( 'Order by 1 PM', 'wildflower' ) ),
			array( 'M12 3v18M5 8c3 0 5-2 7-5 2 3 4 5 7 5', __( 'Farm-fresh', 'wildflower' ), __( 'Cut this week', 'wildflower' ) ),
			array( 'M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6', __( 'Honest pricing', 'wildflower' ), __( '$50–$130, no markups', 'wildflower' ) ),
			array( 'M12 2l2.4 7.4H22l-6 4.5 2.3 7.1L12 16.8 5.7 21l2.3-7.1-6-4.5h7.6z', __( 'Loved in Boston', 'wildflower' ), __( '4.9★ · 820+ reviews', 'wildflower' ) ),
		);
		foreach ( $trust as $t ) :
			?>
			<div class="trust-strip__item reveal">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo esc_attr( $t[0] ); ?>"/></svg>
				<span><strong><?php echo esc_html( $t[1] ); ?></strong><em><?php echo esc_html( $t[2] ); ?></em></span>
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
<!-- 6 · BESTSELLERS — 3 BIG -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head">
			<div style="max-width:36rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'The line-up', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Bestsellers', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<a class="link-underline reveal" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'View all', 'wildflower' ); ?></a>
		</div>
		<div class="products--big">
			<?php echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- 7 · COMPLETE THE GIFT — ADD-ONS -->
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

<!-- 8 · SUBSCRIPTION -->
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

<!-- 9 · HOW IT WORKS -->
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

<!-- 10 · OUR STORY -->
<section class="section">
	<div class="container">
		<div class="story">
			<div class="story__media media">
				<?php wildflower_media( null, 'large', 'In the studio', true ); ?>
			</div>
			<div class="story__body reveal">
				<p class="eyebrow" style="color:var(--accent);"><?php esc_html_e( 'Our story', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.75rem;"><?php echo wildflower_kinetic( __( 'A real florist, around the corner', 'wildflower' ) ); // phpcs:ignore ?></h2>
				<p class="story__text"><?php esc_html_e( 'We’re a small Boston studio, not a warehouse. Every bouquet is hand-tied the morning it’s delivered, from stems cut this week by New England growers. You can tell a person made it — because one did.', 'wildflower' ); ?></p>
				<div class="story__stats">
					<div><strong>2015</strong><span><?php esc_html_e( 'Studio founded', 'wildflower' ); ?></span></div>
					<div><strong>40k+</strong><span><?php esc_html_e( 'Bouquets delivered', 'wildflower' ); ?></span></div>
					<div><strong>100%</strong><span><?php esc_html_e( 'Hand-tied daily', 'wildflower' ); ?></span></div>
				</div>
				<a class="btn--outline" style="margin-top:2rem;" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Read our story', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<!-- 11 · DELIVERY ZONES -->
<section class="section section--alt">
	<div class="container">
		<div class="delivery">
			<div class="delivery__body reveal">
				<p class="eyebrow" style="color:var(--accent);"><?php esc_html_e( 'Delivery', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Across Greater Boston', 'wildflower' ) ); // phpcs:ignore ?></h2>
				<ul class="zones">
					<?php
					$zones = array(
						array( 'Boston · Cambridge · Somerville', __( 'Same-day', 'wildflower' ) ),
						array( 'Brookline · Newton · Quincy', __( 'Same-day', 'wildflower' ) ),
						array( 'Greater metro (within Rt-128)', __( 'Next-day', 'wildflower' ) ),
					);
					foreach ( $zones as $z ) :
						?>
						<li class="zone"><span><?php echo esc_html( $z[0] ); ?></span><em><?php echo esc_html( $z[1] ); ?></em></li>
					<?php endforeach; ?>
				</ul>
				<p class="cutoff"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> <?php esc_html_e( 'Order before', 'wildflower' ); ?> <strong><?php echo esc_html( $brand['cutoff'] ); ?></strong> <?php esc_html_e( 'for same-day delivery', 'wildflower' ); ?></p>
			</div>
			<div class="delivery__map media" aria-hidden="true">
				<span class="delivery__pin"></span>
				<span class="media-fallback__label"><?php esc_html_e( 'Greater Boston', 'wildflower' ); ?></span>
			</div>
		</div>
	</div>
</section>

<!-- 12 · REVIEWS -->
<section class="section marquee">
	<div class="container" style="margin-bottom:2.5rem;">
		<p class="eyebrow"><?php esc_html_e( 'Loved across the city', 'wildflower' ); ?></p>
		<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Notes from the neighborhood', 'wildflower' ) ); // phpcs:ignore ?></h2>
	</div>
	<?php
	$reviews = array(
		array( 'The nicest flowers I’ve sent, and somehow the cheapest. My sister cried.', 'Maya R.', 'Back Bay' ),
		array( 'Subscription is the best $55 I spend each week. The studio has taste.', 'Daniel K.', 'Cambridge' ),
		array( 'Ordered at noon, delivered by 4. The bouquet looked exactly like the photo.', 'Priya S.', 'Somerville' ),
		array( 'Finally, flowers that don’t look like a gas-station afterthought.', 'Tom W.', 'Brookline' ),
	);
	?>
	<div class="marquee__track">
		<?php for ( $g = 0; $g < 2; $g++ ) : ?>
			<div class="marquee__group"<?php echo $g ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( $reviews as $r ) : ?>
					<figure class="quote-card">
						<span class="quote-card__stars">★★★★★</span>
						<blockquote>&ldquo;<?php echo esc_html( $r[0] ); ?>&rdquo;</blockquote>
						<figcaption><strong style="color:var(--foreground);"><?php echo esc_html( $r[1] ); ?></strong> · <?php echo esc_html( $r[2] ); ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endfor; ?>
	</div>
</section>

<!-- 13 · GALLERY -->
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

<!-- 14 · FINAL CTA -->
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
