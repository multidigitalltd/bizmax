<?php
/**
 * Front-end weight trimming for pages the theme fully controls.
 *
 * The home template is rendered entirely by the theme, so plugin assets that sites load on every
 * page (WooCommerce, JetEngine, booking, payment, smooth-scroll, Elementor-form helpers) do
 * nothing there except cost bandwidth and main-thread time. They are removed on that template
 * only; every other page (Elementor, WooCommerce, DeSchool) keeps whatever its plugins load.
 *
 * WooCommerce order attribution (sourcebuster + order-attribution) is kept on purpose: it records
 * where a visitor came from for later orders.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset source fragments that are unused on the home template.
 *
 * @return string[]
 */
function bizmax_home_unused_asset_patterns(): array {
	$patterns = array(
		'/plugins/woocommerce/assets/css/',
		'/plugins/woocommerce/assets/js/frontend/add-to-cart.',
		'/plugins/woocommerce/assets/js/frontend/woocommerce.',
		'/plugins/woocommerce/assets/js/frontend/cart-fragments.',
		'/plugins/woocommerce/assets/js/jquery-blockui/',
		'/plugins/woocommerce/assets/js/js-cookie/',
		'/plugins/jet-engine/assets/css/frontend',
		'/plugins/jet-appointments-booking/assets/css/public/',
		'/plugins/woocommerce-icredit',
		'/plugins/mousewheel-smooth-scroll/',
		'/uploads/wpmss/',
		'/plugins/simple-cloudflare-turnstile/js/integrations/elementor-forms',
	);

	/**
	 * Filter the asset URL fragments removed on the home template.
	 *
	 * Return an empty array to disable the trimming entirely.
	 *
	 * @param string[] $patterns URL fragments.
	 */
	return (array) apply_filters( 'bizmax_home_unused_assets', $patterns );
}

/**
 * Dequeue unused plugin styles and scripts on the home template.
 */
function bizmax_home_asset_diet(): void {
	if ( ! bizmax_is_home_template() ) {
		return;
	}
	$patterns = bizmax_home_unused_asset_patterns();
	if ( ! $patterns ) {
		return;
	}

	foreach ( array(
		'style'  => wp_styles(),
		'script' => wp_scripts(),
	) as $type => $deps ) {
		foreach ( (array) $deps->queue as $handle ) {
			$src = isset( $deps->registered[ $handle ] ) ? rawurldecode( (string) $deps->registered[ $handle ]->src ) : '';
			if ( '' === $src ) {
				continue;
			}
			foreach ( $patterns as $pattern ) {
				if ( str_contains( $src, $pattern ) ) {
					'style' === $type ? wp_dequeue_style( $handle ) : wp_dequeue_script( $handle );
					break;
				}
			}
		}
	}

	// WooCommerce's inline-only style handle (no src) belongs with its stylesheets.
	wp_dequeue_style( 'woocommerce-inline' );

	// Nothing the theme loads needs jQuery. WordPress prints it anyway if a remaining script depends on it.
	wp_dequeue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'bizmax_home_asset_diet', 9999 );
// Some plugins enqueue late (while rendering); catch those before the footer prints.
add_action( 'wp_print_footer_scripts', 'bizmax_home_asset_diet', 1 );

/**
 * Preconnect to Cloudflare Turnstile on the page with the contact form, so the widget appears sooner.
 *
 * @param array<int,string|array<string,string>> $urls          URLs.
 * @param string                                 $relation_type Hint type.
 * @return array<int,string|array<string,string>>
 */
function bizmax_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type && bizmax_is_home_template() && function_exists( 'bizmax_turnstile_enabled' ) && bizmax_turnstile_enabled() ) {
		$urls[] = array(
			'href'        => 'https://challenges.cloudflare.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'bizmax_resource_hints', 10, 2 );

/**
 * Remove head clutter that no visitor needs (RSD, Windows Live Writer, generator, shortlink).
 */
function bizmax_clean_head(): void {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'after_setup_theme', 'bizmax_clean_head' );
