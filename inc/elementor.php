<?php
/**
 * Elementor compatibility: keep the theme header/footer everywhere, expose Ploni.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register only content locations. Because the theme registers locations,
 * Elementor Pro's Theme Builder cannot swap header.php / footer.php.
 *
 * @param object $manager Elementor theme manager.
 */
function bizmax_elementor_locations( $manager ): void {
	$manager->register_location( 'single' );
	$manager->register_location( 'archive' );
}
add_action( 'elementor/theme/register_locations', 'bizmax_elementor_locations' );

/**
 * Add a "Bizmax" font group and the Ploni family to Elementor's typography controls.
 *
 * @param array<string,string> $groups Groups.
 * @return array<string,string>
 */
function bizmax_elementor_font_group( array $groups ): array {
	return array( 'bizmax' => __( 'ביזמקס', 'bizmax' ) ) + $groups;
}
add_filter( 'elementor/fonts/groups', 'bizmax_elementor_font_group' );

/**
 * Ploni is self-hosted by the theme, so Elementor should not try to load it.
 *
 * @param array<string,string> $fonts Fonts.
 * @return array<string,string>
 */
function bizmax_elementor_fonts( array $fonts ): array {
	$fonts['Ploni'] = 'bizmax';
	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'bizmax_elementor_fonts' );

/**
 * Whether the current view is set to the Elementor Canvas template.
 *
 * Elementor renders Canvas pages with its own blank template, so the theme header
 * and footer never load there. This check also covers cases where a Canvas page is
 * still routed through the theme (Elementor deactivated, another plugin overriding
 * template_include), so header.php / footer.php can stay out of the way.
 * Filter `bizmax_hide_header_footer` to extend it (e.g. to more templates).
 */
function bizmax_is_canvas(): bool {
	static $canvas = null;
	if ( null === $canvas ) {
		$id     = is_singular() ? (int) get_queried_object_id() : 0;
		$canvas = (bool) apply_filters( 'bizmax_hide_header_footer', $id && 'elementor_canvas' === get_page_template_slug( $id ), $id );
	}
	return $canvas;
}
