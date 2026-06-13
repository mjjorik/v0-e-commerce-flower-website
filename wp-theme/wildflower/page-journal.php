<?php
/**
 * Template Name: Journal
 *
 * Lists the latest blog posts in a styled grid (works as a normal page so it
 * shows even before a "Posts page" is configured).
 *
 * @package Wildflower
 */

get_header();
?>
<header class="page-header container">
	<p class="eyebrow"><?php esc_html_e( 'Journal', 'wildflower' ); ?></p>
	<h1 class="kinetic">
		<?php
		$title = get_the_title();
		echo wildflower_kinetic( $title ? $title : __( 'Notes from the studio', 'wildflower' ) ); // phpcs:ignore
		?>
	</h1>
	<p><?php esc_html_e( 'Care guides, seasonal stems and stories from behind the studio door.', 'wildflower' ); ?></p>
</header>

<div class="container" style="padding-bottom:5rem;">
	<?php
	$paged   = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$journal = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 9,
			'paged'          => $paged,
		)
	);

	if ( $journal->have_posts() ) :
		?>
		<div class="product-grid product-grid--3">
			<?php
			while ( $journal->have_posts() ) :
				$journal->the_post();
				?>
				<article <?php post_class( 'reveal' ); ?>>
					<a href="<?php the_permalink(); ?>">
						<div class="media" style="aspect-ratio:4/3;border-radius:var(--radius-lg);">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'large' );
							} else {
								wildflower_media( null, 'large', get_the_title(), false );
							}
							?>
						</div>
						<h2 style="font-size:1.25rem;margin-top:.75rem;"><?php the_title(); ?></h2>
					</a>
					<p class="muted" style="margin-top:.5rem;font-size:.9rem;"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
				</article>
				<?php
			endwhile;
			?>
		</div>
		<?php
		wp_reset_postdata();
	else :
		?>
		<div class="prose">
			<p><?php esc_html_e( 'The first stories are being written. Check back soon — or add a post in WordPress to see it appear here.', 'wildflower' ); ?></p>
		</div>
		<?php
	endif;
	?>
</div>
<?php
get_footer();
