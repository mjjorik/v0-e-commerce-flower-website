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
		<?php wildflower_render_page_hero( get_bloginfo( 'name' ), get_the_title() ); ?>
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
