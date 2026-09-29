<?php
/**
 * Ad Placements helper functions.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render an Ad Placement spot.
 *
 * Output is completely suppressed if the placement has no content.
 *
 * @param int      $placement_number Ad spot number (1 or 2).
 * @param int|null $post_id          Optional post ID. Defaults to current post.
 * @return void
 */
function mansa_render_ad_placement( $placement_number = 1, $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$meta_key = '_mansa_ad_placement_' . absint( $placement_number );
	$ad_code  = get_post_meta( $post_id, $meta_key, true );

	if ( empty( $ad_code ) || ! trim( $ad_code ) ) {
		return;
	}

	?>
	<div class="mansa-ad-placement mansa-ad-placement--<?php echo esc_attr( $placement_number ); ?>" role="complementary" aria-label="<?php echo esc_attr( sprintf( __( 'Advertisement %d', 'generatepress-mansa-child' ), $placement_number ) ); ?>">
		<div class="mansa-ad-placement__inner">
			<?php echo do_shortcode( $ad_code ); ?>
		</div>
	</div>
	<?php
}
