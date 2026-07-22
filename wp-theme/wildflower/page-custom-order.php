<?php
/**
 * Custom Order landing page (auto-applies to the page with slug "custom-order").
 *
 * Built as a self-contained landing page: a visitor arriving cold from search
 * (not via the homepage) gets the full picture — who Wildflower is, what can be
 * ordered, how it works, what it costs, proof, and a working request form.
 *
 * The request form is functional with zero plugins — on submit it builds a
 * pre-filled email to the studio (see [data-custom-order-form] in main.js).
 *
 * SEO / GEO / AEO / E-E-A-T: location-rich copy, order-type coverage, process,
 * transparent pricing, reviews, plain-language FAQ, and Service + FAQPage +
 * BreadcrumbList JSON-LD (LocalBusiness already lives in the head).
 *
 * @package Wildflower
 */

get_header();
$brand = wildflower_brand();
$email = $brand['email'];
$phone = $brand['phone'];
$city  = $brand['city'];
$shop  = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

$svg = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">';

/* What we create — order types (SEO intent coverage). */
$types = array(
	array( $svg . '<path d="M12 21c0-5 3-8 7-9-4-1-7-4-7-9-0 5-3 8-7 9 4 1 7 4 7 9z"/></svg>', __( 'Bespoke bouquets', 'wildflower' ), __( 'A one-off hand-tied bouquet built around your palette, flowers and budget.', 'wildflower' ) ),
	array( $svg . '<path d="M12 2v6"/><path d="M9 5h6"/><path d="M5 21c0-5 3-8 7-9 4 1 7 4 7 9"/></svg>', __( 'Weddings', 'wildflower' ), __( 'Bridal bouquets, ceremony and reception florals, plus on-site styling.', 'wildflower' ) ),
	array( $svg . '<rect x="4" y="9" width="16" height="11" rx="1.5"/><path d="M4 13h16"/><path d="M12 9V5"/><path d="M9 5h6"/></svg>', __( 'Events & parties', 'wildflower' ), __( 'Centerpieces and installations for showers, dinners and launches.', 'wildflower' ) ),
	array( $svg . '<path d="M12 21s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 11c0 5.65-7 10-7 10z"/></svg>', __( 'Sympathy & funeral', 'wildflower' ), __( 'Considered, respectful arrangements delivered with care and discretion.', 'wildflower' ) ),
	array( $svg . '<rect x="3" y="8" width="18" height="12" rx="1.5"/><path d="M3 12h18"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/></svg>', __( 'Corporate & recurring', 'wildflower' ), __( 'Weekly lobby or office flowers on a standing order, invoiced simply.', 'wildflower' ) ),
	array( $svg . '<rect x="4" y="8" width="16" height="13" rx="1.5"/><path d="M4 12h16"/><path d="M12 8v13"/><path d="M12 8S9 3 6.5 5 8 8 12 8zm0 0s3-5 5.5-3S16 8 12 8z"/></svg>', __( 'Gifts & add-ons', 'wildflower' ), __( 'Add a vase, soy candle, Belgian truffles or a handwritten card.', 'wildflower' ) ),
);

/* Process steps. */
$steps = array(
	array( '01', __( 'Tell us your vision', 'wildflower' ), __( 'Share the occasion, palette, size and any inspiration. The more detail, the better we can match it.', 'wildflower' ) ),
	array( '02', __( 'We design & quote', 'wildflower' ), __( 'A florist proposes a design and a transparent price, usually within one business day.', 'wildflower' ) ),
	array( '03', __( 'You approve', 'wildflower' ), __( 'Adjust the palette, flowers or budget until it is exactly right, then confirm.', 'wildflower' ) ),
	array( '04', __( 'We create & deliver', 'wildflower' ), __( 'We hand-tie it fresh and hand-deliver across Greater Boston on your chosen date.', 'wildflower' ) ),
);

/* Pricing guidance — big number + label + note. */
$tiers = array(
	array( '$100+', __( 'Bespoke bouquets', 'wildflower' ), __( 'Most custom bouquets start around $100. You set the budget and we design to it.', 'wildflower' ) ),
	array( '$250+', __( 'Events & centerpieces', 'wildflower' ), __( 'Table and space florals, scoped to your venue, headcount and season.', 'wildflower' ) ),
	array( __( 'By quote', 'wildflower' ), __( 'Weddings', 'wildflower' ), __( 'Full-service wedding florals, quoted individually after a short consult.', 'wildflower' ) ),
);

/* Reviews tailored to custom work. */
$reviews = array(
	array( __( 'I described my grandmother’s garden and they recreated it as a bouquet. I actually teared up.', 'wildflower' ), 'Elena M.', __( 'Brookline', 'wildflower' ) ),
	array( __( 'They handled our whole wedding — bridal, arch, tables. Calm, on time, exactly the palette we asked for.', 'wildflower' ), 'James & Priya', __( 'Cambridge', 'wildflower' ) ),
	array( __( 'We get fresh flowers in the office lobby every Monday now. Clients always comment on them.', 'wildflower' ), 'Marcus T.', __( 'Seaport', 'wildflower' ) ),
);

/* Occasion + budget options for the request form. */
$occasions = array( 'Bespoke bouquet', 'Wedding', 'Event / party', 'Sympathy & funeral', 'Corporate / recurring', 'Anniversary', 'Birthday', 'Just because', 'Other' );
$budgets   = array( 'Under $100', '$100 – $200', '$200 – $400', '$400 – $750', '$750+', 'Not sure yet' );

/* FAQ — drives the accordion and FAQPage schema. */
$faqs = array(
	array( __( 'What is a custom flower order?', 'wildflower' ), __( 'It is an arrangement we design from scratch for you — your colors, flowers, size and occasion — rather than a fixed bouquet off the shop page. Everything from a single bespoke bouquet to full wedding florals starts here.', 'wildflower' ) ),
	array( __( 'How far in advance should I request one?', 'wildflower' ), __( 'For everyday bespoke bouquets, 2–3 days is ideal. Weddings and large events are best started 2–4 weeks ahead so we can source specialty stems. Need something sooner? Ask — we often accommodate rush requests.', 'wildflower' ) ),
	array( __( 'How much does a custom arrangement cost?', 'wildflower' ), __( 'It depends on size, flowers and season. Most custom bouquets start around $100, event and centerpiece work from about $250, and weddings are quoted individually. Tell us your budget and we design to it — you always approve a transparent quote first.', 'wildflower' ) ),
	array( __( 'Can you match a photo or a specific palette?', 'wildflower' ), sprintf( __( 'Yes. Send a photo or describe the colors and we get as close as the season allows, suggesting the nearest available blooms where needed. We are based in %s and know what is fresh locally week to week.', 'wildflower' ), $city ) ),
	array( __( 'Do you deliver custom orders?', 'wildflower' ), sprintf( __( 'We hand-deliver same-day across Boston & Nearby and most of Greater Boston, and reach regional destinations by arrangement. Delivery is calculated from the destination ZIP and shown before you pay — a flat $15 on orders of $85+. Order by %s for same-day.', 'wildflower' ), $brand['cutoff'] ) ),
	array( __( 'Can I set up recurring or corporate flowers?', 'wildflower' ), __( 'Absolutely. We run standing weekly or biweekly orders for homes and businesses — lobbies, offices, restaurants — with simple invoicing and a designer’s choice of what is best that week.', 'wildflower' ) ),
);
?>

<!-- HERO -->
<section class="section corder-hero">
	<div class="container">
		<?php wildflower_breadcrumbs(); ?>
		<p class="eyebrow reveal"><?php printf( esc_html__( 'Custom florals · %s', 'wildflower' ), esc_html( $city ) ); ?></p>
		<h1 class="kinetic corder-hero__title"><?php echo wildflower_kinetic( __( 'Custom flower orders in Boston', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="corder-hero__lead reveal"><?php printf( esc_html__( 'Wildflower is a Boston flower studio designing bespoke bouquets and arrangements to order — your colors, your flowers, your occasion, hand-tied fresh and delivered across %s. Tell us what you have in mind and we’ll design it, quote it, and bring it to life.', 'wildflower' ), esc_html( $city ) ); ?></p>
		<div class="corder-hero__cta reveal">
			<a class="btn--accent btn--lg" href="#request"><?php esc_html_e( 'Start your request', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			<a class="btn--outline btn--lg" href="#what"><?php esc_html_e( 'See what we make', 'wildflower' ); ?></a>
		</div>
		<p class="corder-hero__meta reveal"><?php esc_html_e( 'Same-day delivery · Hand-tied by local florists · Serving Boston since 2015', 'wildflower' ); ?></p>
	</div>
</section>

<!-- TRUST (E-E-A-T) -->
<section class="section" style="padding-top:0;">
	<div class="container trust-strip__row" style="border:0;">
		<?php
		$trust = array(
			array( $svg . '<path d="M3 7.5h10v8.5H3z"/><path d="M13 10.5h4l3.2 3v2.5H13z"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17.2" cy="17.5" r="1.6"/></svg>', __( 'Local couriers', 'wildflower' ), __( 'Boston-based, hand-delivered', 'wildflower' ) ),
			array( $svg . '<path d="M12 21v-9"/><path d="M12 14C8.5 14 6 11 6 7c4 0 6 3 6 7z"/><path d="M12 12c3.2 0 5.5-2.2 5.5-5.5C14 6.5 12 8.6 12 12z"/></svg>', __( 'Farm-fresh stems', 'wildflower' ), __( 'Cut this week, hydrated in transit', 'wildflower' ) ),
			array( $svg . '<path d="M20 6 9 17l-5-5"/></svg>', __( 'You approve first', 'wildflower' ), __( 'A transparent quote before we start', 'wildflower' ) ),
			array( $svg . '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>', __( 'Since 2015', 'wildflower' ), __( '40k+ arrangements delivered', 'wildflower' ) ),
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

<!-- WHAT WE CREATE -->
<section class="section section--alt" id="what">
	<div class="container">
		<div class="section-head"><div style="max-width:40rem;"><p class="eyebrow reveal"><?php esc_html_e( 'What we create', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Anything you can dream in flowers', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="corder-types">
			<?php foreach ( $types as $ti => $t ) : ?>
				<div class="corder-type reveal" data-delay="<?php echo esc_attr( $ti * 70 ); ?>">
					<span class="corder-type__ic"><?php echo $t[0]; // phpcs:ignore ?></span>
					<h3><?php echo esc_html( $t[1] ); ?></h3>
					<p><?php echo esc_html( $t[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- PROCESS -->
<section class="section">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'The process', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'From idea to doorstep', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light how__grid--quad">
			<?php foreach ( $steps as $si => $s ) : ?>
				<div class="reveal" data-delay="<?php echo esc_attr( $si * 110 ); ?>">
					<p class="how__num"><?php echo esc_html( $s[0] ); ?></p>
					<h3><?php echo esc_html( $s[1] ); ?></h3>
					<p><?php echo esc_html( $s[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- PRICING GUIDANCE -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:40rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Transparent pricing', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Where custom orders start', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light corder-price">
			<?php foreach ( $tiers as $pi => $p ) : ?>
				<div class="reveal" data-delay="<?php echo esc_attr( $pi * 110 ); ?>">
					<p class="how__num"><?php echo esc_html( $p[0] ); ?></p>
					<h3><?php echo esc_html( $p[1] ); ?></h3>
					<p><?php echo esc_html( $p[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="corder-price__note muted"><?php esc_html_e( 'You always approve a transparent quote before we begin. Delivery is calculated by destination ZIP and shown at checkout — a flat $15 on orders of $85 or more.', 'wildflower' ); ?></p>
	</div>
</section>

<!-- REVIEWS (E-E-A-T) -->
<section class="section reviews">
	<div class="container">
		<div class="section-head">
			<div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'In their words', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Custom orders, real people', 'wildflower' ) ); // phpcs:ignore ?></h2></div>
			<span class="reviews__rating reveal">★ 4.9 · 820+ <?php esc_html_e( 'reviews', 'wildflower' ); ?></span>
		</div>
		<div class="reviews-grid">
			<?php foreach ( $reviews as $ri => $r ) : ?>
				<figure class="quote-card reveal" data-delay="<?php echo esc_attr( $ri * 110 ); ?>">
					<span class="quote-card__stars">★★★★★</span>
					<blockquote>&ldquo;<?php echo esc_html( $r[0] ); ?>&rdquo;</blockquote>
					<figcaption class="quote-card__by">
						<span class="quote-card__avatar"><span class="media-fallback media-fallback--<?php echo esc_attr( ( $ri % 5 ) + 1 ); ?>" aria-hidden="true"><?php echo wildflower_flower_svg(); // phpcs:ignore ?></span></span>
						<span class="quote-card__who"><strong><?php echo esc_html( $r[1] ); ?></strong><span class="quote-card__loc"><?php echo esc_html( $r[2] ); ?> · <?php esc_html_e( 'Verified buyer', 'wildflower' ); ?></span></span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- REQUEST FORM -->
<section class="section" id="request">
	<div class="container">
		<div class="corder">
			<div class="corder__form-col">
				<div class="section-head" style="margin-bottom:1.5rem;"><div><p class="eyebrow reveal"><?php esc_html_e( 'Your request', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Tell us what you are dreaming up', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>

				<form class="corder__form reveal" data-custom-order-form data-studio-email="<?php echo esc_attr( $email ); ?>" method="post" action="#" novalidate>
					<div class="field-row">
						<label class="field"><span><?php esc_html_e( 'Full name', 'wildflower' ); ?></span><input type="text" name="name" autocomplete="name" required></label>
						<label class="field"><span><?php esc_html_e( 'Email', 'wildflower' ); ?></span><input type="email" name="email" autocomplete="email" required></label>
					</div>
					<div class="field-row">
						<label class="field"><span><?php esc_html_e( 'Phone', 'wildflower' ); ?></span><input type="tel" name="phone" autocomplete="tel"></label>
						<label class="field"><span><?php esc_html_e( 'Occasion', 'wildflower' ); ?></span>
							<select name="occasion">
								<?php foreach ( $occasions as $o ) : ?>
									<option value="<?php echo esc_attr( $o ); ?>"><?php echo esc_html( $o ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					</div>
					<div class="field-row">
						<label class="field"><span><?php esc_html_e( 'Date needed', 'wildflower' ); ?></span><input type="date" name="date"></label>
						<label class="field"><span><?php esc_html_e( 'Budget', 'wildflower' ); ?></span>
							<select name="budget">
								<?php foreach ( $budgets as $bgt ) : ?>
									<option value="<?php echo esc_attr( $bgt ); ?>"><?php echo esc_html( $bgt ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					</div>
					<div class="field-row">
						<label class="field"><span><?php esc_html_e( 'Colors / palette', 'wildflower' ); ?></span><input type="text" name="palette" placeholder="<?php esc_attr_e( 'e.g. blush, ivory, dusty blue', 'wildflower' ); ?>"></label>
						<label class="field"><span><?php esc_html_e( 'Delivery city or ZIP', 'wildflower' ); ?></span><input type="text" name="location" placeholder="<?php esc_attr_e( 'e.g. Cambridge 02139', 'wildflower' ); ?>"></label>
					</div>
					<label class="field"><span><?php esc_html_e( 'Your vision', 'wildflower' ); ?></span><textarea name="details" rows="5" placeholder="<?php esc_attr_e( 'Favorite flowers, style, size, a photo link, anything at all…', 'wildflower' ); ?>"></textarea></label>

					<div class="corder__actions">
						<button type="submit" class="btn--primary btn--lg"><?php esc_html_e( 'Send request', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></button>
						<span class="corder__note"><?php printf( esc_html__( 'Opens your email to %s. Prefer to write yourself? That works too.', 'wildflower' ), esc_html( $email ) ); ?></span>
					</div>
					<p class="corder__ok" data-custom-order-ok hidden><?php esc_html_e( 'Thanks — your email draft is ready. Hit send and we’ll reply within one business day.', 'wildflower' ); ?></p>
				</form>
			</div>

			<aside class="corder__aside reveal">
				<h3 class="corder__aside-title"><?php esc_html_e( 'Why order bespoke', 'wildflower' ); ?></h3>
				<ul class="corder__list">
					<li><?php esc_html_e( 'Designed to your palette, flowers and budget', 'wildflower' ); ?></li>
					<li><?php esc_html_e( 'Hand-tied fresh by a local Boston florist', 'wildflower' ); ?></li>
					<li><?php esc_html_e( 'Transparent quote before you commit', 'wildflower' ); ?></li>
					<li><?php esc_html_e( 'Weddings, events & standing weekly orders welcome', 'wildflower' ); ?></li>
				</ul>
				<p class="corder__aside-contact">
					<?php esc_html_e( 'Rather talk it through?', 'wildflower' ); ?><br>
					<a class="link-underline" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><br>
					<a class="link-underline" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
				</p>
			</aside>
		</div>
	</div>
</section>

<!-- FAQ (AEO) -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Good to know', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Custom order questions', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="faq reveal">
			<?php foreach ( $faqs as $f ) : ?>
				<details class="faq__item">
					<summary class="faq__q"><?php echo esc_html( $f[0] ); ?><span class="faq__icon" aria-hidden="true"></span></summary>
					<div class="faq__a"><p><?php echo esc_html( $f[1] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- FINAL CTA -->
<section class="section">
	<div class="container">
		<div class="cta">
			<p class="eyebrow" style="position:relative;margin-bottom:1.25rem;"><?php esc_html_e( 'Let’s make something', 'wildflower' ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( __( 'Ready to design something one-of-a-kind?', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p><?php esc_html_e( 'Send us your idea and a Boston florist will reply within one business day with a design and a price.', 'wildflower' ); ?></p>
			<div class="cta__row">
				<a class="btn--accent btn--lg btn--pulse" href="#request"><?php esc_html_e( 'Start your request', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
				<a class="btn--lg" style="position:relative;background:transparent;border:1px solid color-mix(in oklab,var(--primary-foreground) 45%,transparent);color:var(--primary-foreground);" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php
/* ---- Structured data: Breadcrumb + FAQPage + Service ---- */
$home   = home_url( '/' );
$faq_ld = array();
foreach ( $faqs as $f ) {
	$faq_ld[] = array(
		'@type'          => 'Question',
		'name'           => $f[0],
		'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ),
	);
}
echo '<script type="application/ld+json">' . wp_json_encode(
	array(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'serviceType' => 'Custom floral design',
			'name'        => 'Custom flower orders — Greater Boston',
			'provider'    => array( '@id' => $home . '#business' ),
			'areaServed'  => $city,
			'url'         => get_permalink(),
			'offers'      => array( '@type' => 'Offer', 'priceCurrency' => 'USD', 'priceSpecification' => array( '@type' => 'PriceSpecification', 'minPrice' => 100, 'priceCurrency' => 'USD' ) ),
		),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Custom order', 'item' => get_permalink() ),
			),
		),
		array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $faq_ld,
		),
	)
) . '</script>'; // phpcs:ignore

get_footer();
