<?php
/**
 * Contact page (auto-applies to the page with slug "contact").
 *
 * Styled form + studio details + map. Wildflower system (theme/pult aware).
 * ContactPage + BreadcrumbList JSON-LD (LocalBusiness already in head).
 *
 * @package Wildflower
 */

get_header();
$brand = wildflower_brand();
$contact_map_id = wildflower_page_media_id( 'contact', 'map' );
$contact_whatsapp = preg_replace( '/[^0-9]/', '', $brand['whatsapp'] );
?>

<!-- CONTACT: HEADER -->
<section class="section page-hero" style="padding-bottom:0;">
	<div class="container">
		<?php wildflower_breadcrumbs(); ?>
		<p class="eyebrow reveal"><?php esc_html_e( 'Contact', 'wildflower' ); ?></p>
		<h1 class="page-hero__title kinetic"><?php echo wildflower_kinetic( __( 'Say hello', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="page-hero__lead reveal"><?php esc_html_e( 'Questions about an order, a custom arrangement, weddings or corporate gifting? We’re a real studio with real people, reach out and we’ll get back same day.', 'wildflower' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="contact">
			<?php
			// A "Choose plan" click on the Subscriptions page lands here with the
			// plan in the query, so the form opens pre-filled and in context
			// instead of dumping the visitor into the catalog.
			$wf_plan     = isset( $_GET['plan'] ) ? sanitize_text_field( wp_unslash( $_GET['plan'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$wf_is_sub   = ( isset( $_GET['topic'] ) && 'subscription' === $_GET['topic'] ) || '' !== $wf_plan; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$wf_prefill  = '';
			if ( '' !== $wf_plan ) {
				/* translators: %s: subscription plan name. */
				$wf_prefill = sprintf( __( "Hi Wildflower, I'd like to start the %s. Please tell me the next steps.", 'wildflower' ), $wf_plan );
			}
			?>
			<form class="contact__form reveal" method="post" data-wildflower-lead-form data-lead-source="contact" aria-describedby="wildflower-contact-status">
				<div class="field-row">
					<label class="field"><span><?php esc_html_e( 'First name', 'wildflower' ); ?> <span aria-hidden="true">*</span></span><input type="text" name="first_name" autocomplete="given-name" required></label>
					<label class="field"><span><?php esc_html_e( 'Last name', 'wildflower' ); ?></span><input type="text" name="last_name" autocomplete="family-name"></label>
				</div>
				<div class="field-row">
					<label class="field"><span><?php esc_html_e( 'Email', 'wildflower' ); ?> <span aria-hidden="true">*</span></span><input type="email" name="email" autocomplete="email" spellcheck="false" required></label>
					<label class="field"><span><?php esc_html_e( 'Phone', 'wildflower' ); ?></span><input type="tel" name="phone" autocomplete="tel" inputmode="tel"></label>
				</div>
				<label class="field"><span><?php esc_html_e( 'Topic', 'wildflower' ); ?></span>
					<select name="topic">
						<?php
						// The slug is what gets submitted, so routing never depends on
						// a translated label. "Flower subscription" goes to the
						// subscriptions mailbox; everything else to orders.
						$wf_topic = $wf_is_sub ? 'subscription' : 'order';
						foreach ( wildflower_contact_topics() as $wf_topic_slug => $wf_topic_label ) :
							?>
							<option value="<?php echo esc_attr( $wf_topic_slug ); ?>"<?php selected( $wf_topic, $wf_topic_slug ); ?>><?php echo esc_html( $wf_topic_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label class="field"><span><?php esc_html_e( 'Message', 'wildflower' ); ?> <span aria-hidden="true">*</span></span><textarea name="message" rows="5" required><?php echo esc_textarea( $wf_prefill ); ?></textarea></label>
				<div class="wf-honeypot" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
				<button type="submit" class="btn--primary btn--lg"><?php esc_html_e( 'Send message', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></button>
				<p class="contact__note muted"><?php esc_html_e( 'Your request goes directly to our studio team. Prefer a quick chat?', 'wildflower' ); ?> <a class="link-underline" href="https://wa.me/<?php echo esc_attr( $contact_whatsapp ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Message us on WhatsApp.', 'wildflower' ); ?></a></p>
				<p id="wildflower-contact-status" class="wf-form-status" data-wildflower-form-status role="status" aria-live="polite" tabindex="-1"></p>
			</form>

			<aside class="contact__aside reveal">
				<div class="contact__card">
					<ul class="contact__details">
						<?php if ( ! empty( $brand['legal_name'] ) ) : ?>
							<li>
								<span class="contact__label"><?php esc_html_e( 'Operated by', 'wildflower' ); ?></span>
								<span><?php echo esc_html( $brand['legal_name'] ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( ! empty( $brand['address'] ) ) : ?>
							<li>
								<span class="contact__label"><?php esc_html_e( 'Studio', 'wildflower' ); ?></span>
								<a class="link-underline" href="<?php echo esc_url( $brand['maps'] ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $brand['address'] ); ?></a>
							</li>
						<?php endif; ?>
						<li>
							<span class="contact__label"><?php esc_html_e( 'Phone', 'wildflower' ); ?></span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $brand['phone'] ) ); ?>"><?php echo esc_html( $brand['phone'] ); ?></a>
						</li>
						<li>
							<span class="contact__label"><?php esc_html_e( 'WhatsApp', 'wildflower' ); ?></span>
							<a class="link-underline" href="https://wa.me/<?php echo esc_attr( $contact_whatsapp ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Chat with the studio', 'wildflower' ); ?></a>
						</li>
						<li>
							<span class="contact__label"><?php esc_html_e( 'Studio hours', 'wildflower' ); ?></span>
							<span><?php esc_html_e( 'Mon–Sat · 10 AM – 6 PM ET', 'wildflower' ); ?><br><?php printf( esc_html__( 'Same-day before %s', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></span>
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
			</aside>
		</div>
	</div>
</section>

	<!-- STUDIO MAP (full-width band) -->
	<section class="section" style="padding-top:0;">
		<div class="container">
			<div class="contact__map contact__map--wide media">
					<?php if ( $contact_map_id ) : ?>
						<?php echo wp_get_attachment_image( $contact_map_id, 'large', false, array( 'alt' => esc_attr__( 'Greater Boston flower delivery area map', 'wildflower' ), 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php elseif ( ! empty( $brand['address'] ) ) : ?>
						<iframe title="<?php esc_attr_e( 'Wildflower studio map', 'wildflower' ); ?>" src="https://maps.google.com/maps?q=<?php echo rawurlencode( $brand['address'] ); ?>&output=embed" style="width:100%;height:100%;border:0;display:block;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					<?php else : ?>
						<span class="delivery__pin"></span>
						<span class="media-fallback__label"><?php echo esc_html( $brand['city'] ); ?></span>
					<?php endif; ?>
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
