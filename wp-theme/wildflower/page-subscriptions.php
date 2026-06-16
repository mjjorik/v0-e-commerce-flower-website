<?php
/**
 * Subscriptions page (auto-applies to the page with slug "subscriptions").
 *
 * A landing page for recurring flower plans, in the Wildflower system
 * (theme + Studio-remote aware). The plan CTAs point to the shop / subscription
 * products; actual recurring billing is handled by a subscriptions plugin
 * (e.g. WooCommerce Subscriptions) — this page is the marketing + plan chooser.
 *
 * SEO / AEO / E-E-A-T: clear plan offers, plain-language FAQ, Service +
 * OfferCatalog + FAQPage + BreadcrumbList JSON-LD.
 *
 * @package Wildflower
 */

get_header();
$brand = wildflower_brand();
$shop  = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

/* Plans: name, price, cadence, featured, perks[] */
$plans = array(
	array( 'Weekly', 45, __( 'every week', 'wildflower' ), false, array(
		__( 'A fresh seasonal bouquet weekly', 'wildflower' ),
		__( 'Free delivery, always', 'wildflower' ),
		__( 'Skip or pause anytime', 'wildflower' ),
		__( 'Best value per stem', 'wildflower' ),
	) ),
	array( 'Bi-weekly', 49, __( 'every two weeks', 'wildflower' ), true, array(
		__( 'Everything in Weekly', 'wildflower' ),
		__( 'A vase free on your first box', 'wildflower' ),
		__( '10% off all add-ons', 'wildflower' ),
		__( 'Our most-loved rhythm', 'wildflower' ),
	) ),
	array( 'Monthly', 55, __( 'once a month', 'wildflower' ), false, array(
		__( 'Designer’s choice each month', 'wildflower' ),
		__( 'Free delivery, always', 'wildflower' ),
		__( 'Skip or pause anytime', 'wildflower' ),
		__( 'A monthly moment of fresh', 'wildflower' ),
	) ),
);

$faqs = array(
	array(
		__( 'How does a flower subscription work?', 'wildflower' ),
		__( 'Choose a rhythm — weekly, every two weeks, or monthly — and our studio hand-ties a fresh seasonal bouquet for each delivery. You’re billed per delivery, and you can manage everything from your account.', 'wildflower' ),
	),
	array(
		__( 'Can I pause, skip or cancel?', 'wildflower' ),
		__( 'Anytime, with no fees. Going away? Skip a delivery or pause your plan from your account, and cancel whenever you like — no strings, just stems.', 'wildflower' ),
	),
	array(
		__( 'What flowers will I receive?', 'wildflower' ),
		__( 'A designer’s-choice arrangement built from the freshest stems that week, so it’s always seasonal and never the same twice. Tell us any no-go flowers and we’ll work around them.', 'wildflower' ),
	),
	array(
		__( 'When am I charged?', 'wildflower' ),
		__( 'Before each delivery on your chosen schedule. Your first box ships on the next available delivery date after you sign up.', 'wildflower' ),
	),
	array(
		__( 'Can I send a subscription as a gift?', 'wildflower' ),
		__( 'Yes. Gift plans are prepaid for 4, 8 or 12 deliveries and never auto-renew — the recipient simply receives fresh flowers on schedule, with your note in every box.', 'wildflower' ),
	),
	array(
		__( 'Where do you deliver subscriptions?', 'wildflower' ),
		sprintf( __( 'Across Greater Boston, same-day when you order before %s. See our Delivery page for zones and ZIP coverage.', 'wildflower' ), $brand['cutoff'] ),
	),
);
?>

<!-- HERO -->
<section class="section page-hero">
	<div class="container">
		<p class="eyebrow reveal"><?php esc_html_e( 'Subscriptions', 'wildflower' ); ?></p>
		<h1 class="page-hero__title kinetic"><?php echo wildflower_kinetic( __( 'Fresh flowers, on repeat', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="page-hero__lead reveal"><?php esc_html_e( 'A seasonal bouquet hand-tied by our studio and delivered on your schedule. Pause, skip or cancel anytime — flowers should feel like a treat, never a chore.', 'wildflower' ); ?></p>
		<div class="page-hero__facts reveal">
			<div><strong><?php esc_html_e( 'From $45', 'wildflower' ); ?></strong><span><?php esc_html_e( 'per delivery', 'wildflower' ); ?></span></div>
			<div><strong><?php esc_html_e( 'Free', 'wildflower' ); ?></strong><span><?php esc_html_e( 'delivery on every box', 'wildflower' ); ?></span></div>
			<div><strong><?php esc_html_e( 'Cancel', 'wildflower' ); ?></strong><span><?php esc_html_e( 'anytime, no fees', 'wildflower' ); ?></span></div>
		</div>
		<div class="page-hero__cta reveal">
			<a class="btn--primary btn--lg btn--magnetic" data-magnetic="0.25" href="#plans"><?php esc_html_e( 'See plans', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			<a class="btn--outline btn--lg" href="#how"><?php esc_html_e( 'How it works', 'wildflower' ); ?></a>
		</div>
	</div>
</section>

<!-- HOW IT WORKS -->
<section class="section section--alt" id="how">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'How it works', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Set it once, enjoy it always', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light">
			<?php
			$steps = array(
				array( '01', __( 'Choose your rhythm', 'wildflower' ), __( 'Weekly, every two weeks or monthly — and pick a size that fits your space.', 'wildflower' ) ),
				array( '02', __( 'We design it seasonal', 'wildflower' ), __( 'Our florists hand-tie a fresh designer’s-choice bouquet for every delivery.', 'wildflower' ) ),
				array( '03', __( 'Delivered on schedule', 'wildflower' ), __( 'It arrives like clockwork. Pause, skip or cancel anytime from your account.', 'wildflower' ) ),
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

<!-- PLANS -->
<section class="section" id="plans">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Choose a plan', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'A rhythm for every home', 'wildflower' ) ); // phpcs:ignore ?></h2></div><span class="reviews__rating reveal" style="color:var(--accent);"><?php esc_html_e( 'Pause or cancel anytime', 'wildflower' ); ?></span></div>
		<div class="plans">
			<?php foreach ( $plans as $p ) : ?>
				<div class="plan reveal<?php echo $p[3] ? ' plan--featured' : ''; ?>">
					<?php if ( $p[3] ) : ?><span class="plan__badge"><?php esc_html_e( 'Most popular', 'wildflower' ); ?></span><?php endif; ?>
					<h3 class="plan__name"><?php echo esc_html( $p[0] ); ?></h3>
					<p class="plan__price">$<?php echo esc_html( $p[1] ); ?><span><?php esc_html_e( '/ delivery', 'wildflower' ); ?></span></p>
					<p class="plan__cadence"><?php echo esc_html( $p[2] ); ?></p>
					<ul class="plan__features">
						<?php foreach ( $p[4] as $feat ) : ?>
							<li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><?php echo esc_html( $feat ); ?></li>
						<?php endforeach; ?>
					</ul>
					<a class="<?php echo $p[3] ? 'btn--accent' : 'btn--primary'; ?> plan__cta" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Choose plan', 'wildflower' ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="price-zones__note muted" style="text-align:center;margin-inline:auto;"><?php esc_html_e( 'Sizes available on every plan — Petite, Classic or Grand. Prices shown per delivery; you’re only billed for boxes you receive.', 'wildflower' ); ?></p>
	</div>
</section>

<!-- WHAT'S INCLUDED -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Every box', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'What’s always included', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<ul class="incl-grid reveal">
			<?php
			$incl = array(
				array( 'M12 21v-9M12 14C8.5 14 6 11 6 7c4 0 6 3 6 7zM12 12c3.2 0 5.5-2.2 5.5-5.5C14 6.5 12 8.6 12 12z', __( 'Seasonal & fresh', 'wildflower' ), __( 'Hand-tied that morning from the week’s best stems — never the same twice.', 'wildflower' ) ),
				array( 'M5 13l4 4L19 7', __( 'Free delivery', 'wildflower' ), __( 'Every box is delivered free across Greater Boston, on your schedule.', 'wildflower' ) ),
				array( 'M8 2v4M16 2v4M3 10h18M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z', __( 'Total flexibility', 'wildflower' ), __( 'Skip, pause, reschedule or cancel anytime — fully in your control.', 'wildflower' ) ),
				array( 'M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0zM12 8v4l3 2', __( 'Member perks', 'wildflower' ), __( 'Add-on discounts, a free first vase on Bi-weekly, and priority same-day.', 'wildflower' ) ),
			);
			foreach ( $incl as $i ) :
				?>
				<li class="incl reveal">
					<span class="incl__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo esc_attr( $i[0] ); ?>"/></svg></span>
					<h3><?php echo esc_html( $i[1] ); ?></h3>
					<p><?php echo esc_html( $i[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<!-- GIFT SUBSCRIPTION (dark statement) -->
<section class="section how">
	<div class="container deliv-rule__inner">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Gifting', 'wildflower' ); ?></p>
			<h2 class="deliv-rule__title kinetic"><?php echo wildflower_kinetic( __( 'Give flowers that keep arriving', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p class="deliv-rule__text"><?php esc_html_e( 'A gift subscription is prepaid for 4, 8 or 12 deliveries and never auto-renews. They get a fresh bouquet on schedule with your note in every box — no account, no surprises.', 'wildflower' ); ?></p>
			<a class="btn--accent" style="margin-top:1.5rem;" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Gift a subscription', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
		</div>
		<ul class="gift-terms">
			<li><strong>4</strong><span><?php esc_html_e( 'deliveries · a month of flowers', 'wildflower' ); ?></span></li>
			<li><strong>8</strong><span><?php esc_html_e( 'deliveries · two months', 'wildflower' ); ?></span></li>
			<li><strong>12</strong><span><?php esc_html_e( 'deliveries · a season of fresh', 'wildflower' ); ?></span></li>
		</ul>
	</div>
</section>

<!-- FAQ -->
<section class="section section--alt">
	<div class="container faq-wrap">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Good to know', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Subscription questions, answered', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
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
			<p class="eyebrow" style="position:relative;margin-bottom:1.25rem;"><?php esc_html_e( 'Flowers, every week', 'wildflower' ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( __( 'Start your subscription today.', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p><?php esc_html_e( 'Set your rhythm in a minute. Pause, skip or cancel whenever you like.', 'wildflower' ); ?></p>
			<div class="cta__row">
				<a class="btn--accent btn--lg btn--pulse" href="#plans"><?php esc_html_e( 'Choose a plan', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<?php
/* ---- Structured data: Service + OfferCatalog + FAQPage + Breadcrumb ---- */
$home    = home_url( '/' );
$offers  = array();
foreach ( $plans as $p ) {
	$offers[] = array(
		'@type'    => 'Offer',
		'name'     => $p[0] . ' flower subscription',
		'price'    => $p[1],
		'priceCurrency' => 'USD',
		'priceSpecification' => array(
			'@type'         => 'UnitPriceSpecification',
			'price'         => $p[1],
			'priceCurrency' => 'USD',
			'referenceQuantity' => array( '@type' => 'QuantitativeValue', 'value' => 1, 'unitText' => 'delivery' ),
		),
	);
}
$faq_ld = array();
foreach ( $faqs as $f ) {
	$faq_ld[] = array( '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ) );
}
wildflower_print_jsonld(
	array(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'serviceType' => 'Flower subscription',
			'name'        => 'Flower subscriptions — Greater Boston',
			'provider'    => array( '@id' => $home . '#business' ),
			'areaServed'  => 'Greater Boston',
			'offers'      => array( '@type' => 'OfferCatalog', 'name' => 'Subscription plans', 'itemListElement' => $offers ),
			'url'         => get_permalink(),
		),
		array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_ld ),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Subscriptions', 'item' => get_permalink() ),
			),
		),
	)
);

get_footer();
