<?php
/**
 * Single article template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$article_id = get_the_ID();
	$topics = get_the_terms( $article_id, 'mansa_article_topic' );
	$topic_label = $topics && ! is_wp_error( $topics ) ? $topics[0]->name : '';

	$related_products = array();
	$related_brands = array();

	if ( class_exists( 'Mansa\\Relationships\\ArticleRelations' ) ) {
		$relations = new Mansa\Relationships\ArticleRelations();
		$related_products = $relations->get_related_products( $article_id );
		$related_brands   = $relations->get_related_brands( $article_id );
	}
	?>
	<main>
		<section class="archive-header">
			<h1><?php the_title(); ?></h1>
			<?php if ( $topic_label ) : ?>
				<p class="card__meta"><?php echo esc_html( $topic_label ); ?></p>
			<?php endif; ?>
			<p class="card__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
		</section>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="card__image">
				<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
			</div>
		<?php endif; ?>

		<section>
			<div class="card__excerpt"><?php the_content(); ?></div>
		</section>

		<section aria-labelledby="mansa-article-related-products">
			<h2 id="mansa-article-related-products"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_article_related_products_title', __( 'Related Products', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php if ( ! empty( $related_products ) ) : ?>
				<?php
				$products_query = new WP_Query(
					array(
						'post_type'      => 'mansa_product',
						'posts_per_page' => 6,
						'post__in'       => $related_products,
					)
				);
				?>
				<?php if ( $products_query->have_posts() ) : ?>
					<div class="grid">
						<?php
						while ( $products_query->have_posts() ) :
							$products_query->the_post();
							get_template_part( 'template-parts/card', 'product' );
						endwhile;
						?>
					</div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No related products found.', 'generatepress-mansa-child' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No related products yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section aria-labelledby="mansa-article-related-brands">
			<h2 id="mansa-article-related-brands"><?php echo esc_html( \Mansa\Admin\Settings::get_setting( 'mansa_article_related_brands_title', __( 'Related Brands', 'generatepress-mansa-child' ) ) ); ?></h2>
			<?php if ( ! empty( $related_brands ) ) : ?>
				<?php
				$brands_query = new WP_Query(
					array(
						'post_type'      => 'mansa_brand',
						'posts_per_page' => 6,
						'post__in'       => $related_brands,
					)
				);
				?>
				<?php if ( $brands_query->have_posts() ) : ?>
					<div class="grid">
						<?php
						while ( $brands_query->have_posts() ) :
							$brands_query->the_post();
							get_template_part( 'template-parts/card', 'brand' );
						endwhile;
						?>
					</div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No related brands found.', 'generatepress-mansa-child' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No related brands yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section>
			<h2><?php esc_html_e( 'Related articles', 'generatepress-mansa-child' ); ?></h2>
			<?php
			$related_articles = new WP_Query(
				array(
					'post_type'      => 'mansa_article',
					'posts_per_page' => 6,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'post__not_in'   => array( $article_id ),
				)
			);
			?>
			<?php if ( $related_articles->have_posts() ) : ?>
				<div class="grid">
					<?php
					while ( $related_articles->have_posts() ) :
						$related_articles->the_post();
						get_template_part( 'template-parts/card', 'article' );
					endwhile;
					?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No additional articles to show.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

	</main>

	<?php
endwhile;

get_footer();
