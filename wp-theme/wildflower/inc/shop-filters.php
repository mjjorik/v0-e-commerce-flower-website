<?php
/**
 * Shop catalog filters (off-canvas drawer) for the WooCommerce archive.
 *
 * How it maps to WooCommerce (the "right" way):
 *  • Flower Type / Palette / Occasion / Size — global product ATTRIBUTES
 *    (pa_* taxonomies). Rendered dynamically from registered attributes and
 *    filtered with a tax_query.
 *  • New Arrivals / Best Sellers — NOT attributes: query modifiers
 *    (orderby date / total_sales).
 *  • Same-day available — a special flag (a 'same-day' term on a
 *    pa_availability attribute, or a 'same-day' product category).
 *
 * All inputs are GET (shareable, SEO-friendly URLs) and sanitized.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The attribute taxonomies to expose as filter groups, in order.
 * Falls back to all registered product attributes if these aren't present.
 *
 * @return array<string,string> taxonomy => label
 */
function wildflower_filter_attributes() {
	$groups = array();
	if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
		foreach ( wc_get_attribute_taxonomies() as $tax ) {
			$slug = wc_attribute_taxonomy_name( $tax->attribute_name ); // e.g. pa_flower-type
			// Hide the availability attribute — it's surfaced as its own toggle.
			if ( 'pa_availability' === $slug ) {
				continue;
			}
			$groups[ $slug ] = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
		}
	}
	return $groups;
}

/**
 * Render one collapsible filter group of checkboxes.
 *
 * @param string $title   Group heading.
 * @param string $name    Input name (e.g. wf_attr[pa_palette][]).
 * @param array  $options slug => label.
 * @param array  $checked Currently-selected slugs.
 * @param bool   $open    Start expanded.
 */
function wildflower_filter_group( $title, $name, $options, $checked = array(), $open = false ) {
	if ( empty( $options ) ) {
		return;
	}
	?>
	<div class="wf-filter-group<?php echo $open ? ' is-open' : ''; ?>" data-filter-group>
		<button type="button" class="wf-filter-group__head" data-filter-toggle aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
			<span><?php echo esc_html( $title ); ?></span>
			<svg class="wf-filter-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
		</button>
		<div class="wf-filter-group__body">
			<?php foreach ( $options as $slug => $label ) : ?>
				<label class="wf-check">
					<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( (string) $slug, $checked, true ) ); ?>>
					<span class="wf-check__box" aria-hidden="true"></span>
					<span class="wf-check__label"><?php echo esc_html( $label ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Render the filter drawer + the toolbar toggle button.
 */
function wildflower_render_shop_filters() {
	if ( ! ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) ) {
		return;
	}

	$selected_attr = isset( $_GET['wf_attr'] ) && is_array( $_GET['wf_attr'] ) ? wp_unslash( $_GET['wf_attr'] ) : array(); // phpcs:ignore
	$curated       = isset( $_GET['wf_curated'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_GET['wf_curated'] ) ) : array(); // phpcs:ignore
	$same_day      = ! empty( $_GET['wf_same_day'] ); // phpcs:ignore
	$active        = ( $selected_attr ? 1 : 0 ) + ( $curated ? 1 : 0 ) + ( $same_day ? 1 : 0 );
	?>
	<button class="wf-filters-toggle" data-filters-open>
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M6 12h12M10 18h4"/></svg>
		<?php esc_html_e( 'Filters', 'wildflower' ); ?><?php echo $active ? ' <span class="wf-filters-toggle__count">' . esc_html( $active ) . '</span>' : ''; ?>
	</button>

	<div class="wf-filters" data-filters hidden>
		<div class="wf-filters__backdrop" data-filters-close></div>
		<form class="wf-filters__panel" method="get">
			<div class="wf-filters__head">
				<span class="wf-filters__title"><?php esc_html_e( 'Filters', 'wildflower' ); ?></span>
				<a class="wf-filters__reset" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Reset', 'wildflower' ); ?></a>
				<button type="button" class="wf-filters__close" data-filters-close><?php esc_html_e( 'Close', 'wildflower' ); ?></button>
			</div>
			<div class="wf-filters__scroll">
				<p class="wf-filters__lede"><?php esc_html_e( 'Narrow it down — by type, palette, occasion and more.', 'wildflower' ); ?></p>

				<?php
				wildflower_filter_group(
					__( 'Availability', 'wildflower' ),
					'wf_same_day',
					array( '1' => __( 'Same-day available', 'wildflower' ) ),
					$same_day ? array( '1' ) : array(),
					true
				);
				wildflower_filter_group(
					__( 'Curated', 'wildflower' ),
					'wf_curated[]',
					array(
						'new'  => __( 'New Arrivals', 'wildflower' ),
						'best' => __( 'Best Sellers', 'wildflower' ),
					),
					$curated,
					true
				);

				// Attribute groups (Flower Type, Palette, Occasion, Size or Scale…).
				foreach ( wildflower_filter_attributes() as $tax => $label ) {
					$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true ) );
					if ( is_wp_error( $terms ) || empty( $terms ) ) {
						continue;
					}
					$options = array();
					foreach ( $terms as $term ) {
						$options[ $term->slug ] = $term->name;
					}
					$checked = isset( $selected_attr[ $tax ] ) ? array_map( 'sanitize_title', (array) $selected_attr[ $tax ] ) : array();
					wildflower_filter_group( $label, 'wf_attr[' . $tax . '][]', $options, $checked, false );
				}
				?>
			</div>
			<div class="wf-filters__foot">
				<button type="submit" class="btn--primary"><?php esc_html_e( 'Show results', 'wildflower' ); ?></button>
			</div>
		</form>
	</div>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'wildflower_render_shop_filters', 5 );

/* Wrap the toolbar (filters button + result count + ordering) into one row. */
add_action( 'woocommerce_before_shop_loop', function () { echo '<div class="wf-shop-toolbar">'; }, 4 );
add_action( 'woocommerce_before_shop_loop', function () { echo '</div>'; }, 35 );

/**
 * Apply the filters to the shop query.
 *
 * @param WP_Query $q The product query.
 */
function wildflower_apply_shop_filters( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}

	$tax_query = (array) $q->get( 'tax_query' );

	// Attribute filters.
	if ( isset( $_GET['wf_attr'] ) && is_array( $_GET['wf_attr'] ) ) { // phpcs:ignore
		foreach ( wp_unslash( $_GET['wf_attr'] ) as $tax => $terms ) { // phpcs:ignore
			$tax = sanitize_key( $tax );
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = array_filter( array_map( 'sanitize_title', (array) $terms ) );
			if ( $terms ) {
				$tax_query[] = array( 'taxonomy' => $tax, 'field' => 'slug', 'terms' => $terms, 'operator' => 'IN' );
			}
		}
	}

	// Same-day availability (a 'same-day' term on pa_availability, if present).
	if ( ! empty( $_GET['wf_same_day'] ) && taxonomy_exists( 'pa_availability' ) ) { // phpcs:ignore
		$tax_query[] = array( 'taxonomy' => 'pa_availability', 'field' => 'slug', 'terms' => 'same-day' );
	}

	if ( count( array_filter( $tax_query, 'is_array' ) ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}
	if ( $tax_query ) {
		$q->set( 'tax_query', $tax_query );
	}

	// Curated (not attributes).
	$curated = isset( $_GET['wf_curated'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_GET['wf_curated'] ) ) : array(); // phpcs:ignore
	if ( in_array( 'best', $curated, true ) ) {
		$q->set( 'meta_key', 'total_sales' );
		$q->set( 'orderby', 'meta_value_num' );
		$q->set( 'order', 'DESC' );
	} elseif ( in_array( 'new', $curated, true ) ) {
		$q->set( 'orderby', 'date' );
		$q->set( 'order', 'DESC' );
	}
}
add_action( 'woocommerce_product_query', 'wildflower_apply_shop_filters' );
