<?php
/**
 * Support Made in Africa (Like & Share) template part.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = ! empty( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
if ( ! $post_id ) {
	return;
}

$box_title = ! empty( $args['title'] ) ? $args['title'] : __( 'Support Made in Africa', 'generatepress-mansa-child' );
$box_desc  = ! empty( $args['desc'] ) ? $args['desc'] : __( 'Help more people discover African-made products. Like and share this product to spread the word', 'generatepress-mansa-child' );

if ( function_exists( 'mansa_enqueue_like_share_scripts' ) ) {
	mansa_enqueue_like_share_scripts();
}

$likes      = function_exists( 'mansa_get_product_likes' ) ? mansa_get_product_likes( $post_id ) : 0;
$post_title = get_the_title( $post_id );
$post_url   = get_permalink( $post_id );
?>
<section class="mansa-product-section mansa-product-support-section" aria-label="<?php echo esc_attr( $box_title ); ?>">
	<div class="mansa-support-box">
		<h3 class="mansa-support-box__title"><?php echo esc_html( $box_title ); ?></h3>
		<p class="mansa-support-box__desc">
			<?php echo esc_html( $box_desc ); ?>
		</p>
		<div class="mansa-support-box__actions">
			<button type="button"
				class="mansa-support-btn mansa-support-btn--like"
				data-product-id="<?php echo esc_attr( $post_id ); ?>"
				aria-label="<?php esc_attr_e( 'Like this', 'generatepress-mansa-child' ); ?>">
				<svg class="mansa-support-icon mansa-support-icon--heart" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
				</svg>
				<span class="mansa-support-btn__text"><?php esc_html_e( 'Like', 'generatepress-mansa-child' ); ?></span>
				<span class="mansa-support-btn__count <?php echo $likes > 0 ? 'has-count' : ''; ?>"><?php echo $likes > 0 ? esc_html( $likes ) : ''; ?></span>
			</button>

			<button type="button"
				class="mansa-support-btn mansa-support-btn--share"
				data-title="<?php echo esc_attr( $post_title ); ?>"
				data-url="<?php echo esc_url( $post_url ); ?>"
				aria-label="<?php esc_attr_e( 'Share this', 'generatepress-mansa-child' ); ?>">
				<svg class="mansa-support-icon mansa-support-icon--share" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="7" y1="17" x2="17" y2="7"></line>
					<polyline points="7 7 17 7 17 17"></polyline>
				</svg>
				<span class="mansa-support-btn__text"><?php esc_html_e( 'Share', 'generatepress-mansa-child' ); ?></span>
			</button>
		</div>
	</div>
</section>
