<?php
/**
 * Single post template.
 *
 * @package Wildflower
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<header class="page-header container">
			<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
			<h1 class="kinetic"><?php echo wildflower_kinetic( get_the_title() ); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container" style="margin-bottom:2.5rem;">
				<div class="media" style="aspect-ratio:16/9;border-radius:var(--radius-2xl);">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="container prose" style="padding-bottom:4rem;">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
	</article>

	<div class="container" style="padding-bottom:5rem;">
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
