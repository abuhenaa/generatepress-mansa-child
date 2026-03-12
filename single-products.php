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
	$brand_id = get_post_meta( $product_id, '_mansa_brand_id', true );
	$brand = $brand_id ? get_post( absint( $brand_id ) ) : null;
	$brand_name = $brand ? get_the_title( $brand ) : '';
	$brand_link = $brand ? get_permalink( $brand ) : '';

	$categories = get_the_terms( $product_id, 'mansa_product_category' );
	$origin_terms = get_the_terms( $product_id, 'mansa_origin' );

	$category_label = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : '';
	$category_link = $categories && ! is_wp_error( $categories ) ? get_term_link( $categories[0] ) : '';
	$origin_label = $origin_terms && ! is_wp_error( $origin_terms ) ? $origin_terms[0]->name : '';
	// Gallery images (meta box stored as comma-separated attachment IDs)
	$gallery_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $product_id, '_mansa_product_gallery', true ) ) ) );
	if ( empty( $gallery_ids ) && has_post_thumbnail( $product_id ) ) {
		$gallery_ids[] = get_post_thumbnail_id( $product_id );
	}

	$gallery_images = array();
	foreach ( $gallery_ids as $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		$src = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( $src ) {
			$gallery_images[] = array(
				'url' => $src,
				'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $product_id ),
			);
		}
	}

	$buy_links = get_post_meta( $product_id, '_mansa_buy_links', true );
	if ( is_string( $buy_links ) ) {
		$buy_links = json_decode( $buy_links, true );
	}
	if ( ! is_array( $buy_links ) ) {
		$default_link = get_post_meta( $product_id, '_mansa_where_to_buy_link', true );
		$buy_links = $default_link ? array( array( 'label' => __( 'Buy Now', 'generatepress-mansa-child' ), 'url' => $default_link ) ) : array();
	}

	// Supplier placeholder meta.
	$supplier_name = get_post_meta( $product_id, '_mansa_supplier_name', true );
	$supplier_desc = get_post_meta( $product_id, '_mansa_supplier_description', true );
	$supplier_url  = get_post_meta( $product_id, '_mansa_supplier_url', true );

	$benefits_raw = get_post_meta( $product_id, '_mansa_benefits', true );
	$benefits = array();
	if ( is_array( $benefits_raw ) ) {
		$benefits = $benefits_raw;
	} elseif ( is_string( $benefits_raw ) && $benefits_raw ) {
		$benefits = array_filter( array_map( 'trim', explode( "\n", $benefits_raw ) ) );
	}

	?>

	<main class="mansa-product-page">
		<section class="mansa-product-hero">
			<div class="mansa-product-gallery">
				<div class="mansa-product-gallery__main">
					<?php if( ! empty( $gallery_ids ) ) : ?>
						<img src="<?php echo esc_url( $gallery_images[0]['url'] ?? '' ); ?>" alt="<?php echo esc_attr( $gallery_images[0]['alt'] ?? get_the_title() ); ?>" loading="lazy" />
				<?php endif; ?>
				</div>
				<?php if ( count( $gallery_images ) > 1 ) : ?>
					<div class="mansa-product-gallery__thumbs">
						<?php foreach ( $gallery_images as $index => $image ) : ?>
							<img
								class="mansa-product-gallery__thumb<?php echo 0 === $index ? ' active' : ''; ?>"
								data-full="<?php echo esc_url( $image['url'] ); ?>"
								data-alt="<?php echo esc_attr( $image['alt'] ); ?>"
								src="<?php echo esc_url( $image['url'] ); ?>"
								alt="<?php echo esc_attr( $image['alt'] ); ?>" />
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mansa-product-hero__info">
				<?php if ( $brand_name && $brand_link ) : ?>
					<a class="mansa-product-hero__brand" href="<?php echo esc_url( $brand_link ); ?>"><?php echo esc_html( $brand_name ); ?></a>
				<?php elseif ( $brand_name ) : ?>
					<span class="mansa-product-hero__brand"><?php echo esc_html( $brand_name ); ?></span>
				<?php endif; ?>

				<h1 class="mansa-product-hero__title"><?php the_title(); ?></h1>
				<p class="mansa-product-hero__subtitle"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 28, '…' ) ); ?></p>

				<div class="mansa-product-hero__meta">
					<?php if ( $category_label ) : ?>
					<?php if ( $category_link && ! is_wp_error( $category_link ) ) : ?>
						<a class="mansa-product-hero__meta-item" href="<?php echo esc_url( $category_link ); ?>"><?php echo esc_html( $category_label ); ?></a>
					<?php else : ?>
						<span class="mansa-product-hero__meta-item"><?php echo esc_html( $category_label ); ?></span>
					<?php endif; ?>
						<span class="mansa-product-hero__meta-item"><?php echo esc_html( $origin_label ); ?></span>
					<?php endif; ?>
				</div>

				<div class="mansa-product-hero__actions">
					<?php if ( ! empty( $buy_links ) ) : ?>
						<?php foreach ( $buy_links as $link ) : ?>
							<?php if ( empty( $link['url'] ) ) {
								continue;
							} ?>
							<a class="button button--primary" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $link['label'] ?? __( 'Buy Now', 'generatepress-mansa-child' ) ); ?>
							</a>
						<?php endforeach; ?>
					<?php else : ?>
						<a class="button button--outline" href="#">
							<?php esc_html_e( 'Notify me when available', 'generatepress-mansa-child' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-overview">
			<h2 id="mansa-product-overview" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_overview_title', __( 'Product Overview', 'generatepress-mansa-child' ) ) ); ?></h2>
			<div class="mansa-product-overview">
				<?php if ( $origin_label ) : ?>
					<div class="mansa-product-overview__item">
						<div class="mansa-product-overview__label"><?php esc_html_e( 'Origin', 'generatepress-mansa-child' ); ?></div>
						<div class="mansa-product-overview__value"><?php echo esc_html( $origin_label ); ?></div>
					</div>
				<?php endif; ?>
				<?php if ( $category_label ) : ?>
					<div class="mansa-product-overview__item">
						<div class="mansa-product-overview__label"><?php esc_html_e( 'Category', 'generatepress-mansa-child' ); ?></div>
						<div class="mansa-product-overview__value">
						<?php if ( $category_link && ! is_wp_error( $category_link ) ) : ?>
							<a href="<?php echo esc_url( $category_link ); ?>"><?php echo esc_html( $category_label ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $category_label ); ?>
						<?php endif; ?>
					</div>
					</div>
				<?php endif; ?>
				<?php if ( $brand_name ) : ?>
					<div class="mansa-product-overview__item">
						<div class="mansa-product-overview__label"><?php esc_html_e( 'Brand', 'generatepress-mansa-child' ); ?></div>
						<div class="mansa-product-overview__value">
							<?php if ( $brand_link ) : ?>
								<a href="<?php echo esc_url( $brand_link ); ?>"><?php echo esc_html( $brand_name ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $brand_name ); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-about">
			<h2 id="mansa-product-about" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_about_title', __( 'About this product', 'generatepress-mansa-child' ) ) ); ?></h2>
			<div class="mansa-product-longdesc"><?php the_content(); ?></div>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-benefits">
			<h2 id="mansa-product-benefits" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_benefits_title', __( 'Why people buy this', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php if ( ! empty( $benefits ) ) : ?>
				<ul class="mansa-product-benefits">
					<?php foreach ( $benefits as $benefit ) : ?>
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
							<span><?php echo esc_html( $benefit ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="mansa-product-longdesc"><?php esc_html_e( 'Benefits and key features will be added soon.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-where-to-buy">
			<h2 id="mansa-product-where-to-buy" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_where_to_buy_title', __( 'Where to Buy', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php if ( ! empty( $buy_links ) ) : ?>
				<div class="mansa-product-buy">
					<?php foreach ( $buy_links as $link ) : ?>
						<?php if ( empty( $link['url'] ) ) {
							continue;
						} ?>
						<a class="mansa-product-buy__link" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="mansa-product-buy__name"><?php echo esc_html( $link['label'] ?? __( 'Buy', 'generatepress-mansa-child' ) ); ?></span>
							<span class="mansa-product-buy__arrow">→</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="mansa-product-longdesc"><?php esc_html_e( 'Purchase links will be available when the product is listed.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-supplier">
			<h2 id="mansa-product-supplier" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_supplier_title', __( 'Supplier', 'generatepress-mansa-child' ) ) ); ?></h2>
			<div class="mansa-supplier">
				<div class="mansa-supplier__logo"></div>
				<div class="mansa-supplier__info">
					<div class="mansa-supplier__name"><?php echo esc_html( $supplier_name ?: __( 'Supplier Name', 'generatepress-mansa-child' ) ); ?></div>
					<p class="mansa-supplier__desc"><?php echo esc_html( $supplier_desc ?: __( 'Supplier details will be added in the product editor.', 'generatepress-mansa-child' ) ); ?></p>
					<?php if ( $supplier_url ) : ?>
						<a href="<?php echo esc_url( $supplier_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View supplier', 'generatepress-mansa-child' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="mansa-product-section" aria-labelledby="mansa-product-similar">
			<h2 id="mansa-product-similar" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_similar_title', __( 'Similar Products', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php
			$similar_args = array(
				'post_type'      => 'mansa_product',
				'posts_per_page' => 6,
				'post__not_in'   => array( $product_id ),
				'orderby'        => 'date',
				'order'          => 'DESC',
			);
			if ( $brand_id ) {
				$similar_args['meta_query'] = array(
					array(
						'key'   => '_mansa_brand_id',
						'value' => $brand_id,
					),
				);
			}
			$similar_query = new WP_Query( $similar_args );
			if ( $similar_query->have_posts() ) :
				?>
				<div class="mansa-related-grid">
					<?php
					while ( $similar_query->have_posts() ) :
						$similar_query->the_post();
						get_template_part( 'template-parts/card', 'product' );
					endwhile;
					?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="mansa-product-longdesc"><?php esc_html_e( 'No similar products found yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>
		<section class="mansa-product-section" aria-labelledby="mansa-product-related-articles">
			<h2 id="mansa-product-related-articles" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_product_related_articles_title', __( 'Related Articles', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php
			$related_posts = array();
			if ( class_exists( 'Mansa\\Relationships\\ArticleRelations' ) ) {
				$relations = new Mansa\Relationships\ArticleRelations();
				$related_posts = $relations->query_articles_by_products( array( $product_id ), array( 'posts_per_page' => 6 ) );
			}
			if ( $related_posts instanceof WP_Query && $related_posts->have_posts() ) :
				?>
				<div class="mansa-related-list">
					<?php
					while ( $related_posts->have_posts() ) :
						$related_posts->the_post();
						$thumb = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
						?>
						<a href="<?php the_permalink(); ?>" class="mansa-related-list__item">
							<img class="mansa-related-list__thumb" src="<?php echo esc_url( $thumb ?: 'https://placehold.co/72x48/f9fafb/9ca3af?text=Article' ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
							<span class="mansa-related-list__title"><?php the_title(); ?></span>
						</a>
					<?php
					endwhile;
					?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="mansa-product-longdesc"><?php esc_html_e( 'No related articles to show.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<!-- <section class="mansa-product-section" aria-labelledby="mansa-product-faq">
			<h2 id="mansa-product-faq" class="section__title"><?php esc_html_e( 'Frequently Asked Questions', 'generatepress-mansa-child' ); ?></h2>
			<div class="mansa-faq">
				<div class="mansa-faq__item open">
					<button class="mansa-faq__question" type="button">
						<span><?php esc_html_e( 'Is this product organic?', 'generatepress-mansa-child' ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
					</button>
					<div class="mansa-faq__answer"><?php esc_html_e( 'Yes, this product has been sourced through verified organic channels.', 'generatepress-mansa-child' ); ?></div>
				</div>
				<div class="mansa-faq__item">
					<button class="mansa-faq__question" type="button">
						<span><?php esc_html_e( 'How should I store it?', 'generatepress-mansa-child' ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
					</button>
					<div class="mansa-faq__answer"><?php esc_html_e( 'Keep in a cool, dry place and use within 60 days of opening.', 'generatepress-mansa-child' ); ?></div>
				</div>
			</div>
		</section> -->
	</main>

<?php
endwhile;

get_footer();
