<?php
/**
 * Brand archive template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

$paged = gp_mansa_child_get_paged();
$query_args = gp_mansa_child_get_brand_query_args( $paged );
$brands = new WP_Query( $query_args );

$search_value = gp_mansa_child_get_query_var( 's' );
$category_value = gp_mansa_child_get_query_var( 'product-category' );
?>
<main>
	<header class="archive-header">
		<h1><?php esc_html_e( 'Brands', 'generatepress-mansa-child' ); ?></h1>
		<p><?php esc_html_e( 'Discover brands building products that matter.', 'generatepress-mansa-child' ); ?></p>
	</header>

	<section class="filters" aria-label="Brand filters">
		<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'mansa_brand' ) ); ?>">
			<div class="filters__row">
				<div class="filters__field">
					<label for="filter-search"><?php esc_html_e( 'Search brands', 'generatepress-mansa-child' ); ?></label>
					<input id="filter-search" type="search" name="s" value="<?php echo esc_attr( $search_value ); ?>" placeholder="<?php esc_attr_e( 'Search brands…', 'generatepress-mansa-child' ); ?>" />
				</div>
				<div class="filters__field">
					<label for="filter-category"><?php esc_html_e( 'Category', 'generatepress-mansa-child' ); ?></label>
					<select id="filter-category" name="product-category">
						<option value=""><?php esc_html_e( 'All categories', 'generatepress-mansa-child' ); ?></option>
						<?php
						$categories = get_terms(
							array(
								'taxonomy'   => 'mansa_product_category',
								'hide_empty' => true,
							)
						);

						if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
							foreach ( $categories as $category ) {
								printf(
									'<option value="%s" %s>%s</option>',
									esc_attr( $category->slug ),
									selected( $category_value, $category->slug, false ),
									esc_html( $category->name )
								);
							}
						}
						?>
					</select>
				</div>
				<div class="filters__actions">
					<button type="submit" class="button"><?php esc_html_e( 'Apply filters', 'generatepress-mansa-child' ); ?></button>
					<?php if ( $search_value || $category_value ) : ?>
						<a class="button button--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'mansa_brand' ) ); ?>"><?php esc_html_e( 'Clear filters', 'generatepress-mansa-child' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</form>
	</section>

	<?php if ( $brands->have_posts() ) : ?>
		<div class="grid">
			<?php
			while ( $brands->have_posts() ) :
				$brands->the_post();
				get_template_part( 'template-parts/card', 'brand' );
			endwhile;
			?>
		</div>

		<?php
echo gp_mansa_child_pagination( $brands );
		wp_reset_postdata();
		?>

	<?php else : ?>
		<p><?php esc_html_e( 'No brands found matching your criteria.', 'generatepress-mansa-child' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer();
