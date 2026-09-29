<?php
/**
 * Single product template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$product_id = get_the_ID();
	$brand_id   = get_post_meta( $product_id, '_mansa_brand_id', true );
	$brand      = $brand_id ? get_post( absint( $brand_id ) ) : null;
	$brand_name = $brand ? get_the_title( $brand ) : '';
	$brand_link = $brand ? get_permalink( $brand ) : '';

	$categories   = get_the_terms( $product_id, 'mansa_product_category' );
	$origin_terms = get_the_terms( $product_id, 'mansa_origin' );

	$category_label = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : '';
	$category_link  = $categories && ! is_wp_error( $categories ) ? get_term_link( $categories[0] ) : '';
	$origin_label   = $origin_terms && ! is_wp_error( $origin_terms ) ? $origin_terms[0]->name : '';

	// Hero content / subtitle.
	$hero_content = get_post_meta( $product_id, '_mansa_product_hero_content', true );
	if ( empty( $hero_content ) ) {
		$hero_content = get_post_meta( $product_id, '_mansa_hero_content', true );
	}
	if ( empty( $hero_content ) && has_excerpt() ) {
		$hero_content = get_the_excerpt();
	}

	// Gallery images (meta box stored as comma-separated attachment IDs).
	$gallery_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $product_id, '_mansa_product_gallery', true ) ) ) );
	if ( empty( $gallery_ids ) && has_post_thumbnail( $product_id ) ) {
		$gallery_ids[] = get_post_thumbnail_id( $product_id );
	}

	$gallery_images = array();
	foreach ( $gallery_ids as $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		$src           = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( $src ) {
			$gallery_images[] = array(
				'url' => $src,
				'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $product_id ),
			);
		}
	}

	// Testimonials images (meta box stored as comma-separated attachment IDs).
	$testimonial_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $product_id, '_mansa_product_testimonials', true ) ) ) );
	$testimonial_images = array();
	foreach ( $testimonial_ids as $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		$src           = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( $src ) {
			$testimonial_images[] = array(
				'url' => $src,
				'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: sprintf( /* translators: %s: product title */ __( 'Testimonial for %s', 'generatepress-mansa-child' ), get_the_title( $product_id ) ),
			);
		}
	}

	$buy_links = get_post_meta( $product_id, '_mansa_buy_links', true );
	if ( is_string( $buy_links ) ) {
		$buy_links = json_decode( $buy_links, true );
	}
	if ( ! is_array( $buy_links ) ) {
		$default_link = get_post_meta( $product_id, '_mansa_where_to_buy_link', true );
		$buy_links    = $default_link ? array( array( 'label' => __( 'Buy Now', 'generatepress-mansa-child' ), 'url' => $default_link ) ) : array();
	}
	?>

	<main class="mansa-product-page">
		<section class="mansa-product-hero">
			<div class="mansa-product-header">
				<h1 class="mansa-product-title"><?php the_title(); ?></h1>

				<?php if ( $brand_name ) : ?>
					<div class="mansa-product-by">
						<span><?php esc_html_e( 'by', 'generatepress-mansa-child' ); ?></span>
						<?php if ( $brand_link ) : ?>
							<a href="<?php echo esc_url( $brand_link ); ?>" class="mansa-product-by__link"><?php echo esc_html( $brand_name ); ?></a>
						<?php else : ?>
							<span class="mansa-product-by__name"><?php echo esc_html( $brand_name ); ?></span>
						<?php endif; ?>
					</div>
				<?php elseif ( $category_label ) : ?>
					<div class="mansa-product-by">
						<span><?php esc_html_e( 'by', 'generatepress-mansa-child' ); ?></span>
						<?php if ( $category_link && ! is_wp_error( $category_link ) ) : ?>
							<a href="<?php echo esc_url( $category_link ); ?>" class="mansa-product-by__link"><?php echo esc_html( $category_label ); ?></a>
						<?php else : ?>
							<span class="mansa-product-by__name"><?php echo esc_html( $category_label ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $hero_content ) ) : ?>
					<div class="mansa-product-hero-content">
						<p><?php echo nl2br( esc_html( $hero_content ) ); ?></p>
					</div>
				<?php endif; ?>

				<div class="mansa-product-gallery">
					<?php if ( count( $gallery_images ) > 1 ) : ?>
						<div class="mansa-product-gallery__slider">
							<?php foreach ( $gallery_images as $image ) : ?>
								<div class="mansa-product-gallery__slide">
									<div class="mansa-product-gallery__image-wrap">
										<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php elseif ( ! empty( $gallery_images ) ) : ?>
						<div class="mansa-product-gallery__single">
							<div class="mansa-product-gallery__image-wrap">
								<img src="<?php echo esc_url( $gallery_images[0]['url'] ); ?>" alt="<?php echo esc_attr( $gallery_images[0]['alt'] ); ?>" />
							</div>
						</div>
					<?php elseif ( has_post_thumbnail() ) : ?>
						<div class="mansa-product-gallery__single">
							<div class="mansa-product-gallery__image-wrap">
								<?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $buy_links ) ) : ?>
					<div class="mansa-product-actions">
						<?php foreach ( $buy_links as $link ) : ?>
							<?php
							if ( empty( $link['url'] ) ) {
								continue;
							}
							?>
							<a class="button button--primary mansa-product-action-btn" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $link['label'] ?? __( 'Buy Now', 'generatepress-mansa-child' ) ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( get_the_content() ) : ?>
			<section class="mansa-product-section mansa-product-section--content" aria-label="<?php esc_attr_e( 'Product details', 'generatepress-mansa-child' ); ?>">
				<div class="mansa-product-content">
					<?php the_content(); ?>
				</div>
			</section>
		<?php endif; ?>

		<?php mansa_render_ad_placement( 1 ); ?>

		<?php if ( ! empty( $testimonial_images ) ) : ?>
			<section class="mansa-product-section mansa-product-testimonials-section" aria-labelledby="mansa-product-testimonials">
				<h2 id="mansa-product-testimonials" class="section__title"><?php echo esc_html( class_exists( 'Mansa\\Admin\\Settings' ) ? \Mansa\Admin\Settings::get_setting( 'mansa_product_testimonials_title', __( 'What People Are Saying', 'generatepress-mansa-child' ) ) : __( 'What People Are Saying', 'generatepress-mansa-child' ) ); ?></h2>
				<div class="mansa-product-testimonials-slider-wrap">
					<?php if ( count( $testimonial_images ) > 1 ) : ?>
						<div class="mansa-product-testimonials-slider">
							<?php foreach ( $testimonial_images as $image ) : ?>
								<div class="mansa-product-testimonial__slide">
									<div class="mansa-product-testimonial__card">
										<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy" />
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="mansa-product-testimonial__single">
							<div class="mansa-product-testimonial__card">
								<img src="<?php echo esc_url( $testimonial_images[0]['url'] ); ?>" alt="<?php echo esc_attr( $testimonial_images[0]['alt'] ); ?>" loading="lazy" />
							</div>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/product-support-box' ); ?>

		<section class="mansa-product-section" aria-labelledby="mansa-product-related-articles">
			<h2 id="mansa-product-related-articles" class="section__title"><?php echo esc_html( class_exists( 'Mansa\\Admin\\Settings' ) ? \Mansa\Admin\Settings::get_setting( 'mansa_product_related_articles_title', __( 'Related Articles', 'generatepress-mansa-child' ) ) : __( 'Related Articles', 'generatepress-mansa-child' ) ); ?></h2>
			<?php
			$related_posts = array();
			if ( class_exists( 'Mansa\\Relationships\\ArticleRelations' ) ) {
				$relations     = new Mansa\Relationships\ArticleRelations();
				$related_posts = $relations->query_articles_by_products( array( $product_id ), array( 'posts_per_page' => 6 ) );
			}
			if ( $related_posts instanceof WP_Query && $related_posts->have_posts() ) :
				gp_mansa_child_render_slick( $related_posts, 'article' );
			else :
				if ( $related_posts instanceof WP_Query ) {
					wp_reset_postdata();
				}
				?>
				<p class="mansa-product-empty"><?php esc_html_e( 'No related articles to show.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-related-products">
			<h2 id="mansa-product-related-products" class="section__title"><?php echo esc_html( class_exists( 'Mansa\\Admin\\Settings' ) ? \Mansa\Admin\Settings::get_setting( 'mansa_product_related_products_title', __( 'Related Products', 'generatepress-mansa-child' ) ) : __( 'Related Products', 'generatepress-mansa-child' ) ); ?></h2>
			<?php
			$related_products_args = array(
				'post_type'      => 'mansa_product',
				'posts_per_page' => 6,
				'post_status'    => 'publish',
				'post__not_in'   => array( $product_id ),
			);
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$related_products_args['tax_query'] = array(
					array(
						'taxonomy' => 'mansa_product_category',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $categories, 'term_id' ),
					),
				);
			}
			$related_products_query = new WP_Query( $related_products_args );
			if ( $related_products_query->have_posts() ) :
				gp_mansa_child_render_slick( $related_products_query, 'product' );
			else :
				wp_reset_postdata();
				?>
				<p class="mansa-product-empty"><?php esc_html_e( 'No related products found yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<?php mansa_render_ad_placement( 2 ); ?>
	</main>

<?php
endwhile;

get_footer();
