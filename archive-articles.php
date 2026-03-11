<?php
/**
 * Article archive template.
 *
 * @package GeneratePress_Mansa_Child
 */

get_header();

$paged = gp_mansa_child_get_paged();
$query_args = gp_mansa_child_get_article_query_args( $paged );
$articles = new WP_Query( $query_args );

$search_value = gp_mansa_child_get_query_var( 's' );
$topic_value = gp_mansa_child_get_query_var( 'article-topic' );
?>
<main>
	<header class="archive-header">
		<h1><?php esc_html_e( 'Guides', 'generatepress-mansa-child' ); ?></h1>
		<p><?php esc_html_e( 'In-depth insights and stories to help you explore new products and brands.', 'generatepress-mansa-child' ); ?></p>
	</header>

	<section class="filters" aria-label="Article filters">
		<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'mansa_article' ) ); ?>">
			<div class="filters__row">
				<div class="filters__field">
					<label for="filter-search"><?php esc_html_e( 'Search guides', 'generatepress-mansa-child' ); ?></label>
					<input id="filter-search" type="search" name="s" value="<?php echo esc_attr( $search_value ); ?>" placeholder="<?php esc_attr_e( 'Search guides…', 'generatepress-mansa-child' ); ?>" />
				</div>
				<div class="filters__field">
					<label for="filter-topic"><?php esc_html_e( 'Topic', 'generatepress-mansa-child' ); ?></label>
					<select id="filter-topic" name="article-topic">
						<option value=""><?php esc_html_e( 'All topics', 'generatepress-mansa-child' ); ?></option>
						<?php
						$topics = get_terms(
							array(
								'taxonomy'   => 'mansa_article_topic',
								'hide_empty' => true,
							)
						);

						if ( ! empty( $topics ) && ! is_wp_error( $topics ) ) {
							foreach ( $topics as $topic ) {
								printf(
									'<option value="%s" %s>%s</option>',
									esc_attr( $topic->slug ),
									selected( $topic_value, $topic->slug, false ),
									esc_html( $topic->name )
								);
							}
						}
						?>
					</select>
				</div>
				<div class="filters__actions">
					<button type="submit" class="button"><?php esc_html_e( 'Apply filters', 'generatepress-mansa-child' ); ?></button>
					<?php if ( $search_value || $topic_value ) : ?>
						<a class="button button--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'mansa_article' ) ); ?>"><?php esc_html_e( 'Clear filters', 'generatepress-mansa-child' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</form>
	</section>

	<?php if ( $articles->have_posts() ) : ?>
		<div class="grid">
			<?php
			while ( $articles->have_posts() ) :
				$articles->the_post();
				get_template_part( 'template-parts/card', 'article' );
			endwhile;
			?>
		</div>

		<?php
echo gp_mansa_child_pagination( $articles );
		wp_reset_postdata();
		?>

	<?php else : ?>
		<p><?php esc_html_e( 'No guides found matching your criteria.', 'generatepress-mansa-child' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer();
