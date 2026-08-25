<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 

/***************************/
// Category product section product AJAX filter.
/***************************/
add_action( 'wp_ajax_open_mart_cat_filter_ajax', 'open_mart_cat_filter_ajax' );
add_action( 'wp_ajax_nopriv_open_mart_cat_filter_ajax', 'open_mart_cat_filter_ajax' );

function open_mart_cat_filter_ajax() {

	/*
	 * Do not use current_user_can() here.
	 *
	 * This AJAX action is intentionally available to non-logged-in users
	 * via the wp_ajax_nopriv_ hook above. This endpoint only retrieves
	 * publicly available products and does not perform any privileged
	 * or data-modifying action.
	 *
	 * The AJAX request is protected using a nonce.
	 */
	check_ajax_referer( 'openmart_nonce', 'nonce' );

	// Check that WooCommerce product category taxonomy exists.
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		wp_die();
	}

	// Get and sanitize category slug.
	$category_slug = isset( $_POST['data_cat_slug'] )
		? sanitize_title( wp_unslash( $_POST['data_cat_slug'] ) )
		: '';

	// Stop if category slug is empty.
	if ( empty( $category_slug ) ) {
		wp_die();
	}

	// Get selected product option.
	$prdct_optn = sanitize_key(
		get_theme_mod( 'open_mart_category_optn', 'recent' )
	);

	// Base query arguments.
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'tax_query'           => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $category_slug,
			),
		),
	);

	// Product filter option.
	if ( 'random' === $prdct_optn ) {

		$args['orderby'] = 'rand';

	} elseif ( 'featured' === $prdct_optn ) {

		$featured_product_ids = array_filter(
			array_map(
				'absint',
				wc_get_featured_product_ids()
			)
		);

		/*
		 * If no featured products exist, use an impossible product ID
		 * so the query returns no products instead of all products.
		 */
		if ( empty( $featured_product_ids ) ) {
			$featured_product_ids = array( 0 );
		}

		$args['post__in'] = $featured_product_ids;

	} else {

		// Default / recent option.
		$args['orderby'] = 'menu_order';
	}

	// Output filtered products.
	$output = open_mart_product_filter_loop( $args );

	// The function is expected to return HTML.
	echo $output;

	wp_die();
}