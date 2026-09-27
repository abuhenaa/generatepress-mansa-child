<?php
/**
 * Single article template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$article_id   = get_the_ID();
	$topics       = get_the_terms( $article_id, 'mansa_article_topic' );
	$topics       = ( $topics && ! is_wp_error( $topics ) ) ? $topics : array();
	$hero_content = get_post_meta( $article_id, '_mansa_article_hero_content', true );
	if ( empty( $hero_content ) ) {
		$hero_content = get_post_meta( $article_id, '_mansa_hero_content', true );
	}

	$related_products = array();
	$related_brands   = array();

	if ( class_exists( 'Mansa\\Relationships\\ArticleRelations' ) ) {
		$relations        = new Mansa\Relationships\ArticleRelations();
		$related_products = array_filter( $relations->get_related_products( $article_id ) );
		$related_brands   = array_filter( $relations->get_related_brands( $article_id ) );
	}

	$products_title = __( 'Related Products', 'generatepress-mansa-child' );
	$brands_title   = __( 'Related Brands', 'generatepress-mansa-child' );
	if ( class_exists( 'Mansa\\Admin\\Settings' ) ) {
		$products_title = \Mansa\Admin\Settings::get_setting( 'mansa_article_related_products_title', $products_title );
		$brands_title   = \Mansa\Admin\Settings::get_setting( 'mansa_article_related_brands_title', $brands_title );
	}
	?>
	<main class="mansa-article-page">
		<article <?php post_class( 'mansa-article' ); ?>>
			<header class="mansa-article-hero">
				<?php if ( ! empty( $topics ) ) : ?>
					<p class="mansa-article-topics">
						<?php foreach ( $topics as $topic ) : ?>
							<?php $topic_link = get_term_link( $topic ); ?>
							<?php if ( ! is_wp_error( $topic_link ) ) : ?>
								<a class="mansa-article-topic" href="<?php echo esc_url( $topic_link ); ?>"><?php echo esc_html( $topic->name ); ?></a>
							<?php else : ?>
								<span class="mansa-article-topic"><?php echo esc_html( $topic->name ); ?></span>
							<?php endif; ?>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>

				<h1 class="mansa-article-title"><?php the_title(); ?></h1>

				<?php if ( ! empty( $hero_content ) ) : ?>
					<div class="mansa-article-hero-content">
						<p><?php echo nl2br( esc_html( $hero_content ) ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="mansa-article-image">
						<?php
						the_post_thumbnail(
							'large',
							array(
								'loading'       => 'eager',
								'fetchpriority' => 'high',
								'alt'           => esc_attr( get_the_title() ),
							)
						);
						?>
					</figure>
				<?php endif; ?>
			</header>

			<section class="mansa-article-section" aria-labelledby="mansa-article-body">
				<h2 id="mansa-article-body" class="screen-reader-text"><?php esc_html_e( 'Article', 'generatepress-mansa-child' ); ?></h2>
				<div class="mansa-article-content">
					<?php the_content(); ?>
				</div>
			</section>
		</article>

		<section class="mansa-article-section" aria-labelledby="mansa-article-related-products">
			<h2 id="mansa-article-related-products" class="section__title"><?php echo esc_html( $products_title ); ?></h2>
			<?php if ( ! empty( $related_products ) ) : ?>
				<?php
				$products_query = new WP_Query(
					array(
						'post_type'      => 'mansa_product',
						'posts_per_page' => 6,
						'post__in'       => $related_products,
						'orderby'        => 'post__in',
						'post_status'    => 'publish',
					)
				);
				?>
				<?php if ( $products_query->have_posts() ) : ?>
					<?php gp_mansa_child_render_slick( $products_query, 'product' ); ?>
				<?php else : ?>
					<?php wp_reset_postdata(); ?>
					<p class="mansa-article-empty"><?php esc_html_e( 'No related products found.', 'generatepress-mansa-child' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<p class="mansa-article-empty"><?php esc_html_e( 'No related products yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="mansa-article-section" aria-labelledby="mansa-article-related-brands">
			<h2 id="mansa-article-related-brands" class="section__title"><?php echo esc_html( $brands_title ); ?></h2>
			<?php if ( ! empty( $related_brands ) ) : ?>
				<?php
				$brands_query = new WP_Query(
					array(
						'post_type'      => 'mansa_brand',
						'posts_per_page' => 6,
						'post__in'       => $related_brands,
						'orderby'        => 'post__in',
						'post_status'    => 'publish',
					)
				);
				?>
				<?php if ( $brands_query->have_posts() ) : ?>
					<?php gp_mansa_child_render_slick( $brands_query, 'brand' ); ?>
				<?php else : ?>
					<?php wp_reset_postdata(); ?>
					<p class="mansa-article-empty"><?php esc_html_e( 'No related brands found.', 'generatepress-mansa-child' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<p class="mansa-article-empty"><?php esc_html_e( 'No related brands yet.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="mansa-article-section" aria-labelledby="mansa-article-related-articles">
			<h2 id="mansa-article-related-articles" class="section__title"><?php esc_html_e( 'Related articles', 'generatepress-mansa-child' ); ?></h2>
			<?php
			$related_articles_args = array(
				'post_type'      => 'mansa_article',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post__not_in'   => array( $article_id ),
				'post_status'    => 'publish',
			);

			if ( ! empty( $topics ) ) {
				$related_articles_args['tax_query'] = array(
					array(
						'taxonomy' => 'mansa_article_topic',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $topics, 'term_id' ),
					),
				);
			}

			$related_articles = new WP_Query( $related_articles_args );
			?>
			<?php if ( $related_articles->have_posts() ) : ?>
				<?php gp_mansa_child_render_slick( $related_articles, 'article' ); ?>
			<?php else : ?>
				<?php wp_reset_postdata(); ?>
				<p class="mansa-article-empty"><?php esc_html_e( 'No additional articles to show.', 'generatepress-mansa-child' ); ?></p>
			<?php endif; ?>
		</section>
	</main>
	<?php
endwhile;

get_footer();
