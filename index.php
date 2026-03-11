<?php
/**
 * The main template file.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();
?>
<main>
	<section class="archive-header">
		<h1><?php esc_html_e( 'Latest', 'generatepress-mansa-child' ); ?></h1>
		<p><?php esc_html_e( 'Discover new products, brands, and stories.', 'generatepress-mansa-child' ); ?></p>
	</section>

	<div class="grid">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/card', 'product' );
			endwhile;
		else :
			_e( 'No content found.', 'generatepress-mansa-child' );
		endif;
		?>
	</div>

	<?php
	if ( function_exists( 'gp_mansa_child_pagination' ) ) {
		echo gp_mansa_child_pagination();
	}
	?>
</main>

<?php
get_footer();
