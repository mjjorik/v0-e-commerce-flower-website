<?php
/**
 * Occasions page (auto-applies to the page with slug "occasions").
 *
 * Landing grid of occasions linking into the shop (filtered by occasion).
 * Wildflower system (theme/pult aware). CollectionPage + ItemList +
 * BreadcrumbList JSON-LD.
 *
 * @package Wildflower
 */

get_header();
$brand   = wildflower_brand();
$has_woo = class_exists( 'WooCommerce' );
$shop    = $has_woo ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$media   = wildflower_page_media( 'occasions' );

$occasions = array(
	array( 'Birthday', 'Make their whole year bloom.' ),
	array( 'Anniversary', 'Romance, distilled into stems.' ),
	array( 'Love & Romance', 'Say it without saying it.' ),
	array( 'Sympathy', 'When words fall short.' ),
	array( 'Get Well', 'A little lift, hand-delivered.' ),
	array( 'New Baby', 'A soft hello to someone small.' ),
	array( 'Congratulations', 'Toast the big moment.' ),
	array( 'Thank You', 'Gratitude, in bloom.' ),
	array( 'Just Because', 'The best reason there is.' ),
	array( 'Housewarming', 'Warm a brand-new space.' ),
	array( 'Weddings & Events', 'Let’s plan something beautiful.' ),
	array( 'Corporate Gifting', 'Impress, on schedule.' ),
);
?>

<!-- OCCASIONS: HEADER -->
<section class="section page-hero" style="padding-bottom:0;">
	<div class="container">
		<?php wildflower_breadcrumbs(); ?>
		<p class="eyebrow reveal"><?php esc_html_e( 'Occasions', 'wildflower' ); ?></p>
		<?php $occ_say = array( 'happy birthday.', 'thank you.', 'get well soon.', 'I’m sorry.', 'congrats.', 'just because.' ); ?>
		<h1 class="page-hero__title kinetic"><?php echo wildflower_kinetic( __( 'Flowers that say', 'wildflower' ) ); ?><br>
			<span class="hero__rotate" data-rotate style="color:var(--accent);" aria-label="<?php echo esc_attr( implode( ' ', $occ_say ) ); ?>">
				<?php foreach ( $occ_say as $si => $w ) : ?>
					<span class="hero__rotate-word<?php echo 0 === $si ? ' is-active' : ''; ?>"><?php echo esc_html( $w ); ?></span>
				<?php endforeach; ?>
			</span></h1>
		<p class="page-hero__lead reveal"><?php esc_html_e( 'Whatever the moment, there’s a bouquet for it, hand-tied and delivered same-day across Greater Boston. Pick an occasion to start.', 'wildflower' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<ul class="occ-grid">
			<?php foreach ( $occasions as $oi => $o ) :
				$occasion_slug     = sanitize_title( $o[0] );
				$link              = $has_woo ? add_query_arg( 'occasion', $occasion_slug, $shop ) : $shop;
				$occasion_image_id = isset( $media[ $occasion_slug ] ) ? absint( $media[ $occasion_slug ] ) : 0;
				?>
				<li class="occ-card reveal" data-delay="<?php echo esc_attr( ( $oi % 3 ) * 80 ); ?>">
					<a class="occ-card__link" href="<?php echo esc_url( $link ); ?>">
						<span class="occ-card__media">
							<?php if ( $occasion_image_id ) : ?>
								<?php echo wp_get_attachment_image( $occasion_image_id, 'large', false, array( 'alt' => esc_attr( sprintf( __( '%s flowers by Wild Flower Boston', 'wildflower' ), $o[0] ) ), 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span class="media-fallback media-fallback--<?php echo esc_attr( ( $oi % 5 ) + 1 ); ?>" aria-hidden="true"><?php echo wildflower_flower_svg(); // phpcs:ignore ?></span>
							<?php endif; ?>
						</span>
						<span class="occ-card__overlay" aria-hidden="true"></span>
						<span class="occ-card__cap">
							<span class="occ-card__name"><?php echo esc_html( $o[0] ); ?></span>
							<span class="occ-card__desc"><?php echo esc_html( $o[1] ); ?></span>
							<span class="occ-card__go"><?php esc_html_e( 'Shop', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<!-- NOT SURE? (dark) -->
<section class="section how">
	<div class="container deliv-rule__inner">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Not sure?', 'wildflower' ); ?></p>
			<h2 class="deliv-rule__title kinetic"><?php echo wildflower_kinetic( __( 'Let us choose something perfect', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<p class="deliv-rule__text"><?php esc_html_e( 'Tell us the vibe and budget at checkout and our florists will design a seasonal arrangement to suit. Or gift a subscription and let the flowers keep coming.', 'wildflower' ); ?></p>
			<div class="cta__row" style="justify-content:flex-start;margin-top:1.5rem;">
				<a class="btn--accent" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Designer’s choice', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
				<a class="btn--lg" style="background:transparent;border:1px solid color-mix(in oklab,var(--primary-foreground) 45%,transparent);color:var(--primary-foreground);" href="<?php echo esc_url( home_url( '/subscriptions/' ) ); ?>"><?php esc_html_e( 'Subscriptions', 'wildflower' ); ?></a>
			</div>
		</div>
		<ul class="gift-terms">
			<li><strong><?php esc_html_e( '1 PM', 'wildflower' ); ?></strong><span><?php esc_html_e( 'same-day cutoff', 'wildflower' ); ?></span></li>
			<li><strong><?php esc_html_e( '$50+', 'wildflower' ); ?></strong><span><?php esc_html_e( 'bouquets for any day', 'wildflower' ); ?></span></li>
			<li><strong><?php esc_html_e( '4.9★', 'wildflower' ); ?></strong><span><?php esc_html_e( '820+ happy senders', 'wildflower' ); ?></span></li>
		</ul>
	</div>
</section>

<!-- LIGHT BAND, separates the dark section above from the dark footer below -->
<section class="section section--alt">
	<div class="container" style="text-align:center;">
		<p class="eyebrow reveal"><?php esc_html_e( 'Every day, not just occasions', 'wildflower' ); ?></p>
		<h2 class="kinetic" style="margin:.5rem auto 0;max-width:30rem;"><?php echo wildflower_kinetic( __( 'Send flowers for any reason at all', 'wildflower' ) ); // phpcs:ignore ?></h2>
		<p class="reveal" style="margin:1.1rem auto 1.75rem;max-width:34rem;color:color-mix(in oklab,var(--foreground) 72%,transparent);line-height:1.6;"><?php printf( esc_html__( 'Hand-tied fresh and hand-delivered same-day across Greater Boston. Order by %s ET.', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></p>
		<a class="btn--primary btn--lg" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop all bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
	</div>
</section>

<?php
$home  = home_url( '/' );
$items = array();
foreach ( $occasions as $oi => $o ) {
	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $oi + 1,
		'name'     => $o[0],
		'url'      => $has_woo ? add_query_arg( 'occasion', sanitize_title( $o[0] ), $shop ) : $shop,
	);
}
wildflower_print_jsonld(
	array(
		array(
			'@context'      => 'https://schema.org',
			'@type'         => 'CollectionPage',
			'name'          => 'Flowers by occasion',
			'url'           => get_permalink(),
			'isPartOf'      => array( '@id' => $home . '#website' ),
			'mainEntity'    => array( '@type' => 'ItemList', 'itemListElement' => $items ),
		),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Occasions', 'item' => get_permalink() ),
			),
		),
	)
);

get_footer();
