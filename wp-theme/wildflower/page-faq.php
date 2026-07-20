<?php
/**
 * FAQ landing page (auto-applies to the page with slug "faq").
 *
 * Grouped, plain-language answers built for AEO (answer-engine optimization):
 * a visitor — or an AI assistant — can land here cold and get the studio's
 * whole model. Emits a single FAQPage JSON-LD across every group + Breadcrumb.
 *
 * @package Wildflower
 */

get_header();
$brand  = wildflower_brand();
$cutoff = $brand['cutoff'];
$city   = $brand['city'];
$email  = $brand['email'];
$shop   = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

/* Grouped FAQ: each group = array( heading, array( array( q, a ) ) ). */
$groups = array(
	array(
		__( 'Ordering & delivery', 'wildflower' ),
		array(
			array( __( 'Do you offer same-day flower delivery in Boston?', 'wildflower' ), sprintf( __( 'Yes. Order before %1$s ET and we hand-deliver the same day across Boston & Nearby and most of Greater Boston. Orders placed later arrive the next day.', 'wildflower' ), $cutoff ) ),
			array( __( 'Which areas do you deliver to?', 'wildflower' ), __( 'Same-day across Boston & Nearby (Cambridge, Somerville, Brookline, Newton and more) and Greater Boston (Quincy, Waltham, Lexington, Salem and more). We also reach regional destinations like Providence, Worcester and southern New Hampshire by arrangement.', 'wildflower' ) ),
			array( __( 'How much does delivery cost?', 'wildflower' ), __( 'Delivery is calculated from the destination ZIP code — Boston & Nearby starts at $19, Greater Boston from $25, and regional deliveries are quoted individually. The exact fee is shown at checkout, and delivery is a flat $15 on orders of $85 or more.', 'wildflower' ) ),
			array( __( 'Can I schedule delivery for a future date?', 'wildflower' ), __( 'Yes — pick your delivery date at checkout. We recommend ordering ahead for peak dates like Valentine’s Day and Mother’s Day.', 'wildflower' ) ),
		),
	),
	array(
		__( 'Custom orders', 'wildflower' ),
		array(
			array( __( 'Can you make a custom or bespoke arrangement?', 'wildflower' ), sprintf( __( 'Absolutely — it’s what we love. Share your colors, flowers, size and occasion on our %1$scustom order page%2$s and a florist will design and quote it, usually within one business day.', 'wildflower' ), '<a class="link-underline" href="' . esc_url( home_url( '/custom-order/' ) ) . '">', '</a>' ) ),
			array( __( 'Do you do weddings and events?', 'wildflower' ), __( 'Yes. From bridal bouquets to full ceremony and reception florals, and centerpieces for showers, dinners and corporate events. Start a request and we’ll set up a short consult.', 'wildflower' ) ),
			array( __( 'Can I set up recurring or corporate flowers?', 'wildflower' ), __( 'We run standing weekly or biweekly orders for homes and businesses — lobbies, offices, restaurants — with simple invoicing and a designer’s choice of what’s freshest that week.', 'wildflower' ) ),
		),
	),
	array(
		__( 'Freshness & care', 'wildflower' ),
		array(
			array( __( 'How fresh are the flowers?', 'wildflower' ), __( 'Every arrangement is hand-tied to order from stems cut that week and kept hydrated in transit. We never pre-make and hold bouquets.', 'wildflower' ) ),
			array( __( 'Do you have a freshness guarantee?', 'wildflower' ), __( 'Yes. If your flowers wilt earlier than they should, tell us within a few days and we’ll replace them. Your happiness is the whole point.', 'wildflower' ) ),
			array( __( 'How do I make my flowers last longer?', 'wildflower' ), __( 'Trim the stems on an angle, change the water every couple of days, keep them out of direct sun and away from ripening fruit, and remove any leaves below the waterline.', 'wildflower' ) ),
		),
	),
	array(
		__( 'Account & payment', 'wildflower' ),
		array(
			array( __( 'What payment methods do you accept?', 'wildflower' ), __( 'All major credit and debit cards through our secure checkout. Corporate accounts can be invoiced by arrangement.', 'wildflower' ) ),
			array( __( 'Can I add a gift message?', 'wildflower' ), __( 'Yes — add a handwritten card and your message at checkout, and choose add-ons like a vase, soy candle or Belgian truffles.', 'wildflower' ) ),
			array( __( 'How do I change or cancel an order?', 'wildflower' ), sprintf( __( 'Email %s as soon as possible. We can usually adjust or cancel before the arrangement is made and dispatched.', 'wildflower' ), $email ) ),
		),
	),
);
?>

<!-- HERO -->
<section class="section corder-hero">
	<div class="container">
		<p class="eyebrow reveal"><?php esc_html_e( 'Help & answers', 'wildflower' ); ?></p>
		<h1 class="kinetic corder-hero__title"><?php echo wildflower_kinetic( __( 'Frequently asked questions', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="corder-hero__lead reveal"><?php printf( esc_html__( 'Everything about ordering, delivery, custom work and caring for your flowers from Wildflower, a Boston flower studio serving %s. Can’t find your answer? We’re a quick message away.', 'wildflower' ), esc_html( $city ) ); ?></p>
		<div class="corder-hero__cta reveal">
			<a class="btn--accent btn--lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Ask us anything', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			<a class="btn--outline btn--lg" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop flowers', 'wildflower' ); ?></a>
		</div>
	</div>
</section>

<!-- FAQ GROUPS -->
<?php foreach ( $groups as $gi => $g ) : ?>
	<section class="section<?php echo 0 === $gi % 2 ? ' section--alt' : ''; ?>">
		<div class="container">
			<div class="section-head"><div style="max-width:36rem;"><h2 class="kinetic" style="margin-top:.25rem;"><?php echo wildflower_kinetic( $g[0] ); // phpcs:ignore ?></h2></div></div>
			<div class="faq reveal">
				<?php foreach ( $g[1] as $f ) : ?>
					<details class="faq__item">
						<summary class="faq__q"><?php echo esc_html( $f[0] ); ?><span class="faq__icon" aria-hidden="true"></span></summary>
						<div class="faq__a"><p><?php echo wp_kses_post( $f[1] ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endforeach; ?>

<!-- CTA -->
<section class="section" style="padding-top:0;">
	<div class="container">
		<div class="cta">
			<p class="eyebrow" style="position:relative;margin-bottom:1.25rem;"><?php esc_html_e( 'Still curious?', 'wildflower' ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( __( 'We’re happy to help.', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p><?php esc_html_e( 'Reach the studio and a real Boston florist will get back to you — usually the same day.', 'wildflower' ); ?></p>
			<div class="cta__row">
				<a class="btn--accent btn--lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact us', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<?php
/* ---- Structured data: FAQPage (all groups) + Breadcrumb ---- */
$home   = home_url( '/' );
$faq_ld = array();
foreach ( $groups as $g ) {
	foreach ( $g[1] as $f ) {
		$faq_ld[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $f[0] ),
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $f[1] ) ),
		);
	}
}
echo '<script type="application/ld+json">' . wp_json_encode(
	array(
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
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'FAQ', 'item' => get_permalink() ),
			),
		),
	)
) . '</script>'; // phpcs:ignore

get_footer();
