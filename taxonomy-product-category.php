<?php
/**
 * Product category taxonomy template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

$term = get_queried_object();
?>
<main>
	<header class="archive-header">
		<h1><?php echo esc_html( $term->name ); ?></h1>
		<?php if ( ! empty( $term->description ) ) : ?>
			<p><?php echo wp_kses_post( wpautop( $term->description ) ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="grid">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( 'mansa_product' === get_post_type() ) {
					get_template_part( 'template-parts/card', 'product' );
				} elseif ( 'mansa_brand' === get_post_type() ) {
					get_template_part( 'template-parts/card', 'brand' );
				}
			endwhile;
			?>
		</div>
		<?php
		if ( function_exists( 'gp_mansa_child_pagination' ) ) {
			echo gp_mansa_child_pagination();
		}
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'No items found in this category.', 'generatepress-mansa-child' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer();
