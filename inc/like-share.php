<?php
/**
 * Like and Share functionality for Mansa Products, Articles, Brands & Custom Pages.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get post like count.
 *
 * @param int $post_id Post ID.
 * @return int Number of likes.
 */
function mansa_get_product_likes( $post_id ) {
	$count = get_post_meta( $post_id, '_mansa_likes_count', true );
	return max( 0, (int) $count );
}

/**
 * Handle AJAX request to toggle post like.
 */
function mansa_ajax_toggle_product_like() {
	check_ajax_referer( 'mansa_like_nonce', 'nonce' );

	$post_id     = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$action_type = isset( $_POST['like_action'] ) ? sanitize_key( $_POST['like_action'] ) : 'like';

	if ( ! $post_id || ! get_post_status( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid post.', 'generatepress-mansa-child' ) ) );
	}

	$current_likes = mansa_get_product_likes( $post_id );

	if ( 'unlike' === $action_type ) {
		$new_likes = max( 0, $current_likes - 1 );
		$liked     = false;
	} else {
		$new_likes = $current_likes + 1;
		$liked     = true;
	}

	update_post_meta( $post_id, '_mansa_likes_count', $new_likes );

	wp_send_json_success(
		array(
			'product_id' => $post_id,
			'likes'      => $new_likes,
			'liked'      => $liked,
		)
	);
}
add_action( 'wp_ajax_mansa_toggle_like', 'mansa_ajax_toggle_product_like' );
add_action( 'wp_ajax_nopriv_mansa_toggle_like', 'mansa_ajax_toggle_product_like' );

/**
 * Shortcode to render the Support Made in Africa (Like & Share) box.
 *
 * Usage:
 * [mansa_support_box]
 *
 * With custom text or specific post:
 * [mansa_support_box title="Support African Creators" desc="Share this story" post_id="123"]
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function mansa_support_box_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'title'       => __( 'Support Made in Africa', 'generatepress-mansa-child' ),
			'desc'        => __( 'Help more people discover African-made products. Like and share this product to spread the word', 'generatepress-mansa-child' ),
			'description' => '',
			'post_id'     => get_the_ID(),
			'id'          => '',
		),
		$atts,
		'mansa_support_box'
	);

	if ( ! empty( $atts['description'] ) ) {
		$atts['desc'] = $atts['description'];
	}
	if ( ! empty( $atts['id'] ) ) {
		$atts['post_id'] = $atts['id'];
	}

	// Ensure the script and localized data are enqueued when shortcode is used
	if ( ! wp_script_is( 'generatepress-mansa-child-like-share', 'enqueued' ) ) {
		wp_enqueue_script(
			'generatepress-mansa-child-like-share',
			get_stylesheet_directory_uri() . '/assets/js/like-share.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_localize_script(
			'generatepress-mansa-child-like-share',
			'mansa_like_share',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'mansa_like_nonce' ),
				'copied_text' => __( 'Link copied to clipboard!', 'generatepress-mansa-child' ),
			)
		);
	}

	ob_start();
	get_template_part(
		'template-parts/product-support-box',
		null,
		array(
			'post_id' => $atts['post_id'],
			'title'   => $atts['title'],
			'desc'    => $atts['desc'],
		)
	);
	return ob_get_clean();
}
add_shortcode( 'mansa_support_box', 'mansa_support_box_shortcode' );
