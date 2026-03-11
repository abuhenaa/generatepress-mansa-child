<?php
/**
 * Filters and template helpers for GeneratePress Mansa Child.
 *
 * @package GeneratePress_Mansa_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the primary site header.
 */
function gp_mansa_child_render_header() {
	?>
	<header class="header" role="banner">
		<div class="header__inner">
			<a class="header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			</a>
			<nav class="header__nav" aria-label="Primary">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => '',
						'items_wrap'     => '%3$s',
						'menu_class'     => '',
						'fallback_cb'    => 'gp_mansa_child_default_primary_menu',
					)
				);
				?>
			</nav>
		</div>
	</header>
	<?php
}

/**
 * Default fallback menu for primary navigation.
 */
function gp_mansa_child_default_primary_menu() {
	$items = array(
		array(
			'title' => __( 'Products', 'generatepress-mansa-child' ),
			'url'   => esc_url( get_post_type_archive_link( 'mansa_product' ) ),
		),
		array(
			'title' => __( 'Brands', 'generatepress-mansa-child' ),
			'url'   => esc_url( get_post_type_archive_link( 'mansa_brand' ) ),
		),
		array(
			'title' => __( 'Guides', 'generatepress-mansa-child' ),
			'url'   => esc_url( get_post_type_archive_link( 'mansa_article' ) ),
		),
		array(
			'title' => __( 'About', 'generatepress-mansa-child' ),
			'url'   => esc_url( home_url( '/about/' ) ),
		),
	);

	foreach ( $items as $item ) {
		echo '<a class="header__nav-link" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a>';
	}
}