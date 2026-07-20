<?php
/**
 * Custom Order page (auto-applies to the page with slug "custom-order").
 *
 * A bespoke-florals request page: explains the process, then a structured
 * request form. The form is functional with zero plugins — on submit it builds
 * a pre-filled email to the studio (see [data-custom-order-form] in main.js).
 * Wire it to a form plugin later if server-side capture is preferred.
 *
 * SEO / AEO / E-E-A-T: process steps, plain-language FAQ, BreadcrumbList +
 * FAQPage JSON-LD (LocalBusiness already lives in the head).
 *
 * @package Wildflower
 */

get_header();
$brand = wildflower_brand();
$email = $brand['email'];

/* Process steps. */
$steps = array(
	array( '01', __( 'Tell us your vision', 'wildflower' ), __( 'Share the occasion, palette, size and any inspiration. The more detail, the better we can match it.', 'wildflower' ) ),
	array( '02', __( 'We design & quote', 'wildflower' ), __( 'A florist proposes a design and a transparent price, usually within one business day.', 'wildflower' ) ),
	array( '03', __( 'You approve', 'wildflower' ), __( 'Tweak the palette, flowers or budget until it is exactly right, then confirm.', 'wildflower' ) ),
	array( '04', __( 'We create & deliver', 'wildflower' ), __( 'We hand-tie it fresh and hand-deliver across Greater Boston on your chosen date.', 'wildflower' ) ),
);

/* Occasion + budget options for the request form. */
$occasions = array( 'Wedding', 'Event / party', 'Sympathy & funeral', 'Corporate / recurring', 'Anniversary', 'Birthday', 'Just because', 'Other' );
$budgets   = array( 'Under $100', '$100 – $200', '$200 – $400', '$400 – $750', '$750+', 'Not sure yet' );

/* FAQ — drives the accordion and FAQPage schema. */
$faqs = array(
	array( __( 'How far in advance should I request a custom order?', 'wildflower' ), __( 'For everyday bespoke bouquets, 2–3 days is ideal. Weddings and large events are best started 2–4 weeks ahead so we can source specialty stems.', 'wildflower' ) ),
	array( __( 'How much does a custom arrangement cost?', 'wildflower' ), __( 'It depends on size, flowers and season. Tell us a budget and we design to it — most custom bouquets start around $100, with event work quoted individually.', 'wildflower' ) ),
	array( __( 'Can you match a photo or a specific palette?', 'wildflower' ), __( 'Yes. Send a photo or describe the colors and we will get as close as the season allows, suggesting the nearest available blooms where needed.', 'wildflower' ) ),
	array( __( 'Do you deliver custom orders?', 'wildflower' ), __( 'We hand-deliver same-day across Boston & Nearby and most of Greater Boston, and reach regional destinations by arrangement.', 'wildflower' ) ),
);
?>

<!-- HERO -->
<section class="section corder-hero">
	<div class="container">
		<p class="eyebrow reveal"><?php esc_html_e( 'Bespoke florals', 'wildflower' ); ?></p>
		<h1 class="kinetic corder-hero__title"><?php echo wildflower_kinetic( __( 'Custom orders, designed around you', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="corder-hero__lead reveal"><?php esc_html_e( 'Something specific in mind — a color story, a favorite flower, a wedding, a standing weekly order? Tell us your vision and our florists will design it, quote it and deliver it across Greater Boston.', 'wildflower' ); ?></p>
		<p class="reveal"><a class="btn--accent btn--lg" href="#request"><?php esc_html_e( 'Start a request', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a></p>
	</div>
</section>

<!-- HOW IT WORKS -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'The process', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'From idea to doorstep', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light">
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
					<label class="field"><span><?php esc_html_e( 'Your vision', 'wildflower' ); ?></span><textarea name="details" rows="5" placeholder="<?php esc_attr_e( 'Favourite flowers, style, size, a photo link, anything at all…', 'wildflower' ); ?>"></textarea></label>

					<div class="corder__actions">
						<button type="submit" class="btn--primary btn--lg"><?php esc_html_e( 'Send request', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></button>
						<span class="corder__note"><?php printf( esc_html__( 'Opens your email to %s. Prefer to write yourself? That works too.', 'wildflower' ), esc_html( $email ) ); ?></span>
					</div>
					<p class="corder__ok" data-custom-order-ok hidden><?php esc_html_e( 'Thanks — your email draft is ready. Hit send and we will reply within one business day.', 'wildflower' ); ?></p>
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
					<a class="link-underline" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $brand['phone'] ) ); ?>"><?php echo esc_html( $brand['phone'] ); ?></a>
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

<?php
/* ---- Structured data: Breadcrumb + FAQPage ---- */
$home    = home_url( '/' );
$faq_ld  = array();
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
