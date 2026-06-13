<?php
/**
 * Fallback template (blog index / archives).
 *
 * @package Wildflower
 */

get_header();
?>
<div class="container" style="padding-block:3rem 5rem;">
	<?php if ( have_posts() ) : ?>
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="page-header">
				<h1 class="kinetic"><?php echo wildflower_kinetic( get_the_title( get_option( 'page_for_posts' ) ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Journal', 'wildflower' ) ); ?></h1>
			</header>
		<?php endif; ?>

		<div class="product-grid product-grid--3">
			<?php
			while ( have_posts() ) :
				the_post();
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
					<p class="muted" style="margin-top:.5rem;font-size:.9rem;"><?php echo esc_html( get_the_excerpt() ); ?></p>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<div style="margin-top:3rem;">
			<?php the_posts_pagination(); ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing here yet.', 'wildflower' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
