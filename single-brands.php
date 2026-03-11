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

	?>
	<main>
		<section class="archive-header">
			<h1><?php the_title(); ?></h1>
			<p class="card__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
		</section>

		<div class="grid">
			<div>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="card__image">
						<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
					</div>
				<?php endif; ?>

				<section>
					<h2><?php esc_html_e( 'About the brand', 'generatepress-mansa-child' ); ?></h2>
					<div class="card__excerpt"><?php the_content(); ?></div>
				</section>

				<section>
					<h2><?php esc_html_e( 'Contact', 'generatepress-mansa-child' ); ?></h2>
					<ul>
						<?php if ( $brand_website ) : ?>
							<li><strong><?php esc_html_e( 'Website', 'generatepress-mansa-child' ); ?>:</strong> <a href="<?php echo esc_url( $brand_website ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $brand_website ); ?></a></li>
						<?php endif; ?>
						<?php if ( $brand_social ) : ?>
							<li><strong><?php esc_html_e( 'Social', 'generatepress-mansa-child' ); ?>:</strong> <?php echo esc_html( $brand_social ); ?></li>
						<?php endif; ?>
					</ul>
				</section>
			</div>

			<div>
				<section>
					<h2><?php esc_html_e( 'Products from this brand', 'generatepress-mansa-child' ); ?></h2>
					<?php
					$products_query = new WP_Query(
						array(
							'post_type'      => 'mansa_product',
							'posts_per_page' => 6,
							'meta_query'     => array(
								array(
									'key'   => '_mansa_brand_id',
									'value' => $brand_id,
								),
							),
						)
					);

					if ( $products_query->have_posts() ) :
						?>
						<div class="grid">
							<?php
							while ( $products_query->have_posts() ) :
								$products_query->the_post();
								get_template_part( 'template-parts/card', 'product' );
							endwhile;
							?>
						</div>
						<?php
						wp_reset_postdata();
					else :
						?><p><?php esc_html_e( 'No products found for this brand yet.', 'generatepress-mansa-child' ); ?></p><?php
					endif;
					?>
				</section>

				<section>
					<h2><?php esc_html_e( 'Similar brands', 'generatepress-mansa-child' ); ?></h2>
					<p><?php esc_html_e( 'Check out other brands in the same category and niche.', 'generatepress-mansa-child' ); ?></p>
					<?php
					$similar_brands = new WP_Query(
						array(
							'post_type'      => 'mansa_brand',
							'posts_per_page' => 6,
							'post__not_in'   => array( $brand_id ),
							'orderby'        => 'date',
							'order'          => 'DESC',
						)
					);

					if ( $similar_brands->have_posts() ) :
						?>
						<div class="grid">
							<?php
							while ( $similar_brands->have_posts() ) :
								$similar_brands->the_post();
								get_template_part( 'template-parts/card', 'brand' );
							endwhile;
							?>
						</div>
						<?php
						wp_reset_postdata();
					else :
						?><p><?php esc_html_e( 'No similar brands found yet.', 'generatepress-mansa-child' ); ?></p><?php
					endif;
					?>
				</section>

				<section>
					<h2><?php esc_html_e( 'Related articles', 'generatepress-mansa-child' ); ?></h2>
					<?php
					$related_posts = array();
					if ( class_exists( 'Mansa\\Relationships\\ArticleRelations' ) ) {
						$relations = new Mansa\Relationships\ArticleRelations();
						$related_posts = $relations->query_articles_by_brands( array( $brand_id ), array( 'posts_per_page' => 6 ) );
					}

					if ( $related_posts instanceof WP_Query && $related_posts->have_posts() ) :
						?>
						<div class="grid">
							<?php
							while ( $related_posts->have_posts() ) :
								$related_posts->the_post();
								get_template_part( 'template-parts/card', 'article' );
							endwhile;
							?>
						</div>
						<?php
						wp_reset_postdata();
					else :
						?><p><?php esc_html_e( 'No related articles yet.', 'generatepress-mansa-child' ); ?></p><?php
					endif;
					?>
				</section>
			</div>
		</div>
	</main>

	<?php
endwhile;

get_footer();
