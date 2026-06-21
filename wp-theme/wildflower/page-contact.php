<?php
/**
 * Contact page (auto-applies to the page with slug "contact").
 *
 * Styled form + studio details + map. Wildflower system (theme/pult aware).
 * ContactPage + BreadcrumbList JSON-LD (LocalBusiness already in head).
 *
 * NOTE: the form markup is presentational. Wire it to a form plugin
 * (Contact Form 7 / WPForms / Fluent Forms) or admin-post for real sending.
 *
 * @package Wildflower
 */

get_header();
$brand = wildflower_brand();
?>

<!-- CONTACT: HEADER -->
<section class="section page-hero" style="padding-bottom:0;">
	<div class="container">
		<p class="eyebrow reveal"><?php esc_html_e( 'Contact', 'wildflower' ); ?></p>
		<h1 class="page-hero__title kinetic"><?php echo wildflower_kinetic( __( 'Say hello', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="page-hero__lead reveal"><?php esc_html_e( 'Questions about an order, a custom arrangement, weddings or corporate gifting? We’re a real studio with real people — reach out and we’ll get back same day.', 'wildflower' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="contact">
			<form class="contact__form reveal" method="post" action="#" novalidate>
				<div class="field-row">
					<label class="field"><span><?php esc_html_e( 'First name', 'wildflower' ); ?></span><input type="text" name="first_name" autocomplete="given-name"></label>
					<label class="field"><span><?php esc_html_e( 'Last name', 'wildflower' ); ?></span><input type="text" name="last_name" autocomplete="family-name"></label>
				</div>
				<label class="field"><span><?php esc_html_e( 'Email', 'wildflower' ); ?></span><input type="email" name="email" autocomplete="email"></label>
				<label class="field"><span><?php esc_html_e( 'Topic', 'wildflower' ); ?></span>
					<select name="topic">
						<option><?php esc_html_e( 'An order', 'wildflower' ); ?></option>
						<option><?php esc_html_e( 'Custom / bespoke arrangement', 'wildflower' ); ?></option>
						<option><?php esc_html_e( 'Weddings & events', 'wildflower' ); ?></option>
						<option><?php esc_html_e( 'Corporate gifting', 'wildflower' ); ?></option>
						<option><?php esc_html_e( 'Something else', 'wildflower' ); ?></option>
					</select>
				</label>
				<label class="field"><span><?php esc_html_e( 'Message', 'wildflower' ); ?></span><textarea name="message" rows="5"></textarea></label>
				<button type="submit" class="btn--primary btn--lg"><?php esc_html_e( 'Send message', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></button>
				<p class="contact__note muted"><?php esc_html_e( 'Prefer email? Write us directly — we reply within a few hours during studio hours.', 'wildflower' ); ?></p>
			</form>

			<aside class="contact__aside reveal">
				<div class="contact__card">
					<ul class="contact__details">
						<li>
							<span class="contact__label"><?php esc_html_e( 'Email', 'wildflower' ); ?></span>
							<a href="mailto:<?php echo esc_attr( $brand['email'] ); ?>"><?php echo esc_html( $brand['email'] ); ?></a>
						</li>
						<li>
							<span class="contact__label"><?php esc_html_e( 'Phone', 'wildflower' ); ?></span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $brand['phone'] ) ); ?>"><?php echo esc_html( $brand['phone'] ); ?></a>
						</li>
						<li>
							<span class="contact__label"><?php esc_html_e( 'Studio hours', 'wildflower' ); ?></span>
							<span><?php esc_html_e( 'Mon–Sat · 8 AM – 4 PM ET', 'wildflower' ); ?><br><?php printf( esc_html__( 'Same-day before %s', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></span>
						</li>
						<li>
							<span class="contact__label"><?php esc_html_e( 'Area', 'wildflower' ); ?></span>
							<span><?php echo esc_html( $brand['city'] ); ?> · <a class="link-underline" href="<?php echo esc_url( home_url( '/delivery/' ) ); ?>"><?php esc_html_e( 'see delivery zones', 'wildflower' ); ?></a></span>
						</li>
						<li>
							<span class="contact__label"><?php esc_html_e( 'Social', 'wildflower' ); ?></span>
							<a class="link-underline" href="<?php echo esc_url( $brand['instagram'] ); ?>" rel="noopener"><?php echo esc_html( $brand['handle'] ); ?></a>
						</li>
					</ul>
				</div>
				<div class="contact__map media" aria-hidden="true">
					<span class="delivery__pin"></span>
					<span class="media-fallback__label"><?php echo esc_html( $brand['city'] ); ?></span>
				</div>
			</aside>
		</div>
	</div>
</section>

<?php
$home = home_url( '/' );
wildflower_print_jsonld(
	array(
		array(
			'@context' => 'https://schema.org',
			'@type'    => 'ContactPage',
			'name'     => 'Contact ' . $brand['name'],
			'url'      => get_permalink(),
			'about'    => array( '@id' => $home . '#business' ),
		),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Contact', 'item' => get_permalink() ),
			),
		),
	)
);

get_footer();
