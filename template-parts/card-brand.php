<?php
/**
 * Brand card template.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_id = get_the_ID();
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
		<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="card__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></div>
		<a class="button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View brand', 'generatepress-mansa-child' ); ?></a>
	</div>
</article>
