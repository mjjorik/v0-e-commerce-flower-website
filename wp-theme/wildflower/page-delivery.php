<?php
/**
 * Delivery page (auto-applies to the page with slug "delivery").
 *
 * Information mirrors the brand's delivery model — region tiers + a ZIP-based
 * rate ladder, "exact fee by destination ZIP at checkout" — rewritten in the
 * Wildflower system (so it inherits the active theme + Studio remote).
 * Per the brief: the closest tier starts at $19 (not $20), and delivery is a
 * flat $15 on orders of $85+.
 *
 * SEO / GEO / AEO / E-E-A-T: location-rich tiers + ZIP coverage, plain Q&A FAQ,
 * Service + FAQPage + BreadcrumbList JSON-LD (LocalBusiness is already in head).
 *
 * @package Wildflower
 */

get_header();
$brand  = wildflower_brand();
$cutoff = $brand['cutoff']; // "1 PM"
$shop   = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

/* Region tiers: label, rate note, fulfilment, areas[] */
$tiers = array(
	array(
		'Boston & Nearby', 'Rates from $19', 'Same-day',
		array( 'Boston', 'Brighton', 'Allston', 'Back Bay', 'Beacon Hill', 'South End', 'Seaport', 'Charlestown', 'Jamaica Plain', 'Roxbury', 'Dorchester', 'Cambridge', 'Somerville', 'Brookline', 'Watertown', 'Newton', 'Belmont', 'Arlington', 'Medford', 'Everett', 'Malden', 'Revere', 'Chelsea' ),
	),
	array(
		'Greater Boston', 'Rates from $25', 'Same-day',
		array( 'Waltham', 'Quincy', 'Needham', 'Burlington', 'Lexington', 'Winchester', 'Milton', 'Dedham', 'Framingham', 'Natick', 'Wellesley', 'Braintree', 'Canton', 'Norwood', 'Westwood', 'Wakefield', 'Reading', 'Stoneham', 'Melrose', 'Woburn', 'Peabody', 'Lynn', 'Salem' ),
	),
	array(
		'Regional Delivery', 'Quoted individually', 'By arrangement',
		array( 'Providence', 'Warwick', 'Cranston', 'Pawtucket', 'Worcester', 'Lowell', 'Lawrence', 'Haverhill', 'Gloucester', 'Newburyport', 'Selected Cape Cod ZIPs', 'Plymouth', 'Portsmouth, NH', 'Nashua, NH', 'Manchester, NH', 'Concord, NH', 'Hartford, CT', 'Portland, ME' ),
	),
);

/* The published rate ladder (exact fee resolved by destination ZIP). */
$rates = array( '$19', '$25', '$35', '$40', '$45', '$50', '$55', '$65', '$75', '$85', '$105', '$135', '$165+' );

/* FAQ — drives the on-page accordion and FAQPage schema (AEO). */
$faqs = array(
	array(
		__( 'Do you offer same-day flower delivery in Boston?', 'wildflower' ),
		sprintf( __( 'Yes. Order before %1$s ET and we hand-deliver the same day across Boston & Nearby and most of Greater Boston. Orders placed after %1$s are delivered the next day.', 'wildflower' ), $cutoff ),
	),
	array(
		__( 'How much does flower delivery cost?', 'wildflower' ),
		__( 'Delivery is calculated from the destination ZIP code: Boston & Nearby starts at $19, Greater Boston from $25, and regional deliveries are quoted individually. Rates step up by distance ($19, $25, $35 … up to $165+) and the exact fee is shown at checkout. Spend $85 or more and delivery is a flat $15.', 'wildflower' ),
	),
	array(
		__( 'Which areas do you deliver to?', 'wildflower' ),
		__( 'Same-day across Boston & Nearby (Cambridge, Somerville, Brookline, Newton and more) and Greater Boston (Quincy, Waltham, Lexington, Salem and more). We also reach regional destinations like Providence, Worcester and southern New Hampshire by arrangement.', 'wildflower' ),
	),
	array(
		__( 'How is my exact delivery fee determined?', 'wildflower' ),
		__( 'By your destination ZIP code. We map every ZIP to a rate based on distance from our studio, so you always see the precise fee at checkout — no surprises. Extended-distance orders may require a quick custom quote.', 'wildflower' ),
	),
	array(
		__( 'Can I pick up my order?', 'wildflower' ),
		__( 'We run as a closed studio for the freshest, fastest dispatch, so pick-up isn’t available — every order is hand-delivered by a local courier.', 'wildflower' ),
	),
	array(
		__( 'What if no one is home?', 'wildflower' ),
		__( 'Our courier leaves the bouquet in a safe, shaded spot and sends a photo confirmation, or follows any delivery note you add at checkout.', 'wildflower' ),
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
			<div><strong><?php esc_html_e( 'From $19', 'wildflower' ); ?></strong><span><?php esc_html_e( '$15 on orders $85+', 'wildflower' ); ?></span></div>
			<div><strong><?php esc_html_e( 'By ZIP', 'wildflower' ); ?></strong><span><?php esc_html_e( 'Exact fee at checkout', 'wildflower' ); ?></span></div>
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
		<p class="deliv-rule__text"><?php printf( esc_html__( 'Place your order before %1$s Eastern and a local florist arranges and hand-delivers it the same day across same-day regions. After %1$s, your flowers arrive the next day.', 'wildflower' ), esc_html( $cutoff ) ); ?></p>
	</div>
</section>

<!-- COVERAGE BY REGION (GEO) -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head">
			<div style="max-width:40rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'Where we deliver', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Coverage & rates by region', 'wildflower' ) ); // phpcs:ignore ?></h2>
			</div>
			<span class="reviews__rating reveal" style="color:var(--accent);"><?php esc_html_e( '$15 on orders $85+', 'wildflower' ); ?></span>
		</div>
		<div class="deliv-tiers">
			<?php foreach ( $tiers as $t ) : ?>
				<div class="deliv-tier reveal">
					<div class="deliv-tier__head">
						<h3 class="deliv-tier__name"><?php echo esc_html( $t[0] ); ?></h3>
						<span class="deliv-tier__rate"><?php echo esc_html( $t[1] ); ?></span>
						<span class="deliv-tier__badge"><?php echo esc_html( $t[2] ); ?></span>
					</div>
					<ul class="chips">
						<?php foreach ( $t[3] as $area ) : ?>
							<li class="chip"><?php echo esc_html( $area ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- RATE LADDER (transparency) -->
<section class="section">
	<div class="container">
		<div class="section-head"><div style="max-width:40rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Pricing transparency', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Current delivery rates', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<ul class="rates reveal">
			<?php foreach ( $rates as $r ) : ?>
				<li class="rate"><?php echo esc_html( $r ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p class="price-zones__note muted"><?php esc_html_e( 'The exact fee is calculated from the destination ZIP code and shown at checkout — about 1,291 Greater Boston ZIPs are mapped by distance from our studio. Spend $85 or more and delivery is a flat $15. Extended-distance deliveries may require a quick custom quote.', 'wildflower' ); ?></p>
	</div>
</section>

<!-- HOW IT WORKS -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'How it works', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'From order to doorstep', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light">
			<?php
			$steps = array(
				array( '01', sprintf( __( 'Order before %s', 'wildflower' ), $cutoff ), __( 'Pick a bouquet, add a gift message and choose your delivery date at checkout.', 'wildflower' ) ),
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
/* ---- Structured data: Service + FAQPage + Breadcrumb ---- */
$home    = home_url( '/' );
$all_areas = array();
foreach ( $tiers as $t ) {
	$all_areas = array_merge( $all_areas, $t[3] );
}
$faq_ld = array();
foreach ( $faqs as $f ) {
	$faq_ld[] = array(
		'@type'          => 'Question',
		'name'           => $f[0],
		'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ),
	);
}
wildflower_print_jsonld(
	array(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'serviceType' => 'Flower delivery',
			'name'        => 'Same-day flower delivery — Greater Boston',
			'provider'    => array( '@id' => $home . '#business' ),
			'areaServed'  => $all_areas,
			'offers'      => array( '@type' => 'Offer', 'priceCurrency' => 'USD', 'priceSpecification' => array( '@type' => 'PriceSpecification', 'minPrice' => 15, 'maxPrice' => 165, 'priceCurrency' => 'USD' ) ),
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
