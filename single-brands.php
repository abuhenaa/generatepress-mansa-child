<?php
/**
 * Single brand template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$brand_id = get_the_ID();
	$brand_website = get_post_meta( $brand_id, '_mansa_brand_website', true );
	$brand_social = get_post_meta( $brand_id, '_mansa_brand_social', true );
	$founder_story = get_post_meta( $brand_id, '_mansa_brand_founder_story', true );

	?>
	<main class="mansa-brand-page">
		<section class="mansa-brand-hero">
			<div class="mansa-brand-header">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="mansa-brand-image">
						<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
					</div>
				<?php endif; ?>

				<div class="mansa-brand-header__info">
					<h1 class="mansa-brand-title"><?php the_title(); ?></h1>
					<p class="mansa-brand-subtitle"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>

					<div class="mansa-brand-contact">
						<?php if ( $brand_website ) : ?>
							<a href="<?php echo esc_url( $brand_website ); ?>" class="button button--primary" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Visit Website', 'generatepress-mansa-child' ); ?>
							</a>
						<?php endif; ?>
					</div>
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

		<section class="mansa-brand-section" aria-labelledby="mansa-brand-about">
			<h2 id="mansa-brand-about" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_brand_about_title', __( 'About the Brand', 'generatepress-mansa-child' ) ) ); ?></h2>
			<div class="mansa-brand-description">
				<?php the_content(); ?>
			</div>
		</section>

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

		<section class="mansa-brand-section" aria-labelledby="mansa-brand-products">
			<h2 id="mansa-brand-products" class="section__title"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_brand_products_title', __( 'Products from this Brand', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php
			$products_query = new WP_Query(
				array(
					'post_type'      => 'mansa_product',
					'posts_per_page' => 12,
					'meta_query'     => array(
						array(
							'key'   => '_mansa_brand_id',
							'value' => $brand_id,
						),
					),
				)
			);

			if ( $products_query->have_posts() ) :
				gp_mansa_child_render_slick( $products_query, 'product' );
			else :
				wp_reset_postdata();
				?>
				<p class="mansa-brand-empty"><?php esc_html_e( 'No products found for this brand yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="mansa-brand-section" aria-labelledby="mansa-brand-related-brands">
			<h2 id="mansa-brand-related-brands" class="section__title"><?php echo esc_html( class_exists( 'Mansa\\Admin\\Settings' ) ? \Mansa\Admin\Settings::get_setting( 'mansa_brand_related_brands_title', __( 'Related Brands', 'generatepress-mansa-child' ) ) : __( 'Related Brands', 'generatepress-mansa-child' ) ); ?></h2>
			<?php
			$brand_cats = get_the_terms( $brand_id, 'mansa_product_category' );
			$related_brands_args = array(
				'post_type'      => 'mansa_brand',
				'posts_per_page' => 6,
				'post__not_in'   => array( $brand_id ),
				'post_status'    => 'publish',
			);
			if ( ! empty( $brand_cats ) && ! is_wp_error( $brand_cats ) ) {
				$related_brands_args['tax_query'] = array(
					array(
						'taxonomy' => 'mansa_product_category',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $brand_cats, 'term_id' ),
					),
				);
			}
			$related_brands_query = new WP_Query( $related_brands_args );
			if ( $related_brands_query->have_posts() ) :
				gp_mansa_child_render_slick( $related_brands_query, 'brand' );
			else :
				wp_reset_postdata();
				?>
				<p class="mansa-brand-empty"><?php esc_html_e( 'No related brands yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
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
	</main>

	<?php
endwhile;

get_footer();

