<?php
/**
 * Delivery page (auto-applies to the page with slug "delivery").
 *
 * Content only — information mirrored from the brand's delivery data
 * (zones, fees, the 1 PM same-day cutoff). Design is the Wildflower system,
 * so it inherits the active theme + the Studio remote automatically.
 *
 * Built for SEO / GEO / AEO / E-E-A-T:
 *  • keyword-rich, location-specific copy (Greater Boston + neighborhoods);
 *  • FAQ in plain Q&A (answer-engine friendly) + FAQPage schema;
 *  • Service + BreadcrumbList JSON-LD (LocalBusiness/Florist is already in <head>);
 *  • trust signals (real local studio, hand-delivery, guarantee) for E-E-A-T.
 *
 * @package Wildflower
 */

get_header();
$brand  = wildflower_brand();
$cutoff = $brand['cutoff']; // "1 PM"
$shop   = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

/* Zones & pricing — single source for both the visible table and schema. */
$zones = array(
	array( 'Boston — Downtown, Back Bay & South End', 15, true ),
	array( 'Cambridge & Somerville', 18, true ),
	array( 'Brookline & Newton', 22, true ),
	array( 'Medford & Arlington', 25, true ),
	array( 'Quincy & Milton', 28, false ),
);

/* Neighborhoods we cover (GEO depth). */
$neighborhoods = array(
	'Back Bay', 'Beacon Hill', 'South End', 'North End', 'Downtown', 'Seaport',
	'Cambridge', 'Somerville', 'Brookline', 'Newton', 'Medford', 'Arlington',
	'Jamaica Plain', 'Charlestown', 'Quincy', 'Milton',
);

/* FAQ — drives both the on-page accordion and FAQPage schema (AEO). */
$faqs = array(
	array(
		__( 'Do you offer same-day flower delivery in Boston?', 'wildflower' ),
		sprintf( __( 'Yes. Order before %s ET and we hand-deliver the same day across most of Greater Boston — including Boston, Cambridge, Somerville, Brookline and Newton. Orders placed after %s are delivered the next day.', 'wildflower' ), $cutoff, $cutoff ),
	),
	array(
		__( 'How much does flower delivery cost?', 'wildflower' ),
		__( 'Delivery fees range from $15 in downtown Boston to $28 for outer zones such as Quincy and Milton. Delivery is free on orders over $75. The exact fee is shown at checkout based on your address.', 'wildflower' ),
	),
	array(
		__( 'Which areas do you deliver to?', 'wildflower' ),
		__( 'We cover Greater Boston: Boston, Cambridge, Somerville, Brookline, Newton, Medford, Arlington, Quincy and Milton, plus the surrounding metro within Route 128. Most zones are same-day eligible.', 'wildflower' ),
	),
	array(
		__( 'Can I pick up my order?', 'wildflower' ),
		__( 'We run as a closed studio for the freshest, fastest dispatch, so pick-up isn’t available — every order is hand-delivered by a local courier.', 'wildflower' ),
	),
	array(
		__( 'How are the flowers packaged for delivery?', 'wildflower' ),
		__( 'Each bouquet is hydrated with eco-friendly wet wraps and packed in our signature Wildflower tote box so it arrives upright, fresh and ready to display.', 'wildflower' ),
	),
	array(
		__( 'What if no one is home?', 'wildflower' ),
		__( 'Our courier will leave the bouquet in a safe, shaded spot and send a photo confirmation, or follow any delivery note you add at checkout.', 'wildflower' ),
	),
);
?>

<!-- DELIVERY: HERO -->
<section class="section page-hero">
	<div class="container">
		<p class="eyebrow reveal"><?php esc_html_e( 'Delivery', 'wildflower' ); ?></p>
		<h1 class="page-hero__title kinetic"><?php echo wildflower_kinetic( __( 'Same-day flower delivery across Greater Boston', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="page-hero__lead reveal"><?php printf( esc_html__( 'Hand-delivered by local couriers who keep your flowers upright and fresh. Order before %s and it lands today.', 'wildflower' ), esc_html( $cutoff ) ); ?></p>
		<div class="page-hero__facts reveal">
			<div><strong><?php echo esc_html( $cutoff ); ?></strong><span><?php esc_html_e( 'Same-day cutoff', 'wildflower' ); ?></span></div>
			<div><strong><?php esc_html_e( 'From $15', 'wildflower' ); ?></strong><span><?php esc_html_e( 'Free over $75', 'wildflower' ); ?></span></div>
			<div><strong><?php esc_html_e( '16+ areas', 'wildflower' ); ?></strong><span><?php esc_html_e( 'Across the metro', 'wildflower' ); ?></span></div>
		</div>
	</div>
</section>

<!-- THE 1 PM RULE (dark statement) -->
<section class="section how deliv-rule">
	<div class="container deliv-rule__inner">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'The simple rule', 'wildflower' ); ?></p>
			<h2 class="deliv-rule__title kinetic"><?php echo wildflower_kinetic( sprintf( __( 'Order by %s, get it today', 'wildflower' ), $cutoff ) ); // phpcs:ignore ?></h2>
		</div>
		<p class="deliv-rule__text"><?php printf( esc_html__( 'Place your order before %s Eastern and a local florist arranges and hand-delivers it the same day across same-day zones. After %s, your flowers arrive the next day.', 'wildflower' ), esc_html( $cutoff ), esc_html( $cutoff ) ); ?></p>
	</div>
</section>

<!-- ZONES & PRICING -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head">
			<div style="max-width:36rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'Zones & pricing', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Find your zone', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<span class="reviews__rating reveal" style="color:var(--accent);"><?php esc_html_e( 'Free over $75', 'wildflower' ); ?></span>
		</div>
		<ul class="price-zones reveal">
			<?php foreach ( $zones as $z ) : ?>
				<li class="price-zone">
					<span class="price-zone__name"><?php echo esc_html( $z[0] ); ?></span>
					<span class="price-zone__meta">
						<span class="price-zone__badge<?php echo $z[2] ? ' is-same' : ''; ?>"><?php echo $z[2] ? esc_html__( 'Same-day', 'wildflower' ) : esc_html__( 'Next-day', 'wildflower' ); ?></span>
						<span class="price-zone__fee">$<?php echo esc_html( $z[1] ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="price-zones__note muted"><?php esc_html_e( 'The exact fee is calculated at checkout from your delivery address. Not sure about your area? Reach out and we’ll confirm.', 'wildflower' ); ?></p>
	</div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'How it works', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'From order to doorstep', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light">
			<?php
			$steps = array(
				array( '01', __( 'Order before ' . $cutoff, 'wildflower' ), __( 'Pick a bouquet, add a gift message and choose your delivery date at checkout.', 'wildflower' ) ),
				array( '02', __( 'We hand-tie it fresh', 'wildflower' ), __( 'A local florist arranges your flowers the same morning from stems cut this week.', 'wildflower' ) ),
				array( '03', __( 'Delivered to the door', 'wildflower' ), __( 'Our courier delivers it upright and hydrated, with photo confirmation on request.', 'wildflower' ) ),
			);
			foreach ( $steps as $si => $s ) :
				?>
				<div class="reveal" data-delay="<?php echo esc_attr( $si * 120 ); ?>">
					<p class="how__num"><?php echo esc_html( $s[0] ); ?></p>
					<h3><?php echo esc_html( $s[1] ); ?></h3>
					<p><?php echo esc_html( $s[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- COVERAGE / NEIGHBORHOODS (GEO) -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:40rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Coverage', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Neighborhoods we deliver to', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<ul class="neigh-grid reveal">
			<?php foreach ( $neighborhoods as $n ) : ?>
				<li class="neigh"><?php echo esc_html( $n ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<!-- TRUST (E-E-A-T) -->
<section class="section">
	<div class="container trust-strip__row" style="border:0;">
		<?php
		$svg = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">';
		$trust = array(
			array( $svg . '<path d="M3 7.5h10v8.5H3z"/><path d="M13 10.5h4l3.2 3v2.5H13z"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17.2" cy="17.5" r="1.6"/></svg>', __( 'Local couriers', 'wildflower' ), __( 'Boston-based, hand-delivered', 'wildflower' ) ),
			array( $svg . '<path d="M12 21v-9"/><path d="M12 14C8.5 14 6 11 6 7c4 0 6 3 6 7z"/><path d="M12 12c3.2 0 5.5-2.2 5.5-5.5C14 6.5 12 8.6 12 12z"/></svg>', __( 'Farm-fresh', 'wildflower' ), __( 'Cut this week, hydrated in transit', 'wildflower' ) ),
			array( $svg . '<path d="M20 6 9 17l-5-5"/></svg>', __( 'Freshness guarantee', 'wildflower' ), __( 'Wilts early? We replace it.', 'wildflower' ) ),
			array( $svg . '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>', __( 'Since 2015', 'wildflower' ), __( '40k+ bouquets delivered', 'wildflower' ) ),
		);
		foreach ( $trust as $t ) :
			?>
			<div class="trust-strip__item reveal">
				<span class="trust-strip__icon"><?php echo $t[0]; // phpcs:ignore ?></span>
				<span class="trust-strip__text"><strong><?php echo esc_html( $t[1] ); ?></strong><em><?php echo esc_html( $t[2] ); ?></em></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<!-- FAQ (AEO) -->
<section class="section section--alt">
	<div class="container faq-wrap">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Good to know', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Delivery questions, answered', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="faq">
			<?php foreach ( $faqs as $f ) : ?>
				<details class="faq__item reveal">
					<summary class="faq__q"><?php echo esc_html( $f[0] ); ?><span class="faq__icon" aria-hidden="true"></span></summary>
					<div class="faq__a"><p><?php echo esc_html( $f[1] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- CTA -->
<section class="section" style="padding-top:0;">
	<div class="container">
		<div class="cta">
			<span class="cta__glow" aria-hidden="true" data-parallax="-70"></span>
			<p class="eyebrow" style="position:relative;margin-bottom:1.25rem;"><?php printf( esc_html__( 'Same-day before %s', 'wildflower' ), esc_html( $cutoff ) ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( __( 'Send flowers across Boston today.', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p><?php esc_html_e( 'Fresh, hand-tied and delivered in hours.', 'wildflower' ); ?></p>
			<div class="cta__row">
				<a class="btn--accent btn--lg btn--pulse" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<?php
/* ---- Structured data: Service deliveryArea + FAQPage + Breadcrumb ---- */
$home   = home_url( '/' );
$faq_ld = array();
foreach ( $faqs as $f ) {
	$faq_ld[] = array(
		'@type'          => 'Question',
		'name'           => $f[0],
		'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ),
	);
}
$areas = array();
foreach ( $zones as $z ) {
	$areas[] = $z[0];
}
wildflower_print_jsonld(
	array(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'serviceType' => 'Flower delivery',
			'name'        => 'Same-day flower delivery — Greater Boston',
			'provider'    => array( '@id' => $home . '#business' ),
			'areaServed'  => array_merge( $areas, $neighborhoods ),
			'offers'      => array( '@type' => 'Offer', 'priceCurrency' => 'USD', 'priceSpecification' => array( '@type' => 'PriceSpecification', 'minPrice' => 15, 'maxPrice' => 28, 'priceCurrency' => 'USD' ) ),
			'url'         => get_permalink(),
		),
		array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $faq_ld,
		),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Delivery', 'item' => get_permalink() ),
			),
		),
	)
);

get_footer();
