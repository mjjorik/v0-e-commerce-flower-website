<?php
/**
 * Template Name: City Delivery Page
 *
 * Renders a per-city same-day delivery landing page from the unique data in
 * inc/delivery-cities.php (matched by page slug). Assigned automatically to the
 * provisioned city pages. Each page's copy is genuinely distinct — this is a
 * set of helpful local pages, not templated doorway clones.
 *
 * @package Wildflower
 */

$wf_city = function_exists( 'wildflower_current_city' ) ? wildflower_current_city() : null;

// Use the city's crafted <title> for this page.
if ( $wf_city ) {
	add_filter(
		'pre_get_document_title',
		function () use ( $wf_city ) {
			return $wf_city['title'];
		}
	);
}

get_header();

$brand = wildflower_brand();
$shop  = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

// Fallback: if this page isn't a known city, render its normal content.
if ( ! $wf_city ) {
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="page-header container"><p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p><h1 class="kinetic"><?php echo wildflower_kinetic( get_the_title() ); ?></h1></header>
			<div class="container prose" style="padding-bottom:5rem;"><?php the_content(); ?></div>
		</article>
		<?php
	endwhile;
	get_footer();
	return;
}

$city = $wf_city;
?>

<!-- HERO (answer-first) -->
<section class="section page-hero">
	<div class="container">
		<p class="eyebrow reveal"><?php printf( esc_html__( 'Flower delivery · %s, MA', 'wildflower' ), esc_html( $city['name'] ) ); ?></p>
		<h1 class="kinetic page-hero__title"><?php echo wildflower_kinetic( sprintf( __( 'Same-day flower delivery in %s', 'wildflower' ), $city['name'] ) ); // phpcs:ignore ?></h1>
		<p class="page-hero__lead reveal"><?php echo esc_html( $city['answer'] ); ?></p>
		<div class="corder-hero__cta reveal">
			<a class="btn--accent btn--lg" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			<a class="btn--outline btn--lg" href="<?php echo esc_url( home_url( '/delivery/' ) ); ?>"><?php esc_html_e( 'All delivery areas', 'wildflower' ); ?></a>
		</div>
		<p class="corder-hero__meta reveal"><?php printf( esc_html__( 'Order by %1$s ET for same-day · Hand-tied by local Boston florists · Since 2015', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></p>
	</div>
</section>

<!-- INTRO + COVERAGE -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:42rem;"><p class="eyebrow reveal"><?php printf( esc_html__( 'Serving all of %s', 'wildflower' ), esc_html( $city['name'] ) ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( __( 'Neighborhoods we cover', 'wildflower' ) ); // phpcs:ignore ?></h2></div></div>
		<p class="page-hero__lead reveal" style="margin-top:0;"><?php echo esc_html( $city['lead'] ); ?></p>
		<ul class="chips reveal" style="margin-top:1.5rem;">
			<?php foreach ( $city['areas'] as $area ) : ?>
				<li class="chip"><?php echo esc_html( $area ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p class="price-zones__note muted" style="margin-top:1.5rem;">
			<strong><?php esc_html_e( 'ZIP codes served:', 'wildflower' ); ?></strong> <?php echo esc_html( implode( ', ', $city['zips'] ) ); ?>.
			<br><?php echo esc_html( $city['fee'] ); ?>
		</p>
	</div>
</section>

<!-- LOCAL ANGLE -->
<section class="section">
	<div class="container">
		<div class="corder" style="align-items:center;">
			<div>
				<p class="eyebrow reveal"><?php esc_html_e( 'Local know-how', 'wildflower' ); ?></p>
				<h2 class="kinetic" style="margin:.5rem 0 1rem;"><?php echo wildflower_kinetic( $city['angle_t'] ); // phpcs:ignore ?></h2>
				<p class="page-hero__lead reveal" style="margin:0;"><?php echo esc_html( $city['angle_b'] ); ?></p>
			</div>
			<aside class="corder__aside reveal">
				<h3 class="corder__aside-title"><?php printf( esc_html__( 'Popular in %s', 'wildflower' ), esc_html( $city['name'] ) ); ?></h3>
				<ul class="corder__list">
					<?php foreach ( $city['occasions'] as $o ) : ?>
						<li><strong style="display:block;font-family:var(--font-serif);font-size:1.05rem;color:var(--foreground);"><?php echo esc_html( $o[0] ); ?></strong><?php echo esc_html( $o[1] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</aside>
		</div>
	</div>
</section>

<!-- HOW SAME-DAY WORKS -->
<section class="section section--alt">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'How same-day works', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( sprintf( __( 'Order by %s, delivered today', 'wildflower' ), $brand['cutoff'] ) ); // phpcs:ignore ?></h2></div></div>
		<div class="how__grid how__grid--light">
			<?php
			$steps = array(
				array( '01', sprintf( __( 'Order by %s ET', 'wildflower' ), $brand['cutoff'] ), sprintf( __( 'Pick a bouquet and enter a %s address. Add a gift note and any building or floor details.', 'wildflower' ), $city['name'] ) ),
				array( '02', __( 'Hand-tied fresh', 'wildflower' ), __( 'A local florist arranges it the same morning from stems cut this week.', 'wildflower' ) ),
				array( '03', __( 'Delivered today', 'wildflower' ), sprintf( __( 'Our courier hand-delivers it across %s, upright and hydrated, with photo confirmation on request.', 'wildflower' ), $city['name'] ) ),
			);
			foreach ( $steps as $si => $s ) :
				?>
				<div class="reveal" data-delay="<?php echo esc_attr( $si * 110 ); ?>">
					<p class="how__num"><?php echo esc_html( $s[0] ); ?></p>
					<h3><?php echo esc_html( $s[1] ); ?></h3>
					<p><?php echo esc_html( $s[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- FAQ -->
<section class="section">
	<div class="container">
		<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow reveal"><?php esc_html_e( 'Good to know', 'wildflower' ); ?></p><h2 class="kinetic" style="margin-top:.5rem;"><?php echo wildflower_kinetic( sprintf( __( 'Flower delivery in %s — FAQ', 'wildflower' ), $city['name'] ) ); // phpcs:ignore ?></h2></div></div>
		<div class="faq reveal">
			<?php foreach ( $city['faqs'] as $f ) : ?>
				<details class="faq__item">
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
			<p class="eyebrow" style="position:relative;margin-bottom:1.25rem;"><?php printf( esc_html__( 'Sending flowers in %s?', 'wildflower' ), esc_html( $city['name'] ) ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( __( 'Order by 1 PM, and it arrives today.', 'wildflower' ) ); // phpcs:ignore ?></h2>
			<div class="cta__row">
				<a class="btn--accent btn--lg btn--pulse" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<?php
/* ---- Structured data: Service + FAQPage + Breadcrumb ---- */
$home    = home_url( '/' );
$faq_ld  = array();
foreach ( $city['faqs'] as $f ) {
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
			'serviceType' => 'Same-day flower delivery',
			'name'        => sprintf( 'Same-day flower delivery in %s, MA', $city['name'] ),
			'provider'    => array( '@id' => $home . '#business' ),
			'areaServed'  => array( '@type' => 'City', 'name' => $city['name'] . ', MA' ),
			'url'         => get_permalink(),
			'offers'      => array( '@type' => 'Offer', 'priceCurrency' => 'USD', 'priceSpecification' => array( '@type' => 'PriceSpecification', 'minPrice' => 19, 'priceCurrency' => 'USD' ) ),
		),
		array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_ld ),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Delivery', 'item' => home_url( '/delivery/' ) ),
				array( '@type' => 'ListItem', 'position' => 3, 'name' => $city['name'], 'item' => get_permalink() ),
			),
		),
	)
);

get_footer();
