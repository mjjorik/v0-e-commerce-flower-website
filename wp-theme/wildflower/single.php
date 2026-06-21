<?php
/**
 * Single post (article) template — editorial reading layout.
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
				<p class="eyebrow reveal"><?php echo esc_html( $cname ); ?></p>
				<h1 class="article__title kinetic"><?php echo wildflower_kinetic( get_the_title() ); ?></h1>
				<p class="article__meta reveal">
					<?php
					printf(
						/* translators: 1: author, 2: date, 3: read time. */
						esc_html__( 'By %1$s · %2$s · %3$s', 'wildflower' ),
						esc_html( get_the_author() ),
						esc_html( get_the_date() ),
						esc_html( wildflower_read_time() )
					);
					?>
				</p>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container article__cover-wrap">
				<div class="article__cover media"><?php the_post_thumbnail( 'large' ); ?></div>
			</div>
		<?php endif; ?>

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

			<div class="article__author">
				<span class="article__avatar"><?php echo get_avatar( get_the_author_meta( 'ID' ), 96 ); ?></span>
				<div>
					<strong><?php echo esc_html( get_the_author() ); ?></strong>
					<p><?php echo esc_html( get_the_author_meta( 'description' ) ? get_the_author_meta( 'description' ) : __( 'Wildflower Studio — farm-fresh flowers, hand-tied and delivered same-day across Greater Boston.', 'wildflower' ) ); ?></p>
				</div>
			</div>
		</div>
	</article>

	<?php
	// Related posts (same category).
	$related_ids = array();
	if ( ! empty( $cat ) ) {
		$related = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'category__in'        => wp_list_pluck( $cat, 'term_id' ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( $related->have_posts() ) :
			?>
			<section class="section section--alt">
				<div class="container">
					<div class="section-head"><div style="max-width:36rem;"><p class="eyebrow"><?php esc_html_e( 'Keep reading', 'wildflower' ); ?></p><h2 style="margin-top:.5rem;"><?php esc_html_e( 'More from the journal', 'wildflower' ); ?></h2></div><a class="link-underline" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'All stories', 'wildflower' ); ?></a></div>
					<div class="journal-grid">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							wildflower_post_card( false );
						endwhile;
						?>
					</div>
				</div>
			</section>
			<?php
			wp_reset_postdata();
		endif;
	}

	// JSON-LD: BlogPosting.
	$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	wildflower_print_jsonld(
		array(
			'@context'      => 'https://schema.org',
			'@type'         => 'BlogPosting',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
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
