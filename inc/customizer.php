<?php
/**
 * Site-wide settings (contact details, map, form recipient) via the Customizer.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults for theme mods.
 *
 * @return array<string,mixed>
 */
function bizmax_mod_defaults(): array {
	return array(
		'bizmax_phone'         => '073-200-800-50',
		'bizmax_whatsapp'      => '97273200800',
		'bizmax_email'         => 'info@bizmax.co.il',
		'bizmax_address'       => 'הצבי 15, ירושלים',
		'bizmax_map_link'      => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'הצבי 15, ירושלים' ),
		'bizmax_map_embed'     => '',
		'bizmax_map_image'     => 0,
		'bizmax_form_to'       => '',
		'bizmax_show_whatsapp' => true,
	);
}

/**
 * Read a theme mod with the theme default.
 *
 * @param string $key Mod key.
 * @return mixed
 */
function bizmax_mod( string $key ) {
	$defaults = bizmax_mod_defaults();
	return get_theme_mod( $key, $defaults[ $key ] ?? '' );
}

/**
 * Validate a Google Maps embed URL (only that host is rendered in an iframe).
 *
 * @param string $url Raw value.
 */
function bizmax_sanitize_map_embed( string $url ): string {
	$url  = esc_url_raw( trim( $url ) );
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! $host || ! preg_match( '/(^|\.)google\.[a-z.]+$/i', $host ) || ! str_contains( $url, '/maps/embed' ) ) {
		return '';
	}
	return $url;
}

/**
 * Register the Customizer section and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function bizmax_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'bizmax_contact',
		array(
			'title'    => __( 'ביזמקס – פרטי קשר ופוטר', 'bizmax' ),
			'priority' => 30,
		)
	);

	$defaults = bizmax_mod_defaults();

	$text_controls = array(
		'bizmax_phone'    => array( __( 'טלפון', 'bizmax' ), 'sanitize_text_field' ),
		'bizmax_whatsapp' => array( __( 'וואטסאפ (ספרות בלבד, עם קידומת מדינה, למשל 97273200800)', 'bizmax' ), 'bizmax_sanitize_digits' ),
		'bizmax_email'    => array( __( 'מייל', 'bizmax' ), 'sanitize_email' ),
		'bizmax_address'  => array( __( 'כתובת', 'bizmax' ), 'sanitize_text_field' ),
		'bizmax_map_link' => array( __( 'קישור למפה (בלחיצה)', 'bizmax' ), 'esc_url_raw' ),
		'bizmax_form_to'  => array( __( 'מייל לקבלת פניות מהטופס (ריק = מייל המנהל)', 'bizmax' ), 'sanitize_email' ),
	);

	foreach ( $text_controls as $key => [ $label, $sanitize ] ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'bizmax_contact',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_setting(
		'bizmax_map_embed',
		array(
			'default'           => '',
			'sanitize_callback' => 'bizmax_sanitize_map_embed',
		)
	);
	$wp_customize->add_control(
		'bizmax_map_embed',
		array(
			'label'       => __( 'כתובת הטמעה של Google Maps (src של ה-iframe)', 'bizmax' ),
			'description' => __( 'ב-Google Maps: שיתוף → הטמעת מפה → העתיקו רק את כתובת ה-src. אם ריק – תוצג תמונת המפה.', 'bizmax' ),
			'section'     => 'bizmax_contact',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'bizmax_map_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'bizmax_map_image',
			array(
				'label'     => __( 'תמונת מפה (חלופה להטמעה)', 'bizmax' ),
				'section'   => 'bizmax_contact',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'bizmax_show_whatsapp',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'bizmax_show_whatsapp',
		array(
			'label'   => __( 'הצגת כפתור וואטסאפ צף', 'bizmax' ),
			'section' => 'bizmax_contact',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'bizmax_customize_register' );

/**
 * Keep only digits.
 *
 * @param string $value Raw value.
 */
function bizmax_sanitize_digits( string $value ): string {
	return preg_replace( '/\D+/', '', $value ) ?? '';
}
