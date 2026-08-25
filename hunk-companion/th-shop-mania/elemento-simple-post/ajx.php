<?php
include_once 'post-setting.php';
// add_action('wp_ajax_elemento_simple_post', 'elemento_simple_post');
// add_action('wp_ajax_nopriv_elemento_simple_post', 'elemento_simple_post');
// function elemento_simple_post()
// {

//     // print_r($_POST);
//     // return;

//     // echo '<pre>';
//     if (isset($_POST['post_data']['current_page']) && isset($_POST['post_data']['total_page'])) {
//         $postSEttings = new elemento_post_simple();
//         $allSEttings = $_POST['post_data'];
//         $numOfPost = intval($allSEttings['post_per_page']);
//         $category_ = $allSEttings['category'];
//         // $trigger_page = $_POST['trigger'];
//         $currentPage = $allSEttings['current_page'];
//         $args = array(
//             'post_type' => 'post',
//             'posts_per_page' => $numOfPost,
//         );
//         // post show by 
//         if ($allSEttings['post_show_by'] !==  'recent') {
//             $args['orderby'] = $allSEttings['post_show_by'];
//         }
//         // page if number 
//         // $checkINtPage = intval($trigger_page);
//         if ($currentPage) {
//             $args['paged'] = $currentPage;
//         }
//         $postHtml = '';
//         $stringCate = implode(",", $category_);
//         if (!in_array('all', $category_)) {
//             $args['category_name'] = $stringCate;
//         }
//         // html options 
//         $query = new WP_Query($args);

//         if ($query->have_posts()) {
//             // pagination data -------------- 
//             // $pagination_ = '';
//             $pagination_ = $postSEttings->pagination($allSEttings['total_page'], $currentPage);
//             while ($query->have_posts()) {
//                 $query->the_post();
//                 $post_id_ = get_the_ID();
//                 $postHtml .= '<div class="elemento-post-layout-iteme">';
//                 $postHtml .= $postSEttings->postContentHtml($post_id_, $allSEttings['options']);
//                 $postHtml .= '</div>';
//             }
//             // wp_send_json_success($items);
//             $sendData = ['success' => true, 'posthtml' => $postHtml, 'pagination' => $pagination_, 'settings' => $allSEttings];
//             wp_send_json_success($sendData);
//         } else {
//             $sendData = ['error' => true];
//             wp_send_json_error($sendData);
//         }
//     }
// }

add_action( 'wp_ajax_elemento_simple_post', 'elemento_simple_post_call' );
add_action( 'wp_ajax_nopriv_elemento_simple_post', 'elemento_simple_post_call' );

function elemento_simple_post_call() {

	/*
	 * This AJAX action is intentionally available to both logged-in
	 * and non-logged-in users because it only retrieves publicly
	 * available posts and does not perform any privileged action.
	 */
	check_ajax_referer(
		'elemento_quick_view',
		'nonce'
	);

	if (
		! isset( $_POST['post_data'] ) ||
		! is_array( $_POST['post_data'] )
	) {
		wp_send_json_error(
			array(
				'message' => 'Invalid request data.',
			),
			400
		);
	}

	$all_settings = wp_unslash( $_POST['post_data'] );

	/*
	 * Current page.
	 */
	$current_page = isset( $all_settings['current_page'] )
		? absint( $all_settings['current_page'] )
		: 1;

	if ( $current_page < 1 ) {
		$current_page = 1;
	}

	/*
	 * Posts per page.
	 */
	$num_of_post = isset( $all_settings['post_per_page'] )
		? absint( $all_settings['post_per_page'] )
		: absint( get_option( 'posts_per_page' ) );

	if ( $num_of_post < 1 ) {
		$num_of_post = 10;
	}

	/*
	 * Limit request size.
	 */
	$num_of_post = min( $num_of_post, 100 );

	/*
	 * Category.
	 */
	$category = isset( $all_settings['category'] )
		? $all_settings['category']
		: array( 'all' );

	if ( ! is_array( $category ) ) {
		$category = array( $category );
	}

	$category = array_filter(
		array_map(
			'sanitize_title',
			$category
		)
	);

	if ( empty( $category ) ) {
		$category = array( 'all' );
	}

	/*
	 * Post order.
	 */
	$post_show_by = isset( $all_settings['post_show_by'] )
		? sanitize_key( $all_settings['post_show_by'] )
		: 'recent';

	$allowed_orderby = array(
		'recent',
		'date',
		'title',
		'modified',
		'rand',
		'comment_count',
	);

	if ( ! in_array( $post_show_by, $allowed_orderby, true ) ) {
		$post_show_by = 'recent';
	}

	/*
	 * Build query.
	 */
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $num_of_post,
		'paged'               => $current_page,
		'ignore_sticky_posts' => true,
	);

	if ( 'recent' === $post_show_by ) {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	} else {
		$args['orderby'] = $post_show_by;
	}

	/*
	 * Category filter.
	 */
	if (
		! empty( $category ) &&
		! in_array( 'all', $category, true )
	) {
		$args['category_name'] = implode( ',', $category );
	}

	$query = new WP_Query( $args );

	/*
	 * Get actual page count from the query.
	 */
	$total_page = absint( $query->max_num_pages );

	/*
	 * Prepare settings object for next AJAX request.
	 */
	$all_settings['current_page']  = $current_page;
	$all_settings['total_page']    = $total_page;
	$all_settings['post_per_page'] = $num_of_post;
	$all_settings['category']      = $category;
	$all_settings['post_show_by']  = $post_show_by;

	$post_settings = new elemento_post_simple();
	$post_html     = '';

	if ( $query->have_posts() ) {

		$pagination = $post_settings->pagination(
			$total_page,
			$current_page
		);

		while ( $query->have_posts() ) {

			$query->the_post();

			$post_id = get_the_ID();

			$post_html .= '<div class="elemento-post-layout-iteme">';

			$options = isset( $all_settings['options'] )
				? $all_settings['options']
				: array();

			$post_html .= $post_settings->postContentHtml(
				$post_id,
				$options
			);

			$post_html .= '</div>';
		}

		wp_reset_postdata();

		wp_send_json_success(
			array(
				'posthtml'   => $post_html,
				'pagination' => $pagination,
				'settings'   => $all_settings,
			)
		);
	}

	wp_reset_postdata();

	/*
	 * Page request is valid but no posts were found.
	 */
	wp_send_json_error(
		array(
			'message' => 'No posts found.',
		)
	);
}