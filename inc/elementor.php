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
 * Elementor's "Full Width" template calls get_header()/get_footer(), which are ours.
 * Elementor Canvas intentionally renders without them; nothing to do.
 */
