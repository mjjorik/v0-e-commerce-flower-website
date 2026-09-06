<?php
/**
 * Single post (article) template, editorial reading layout.
 *
 * Reading-progress bar, centered hero, full cover, readable prose column,
 * author + related posts. BlogPosting JSON-LD. Wildflower system.
 *
 * @package Wildflower
 */

get_header();

while ( have_posts() ) :
	the_post();
	$cat   = get_the_category();
	$cname = ! empty( $cat ) ? $cat[0]->name : __( 'Journal', 'wildflower' );
	?>
	<div class="read-progress" data-read-progress aria-hidden="true"><span></span></div>

	<article <?php post_class( 'article' ); ?>>
		<header class="section article__head">
			<div class="container article__head-inner">
				<?php wildflower_breadcrumbs(); ?>
				<p class="eyebrow reveal"><?php echo esc_html( $cname ); ?></p>
				<h1 class="article__title kinetic"><?php echo wildflower_kinetic( get_the_title() ); ?></h1>
				<p class="article__meta reveal">
					<?php
					printf(
						/* translators: 1: author, 2: date, 3: read time. */
						esc_html__( 'By %1$s · %2$s · %3$s', 'wildflower' ),
						esc_html( wildflower_post_author() ),
						esc_html( get_the_date() ),
						esc_html( wildflower_read_time() )
					);
					?>
				</p>
			</div>
		</header>

		<div class="container article__cover-wrap">
			<div class="article__cover media">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'large' );
				} else {
					echo '<span class="media-fallback media-fallback--' . esc_attr( ( get_the_ID() % 5 ) + 1 ) . '" aria-hidden="true">' . wildflower_flower_svg() . '</span>'; // phpcs:ignore
				}
				?>
			</div>
		</div>

		<div class="container">
			<div class="prose article__body">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<nav class="wp-link-pages">', 'after' => '</nav>' ) );
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<div class="article__tags"><?php the_tags( '', '', '' ); ?></div>
			<?php endif; ?>

			<?php $wf_author = wildflower_article_author(); ?>
			<div class="article__author">
				<span class="article__avatar media-fallback media-fallback--2" aria-hidden="true"><?php echo wildflower_flower_svg(); // phpcs:ignore ?></span>
				<div>
					<strong><?php echo esc_html( $wf_author['name'] ); ?></strong>
					<span class="article__author-role"><?php echo esc_html( $wf_author['role'] ); ?></span>
					<p><?php echo esc_html( $wf_author['bio'] ); ?></p>
				</div>
			</div>
		</div>
	</article>

	<?php
	// Related posts, same category first, then topped up with recent posts
		// so the row always shows 3 (no empty column when a category is small).
		$current_id    = get_the_ID();
		$related_posts = array();
		if ( ! empty( $cat ) ) {
			$related_posts = get_posts(
				array(
					'post_type'           => 'post',
					'numberposts'         => 3,
					'post__not_in'        => array( $current_id ),
					'category__in'        => wp_list_pluck( $cat, 'term_id' ),
					'ignore_sticky_posts' => true,
				)
			);
		}
		if ( count( $related_posts ) < 3 ) {
			$exclude       = array_merge( array( $current_id ), wp_list_pluck( $related_posts, 'ID' ) );
			$related_posts = array_merge(
				$related_posts,
				get_posts(
					array(
						'post_type'           => 'post',
						'numberposts'         => 3 - count( $related_posts ),
						'post__not_in'        => $exclude,
						'ignore_sticky_posts' => true,
					)
				)
			);
		}
		if ( ! empty( $related_posts ) ) :
			?>
			<section class="section section--alt">
				<div class="container">
					<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow"><?php esc_html_e( 'Keep reading', 'wildflower' ); ?></p><h2 style="margin-top:.5rem;"><?php esc_html_e( 'More from the journal', 'wildflower' ); ?></h2></div><a class="link-underline" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'All stories', 'wildflower' ); ?></a></div>
					<div class="journal-grid">
						<?php
						foreach ( $related_posts as $related_post ) :
							$GLOBALS['post'] = $related_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							setup_postdata( $related_post );
							wildflower_post_card( false );
						endforeach;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
			<?php
		endif;

		// JSON-LD: BlogPosting.
	$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	wildflower_print_jsonld(
		array(
			'@context'      => 'https://schema.org',
			'@type'         => 'BlogPosting',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array( '@type' => 'Person', 'name' => wildflower_post_author() ),
			'publisher'     => array( '@id' => home_url( '/' ) . '#business' ),
			'image'         => $img ? array( $img ) : array(),
			'mainEntityOfPage' => get_permalink(),
		)
	);

	if ( comments_open() || get_comments_number() ) {
		echo '<div class="container" style="padding-bottom:4rem;">';
		comments_template();
		echo '</div>';
	}
endwhile;

get_footer();
