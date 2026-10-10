<?php
/**
 * Page content for the theme's own templates: schema access, defaults, sanitization and retrieval.
 *
 * Each template-driven page (the home page, the BizLabs page) keeps ALL its content in ONE post
 * meta row on the page itself, so rendering costs a single (already cached) meta read. A schema
 * file per template defines sections → fields → defaults; the same schema drives the admin form,
 * the sanitizer and the templates.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

const BIZMAX_HOME_META = '_bizmax_home';

/**
 * Template-driven page types.
 *
 * @return array<string,array{template:string,schema:string,meta:string,field:string,label:string,template_label:string}>
 */
function bizmax_page_types(): array {
	return array(
		'home'    => array(
			'template'       => 'template-home.php',
			'schema'         => '/inc/home-schema.php',
			'meta'           => BIZMAX_HOME_META,
			'field'          => 'bizmax_home',
			'label'          => __( 'תוכן דף הבית', 'bizmax' ),
			'template_label' => __( 'דף הבית – ביזמקס', 'bizmax' ),
		),
		'bizlabs' => array(
			'template'       => 'template-bizlabs.php',
			'schema'         => '/inc/bizlabs-schema.php',
			'meta'           => '_bizmax_bizlabs',
			'field'          => 'bizmax_bizlabs',
			'label'          => __( 'תוכן עמוד ביזלאבס', 'bizmax' ),
			'template_label' => __( 'ביזלאבס – ביזמקס', 'bizmax' ),
		),
	);
}

/**
 * The page type a page uses ('' when it uses none of the theme's content templates).
 *
 * @param int $post_id Page ID.
 */
function bizmax_page_type_for( int $post_id ): string {
	$template = (string) get_page_template_slug( $post_id );
	foreach ( bizmax_page_types() as $type => $def ) {
		if ( $def['template'] === $template ) {
			return $type;
		}
	}
	return '';
}

/**
 * A page type's schema (loaded once per request).
 *
 * @param string $type Page type.
 * @return array<string,array<string,mixed>>
 */
function bizmax_page_schema( string $type ): array {
	static $schemas = array();
	if ( ! isset( $schemas[ $type ] ) ) {
		$types            = bizmax_page_types();
		$schemas[ $type ] = isset( $types[ $type ] ) ? (array) require BIZMAX_DIR . $types[ $type ]['schema'] : array();
	}
	return $schemas[ $type ];
}

/**
 * The home schema.
 *
 * @return array<string,array<string,mixed>>
 */
function bizmax_home_schema(): array {
	return bizmax_page_schema( 'home' );
}

/**
 * Default value for a single field definition.
 *
 * @param array<string,mixed> $field Field definition.
 * @return mixed
 */
function bizmax_home_field_default( array $field ) {
	switch ( $field['type'] ) {
		case 'group':
			return bizmax_home_fields_defaults( $field['fields'] );
		case 'repeater':
			$rows = array();
			foreach ( (array) ( $field['default'] ?? array() ) as $row ) {
				$rows[] = array_merge( bizmax_home_fields_defaults( $field['fields'] ), $row );
			}
			return $rows;
		case 'heading':
			return null;
		default:
			return $field['default'] ?? '';
	}
}

/**
 * Defaults for a list of fields.
 *
 * @param array<string,array<string,mixed>> $fields Field definitions.
 * @return array<string,mixed>
 */
function bizmax_home_fields_defaults( array $fields ): array {
	$out = array();
	foreach ( $fields as $key => $field ) {
		if ( 'heading' === $field['type'] ) {
			continue;
		}
		$out[ $key ] = bizmax_home_field_default( $field );
	}
	return $out;
}

/**
 * Sanitize one submitted value according to its field definition.
 *
 * @param mixed               $value Raw value.
 * @param array<string,mixed> $field Field definition.
 * @return mixed
 */
function bizmax_home_sanitize_field( $value, array $field ) {
	switch ( $field['type'] ) {
		case 'text':
			return sanitize_text_field( (string) $value );
		case 'textarea':
			return wp_kses_post( trim( (string) $value ) );
		case 'url':
			$value = trim( (string) $value );
			// Allow in-page anchors ("#contact") as well as absolute/relative URLs.
			return str_starts_with( $value, '#' ) ? '#' . sanitize_title( substr( $value, 1 ) ) : esc_url_raw( $value );
		case 'email':
			return sanitize_email( (string) $value );
		case 'image':
		case 'number':
			return absint( $value );
		case 'decimal':
			return is_numeric( $value ) ? round( max( 0, (float) $value ), 2 ) : 0;
		case 'color':
			return (string) ( sanitize_hex_color( (string) $value ) ?? '' );
		case 'asset':
			// A bundled default image (see bizmax_placeholders()); anything else is dropped.
			$value = (string) $value;
			return isset( bizmax_placeholders()[ $value ] ) ? $value : '';
		case 'checkbox':
			return rest_sanitize_boolean( $value );
		case 'select':
			$value = sanitize_key( (string) $value );
			return isset( $field['options'][ $value ] ) ? $value : ( $field['default'] ?? '' );
		case 'date':
			$value = sanitize_text_field( (string) $value );
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) && strtotime( $value ) ? $value : '';
		case 'group':
			return bizmax_home_sanitize_fields( is_array( $value ) ? $value : array(), $field['fields'] );
		case 'repeater':
			$rows = array();
			if ( is_array( $value ) ) {
				$max = (int) ( $field['max'] ?? 20 );
				foreach ( array_slice( array_values( $value ), 0, $max ) as $row ) {
					$rows[] = bizmax_home_sanitize_fields( is_array( $row ) ? $row : array(), $field['fields'] );
				}
			}
			return $rows;
		default:
			return '';
	}
}

/**
 * Sanitize a set of submitted fields; every schema key is always present in the result.
 *
 * @param array<string,mixed>               $input  Raw values.
 * @param array<string,array<string,mixed>> $fields Field definitions.
 * @return array<string,mixed>
 */
function bizmax_home_sanitize_fields( array $input, array $fields ): array {
	$out = array();
	foreach ( $fields as $key => $field ) {
		if ( 'heading' === $field['type'] ) {
			continue;
		}
		if ( 'checkbox' === $field['type'] ) {
			$out[ $key ] = isset( $input[ $key ] ) && rest_sanitize_boolean( $input[ $key ] );
			continue;
		}
		$out[ $key ] = array_key_exists( $key, $input )
			? bizmax_home_sanitize_field( $input[ $key ], $field )
			: ( 'repeater' === $field['type'] ? array() : ( 'group' === $field['type'] ? bizmax_home_sanitize_fields( array(), $field['fields'] ) : '' ) );
	}
	return $out;
}

/**
 * Sanitize a full submission (all sections) for a page type.
 *
 * @param string              $type  Page type.
 * @param array<string,mixed> $input Raw POST array.
 * @return array<string,array<string,mixed>>
 */
function bizmax_page_sanitize( string $type, array $input ): array {
	$out = array();
	foreach ( bizmax_page_schema( $type ) as $section => $def ) {
		$out[ $section ] = bizmax_home_sanitize_fields( is_array( $input[ $section ] ?? null ) ? $input[ $section ] : array(), $def['fields'] );
	}
	return $out;
}

/**
 * Sanitize a full home submission.
 *
 * @param array<string,mixed> $input Raw POST array.
 * @return array<string,array<string,mixed>>
 */
function bizmax_home_sanitize( array $input ): array {
	return bizmax_page_sanitize( 'home', $input );
}

/**
 * Merge saved values over defaults (adds keys introduced after the page was saved).
 *
 * @param array<string,mixed>               $saved  Saved values.
 * @param array<string,array<string,mixed>> $fields Field definitions.
 * @return array<string,mixed>
 */
function bizmax_home_merge_defaults( array $saved, array $fields ): array {
	$out = array();
	foreach ( $fields as $key => $field ) {
		if ( 'heading' === $field['type'] ) {
			continue;
		}
		if ( ! array_key_exists( $key, $saved ) ) {
			$out[ $key ] = bizmax_home_field_default( $field );
		} elseif ( 'group' === $field['type'] ) {
			$out[ $key ] = bizmax_home_merge_defaults( (array) $saved[ $key ], $field['fields'] );
		} elseif ( 'repeater' === $field['type'] ) {
			$out[ $key ] = array();
			foreach ( (array) $saved[ $key ] as $row ) {
				$out[ $key ][] = bizmax_home_merge_defaults( (array) $row, $field['fields'] );
			}
		} else {
			$out[ $key ] = $saved[ $key ];
		}
	}
	return $out;
}

/**
 * Get the complete content of a template-driven page (saved values over defaults).
 *
 * @param string $type    Page type.
 * @param int    $post_id Page ID.
 * @return array<string,array<string,mixed>>
 */
function bizmax_page_get( string $type, int $post_id ): array {
	static $cache = array();
	$key          = $type . ':' . $post_id;
	if ( isset( $cache[ $key ] ) && ! is_admin() ) {
		return $cache[ $key ]; // The header and the template read the same page content.
	}
	$types = bizmax_page_types();
	if ( ! isset( $types[ $type ] ) ) {
		return array();
	}
	$saved = $post_id > 0 ? get_post_meta( $post_id, $types[ $type ]['meta'], true ) : array();
	$saved = is_array( $saved ) ? $saved : array();

	$content = array();
	foreach ( bizmax_page_schema( $type ) as $section => $def ) {
		$content[ $section ] = bizmax_home_merge_defaults( is_array( $saved[ $section ] ?? null ) ? $saved[ $section ] : array(), $def['fields'] );
	}

	/**
	 * Filter a template page's content before rendering (e.g. "bizmax_home_content",
	 * "bizmax_bizlabs_content"; the home filter can inject events from a CPT).
	 *
	 * @param array $content Content by section.
	 * @param int   $post_id Page ID.
	 */
	$cache[ $key ] = apply_filters( "bizmax_{$type}_content", $content, $post_id );
	return $cache[ $key ];
}

/**
 * Get the complete home content for a page.
 *
 * @param int $post_id Page ID.
 * @return array<string,array<string,mixed>>
 */
function bizmax_home_get( int $post_id ): array {
	return bizmax_page_get( 'home', $post_id );
}

/**
 * Save sanitized content for a page type.
 *
 * @param string              $type    Page type.
 * @param int                 $post_id Page ID.
 * @param array<string,mixed> $input   Raw POST array.
 */
function bizmax_page_save( string $type, int $post_id, array $input ): void {
	$types = bizmax_page_types();
	if ( isset( $types[ $type ] ) ) {
		update_post_meta( $post_id, $types[ $type ]['meta'], bizmax_page_sanitize( $type, $input ) );
	}
}

/**
 * Save sanitized home content.
 *
 * @param int                 $post_id Page ID.
 * @param array<string,mixed> $input   Raw POST array.
 */
function bizmax_home_save( int $post_id, array $input ): void {
	bizmax_page_save( 'home', $post_id, $input );
}

/**
 * One-time content updates for pages saved with an earlier theme version.
 *
 * A saved value always wins over the schema default, so when a default changes, pages that
 * were saved with the old default keep it. Each step below replaces a value only while it
 * still equals the old default; anything an editor changed is left untouched. Runs once.
 */
function bizmax_maybe_upgrade_home_content(): void {
	if ( (int) get_option( 'bizmax_content_setup', 0 ) >= 2 ) {
		return;
	}
	update_option( 'bizmax_content_setup', 2 );

	$page_id = bizmax_find_home_page();
	$saved   = $page_id ? get_post_meta( $page_id, BIZMAX_HOME_META, true ) : null;
	if ( ! is_array( $saved ) ) {
		return; // Nothing saved yet: the new defaults already apply.
	}

	// 1.3.1: the About cards link to the programme pages instead of in-page sections.
	$changes = array(
		'link_1' => array( '#deschool', home_url( '/theschool/' ) ),
		'link_2' => array( '#bizlabs', home_url( '/bizlabs-new/' ) ),
	);
	$changed = false;
	foreach ( $changes as $card => [ $old, $new ] ) {
		if ( isset( $saved['about'][ $card ]['url'] ) && $old === $saved['about'][ $card ]['url'] ) {
			$saved['about'][ $card ]['url'] = esc_url_raw( $new );
			$changed                        = true;
		}
	}
	if ( $changed ) {
		update_post_meta( $page_id, BIZMAX_HOME_META, $saved );
	}
}
add_action( 'init', 'bizmax_maybe_upgrade_home_content', 20 );
