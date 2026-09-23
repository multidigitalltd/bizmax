<?php
/**
 * Front-end assets: conditional, deferred, preloaded fonts.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve an asset URL, preferring the minified build outside SCRIPT_DEBUG.
 *
 * @param string $relative Path relative to assets/, e.g. "css/main.css".
 */
function bizmax_asset( string $relative ): string {
	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$min   = preg_replace( '/\.(css|js)$/', '.min.$1', $relative );

	if ( ! $debug && $min && file_exists( BIZMAX_DIR . '/assets/' . $min ) ) {
		return BIZMAX_URI . '/assets/' . $min;
	}
	return BIZMAX_URI . '/assets/' . $relative;
}

/**
 * Enqueue site-wide and home-only assets.
 */
function bizmax_enqueue_assets(): void {
	wp_enqueue_style( 'bizmax-main', bizmax_asset( 'css/main.css' ), array(), BIZMAX_VERSION );
	wp_enqueue_script( 'bizmax-main', bizmax_asset( 'js/main.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );

	if ( bizmax_is_home_template() ) {
		wp_enqueue_style( 'bizmax-home', bizmax_asset( 'css/home.css' ), array( 'bizmax-main' ), BIZMAX_VERSION );
		wp_enqueue_script( 'bizmax-home', bizmax_asset( 'js/home.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );
		wp_localize_script(
			'bizmax-home',
			'bizmaxHome',
			array(
				'restUrl'  => esc_url_raw( rest_url( 'bizmax/v1/' ) ),
				'messages' => array(
					'sending' => __( 'שולח…', 'bizmax' ),
					'success' => __( 'נשלח! נחזור אליכם בקרוב', 'bizmax' ),
					'error'   => __( 'השליחה נכשלה, נסו שוב בעוד רגע.', 'bizmax' ),
					'quote'   => __( 'המלצה %1$d מתוך %2$d', 'bizmax' ),
				),
			)
		);
	}

	// The theme does not ship block styles on the front end; Elementor pages carry their own CSS.
	if ( ! is_singular() || ! has_blocks() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'bizmax_enqueue_assets' );

/**
 * Print the `js` class early so reveal animations only apply when JS runs,
 * and preload the two font weights used above the fold.
 */
function bizmax_head_early(): void {
	wp_print_inline_script_tag( 'document.documentElement.classList.add("js");' );

	foreach ( array( 'regular', 'black' ) as $weight ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff" crossorigin>' . "\n",
			esc_url( BIZMAX_URI . '/assets/fonts/ploni-' . $weight . '-aaa.woff' )
		);
	}
}
add_action( 'wp_head', 'bizmax_head_early', 1 );

/**
 * Remove jQuery Migrate on the front end (jQuery itself stays for Elementor).
 *
 * @param WP_Scripts $scripts Scripts registry.
 */
function bizmax_remove_jquery_migrate( WP_Scripts $scripts ): void {
	if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
		return;
	}
	$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
}
add_action( 'wp_default_scripts', 'bizmax_remove_jquery_migrate' );
