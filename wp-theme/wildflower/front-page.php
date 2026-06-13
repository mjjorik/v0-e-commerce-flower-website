<?php
/**
 * Front page — the Wildflower homepage.
 *
 * @package Wildflower
 */

get_header();
$brand   = wildflower_brand();
$has_woo = class_exists( 'WooCommerce' );
$shop    = $has_woo ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>

<!-- HERO -->
<section class="hero">
	<span class="hero__glow" aria-hidden="true"></span>
	<div class="container--wide">
		<div class="hero__grid">
			<div>
				<span class="hero__badge"><span class="dot"></span> <?php printf( esc_html__( 'Fresh flowers · %s', 'wildflower' ), esc_html( $brand['city'] ) ); ?></span>
				<h1 class="kinetic">
					<?php echo wildflower_kinetic( 'Beautiful flowers.' ); ?><br>
					<span class="italic"><?php echo wildflower_kinetic( 'Honest' ); ?></span> <?php echo wildflower_kinetic( 'prices.' ); ?>
				</h1>
				<div class="hero__lead">
					<p><?php printf( esc_html__( 'Farm-fresh bouquets and weekly subscriptions, hand-delivered same-day across Greater Boston. Order by %s.', 'wildflower' ), esc_html( $brand['cutoff'] ) ); ?></p>
					<a class="btn--primary btn--lg" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
				</div>
			</div>
			<div>
				<div class="hero__media media" data-hero-media>
					<?php
					$hero_id = (int) get_theme_mod( 'wildflower_hero_image', 0 );
					wildflower_media( $hero_id ? $hero_id : null, 'large', 'Wildflower', true );
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if ( $has_woo ) : ?>
<!-- CATEGORY ROW -->
<section class="section" style="padding-bottom:0;">
	<div class="container section-head">
		<h2 class="reveal"><?php esc_html_e( 'Find your bunch', 'wildflower' ); ?></h2>
		<a class="link-underline reveal" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'View all', 'wildflower' ); ?></a>
	</div>
	<div class="cat-row no-scrollbar">
		<?php
		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => 6,
				'exclude'    => array( get_option( 'default_product_cat' ) ),
			)
		);
		if ( ! is_wp_error( $cats ) ) {
			foreach ( $cats as $cat ) {
				$thumb_id = (int) get_term_meta( $cat->term_id, 'thumbnail_id', true );
				?>
				<a class="cat-card" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
					<?php wildflower_media( $thumb_id ? $thumb_id : null, 'large', $cat->name, false ); ?>
					<span class="cat-card__overlay"></span>
					<h3><?php echo esc_html( $cat->name ); ?></h3>
				</a>
				<?php
			}
		}
		?>
	</div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section">
	<div class="container">
		<div class="reveal" style="max-width:36rem;margin-bottom:2.5rem;">
			<p class="eyebrow"><?php esc_html_e( 'The line-up', 'wildflower' ); ?></p>
			<h2 style="font-size:clamp(1.875rem,4vw,3rem);margin-top:.5rem;"><?php esc_html_e( 'Bouquets people keep coming back for', 'wildflower' ); ?></h2>
		</div>
		<?php
		// Featured first, fall back to best-selling / recent.
		$markup = do_shortcode( '[products limit="4" columns="4" visibility="featured"]' );
		if ( false === strpos( $markup, '<li' ) ) {
			$markup = do_shortcode( '[products limit="4" columns="4" orderby="popularity"]' );
		}
		echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div style="margin-top:3rem;text-align:center;">
			<a class="btn--outline reveal" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop all bouquets', 'wildflower' ); ?></a>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- VALUE PROPS -->
<section class="section value-props">
	<div class="container value-props__grid">
		<?php
		$props = array(
			array( 'Same-Day Delivery', 'Order by ' . $brand['cutoff'] . ' and it lands on their doorstep today, across Greater Boston.' ),
			array( 'Farm-Fresh Guarantee', 'Cut this week from New England growers. If it wilts early, we replace it.' ),
			array( '$50–$130, No Markups', 'Honest pricing on every stem. The kind of flowers you can send on a Tuesday.' ),
		);
		foreach ( $props as $p ) {
			?>
			<div class="value-prop reveal">
				<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 20h10M12 4v16M12 4C9 4 7 6 7 9c2 0 4-1 5-3 1 2 3 3 5 3 0-3-2-5-5-5Z"/></svg>
				<h3><?php echo esc_html( $p[0] ); ?></h3>
				<p><?php echo esc_html( $p[1] ); ?></p>
			</div>
			<?php
		}
		?>
	</div>
</section>

<!-- SUBSCRIPTION TEASER -->
<section class="section">
	<div class="container">
		<div class="sub-teaser">
			<div class="media">
				<?php wildflower_media( null, 'large', 'Weekly ritual', true ); ?>
			</div>
			<div class="sub-teaser__body reveal">
				<p class="eyebrow" style="color:color-mix(in oklab,var(--primary) 70%,transparent);"><?php esc_html_e( 'The ritual', 'wildflower' ); ?></p>
				<h2 style="margin-top:.75rem;"><?php esc_html_e( 'Fresh flowers, every week', 'wildflower' ); ?></h2>
				<p style="margin-top:1.25rem;max-width:28rem;color:color-mix(in oklab,var(--foreground) 75%,transparent);line-height:1.6;"><?php esc_html_e( 'A standing order of seasonal blooms, chosen by our studio and delivered like clockwork. Pause, skip or cancel anytime — no strings, just stems.', 'wildflower' ); ?></p>
				<p class="sub-teaser__price"><?php esc_html_e( 'From', 'wildflower' ); ?> <span class="amt">$55</span> <span style="font-size:1rem;color:var(--muted-foreground);">/ delivery</span></p>
				<a class="btn--primary" style="margin-top:1.75rem;" href="<?php echo esc_url( home_url( '/subscriptions/' ) ); ?>"><?php esc_html_e( 'Explore subscriptions', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<!-- OCCASIONS BENTO -->
<section class="section">
	<div class="container">
		<div class="section-head">
			<div style="max-width:32rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'For every moment', 'wildflower' ); ?></p>
				<h2 class="reveal" style="margin-top:.5rem;"><?php esc_html_e( 'Flowers that say it for you', 'wildflower' ); ?></h2>
			</div>
		</div>
		<div class="bento">
			<?php
			$occasions = array(
				array( 'Birthday', 'Make their year bloom.', true ),
				array( 'Anniversary', 'Romance, distilled into stems.', false ),
				array( 'Sympathy', 'When words fall short.', false ),
				array( 'Just Because', 'The best reason there is.', false ),
				array( 'New Baby', 'A soft hello to someone small.', false ),
			);
			foreach ( $occasions as $o ) {
				$link = $has_woo ? add_query_arg( 'occasion', sanitize_title( $o[0] ), $shop ) : home_url( '/' );
				?>
				<a class="bento__tile reveal <?php echo $o[2] ? 'is-big' : ''; ?>" href="<?php echo esc_url( $link ); ?>">
					<?php wildflower_media( null, 'large', $o[0], false ); ?>
					<span class="bento__overlay"></span>
					<span class="bento__caption">
						<h3><?php echo esc_html( $o[0] ); ?></h3>
						<p><?php echo esc_html( $o[1] ); ?></p>
					</span>
				</a>
				<?php
			}
			?>
		</div>
	</div>
</section>

<!-- HOW IT WORKS -->
<section class="section how">
	<div class="container">
		<h2 style="max-width:28rem;"><?php esc_html_e( 'How it works', 'wildflower' ); ?></h2>
		<div class="how__grid">
			<?php
			$steps = array(
				array( '01', 'Pick your bouquet', 'Browse the line-up or start a subscription. Choose a size — Petite, Classic or Grand.' ),
				array( '02', 'Tell us when & where', 'Add a delivery date, a time slot and a gift message. Same-day if you order by 1 PM.' ),
				array( '03', 'We hand-deliver it', 'Our local couriers bring it fresh to the door, anywhere across Greater Boston.' ),
			);
			foreach ( $steps as $s ) {
				?>
				<div class="reveal">
					<p class="how__num"><?php echo esc_html( $s[0] ); ?></p>
					<h3><?php echo esc_html( $s[1] ); ?></h3>
					<p><?php echo esc_html( $s[2] ); ?></p>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>

<!-- TESTIMONIALS -->
<section class="section marquee">
	<div class="container" style="margin-bottom:2.5rem;">
		<p class="eyebrow"><?php esc_html_e( 'Loved across the city', 'wildflower' ); ?></p>
		<h2 style="margin-top:.5rem;"><?php esc_html_e( 'Notes from the neighborhood', 'wildflower' ); ?></h2>
	</div>
	<?php
	$reviews = array(
		array( 'The nicest flowers I’ve sent, and somehow the cheapest. My sister cried.', 'Maya R.', 'Back Bay' ),
		array( 'Subscription is the best $55 I spend each week. The studio has taste.', 'Daniel K.', 'Cambridge' ),
		array( 'Ordered at noon, delivered by 4. The bouquet looked exactly like the photo.', 'Priya S.', 'Somerville' ),
		array( 'Finally, flowers that don’t look like a gas-station afterthought.', 'Tom W.', 'Brookline' ),
	);
	?>
	<div class="marquee__track">
		<?php for ( $g = 0; $g < 2; $g++ ) : ?>
			<div class="marquee__group"<?php echo $g ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( $reviews as $r ) : ?>
					<figure class="quote-card">
						<blockquote>&ldquo;<?php echo esc_html( $r[0] ); ?>&rdquo;</blockquote>
						<figcaption><strong style="color:var(--foreground);"><?php echo esc_html( $r[1] ); ?></strong> · <?php echo esc_html( $r[2] ); ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endfor; ?>
	</div>
</section>

<!-- GALLERY -->
<section class="section">
	<div class="container">
		<div class="section-head">
			<div style="max-width:32rem;">
				<p class="eyebrow reveal"><?php esc_html_e( 'From the studio', 'wildflower' ); ?></p>
				<h2 class="reveal" style="margin-top:.5rem;"><?php esc_html_e( 'The gallery', 'wildflower' ); ?></h2>
			</div>
			<a class="link-underline reveal" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'View all', 'wildflower' ); ?></a>
		</div>
		<div class="gallery-grid">
			<?php wildflower_gallery( 9 ); ?>
		</div>
		<div style="margin-top:2.5rem;text-align:center;">
			<a class="btn--outline" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Explore the gallery', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
		</div>
	</div>
</section>

<!-- BOTTOM CTA -->
<section class="section" style="padding-top:0;">
	<div class="container">
		<div class="cta">
			<span class="cta__glow" aria-hidden="true"></span>
			<p class="eyebrow" style="position:relative;color:color-mix(in oklab,var(--terracotta-foreground) 70%,transparent);margin-bottom:1.25rem;"><?php esc_html_e( 'No occasion required', 'wildflower' ); ?></p>
			<h2 class="kinetic"><?php echo wildflower_kinetic( "Make someone's Tuesday." ); ?></h2>
			<p><?php esc_html_e( 'The best flowers are the ones nobody expected. Send a little joy, today.', 'wildflower' ); ?></p>
			<a class="btn" style="position:relative;margin-top:2.25rem;background:var(--background);color:var(--foreground);box-shadow:0 8px 24px -8px rgba(0,0,0,.25);" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop Bouquets', 'wildflower' ); ?> <?php echo wildflower_arrow(); // phpcs:ignore ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
