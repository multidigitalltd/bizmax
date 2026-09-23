<?php
/**
 * Home page admin: a schema-driven meta box on the page that uses template-home.php.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

const BIZMAX_HOME_NONCE = 'bizmax_home_save';

/**
 * Whether a page uses the home template.
 *
 * @param int $post_id Page ID.
 */
function bizmax_is_home_page( int $post_id ): bool {
	return 'template-home.php' === get_page_template_slug( $post_id );
}

/**
 * The home page is form-driven: drop the content editor for it (classic edit screen + meta box).
 */
function bizmax_home_admin_editor(): void {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen routing.
	if ( $post_id && 'page' === get_post_type( $post_id ) && bizmax_is_home_page( $post_id ) ) {
		remove_post_type_support( 'page', 'editor' );
		add_filter( 'use_block_editor_for_post', '__return_false', 100 );
	}
}
add_action( 'load-post.php', 'bizmax_home_admin_editor' );

/**
 * Register the meta box.
 *
 * @param string  $post_type Post type.
 * @param WP_Post $post      Post.
 */
function bizmax_home_meta_box( string $post_type, WP_Post $post ): void {
	if ( 'page' !== $post_type ) {
		return;
	}
	if ( ! bizmax_is_home_page( $post->ID ) ) {
		add_meta_box( 'bizmax-home-hint', __( 'דף הבית – ביזמקס', 'bizmax' ), 'bizmax_home_meta_box_hint', 'page', 'side', 'low' );
		return;
	}
	add_meta_box( 'bizmax-home', __( 'תוכן דף הבית', 'bizmax' ), 'bizmax_home_meta_box_render', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'bizmax_home_meta_box', 10, 2 );

/**
 * Hint shown on regular pages.
 */
function bizmax_home_meta_box_hint(): void {
	echo '<p>' . esc_html__( 'כדי לערוך את דף הבית של התבנית: בחרו בתבנית "דף הבית – ביזמקס" תחת "מאפייני עמוד", שמרו, ושדות התוכן יופיעו כאן.', 'bizmax' ) . '</p>';
}

/**
 * Admin assets for the home meta box only.
 *
 * @param string $hook Screen hook.
 */
function bizmax_home_admin_assets( string $hook ): void {
	if ( 'post.php' !== $hook ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id || ! bizmax_is_home_page( $post_id ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'bizmax-admin', BIZMAX_URI . '/assets/css/admin.css', array(), BIZMAX_VERSION );
	wp_enqueue_script( 'bizmax-admin', BIZMAX_URI . '/assets/js/admin.js', array(), BIZMAX_VERSION, true );
	wp_localize_script(
		'bizmax-admin',
		'bizmaxAdmin',
		array(
			'chooseImage' => __( 'בחירת תמונה', 'bizmax' ),
			'useImage'    => __( 'שימוש בתמונה', 'bizmax' ),
			'confirmRm'   => __( 'להסיר את הפריט?', 'bizmax' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'bizmax_home_admin_assets' );

/**
 * Render the tabbed form.
 *
 * @param WP_Post $post Post.
 */
function bizmax_home_meta_box_render( WP_Post $post ): void {
	$schema  = bizmax_home_schema();
	$content = bizmax_home_get( $post->ID );

	wp_nonce_field( BIZMAX_HOME_NONCE, BIZMAX_HOME_NONCE . '_nonce' );

	echo '<p class="description">' . esc_html__( 'כל התוכן של דף הבית נערך כאן ונשמר בתוך הדף. שדות "פסקה" מקבלים HTML בסיסי (b, a, br) ומעברי שורה.', 'bizmax' ) . '</p>';
	echo '<div class="bz-admin">';

	echo '<div class="bz-admin__tabs" role="tablist" aria-label="' . esc_attr__( 'מקטעי דף הבית', 'bizmax' ) . '">';
	$first = true;
	foreach ( $schema as $key => $section ) {
		printf(
			'<button type="button" class="bz-admin__tab%1$s" role="tab" id="bz-tab-%2$s" aria-controls="bz-panel-%2$s" aria-selected="%3$s" tabindex="%4$s">%5$s</button>',
			$first ? ' is-active' : '',
			esc_attr( $key ),
			$first ? 'true' : 'false',
			$first ? '0' : '-1',
			esc_html( $section['label'] )
		);
		$first = false;
	}
	echo '</div>';

	$first = true;
	foreach ( $schema as $key => $section ) {
		printf(
			'<div class="bz-admin__panel" role="tabpanel" id="bz-panel-%1$s" aria-labelledby="bz-tab-%1$s"%2$s>',
			esc_attr( $key ),
			$first ? '' : ' hidden'
		);
		bizmax_home_render_fields( $section['fields'], $content[ $key ], 'bizmax_home[' . $key . ']', 'bz-' . $key );
		echo '</div>';
		$first = false;
	}

	echo '</div>';
}

/**
 * Render a list of fields.
 *
 * @param array<string,array<string,mixed>> $fields Definitions.
 * @param array<string,mixed>               $values Values.
 * @param string                            $prefix Input name prefix, e.g. "bizmax_home[hero]".
 * @param string                            $id     ID prefix (unique per row).
 */
function bizmax_home_render_fields( array $fields, array $values, string $prefix, string $id ): void {
	foreach ( $fields as $key => $field ) {
		$name     = $prefix . '[' . $key . ']';
		$field_id = $id . '-' . $key;
		$value    = $values[ $key ] ?? bizmax_home_field_default( $field );
		bizmax_home_render_field( $field, $value, $name, $field_id );
	}
}

/**
 * Render one field.
 *
 * @param array<string,mixed> $field Definition.
 * @param mixed               $value Value.
 * @param string              $name  Input name.
 * @param string              $id    Input id.
 */
function bizmax_home_render_field( array $field, $value, string $name, string $id ): void {
	$type  = $field['type'];
	$label = $field['label'] ?? '';

	if ( 'heading' === $type ) {
		echo '<h3 class="bz-admin__heading">' . esc_html( $label ) . '</h3>';
		return;
	}

	if ( 'group' === $type ) {
		echo '<fieldset class="bz-admin__group"><legend>' . esc_html( $label ) . '</legend>';
		bizmax_home_render_fields( $field['fields'], (array) $value, $name, $id );
		echo '</fieldset>';
		return;
	}

	if ( 'repeater' === $type ) {
		bizmax_home_render_repeater( $field, (array) $value, $name, $id );
		return;
	}

	echo '<div class="bz-admin__field bz-admin__field--' . esc_attr( $type ) . '">';

	switch ( $type ) {
		case 'checkbox':
			printf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" value="1"%3$s> %4$s</label>',
				esc_attr( $name ),
				esc_attr( $id ),
				checked( (bool) $value, true, false ),
				esc_html( $label )
			);
			break;

		case 'textarea':
			printf(
				'<label for="%1$s">%2$s</label><textarea name="%3$s" id="%1$s" rows="%4$d" class="large-text">%5$s</textarea>',
				esc_attr( $id ),
				esc_html( $label ),
				esc_attr( $name ),
				(int) ( $field['rows'] ?? 6 ),
				esc_textarea( (string) $value )
			);
			break;

		case 'select':
			printf( '<label for="%1$s">%2$s</label><select name="%3$s" id="%1$s">', esc_attr( $id ), esc_html( $label ), esc_attr( $name ) );
			foreach ( (array) $field['options'] as $opt_value => $opt_label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $opt_value ), selected( $value, $opt_value, false ), esc_html( $opt_label ) );
			}
			echo '</select>';
			break;

		case 'image':
			$value = (int) $value;
			$thumb = $value ? wp_get_attachment_image( $value, 'thumbnail', false, array( 'alt' => '' ) ) : '';
			printf(
				'<span class="bz-admin__label">%1$s</span><div class="bz-admin__image" data-bz-image><input type="hidden" name="%2$s" id="%3$s" value="%4$d"><div class="bz-admin__preview">%5$s</div><button type="button" class="button" data-bz-image-pick>%6$s</button> <button type="button" class="button-link-delete" data-bz-image-remove%7$s>%8$s</button></div>',
				esc_html( $label ),
				esc_attr( $name ),
				esc_attr( $id ),
				$value,
				$thumb, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped.
				esc_html__( 'בחירת תמונה', 'bizmax' ),
				$value ? '' : ' hidden',
				esc_html__( 'הסרה', 'bizmax' )
			);
			break;

		default:
			$input_type = match ( $type ) {
				'number' => 'number',
				'date'   => 'date',
				'email'  => 'email',
				default  => 'text',
			};
			printf(
				'<label for="%1$s">%2$s</label><input type="%3$s" name="%4$s" id="%1$s" value="%5$s" class="regular-text"%6$s>',
				esc_attr( $id ),
				esc_html( $label ),
				esc_attr( $input_type ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				'url' === $type ? ' placeholder="https://… או #anchor"' : ''
			);
	}

	echo '</div>';
}

/**
 * Render a repeater with an HTML <template> for new rows.
 *
 * @param array<string,mixed>       $field  Definition.
 * @param array<int,array>          $rows   Values.
 * @param string                    $name   Input name prefix.
 * @param string                    $id     ID prefix.
 */
function bizmax_home_render_repeater( array $field, array $rows, string $name, string $id ): void {
	printf(
		'<div class="bz-admin__repeater" data-bz-repeater data-max="%1$d"><span class="bz-admin__label">%2$s</span><div class="bz-admin__rows">',
		(int) ( $field['max'] ?? 20 ),
		esc_html( $field['label'] )
	);
	foreach ( array_values( $rows ) as $index => $row ) {
		bizmax_home_render_repeater_row( $field, $row, $name, $id, (string) $index );
	}
	echo '</div>';
	echo '<template data-bz-row-template>';
	bizmax_home_render_repeater_row( $field, bizmax_home_fields_defaults( $field['fields'] ), $name, $id, '__i__' );
	echo '</template>';
	echo '<button type="button" class="button button-secondary" data-bz-add>+ ' . esc_html__( 'הוספת פריט', 'bizmax' ) . '</button></div>';
}

/**
 * Render one repeater row.
 *
 * @param array<string,mixed> $field Definition.
 * @param array<string,mixed> $row   Values.
 * @param string              $name  Input name prefix.
 * @param string              $id    ID prefix.
 * @param string              $index Row index or "__i__".
 */
function bizmax_home_render_repeater_row( array $field, array $row, string $name, string $id, string $index ): void {
	echo '<div class="bz-admin__row" data-bz-row>';
	echo '<div class="bz-admin__row-bar"><span class="bz-admin__row-num"></span>';
	echo '<button type="button" class="button-link" data-bz-up aria-label="' . esc_attr__( 'הזזה למעלה', 'bizmax' ) . '">&#9650;</button>';
	echo '<button type="button" class="button-link" data-bz-down aria-label="' . esc_attr__( 'הזזה למטה', 'bizmax' ) . '">&#9660;</button>';
	echo '<button type="button" class="button-link button-link-delete" data-bz-remove>' . esc_html__( 'הסרה', 'bizmax' ) . '</button></div>';
	echo '<div class="bz-admin__row-fields">';
	bizmax_home_render_fields( $field['fields'], $row, $name . '[' . $index . ']', $id . '-' . $index );
	echo '</div></div>';
}

/**
 * Save handler.
 *
 * @param int     $post_id Page ID.
 * @param WP_Post $post    Post.
 */
function bizmax_home_save_meta( int $post_id, WP_Post $post ): void {
	if ( ! isset( $_POST[ BIZMAX_HOME_NONCE . '_nonce' ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ BIZMAX_HOME_NONCE . '_nonce' ] ) ), BIZMAX_HOME_NONCE ) ) {
		return;
	}
	if ( 'page' !== $post->post_type || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['bizmax_home'] ) || ! is_array( $_POST['bizmax_home'] ) ) {
		return;
	}
	// Sanitized field-by-field against the schema in bizmax_home_sanitize().
	bizmax_home_save( $post_id, wp_unslash( $_POST['bizmax_home'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
}
add_action( 'save_post_page', 'bizmax_home_save_meta', 10, 2 );

/**
 * Name the template in the admin template dropdown.
 *
 * @param array<string,string> $templates Templates.
 * @return array<string,string>
 */
function bizmax_home_template_label( array $templates ): array {
	$templates['template-home.php'] = __( 'דף הבית – ביזמקס', 'bizmax' );
	return $templates;
}
add_filter( 'theme_page_templates', 'bizmax_home_template_label' );
