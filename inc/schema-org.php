<?php
/**
 * Organization JSON-LD on the front page (skipped when an SEO plugin handles schema).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print Organization schema with the Customizer contact details.
 */
function bizmax_schema_org(): void {
	if ( ! is_front_page() || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}

	$data = array(
		'@context'  => 'https://schema.org',
		'@type'     => 'Organization',
		'name'      => get_bloginfo( 'name' ),
		'url'       => home_url( '/' ),
		'telephone' => (string) bizmax_mod( 'bizmax_phone' ),
		'email'     => (string) bizmax_mod( 'bizmax_email' ),
		'address'   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => (string) bizmax_mod( 'bizmax_address' ),
			'addressLocality' => 'ירושלים',
			'addressCountry'  => 'IL',
		),
	);

	if ( has_custom_logo() ) {
		$logo = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
		if ( $logo ) {
			$data['logo'] = $logo;
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'bizmax_schema_org', 20 );
