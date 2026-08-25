<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 
/***************************/
// Category product section product AJAX filter.
/***************************/
add_action( 'wp_ajax_top_store_cat_filter_ajax', 'top_store_cat_filter_ajax' );
add_action( 'wp_ajax_nopriv_top_store_cat_filter_ajax', 'top_store_cat_filter_ajax' );

function top_store_cat_filter_ajax() {

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
	check_ajax_referer( 'topstore_nonce', 'nonce' );

	// Check required AJAX data.
	if ( ! isset( $_POST['data_cat_slug'] ) ) {
		wp_die();
	}

	// Get and sanitize category slug.
	$category_slug = sanitize_title(
		wp_unslash( $_POST['data_cat_slug'] )
	);

	// Stop if category slug is empty.
	if ( empty( $category_slug ) ) {
		wp_die();
	}

	// Get product filter option.
	$prdct_optn = sanitize_key(
		get_theme_mod( 'top_store_category_optn', 'recent' )
	);

	// Generate product query.
	$args = top_store_product_query(
		$category_slug,
		$prdct_optn
	);

	// Output filtered products.
	top_store_product_filter_loop( $args );

	wp_die();
}
/*****************************************/
//Product filter for List View ajax filter
/*******************************************/
// add_action('wp_ajax_top_store_cat_list_filter_ajax', 'top_store_cat_list_filter_ajax');
// add_action('wp_ajax_nopriv_top_store_cat_list_filter_ajax', 'top_store_cat_list_filter_ajax');
// function top_store_cat_list_filter_ajax(){
// if(isset($_POST['data_cat_slug'])){
// $prdct_optn = get_theme_mod('top_store_category_tb_list_optn','recent');
// $args = top_store_product_query(sanitize_key($_POST['data_cat_slug']),$prdct_optn);
//  top_store_product_list_filter_loop($args);
// }
//     exit;
// }