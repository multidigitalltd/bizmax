<?php
/**
 * Reusable output helpers (all output is escaped here).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print the brand "flower" mark (four rotated ellipses with a gradient fill).
 *
 * @param string $color blue|orange|green.
 * @param int    $size  Rendered size in px.
 */
function bizmax_flower( string $color = 'blue', int $size = 34 ): void {
	static $counter = 0;
	++$counter;

	$stops = array(
		'blue'   => array( '#1B3764', '#2E4F8F' ),
		'orange' => array( '#F6A81C', '#F7C948' ),
		'green'  => array( '#1e7a4f', '#4fbf8a' ),
	);
	$pair  = $stops[ $color ] ?? $stops['blue'];
	$id    = 'bz-fg-' . $counter;
	$size  = max( 12, $size );

	echo '<svg class="bz-flower" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 40 40" aria-hidden="true" focusable="false">';
	echo '<defs><linearGradient id="' . esc_attr( $id ) . '" x1="0" y1="0" x2="1" y2="1">';
	echo '<stop offset="0" stop-color="' . esc_attr( $pair[0] ) . '"/><stop offset="1" stop-color="' . esc_attr( $pair[1] ) . '"/>';
	echo '</linearGradient></defs><g fill="url(#' . esc_attr( $id ) . ')">';
	foreach ( array( 35, 125, 215, 305 ) as $deg ) {
		echo '<ellipse cx="20" cy="9" rx="4.2" ry="9" transform="rotate(' . (int) $deg . ' 20 20)"/>';
	}
	echo '</g></svg>';
}

/**
 * Print a pill CTA with the decorative ">>" arrow.
 *
 * @param string $text  Label.
 * @param string $url   Href.
 * @param string $style primary|secondary|link.
 * @param string $class Extra classes.
 */
function bizmax_cta( string $text, string $url, string $style = 'secondary', string $class = '' ): void {
	if ( '' === $text ) {
		return;
	}
	$classes = 'link' === $style ? 'bz-link' : 'bz-btn bz-btn--' . sanitize_html_class( $style );
	if ( '' !== $class ) {
		$classes .= ' ' . $class;
	}
	printf(
		'<a class="%1$s" href="%2$s">%3$s <span class="arr" aria-hidden="true">&gt;&gt;</span></a>',
		esc_attr( $classes ),
		esc_url( $url ?: '#contact' ),
		esc_html( $text )
	);
}

/**
 * Dimensions of the bundled placeholder images (for width/height attributes → no CLS).
 *
 * @return array<string,array{0:int,1:int,2:string}>
 */
function bizmax_placeholders(): array {
	return array(
		'photo-office'            => array( 1100, 619, 'webp' ),
		'space-1'                 => array( 900, 827, 'webp' ),
		'space-2'                 => array( 900, 827, 'webp' ),
		'space-3'                 => array( 900, 827, 'webp' ),
		'space-4'                 => array( 900, 827, 'webp' ),
		'portrait-a'              => array( 240, 360, 'webp' ),
		'portrait-b'              => array( 360, 253, 'webp' ),
		'logo-kemach'             => array( 294, 195, 'png' ),
		'logo-achim'              => array( 600, 177, 'webp' ),
		'logo-jda'                => array( 167, 94, 'webp' ),
		'logo-jerusalem-heritage' => array( 279, 181, 'png' ),
		'map-placeholder'         => array( 500, 249, 'webp' ),
		'logo'                    => array( 400, 400, 'png' ),
	);
}

/**
 * Build an <img> for an attachment, falling back to a bundled placeholder.
 *
 * @param int                  $attachment_id Attachment ID (0 = none).
 * @param string               $size          Registered image size.
 * @param string               $fallback      Placeholder key (see bizmax_placeholders()).
 * @param array<string,string> $attr          Extra attributes (class, alt, loading, fetchpriority…).
 * @return string Escaped HTML.
 */
function bizmax_image( int $attachment_id, string $size, string $fallback = '', array $attr = array() ): string {
	if ( $attachment_id > 0 && wp_attachment_is_image( $attachment_id ) ) {
		$html = wp_get_attachment_image( $attachment_id, $size, false, $attr );
		if ( '' !== $html ) {
			return $html;
		}
	}

	$placeholders = bizmax_placeholders();
	if ( '' === $fallback || ! isset( $placeholders[ $fallback ] ) ) {
		return '';
	}

	[ $width, $height, $ext ] = $placeholders[ $fallback ];

	$attr = array_merge(
		array(
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
		),
		$attr
	);
	if ( isset( $attr['fetchpriority'] ) && 'high' === $attr['fetchpriority'] ) {
		unset( $attr['loading'] );
	}

	$out = '<img src="' . esc_url( BIZMAX_URI . '/assets/img/' . $fallback . '.' . $ext ) . '" width="' . (int) $width . '" height="' . (int) $height . '"';
	foreach ( $attr as $key => $value ) {
		$out .= ' ' . sanitize_key( $key ) . '="' . esc_attr( $value ) . '"';
	}
	return $out . '>';
}

/**
 * Print the site logo (custom logo or bundled fallback) wrapped in a home link.
 *
 * @param string $class Wrapper class.
 */
function bizmax_logo( string $class = 'bz-logo' ): void {
	$name = get_bloginfo( 'name', 'display' );
	echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'bz-logo__img', 'alt' => $name, 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped.
	} else {
		echo bizmax_image( 0, 'medium', 'logo', array( 'class' => 'bz-logo__img', 'alt' => $name, 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
	}
	echo '</a>';
}

/**
 * Print a multi-line text field as a paragraph (basic HTML allowed, newlines → <br>).
 *
 * @param string $text  Stored text.
 * @param string $class Paragraph class.
 * @param string $tag   Wrapper tag.
 */
function bizmax_text( string $text, string $class = '', string $tag = 'p' ): void {
	$text = trim( $text );
	if ( '' === $text ) {
		return;
	}
	$tag = in_array( $tag, array( 'p', 'div', 'span' ), true ) ? $tag : 'p';
	echo '<' . $tag . ( '' !== $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>';
	echo wp_kses_post( nl2br( $text ) );
	echo '</' . $tag . '>';
}

/**
 * Inline SVG icons (Lucide, MIT) used by the theme.
 *
 * @param string $name Icon name.
 * @param int    $size Size in px.
 */
function bizmax_icon( string $name, int $size = 17 ): void {
	$paths = array(
		'clock'          => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'graduation-cap' => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
		'rocket'         => '<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>',
		'phone'          => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
		'mail'           => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'map-pin'        => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'x'              => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}
	printf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		(int) $size,
		$paths[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup.
	);
}

/**
 * Print the WhatsApp glyph (Font Awesome, CC BY 4.0).
 */
function bizmax_whatsapp_icon(): void {
	echo '<svg width="30" height="30" viewBox="0 0 448 512" fill="#ffffff" aria-hidden="true" focusable="false"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>';
}

/**
 * Print a registered menu (single level, no container).
 *
 * @param string $location   Menu location.
 * @param string $menu_class UL class.
 * @param bool   $fallback   Whether to fall back to a page list when no menu is assigned.
 */
function bizmax_menu( string $location, string $menu_class, bool $fallback = true ): void {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $menu_class,
			'depth'          => 1,
			'fallback_cb'    => $fallback ? 'bizmax_menu_fallback' : false,
			'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
		)
	);
}

/**
 * Menu fallback (no menu assigned): a page list inside the same <ul class> the CSS expects.
 *
 * @param array<string,mixed> $args wp_nav_menu() arguments.
 */
function bizmax_menu_fallback( array $args ): void {
	$items = wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
			'echo'     => false,
		)
	);
	if ( '' === $items ) {
		return;
	}
	echo '<ul class="' . esc_attr( (string) $args['menu_class'] ) . '">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_list_pages() output is escaped by core.
}

/**
 * Hebrew month names for the events date chip (independent of the site locale).
 *
 * @param string $date Y-m-d.
 * @return array{day:string,month:string,iso:string}
 */
function bizmax_hebrew_date_parts( string $date ): array {
	$months = array( 'ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר' );
	$ts     = strtotime( $date );
	if ( false === $ts ) {
		return array( 'day' => '', 'month' => '', 'iso' => '' );
	}
	return array(
		'day'   => gmdate( 'j', $ts ),
		'month' => $months[ (int) gmdate( 'n', $ts ) - 1 ],
		'iso'   => gmdate( 'Y-m-d', $ts ),
	);
}
