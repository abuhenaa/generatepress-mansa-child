<?php
/**
 * Product archive template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

$paged = gp_mansa_child_get_paged();
$query_args = gp_mansa_child_get_product_query_args( $paged );
$products = new WP_Query( $query_args );

$search_value = gp_mansa_child_get_query_var( 's' );
$category_value = gp_mansa_child_get_query_var( 'product-category' );
$origin_value = gp_mansa_child_get_query_var( 'origin' );
?>
<main>
	<header class="archive-header">
		<h1><?php esc_html_e( 'Products', 'generatepress-mansa-child' ); ?></h1>
		<p><?php esc_html_e( 'Find curated products from emerging brands around the world.', 'generatepress-mansa-child' ); ?></p>
	</header>

	<section class="filters" aria-label="Product filters">
		<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'mansa_product' ) ); ?>">
			<div class="filters__row">
				<div class="filters__field">
					<label for="filter-search"><?php esc_html_e( 'Search products', 'generatepress-mansa-child' ); ?></label>
					<input id="filter-search" type="search" name="s" value="<?php echo esc_attr( $search_value ); ?>" placeholder="<?php esc_attr_e( 'Search products…', 'generatepress-mansa-child' ); ?>" />
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
				<div class="filters__field">
					<label for="filter-origin"><?php esc_html_e( 'Origin', 'generatepress-mansa-child' ); ?></label>
					<select id="filter-origin" name="origin">
						<option value=""><?php esc_html_e( 'All origins', 'generatepress-mansa-child' ); ?></option>
						<?php
						$origins = get_terms(
							array(
								'taxonomy'   => 'mansa_origin',
								'hide_empty' => true,
							)
						);

						if ( ! empty( $origins ) && ! is_wp_error( $origins ) ) {
							foreach ( $origins as $origin ) {
								printf(
									'<option value="%s" %s>%s</option>',
									esc_attr( $origin->slug ),
									selected( $origin_value, $origin->slug, false ),
									esc_html( $origin->name )
								);
							}
						}
						?>
					</select>
				</div>
				<div class="filters__actions">
					<button type="submit" class="button"><?php esc_html_e( 'Apply filters', 'generatepress-mansa-child' ); ?></button>
					<?php if ( $search_value || $category_value || $origin_value ) : ?>
						<a class="button button--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'mansa_product' ) ); ?>"><?php esc_html_e( 'Clear filters', 'generatepress-mansa-child' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</form>
	</section>

	<?php if ( $products->have_posts() ) : ?>
		<div class="grid">
			<?php
			while ( $products->have_posts() ) :
				$products->the_post();
				get_template_part( 'template-parts/card', 'product' );
			endwhile;
			?>
		</div>

		<?php
echo gp_mansa_child_pagination( $products );
		wp_reset_postdata();
		?>

	<?php else : ?>
		<p><?php esc_html_e( 'No products found matching your criteria.', 'generatepress-mansa-child' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer();
