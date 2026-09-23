<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and navigation menus.
 */
function bizmax_setup(): void {
	load_theme_textdomain( 'bizmax', BIZMAX_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'woocommerce' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 190,
			'width'       => 190,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary'  => __( 'תפריט ראשי (הידר)', 'bizmax' ),
			'drawer'   => __( 'תפריט המבורגר (מגירה)', 'bizmax' ),
			'footer_1' => __( 'פוטר – עמודה 1', 'bizmax' ),
			'footer_2' => __( 'פוטר – עמודה 2', 'bizmax' ),
		)
	);

	// Image sizes match the 2x rendering size of each slot in the home design.
	add_image_size( 'bizmax-feature', 1200, 800, true );
	add_image_size( 'bizmax-card', 640, 420, true );
	add_image_size( 'bizmax-tall', 900, 1000, true );
	add_image_size( 'bizmax-round', 320, 320, true );
	add_image_size( 'bizmax-avatar', 120, 120, true );
}
add_action( 'after_setup_theme', 'bizmax_setup' );

/**
 * Content width for embeds.
 */
function bizmax_content_width(): void {
	$GLOBALS['content_width'] = 1152; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}
add_action( 'after_setup_theme', 'bizmax_content_width', 0 );

/**
 * Expose the custom image sizes in the media chooser.
 *
 * @param array<string,string> $sizes Size names.
 * @return array<string,string>
 */
function bizmax_image_size_names( array $sizes ): array {
	return array_merge(
		$sizes,
		array(
			'bizmax-feature' => __( 'ביזמקס – תמונת מקטע', 'bizmax' ),
			'bizmax-card'    => __( 'ביזמקס – כרטיס', 'bizmax' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'bizmax_image_size_names' );

/**
 * Body classes used by the stylesheets.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function bizmax_body_class( array $classes ): array {
	if ( bizmax_is_home_template() ) {
		$classes[] = 'bz-home';
	}
	return $classes;
}
add_filter( 'body_class', 'bizmax_body_class' );

/**
 * Whether the current request renders the theme home template.
 */
function bizmax_is_home_template(): bool {
	return is_page_template( 'template-home.php' );
}

/**
 * Remove emoji scripts (not used; saves a request and inline script).
 */
function bizmax_disable_emoji(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'bizmax_disable_emoji' );

/**
 * Mark the current menu item for assistive tech.
 *
 * @param array<string,string> $atts Link attributes.
 * @param WP_Post              $item Menu item.
 * @return array<string,string>
 */
function bizmax_menu_aria_current( array $atts, WP_Post $item ): array {
	if ( ! empty( $item->current ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'bizmax_menu_aria_current', 10, 2 );
