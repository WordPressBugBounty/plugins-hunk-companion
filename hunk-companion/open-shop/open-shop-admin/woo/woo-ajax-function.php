<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 

/***************************/
//category product section product ajax filter
/***************************/
add_action( 'wp_ajax_open_shop_cat_filter_ajax', 'open_shop_cat_filter_ajax' );
add_action( 'wp_ajax_nopriv_open_shop_cat_filter_ajax', 'open_shop_cat_filter_ajax' );

function open_shop_cat_filter_ajax() {

	// Verify AJAX nonce.
	check_ajax_referer( 'openshop_nonce', 'nonce' );

	// Check required AJAX data.
	if ( ! isset( $_POST['data_cat_slug'] ) ) {
		wp_die();
	}

	// Sanitize category slug.
	$category_slug = sanitize_title(
		wp_unslash( $_POST['data_cat_slug'] )
	);

	// Stop if empty.
	if ( empty( $category_slug ) ) {
		wp_die();
	}

	// Get product option safely.
	$prdct_optn = sanitize_key(
		get_theme_mod( 'open_shop_category_optn', 'recent' )
	);

	// Generate product query.
	$args = open_shop_product_query(
		$category_slug,
		$prdct_optn
	);

	// Output filtered products.
	open_shop_product_filter_loop( $args );

	wp_die();
}
 
/*****************************************/
//Product filter for List View ajax filter
/*******************************************/
// add_action('wp_ajax_open_shop_cat_list_filter_ajax', 'open_shop_cat_list_filter_ajax');
// add_action('wp_ajax_nopriv_open_shop_cat_list_filter_ajax', 'open_shop_cat_list_filter_ajax');
// function open_shop_cat_list_filter_ajax(){
// if(isset($_POST['data_cat_slug'])){
// $prdct_optn = get_theme_mod('open_shop_category_tb_list_optn','recent');
// $args = open_shop_product_query(sanitize_key($_POST['data_cat_slug']),$prdct_optn);
// open_shop_product_list_filter_loop($args);
// }
// exit;
// }