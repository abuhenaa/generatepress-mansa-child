<?php
/**
 * GeneratePress Mansa Child functions and definitions.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load helpers.
require_once __DIR__ . '/inc/filters.php';
require_once __DIR__ . '/inc/queries.php';

/**
 * Enqueue theme styles.
 */
function gp_mansa_child_enqueue_assets() {
	$parent_style = 'generatepress-style';

	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(), wp_get_theme( 'GeneratePress' )->get( 'Version' ) );
	wp_enqueue_style( 'generatepress-mansa-child-style', get_stylesheet_uri(), array( $parent_style ), wp_get_theme()->get( 'Version' ) );

	if ( is_singular( 'mansa_product' ) ) {
		wp_enqueue_script(
			'generatepress-mansa-child-single-product',
			get_stylesheet_directory_uri() . '/assets/js/single-product.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'gp_mansa_child_enqueue_assets' );

/**
 * Register theme menus and setup features.
 */
function gp_mansa_child_theme_setup() {
	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'generatepress-mansa-child' ),
			'footer'  => __( 'Footer Navigation', 'generatepress-mansa-child' ),
		)
	);

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'script', 'style' ) );
}
add_action( 'after_setup_theme', 'gp_mansa_child_theme_setup' );

/**
 * Return the current theme root path for template resolution.
 */
function gp_mansa_child_get_template_path( $template_name ) {
	return trailingslashit( get_stylesheet_directory() ) . $template_name;
}

/**
 * Map requested templates for custom post types and taxonomies.
 */
function gp_mansa_child_template_loader( $template ) {
	// Post types registered by Mansa Core.
	$map = array(
		'mansa_product' => 'archive-products.php',
		'mansa_brand'   => 'archive-brands.php',
		'mansa_article' => 'archive-articles.php',
	);

	if ( is_post_type_archive( array_keys( $map ) ) ) {
		$post_type = get_query_var( 'post_type' );
		if ( isset( $map[ $post_type ] ) ) {
			$custom = gp_mansa_child_get_template_path( $map[ $post_type ] );
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}

	// Singe post templates.
	if ( is_singular( array( 'mansa_product', 'mansa_brand', 'mansa_article' ) ) ) {
		$post_type = get_post_type();
		$single_map = array(
			'mansa_product' => 'single-products.php',
			'mansa_brand'   => 'single-brands.php',
			'mansa_article' => 'single-articles.php',
		);

		if ( isset( $single_map[ $post_type ] ) ) {
			$custom = gp_mansa_child_get_template_path( $single_map[ $post_type ] );
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}

	// Taxonomy templates.
	$taxonomy_map = array(
		'mansa_product_category' => 'taxonomy-product-category.php',
		'mansa_origin'           => 'taxonomy-origin.php',
		'mansa_article_topic'    => 'taxonomy-article-topic.php',
	);

	if ( is_tax( array_keys( $taxonomy_map ) ) ) {
		$taxonomy = get_queried_object()->taxonomy;
		if ( isset( $taxonomy_map[ $taxonomy ] ) ) {
			$custom = gp_mansa_child_get_template_path( $taxonomy_map[ $taxonomy ] );
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}

	return $template;
}
add_filter( 'template_include', 'gp_mansa_child_template_loader' );

/**
 * Register meta box for product details.
 */
function gp_mansa_child_register_product_meta_box() {
	add_meta_box(
		'mansa-product-details',
		__( 'Product Details', 'generatepress-mansa-child' ),
		'gp_mansa_child_render_product_meta_box',
		'mansa_product',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'gp_mansa_child_register_product_meta_box' );

/**
 * Render the product meta box.
 *
 * @param \WP_Post $post Post object.
 */
function gp_mansa_child_render_product_meta_box( $post ) {
	wp_nonce_field( 'gp_mansa_child_save_product_meta', 'gp_mansa_child_product_meta_nonce' );

	$gallery = get_post_meta( $post->ID, '_mansa_product_gallery', true );
	$gallery = is_string( $gallery ) ? $gallery : '';

	$supplier_name = get_post_meta( $post->ID, '_mansa_supplier_name', true );
	$supplier_desc = get_post_meta( $post->ID, '_mansa_supplier_description', true );
	$supplier_url  = get_post_meta( $post->ID, '_mansa_supplier_url', true );

	$buy_links = get_post_meta( $post->ID, '_mansa_buy_links', true );
	$buy_links = is_string( $buy_links ) ? json_decode( $buy_links, true ) : ( is_array( $buy_links ) ? $buy_links : array() );

	// Normalize.
	if ( ! is_array( $buy_links ) ) {
		$buy_links = array();
	}
	?>
	<div class="mansa-meta-grid">
		<p><?php esc_html_e( 'Use the gallery picker to build the product image gallery. If left empty, the featured image will be used.', 'generatepress-mansa-child' ); ?></p>
		<div class="mansa-meta-row">
			<button type="button" class="button" id="mansa-product-gallery-button"><?php esc_html_e( 'Select Gallery Images', 'generatepress-mansa-child' ); ?></button>
			<input type="hidden" id="mansa-product-gallery" name="mansa_product_gallery" value="<?php echo esc_attr( $gallery ); ?>" />
		</div>
		<div id="mansa-product-gallery-preview" class="mansa-meta-gallery">
			<?php
			if ( $gallery ) {
				$ids = array_filter( array_map( 'absint', explode( ',', $gallery ) ) );
				foreach ( $ids as $id ) {
					$src = wp_get_attachment_image_url( $id, 'thumbnail' );
					if ( $src ) {
						printf( '<img src="%s" data-id="%d" />', esc_url( $src ), esc_attr( $id ) );
					}
				}
			}
			?>
		</div>

		<h4><?php esc_html_e( 'Supplier info', 'generatepress-mansa-child' ); ?></h4>
		<div class="mansa-meta-row">
			<label for="mansa_supplier_name"><?php esc_html_e( 'Name', 'generatepress-mansa-child' ); ?></label>
			<input type="text" id="mansa_supplier_name" name="mansa_supplier_name" value="<?php echo esc_attr( $supplier_name ); ?>" class="widefat" />
		</div>
		<div class="mansa-meta-row">
			<label for="mansa_supplier_description"><?php esc_html_e( 'Description', 'generatepress-mansa-child' ); ?></label>
			<textarea id="mansa_supplier_description" name="mansa_supplier_description" class="widefat" rows="3"><?php echo esc_textarea( $supplier_desc ); ?></textarea>
		</div>
		<div class="mansa-meta-row">
			<label for="mansa_supplier_url"><?php esc_html_e( 'Website URL', 'generatepress-mansa-child' ); ?></label>
			<input type="url" id="mansa_supplier_url" name="mansa_supplier_url" value="<?php echo esc_attr( $supplier_url ); ?>" class="widefat" />
		</div>

		<h4><?php esc_html_e( 'Buy links', 'generatepress-mansa-child' ); ?></h4>
		<div id="mansa-buy-links" class="mansa-meta-buy-links">
			<?php
			if ( ! empty( $buy_links ) ) {
				foreach ( $buy_links as $index => $link ) {
					printf(
						'<div class="mansa-buy-link-row"><input type="text" name="mansa_buy_links[%1$d][label]" value="%2$s" placeholder="%3$s" /> <input type="url" name="mansa_buy_links[%1$d][url]" value="%4$s" placeholder="%5$s" /> <button type="button" class="button mansa-buy-link-remove">&times;</button></div>',
						absint( $index ),
						esc_attr( $link['label'] ?? '' ),
						esc_attr__( 'Label', 'generatepress-mansa-child' ),
						esc_attr( $link['url'] ?? '' ),
						esc_attr__( 'URL', 'generatepress-mansa-child' )
					);
				}
			}
			?>
		</div>
		<button type="button" class="button" id="mansa-add-buy-link"><?php esc_html_e( 'Add link', 'generatepress-mansa-child' ); ?></button>
	</div>
	<?php
}

/**
 * Save product meta box values.
 *
 * @param int $post_id Post ID.
 */
function gp_mansa_child_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['gp_mansa_child_product_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['gp_mansa_child_product_meta_nonce'] ), 'gp_mansa_child_save_product_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$gallery = isset( $_POST['mansa_product_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['mansa_product_gallery'] ) ) : '';
	update_post_meta( $post_id, '_mansa_product_gallery', $gallery );

	update_post_meta( $post_id, '_mansa_supplier_name', sanitize_text_field( wp_unslash( $_POST['mansa_supplier_name'] ?? '' ) ) );
	update_post_meta( $post_id, '_mansa_supplier_description', sanitize_textarea_field( wp_unslash( $_POST['mansa_supplier_description'] ?? '' ) ) );
	update_post_meta( $post_id, '_mansa_supplier_url', esc_url_raw( wp_unslash( $_POST['mansa_supplier_url'] ?? '' ) ) );

	$buy_links = array();
	if ( isset( $_POST['mansa_buy_links'] ) && is_array( $_POST['mansa_buy_links'] ) ) {
		foreach ( $_POST['mansa_buy_links'] as $item ) {
			$label = sanitize_text_field( wp_unslash( $item['label'] ?? '' ) );
			$url   = esc_url_raw( wp_unslash( $item['url'] ?? '' ) );
			if ( $label || $url ) {
				$buy_links[] = array(
					'label' => $label,
					'url'   => $url,
				);
			}
		}
	}
	update_post_meta( $post_id, '_mansa_buy_links', wp_json_encode( $buy_links ) );
}
add_action( 'save_post', 'gp_mansa_child_save_product_meta' );

/**
 * Enqueue admin scripts for product meta box.
 */
function gp_mansa_child_admin_scripts( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'mansa_product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'gp-mansa-child-admin',
		get_stylesheet_directory_uri() . '/assets/js/admin-product-meta.js',
		array( 'jquery' ),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script( 'gp-mansa-child-admin', 'mansaChildI18n', array(
		'selectImages' => __( 'Select gallery images', 'generatepress-mansa-child' ),
		'useSelected'  => __( 'Use selected images', 'generatepress-mansa-child' ),
		'label'       => __( 'Label', 'generatepress-mansa-child' ),
		'url'         => __( 'URL', 'generatepress-mansa-child' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'gp_mansa_child_admin_scripts' );
