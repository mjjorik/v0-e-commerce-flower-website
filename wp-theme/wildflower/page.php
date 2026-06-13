<?php
/**
 * Default page template.
 *
 * @package Wildflower
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<header class="page-header container">
			<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h1 class="kinetic"><?php echo wildflower_kinetic( get_the_title() ); ?></h1>
		</header>
		<div class="container prose" style="padding-bottom:5rem;">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
