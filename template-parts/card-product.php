<?php
/**
 * Product card template.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product_id = get_the_ID();
$categories = get_the_terms( $product_id, 'mansa_product_category' );
$origin_terms = get_the_terms( $product_id, 'mansa_origin' );
$category_label = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : ''; 
$origin_label = $origin_terms && ! is_wp_error( $origin_terms ) ? $origin_terms[0]->name : '';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
	<a class="card__image" href="<?php the_permalink(); ?>">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) );
		} 
		?>
	</a>
	<div class="card__content">
		<?php if ( $category_label || $origin_label ) : ?>
			<div class="card__meta">
				<?php
				if ( $category_label ) {
					echo esc_html( $category_label );
				}
				if ( $origin_label ) {
					if ( $category_label ) {
						echo ' &bull; ';
					}
					echo esc_html( $origin_label );
				}
				?>
			</div>
		<?php endif; ?>

		<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="card__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></div>
		<a class="button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View product', 'generatepress-mansa-child' ); ?></a>
	</div>
</article>
