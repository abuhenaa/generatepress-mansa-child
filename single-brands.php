<?php
/**
 * Single brand template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$brand_id      = get_the_ID();
	$brand_website = get_post_meta( $brand_id, '_mansa_brand_website', true );
	$brand_social  = get_post_meta( $brand_id, '_mansa_brand_social', true );
	$founder_story = get_post_meta( $brand_id, '_mansa_brand_founder_story', true );
	$hero_content  = get_post_meta( $brand_id, '_mansa_brand_hero_content', true );
	if ( empty( $hero_content ) ) {
		$hero_content = get_post_meta( $brand_id, '_mansa_hero_content', true );
	}
	if ( empty( $hero_content ) && has_excerpt() ) {
		$hero_content = get_the_excerpt();
	}

	?>
	<main class="mansa-brand-page">
		<section class="mansa-brand-hero">
			<div class="mansa-brand-header">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="mansa-brand-image">
						<?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => esc_attr( get_the_title() ) ) ); ?>
					</div>
				<?php endif; ?>

				<div class="mansa-brand-header__info">
					<h1 class="mansa-brand-title"><?php the_title(); ?></h1>
					<?php if ( ! empty( $hero_content ) ) : ?>
						<div class="mansa-brand-hero-content">
							<p><?php echo nl2br( esc_html( $hero_content ) ); ?></p>
						</div>
					<?php endif; ?>

					<?php if ( $brand_website ) : ?>
						<div class="mansa-brand-contact">
							<a href="<?php echo esc_url( $brand_website ); ?>" class="button button--primary" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Visit Website', 'generatepress-mansa-child' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<?php if ( $founder_story ) : ?>
			<section class="mansa-brand-section" aria-labelledby="mansa-brand-founder-story">
				<h2 id="mansa-brand-founder-story" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_brand_founder_story_title', __( 'Founder Story', 'generatepress-mansa-child' ) ) ); ?></h2>
				<div class="mansa-brand-story">
					<?php echo wp_kses_post( $founder_story ); ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $brand_website || $brand_social ) : ?>
			<section class="mansa-brand-section" aria-labelledby="mansa-brand-contact-info">
				<h2 id="mansa-brand-contact-info" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_brand_contact_title', __( 'Connect With Us', 'generatepress-mansa-child' ) ) ); ?></h2>
				<div class="mansa-brand-socials">
					<?php if ( $brand_website ) : ?>
						<div class="mansa-brand-social__item">
							<strong><?php esc_html_e( 'Website', 'generatepress-mansa-child' ); ?></strong>
							<a href="<?php echo esc_url( $brand_website ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( wp_parse_url( $brand_website, PHP_URL_HOST ) ?: $brand_website ); ?>
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $brand_social ) : ?>
						<div class="mansa-brand-social__item">
							<strong><?php esc_html_e( 'Social Media', 'generatepress-mansa-child' ); ?></strong>
							<ul class="mansa-brand-social-links">
								<?php
								$social_links = array_filter( array_map( 'trim', explode( "\n", $brand_social ) ) );
								foreach ( $social_links as $link ) :
									?>
									<li><a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( wp_parse_url( $link, PHP_URL_HOST ) ?: $link ); ?></a></li>
									<?php
								endforeach;
								?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<section class="mansa-brand-section mansa-brand-products-section" aria-labelledby="mansa-brand-products">
			<h2 id="mansa-brand-products" class="section__title"><?php printf( esc_html__( 'Explore %s Product', 'generatepress-mansa-child' ), get_the_title() ); ?></h2>
			<?php
			$brand_products_query = new WP_Query(
				array(
					'post_type'      => 'mansa_product',
					'posts_per_page' => 12,
					'post_status'    => 'publish',
					'meta_query'     => array(
						array(
							'key'   => '_mansa_brand_id',
							'value' => $brand_id,
						),
					),
				)
			);

			// Fallback: If no products mapped directly to this brand yet, load published products so carousel displays
			if ( ! $brand_products_query->have_posts() ) {
				$brand_products_query = new WP_Query(
					array(
						'post_type'      => 'mansa_product',
						'posts_per_page' => 6,
						'post_status'    => 'publish',
					)
				);
			}

			if ( $brand_products_query->have_posts() ) :
				?>
				<div class="mansa-slick mansa-brand-products-slider">
					<?php
					while ( $brand_products_query->have_posts() ) :
						$brand_products_query->the_post();
						?>
						<div class="mansa-slick__slide">
							<a class="mansa-brand-product-image-card" href="<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( get_the_title() ); ?>">
								<div class="mansa-brand-product-thumb">
									<?php
									if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) );
									} else {
										echo '<div class="mansa-placeholder-thumb"><span>' . esc_html( get_the_title() ) . '</span></div>';
									}
									?>
								</div>
								<div class="mansa-brand-product-info">
									<h3 class="mansa-brand-product-title"><?php the_title(); ?></h3>
									<span class="mansa-brand-product-cta"><?php esc_html_e( 'View Product', 'generatepress-mansa-child' ); ?> &rarr;</span>
								</div>
							</a>
						</div>
					<?php endwhile; ?>
				</div>
				<?php
				wp_reset_postdata();
			else :
				?>
				<p class="mansa-brand-empty"><?php esc_html_e( 'No products found for this brand yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<?php
		$testimonial_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $brand_id, '_mansa_brand_testimonials', true ) ) ) );

		// Fallback 1: If no testimonials directly uploaded to brand, pull from the brand's products
		if ( empty( $testimonial_ids ) ) {
			$brand_products_for_t = get_posts(
				array(
					'post_type'      => 'mansa_product',
					'posts_per_page' => 10,
					'meta_query'     => array(
						array(
							'key'   => '_mansa_brand_id',
							'value' => $brand_id,
						),
					),
					'fields'         => 'ids',
				)
			);
			foreach ( $brand_products_for_t as $bp_id ) {
				$prod_t = get_post_meta( $bp_id, '_mansa_product_testimonials', true );
				if ( ! empty( $prod_t ) ) {
					$prod_t_ids      = array_filter( array_map( 'absint', explode( ',', $prod_t ) ) );
					$testimonial_ids = array_merge( $testimonial_ids, $prod_t_ids );
				}
			}
			$testimonial_ids = array_unique( $testimonial_ids );
		}

		// Fallback 2: If still empty, pull from any product testimonials in the site
		if ( empty( $testimonial_ids ) ) {
			$any_prods = get_posts(
				array(
					'post_type'      => 'mansa_product',
					'posts_per_page' => 5,
					'meta_query'     => array(
						array(
							'key'     => '_mansa_product_testimonials',
							'compare' => 'EXISTS',
						),
					),
					'fields'         => 'ids',
				)
			);
			foreach ( $any_prods as $ap_id ) {
				$prod_t = get_post_meta( $ap_id, '_mansa_product_testimonials', true );
				if ( ! empty( $prod_t ) ) {
					$prod_t_ids      = array_filter( array_map( 'absint', explode( ',', $prod_t ) ) );
					$testimonial_ids = array_merge( $testimonial_ids, $prod_t_ids );
				}
			}
			$testimonial_ids = array_unique( $testimonial_ids );
		}

		$testimonial_images = array();
		foreach ( $testimonial_ids as $attachment_id ) {
			$src = wp_get_attachment_image_url( $attachment_id, 'large' );
			if ( $src ) {
				$testimonial_images[] = array(
					'url' => $src,
					'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $brand_id ),
				);
			}
		}
		?>

		<section class="mansa-brand-section mansa-product-testimonials-section" aria-labelledby="mansa-brand-testimonials">
			<h2 id="mansa-brand-testimonials" class="section__title"><?php echo esc_html( class_exists( 'Mansa\\Admin\\Settings' ) ? \Mansa\Admin\Settings::get_setting( 'mansa_product_testimonials_title', __( 'What People Are Saying', 'generatepress-mansa-child' ) ) : __( 'What People Are Saying', 'generatepress-mansa-child' ) ); ?></h2>
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
				<?php elseif ( count( $testimonial_images ) === 1 ) : ?>
					<div class="mansa-product-testimonial__single">
						<div class="mansa-product-testimonial__card">
							<img src="<?php echo esc_url( $testimonial_images[0]['url'] ); ?>" alt="<?php echo esc_attr( $testimonial_images[0]['alt'] ); ?>" loading="lazy" />
						</div>
					</div>
				<?php else : ?>
					<div class="mansa-product-testimonial__single">
						<div class="mansa-product-testimonial__card mansa-product-testimonial__card--placeholder">
							<p style="padding: 2rem; color: #888; text-align: center;"><?php esc_html_e( 'Testimonials coming soon.', 'generatepress-mansa-child' ); ?></p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<?php get_template_part( 'template-parts/product-support-box' ); ?>

		<?php mansa_render_ad_placement( 1 ); ?>
		<!-- Content moved here -->
		<section class="mansa-brand-section" aria-labelledby="mansa-brand-about">
			<h2 id="mansa-brand-about" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_brand_about_title', __( 'About the Brand', 'generatepress-mansa-child' ) ) ); ?></h2>
			<div class="mansa-brand-description">
				<?php the_content(); ?>
			</div>
		</section>

		<section class="mansa-brand-section" aria-labelledby="mansa-brand-related-articles">
			<h2 id="mansa-brand-related-articles" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_brand_related_articles_title', __( 'Related Articles', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php
			$related_posts = array();
			if ( class_exists( 'Mansa\\Relationships\\ArticleRelations' ) ) {
				$relations = new Mansa\Relationships\ArticleRelations();
				$related_posts = $relations->query_articles_by_brands( array( $brand_id ), array( 'posts_per_page' => 6 ) );
			}

			if ( $related_posts instanceof WP_Query && $related_posts->have_posts() ) :
				gp_mansa_child_render_slick( $related_posts, 'article' );
			else :
				if ( $related_posts instanceof WP_Query ) {
					wp_reset_postdata();
				}
				?>
				<p class="mansa-brand-empty"><?php esc_html_e( 'No related articles yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<?php mansa_render_ad_placement( 2 ); ?>
	</main>

	<?php
endwhile;

get_footer();

