<?php
/**
 * Template Name: Journal
 *
 * Editorial blog index: a featured story + a grid of post cards. Works as a
 * normal Page (shows even before a Posts page is set) and falls back to demo
 * cards when there are no posts yet. Wildflower system (theme/pult aware).
 *
 * @package Wildflower
 */

get_header();

$paged   = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$journal = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 10,
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
	)
);

?>

<!-- JOURNAL: HEADER -->
<section class="section page-hero" style="padding-bottom:0;">
	<div class="container">
		<p class="eyebrow reveal"><?php esc_html_e( 'Journal', 'wildflower' ); ?></p>
		<h1 class="page-hero__title kinetic"><?php echo wildflower_kinetic( __( 'Notes from the studio', 'wildflower' ) ); // phpcs:ignore ?></h1>
		<p class="page-hero__lead reveal"><?php esc_html_e( 'Care guides, seasonal stems and stories from behind the studio door.', 'wildflower' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( $journal->have_posts() ) : ?>
			<?php
			$journal->the_post();
			wildflower_post_card( true );
			?>
			<div class="journal-grid">
				<?php
				while ( $journal->have_posts() ) :
					$journal->the_post();
					wildflower_post_card( false );
				endwhile;
				?>
			</div>
			<?php
			wp_reset_postdata();
		else :
			$demo = array(
				array( 'Care Guide', 'How to make cut flowers last twice as long', 'Five florist habits — fresh water, a clean cut, the right spot — that keep a bouquet beautiful for two weeks.' ),
				array( 'Seasonal', 'What’s blooming in Boston this month', 'A look at the stems we’re loving right now and how to style them at home.' ),
				array( 'Behind the studio', 'A morning with our florists', 'From the 5 AM market run to the first delivery — a day in the life of the studio.' ),
				array( 'Gifting', 'The right flowers for every occasion', 'A simple guide to choosing blooms that say exactly what you mean.' ),
				array( 'Care Guide', 'Reviving a tired bouquet', 'Three quick tricks to bring drooping stems back to life.' ),
				array( 'Seasonal', 'Why we buy local & seasonal', 'Fresher, longer-lasting, lower-impact — the case for flowers grown nearby.' ),
				array( 'Gifting', 'Flowers that say thank you', 'Our florists’ picks for showing a little gratitude — and why they work so well.' ),
			);
			?>
			<article class="journal-feature">
				<span class="journal-feature__media media"><span class="media-fallback media-fallback--2" aria-hidden="true"><?php echo wildflower_flower_svg(); // phpcs:ignore ?></span></span>
				<div class="journal-feature__body">
					<span class="post-card__cat"><?php esc_html_e( 'Featured', 'wildflower' ); ?></span>
					<h2 class="journal-feature__title"><a href="#"><?php echo esc_html( $demo[0][1] ); ?></a></h2>
					<p class="post-card__excerpt"><?php echo esc_html( $demo[0][2] ); ?></p>
					<span class="post-card__meta"><?php echo esc_html( gmdate( 'M j, Y' ) ); ?> · <?php esc_html_e( '4 min read', 'wildflower' ); ?></span>
					<a class="link-underline post-card__more" href="#"><?php esc_html_e( 'Read story', 'wildflower' ); ?> →</a>
				</div>
			</article>
			<div class="journal-grid">
				<?php foreach ( array_slice( $demo, 1 ) as $di => $d ) : ?>
					<article class="post-card">
						<span class="post-card__media media"><span class="media-fallback media-fallback--<?php echo esc_attr( ( $di % 5 ) + 1 ); ?>" aria-hidden="true"><?php echo wildflower_flower_svg(); // phpcs:ignore ?></span></span>
						<div class="post-card__body">
							<span class="post-card__cat"><?php echo esc_html( $d[0] ); ?></span>
							<h3 class="post-card__title"><a href="#"><?php echo esc_html( $d[1] ); ?></a></h3>
							<p class="post-card__excerpt"><?php echo esc_html( $d[2] ); ?></p>
							<span class="post-card__meta"><?php echo esc_html( gmdate( 'M j, Y' ) ); ?> · <?php esc_html_e( '3 min read', 'wildflower' ); ?></span>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
