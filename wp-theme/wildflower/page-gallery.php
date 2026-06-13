<?php
/**
 * Template Name: Gallery
 *
 * A mosaic gallery of placeholder tiles (swap for real photos later).
 *
 * @package Wildflower
 */

get_header();
?>
<header class="page-header container">
	<p class="eyebrow"><?php esc_html_e( 'The gallery', 'wildflower' ); ?></p>
	<h1 class="kinetic">
		<?php
		$title = get_the_title();
		echo wildflower_kinetic( $title ? $title : __( 'From the studio', 'wildflower' ) ); // phpcs:ignore
		?>
	</h1>
	<p><?php esc_html_e( 'A living scrapbook of arrangements, weddings and quiet Tuesday bouquets from across Greater Boston.', 'wildflower' ); ?></p>
</header>

<div class="container" style="padding-bottom:5rem;">
	<?php
	// Page body content (optional), then the mosaic.
	while ( have_posts() ) :
		the_post();
		if ( trim( get_the_content() ) ) :
			?>
			<div class="prose" style="margin-bottom:2.5rem;"><?php the_content(); ?></div>
			<?php
		endif;
	endwhile;
	?>
	<div class="gallery-grid">
		<?php wildflower_gallery( 15 ); ?>
	</div>
</div>
<?php
get_footer();
