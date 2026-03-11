<?php
/**
 * Query helpers for GeneratePress Mansa Child.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return sanitized query parameter from GET.
 *
 * @param string $key Query parameter key.
 * @return string|null
 */
function gp_mansa_child_get_query_var( $key ) {
	if ( empty( $_GET[ $key ] ) ) {
		return null;
	}

	return sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
}

/**
 * Build pagination output with current query args preserved.
 *
 * @param \\WP_Query|null $query Optional query object to paginate. If null, uses global.
 * @return string
 */
function gp_mansa_child_pagination( $query = null ) {
	global $wp_query;

	$query = $query ?: $wp_query;

	$paged = max( 1, (int) get_query_var( 'paged' ) );
	$base  = get_pagenum_link( 1 );

	// Preserve existing query vars for filters and search.
	$add_args = array();
	foreach ( array( 's', 'product-category', 'origin', 'article-topic', 'category', 'topic' ) as $key ) {
		$value = gp_mansa_child_get_query_var( $key );
		if ( $value ) {
			$add_args[ $key ] = $value;
		}
	}

	$links = paginate_links(
		array(
			'base'      => trailingslashit( $base ) . '%_%',
			'format'    => 'page/%#%/',
			'current'   => $paged,
			'total'     => $query->max_num_pages,
			'add_args'  => $add_args,
			'type'      => 'list',
			'prev_text' => '&laquo; ' . __( 'Previous', 'generatepress-mansa-child' ),
			'next_text' => __( 'Next', 'generatepress-mansa-child' ) . ' &raquo;',
		)
	);

	if ( ! $links ) {
		return '';
	}

	return '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'generatepress-mansa-child' ) . '">' . $links . '</nav>';
}

/**
 * Parse paged from query vars.
 *
 * @return int
 */
function gp_mansa_child_get_paged() {
	$paged = get_query_var( 'paged' );
	if ( ! $paged || $paged < 1 ) {
		$paged = 1;
	}

	return absint( $paged );
}

/**
 * Build query args for the product archive list.
 *
 * @param int $paged Current page number.
 * @return array
 */
function gp_mansa_child_get_product_query_args( $paged = 1 ) {
	$search = gp_mansa_child_get_query_var( 's' );
	$category = gp_mansa_child_get_query_var( 'product-category' );
	$origin = gp_mansa_child_get_query_var( 'origin' );

	$args = array(
		'post_type'      => 'mansa_product',
		'posts_per_page' => 12,
		'paged'          => $paged,
	);

	if ( $search ) {
		$args['s'] = $search;
	}

	$tax_query = array();

	if ( $category ) {
		$tax_query[] = array(
			'taxonomy' => 'mansa_product_category',
			'field'    => 'slug',
			'terms'    => array( sanitize_key( $category ) ),
		);
	}

	if ( $origin ) {
		$tax_query[] = array(
			'taxonomy' => 'mansa_origin',
			'field'    => 'slug',
			'terms'    => array( sanitize_key( $origin ) ),
		);
	}

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = array( 'relation' => 'AND' );
		$args['tax_query'] = array_merge( $args['tax_query'], $tax_query );
	}

	return $args;
}

/**
 * Build query args for the brand archive list.
 *
 * @param int $paged Current page number.
 * @return array
 */
function gp_mansa_child_get_brand_query_args( $paged = 1 ) {
	$search = gp_mansa_child_get_query_var( 's' );
	$category = gp_mansa_child_get_query_var( 'product-category' );

	$args = array(
		'post_type'      => 'mansa_brand',
		'posts_per_page' => 12,
		'paged'          => $paged,
	);

	if ( $search ) {
		$args['s'] = $search;
	}

	if ( $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'mansa_product_category',
				'field'    => 'slug',
				'terms'    => array( sanitize_key( $category ) ),
			),
		);
	}

	return $args;
}

/**
 * Build query args for the article archive list.
 *
 * @param int $paged Current page number.
 * @return array
 */
function gp_mansa_child_get_article_query_args( $paged = 1 ) {
	$search = gp_mansa_child_get_query_var( 's' );
	$topic = gp_mansa_child_get_query_var( 'article-topic' );

	$args = array(
		'post_type'      => 'mansa_article',
		'posts_per_page' => 12,
		'paged'          => $paged,
	);

	if ( $search ) {
		$args['s'] = $search;
	}

	if ( $topic ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'mansa_article_topic',
				'field'    => 'slug',
				'terms'    => array( sanitize_key( $topic ) ),
			),
		);
	}

	return $args;
}
