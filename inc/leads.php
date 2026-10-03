<?php
/**
 * Lead center: every form submission is stored as a private "lead" post with a status.
 *
 * Sources: the theme contact form, Elementor Pro forms and JetFormBuilder forms. Leads use a
 * non-public post type and custom post statuses (indexed columns, native WordPress counts and
 * search), so there are no extra tables. Only the data the visitor submitted is stored, plus the
 * form name and page; IP addresses are not kept. Personal data export/erase tools include leads.
 *
 * The admin screens live in inc/leads-admin.php.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

const BIZMAX_LEAD_PT = 'bizmax_lead';

/**
 * Lead statuses: post status => [label, colour].
 *
 * @return array<string,array{0:string,1:string}>
 */
function bizmax_lead_statuses(): array {
	return array(
		'bz_new'      => array( __( 'חדש', 'bizmax' ), '#1d4ed8' ),
		'bz_progress' => array( __( 'בטיפול', 'bizmax' ), '#b45309' ),
		'bz_waiting'  => array( __( 'ממתין לתשובה', 'bizmax' ), '#6d28d9' ),
		'bz_won'      => array( __( 'נסגר בהצלחה', 'bizmax' ), '#047857' ),
		'bz_lost'     => array( __( 'לא רלוונטי', 'bizmax' ), '#4b5563' ),
	);
}

/**
 * Register the lead post type and statuses.
 */
function bizmax_register_leads(): void {
	register_post_type(
		BIZMAX_LEAD_PT,
		array(
			'labels'              => array(
				'name'               => __( 'לידים', 'bizmax' ),
				'singular_name'      => __( 'ליד', 'bizmax' ),
				'menu_name'          => __( 'לידים', 'bizmax' ),
				'all_items'          => __( 'כל הלידים', 'bizmax' ),
				'edit_item'          => __( 'פרטי ליד', 'bizmax' ),
				'search_items'       => __( 'חיפוש לידים', 'bizmax' ),
				'not_found'          => __( 'עדיין אין לידים.', 'bizmax' ),
				'not_found_in_trash' => __( 'אין לידים בפח.', 'bizmax' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => false,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title' ),
			'rewrite'             => false,
			'query_var'           => false,
			'can_export'          => true,
			'delete_with_user'    => false,
			// Leads hold personal data: editors and administrators only, and never created by hand.
			'map_meta_cap'        => false,
			'capabilities'        => array(
				'edit_post'              => 'edit_others_posts',
				'read_post'              => 'edit_others_posts',
				'delete_post'            => 'delete_others_posts',
				'edit_posts'             => 'edit_others_posts',
				'edit_others_posts'      => 'edit_others_posts',
				'edit_published_posts'   => 'edit_others_posts',
				'edit_private_posts'     => 'edit_others_posts',
				'read_private_posts'     => 'edit_others_posts',
				'delete_posts'           => 'delete_others_posts',
				'delete_others_posts'    => 'delete_others_posts',
				'delete_published_posts' => 'delete_others_posts',
				'delete_private_posts'   => 'delete_others_posts',
				'publish_posts'          => 'do_not_allow',
				'create_posts'           => 'do_not_allow',
			),
		)
	);

	foreach ( bizmax_lead_statuses() as $status => [ $label ] ) {
		register_post_status(
			$status,
			array(
				'label'                     => $label,
				'public'                    => false,
				'internal'                  => false,
				'protected'                 => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of leads. */
				'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>', 'bizmax' ), // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralSingular,WordPress.WP.I18n.NonSingularStringLiteralPlural -- labels come from bizmax_lead_statuses().
			)
		);
	}
}
add_action( 'init', 'bizmax_register_leads' );

/**
 * Store a lead.
 *
 * @param array<string,mixed> $lead {
 *     Lead data.
 *
 *     @type string               $name       Full name.
 *     @type string               $email      Email.
 *     @type string               $phone      Phone.
 *     @type string               $form       Form name (shown in the list and used for filtering).
 *     @type string               $source_url Page the form was sent from.
 *     @type int                  $source_id  Post ID of that page, if known.
 *     @type array<string,string> $fields     Every submitted field: label => value.
 * }
 * @return int Lead ID (0 on failure).
 */
function bizmax_lead_create( array $lead ): int {
	$name  = sanitize_text_field( (string) ( $lead['name'] ?? '' ) );
	$email = sanitize_email( (string) ( $lead['email'] ?? '' ) );
	$phone = sanitize_text_field( (string) ( $lead['phone'] ?? '' ) );
	$form  = sanitize_text_field( (string) ( $lead['form'] ?? '' ) );
	$url   = esc_url_raw( (string) ( $lead['source_url'] ?? '' ) );

	$fields = array();
	foreach ( (array) ( $lead['fields'] ?? array() ) as $label => $value ) {
		$label = sanitize_text_field( (string) $label );
		$value = is_array( $value ) ? implode( ', ', array_map( 'strval', $value ) ) : (string) $value;
		$value = sanitize_textarea_field( $value );
		if ( '' !== $label && '' !== $value ) {
			$fields[ $label ] = mb_substr( $value, 0, 5000 );
		}
	}

	$title = __( 'ליד ללא שם', 'bizmax' );
	foreach ( array( $name, $email, $phone ) as $candidate ) {
		if ( '' !== $candidate ) {
			$title = $candidate;
			break;
		}
	}

	$id = wp_insert_post(
		array(
			'post_type'    => BIZMAX_LEAD_PT,
			'post_status'  => 'bz_new',
			'post_title'   => $title,
			'post_excerpt' => trim( $email . ' ' . $phone ), // Makes email/phone searchable with core search.
			'post_author'  => 0,
			'meta_input'   => array(
				'_bz_lead_email'      => $email,
				'_bz_lead_phone'      => $phone,
				'_bz_lead_form'       => $form,
				'_bz_lead_source_url' => $url,
				'_bz_lead_source_id'  => absint( $lead['source_id'] ?? 0 ),
				'_bz_lead_fields'     => $fields,
			),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return 0;
	}

	/**
	 * Fires after a lead is stored (webhooks, CRM sync, notifications).
	 *
	 * @param int                 $id   Lead ID.
	 * @param array<string,mixed> $lead Submitted lead data.
	 */
	do_action( 'bizmax_lead_created', (int) $id, $lead );

	return (int) $id;
}

/**
 * Read a lead meta value that is stored as an array (empty array when missing or malformed).
 *
 * @param int    $id  Lead ID.
 * @param string $key Meta key.
 * @return array<int|string,mixed>
 */
function bizmax_lead_meta_array( int $id, string $key ): array {
	$value = get_post_meta( $id, $key, true );
	return is_array( $value ) ? $value : array();
}

/**
 * Pick name, email and phone out of a generic list of submitted fields.
 *
 * @param array<int,array{key:string,label:string,type:string,value:string}> $fields Fields.
 * @return array{name:string,email:string,phone:string}
 */
function bizmax_lead_detect_contact( array $fields ): array {
	$out   = array(
		'name'  => '',
		'email' => '',
		'phone' => '',
	);
	$first = '';
	$last  = '';
	foreach ( $fields as $f ) {
		$value = trim( (string) $f['value'] );
		if ( '' === $value ) {
			continue;
		}
		$hay = mb_strtolower( $f['key'] . ' ' . $f['label'] );
		if ( '' === $out['email'] && ( 'email' === $f['type'] || preg_match( '/mail|מייל|דוא"?ל/u', $hay ) ) && is_email( $value ) ) {
			$out['email'] = $value;
		} elseif ( '' === $out['phone'] && ( 'tel' === $f['type'] || preg_match( '/phone|tel|mobile|טלפון|נייד|פלאפון|סלולרי/u', $hay ) ) ) {
			$out['phone'] = $value;
		} elseif ( '' === $last && preg_match( '/last|משפחה/u', $hay ) ) {
			$last = $value;
		} elseif ( '' === $first && preg_match( '/first|פרטי/u', $hay ) ) {
			$first = $value;
		} elseif ( '' === $out['name'] && preg_match( '/name|שם/u', $hay ) ) {
			$out['name'] = $value;
		}
	}
	if ( '' === $out['name'] ) {
		$out['name'] = trim( $first . ' ' . $last );
	}
	return $out;
}

/**
 * Elementor Pro forms: store every successful submission as a lead.
 *
 * @param object $record       ElementorPro\Modules\Forms\Classes\Form_Record.
 * @param object $ajax_handler Ajax handler (unused).
 */
function bizmax_leads_from_elementor( $record, $ajax_handler = null ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- hook signature.
	if ( ! apply_filters( 'bizmax_leads_capture_elementor', true ) || ! is_object( $record ) || ! method_exists( $record, 'get' ) ) {
		return;
	}

	$generic = array();
	$labeled = array();
	foreach ( (array) $record->get( 'fields' ) as $key => $field ) {
		$field = (array) $field;
		$type  = (string) ( $field['type'] ?? '' );
		if ( in_array( $type, array( 'honeypot', 'recaptcha', 'recaptcha_v3', 'html', 'step', 'hidden_spam' ), true ) ) {
			continue;
		}
		$value = $field['value'] ?? '';
		$value = is_array( $value ) ? implode( ', ', array_map( 'strval', $value ) ) : (string) $value;
		$label = (string) ( $field['title'] ?? '' );
		$label = '' !== $label ? $label : (string) ( $field['id'] ?? $key );

		$generic[]         = array(
			'key'   => (string) ( $field['id'] ?? $key ),
			'label' => $label,
			'type'  => $type,
			'value' => $value,
		);
		$labeled[ $label ] = $value;
	}

	$meta = (array) $record->get( 'meta' );
	$url  = (string) ( $meta['page_url']['value'] ?? '' );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Elementor Pro verified this submission before firing the hook.
	if ( '' === $url && isset( $_POST['referrer'] ) ) {
		$url = esc_url_raw( wp_unslash( $_POST['referrer'] ) );
	}
	$page_id = isset( $_POST['queried_id'] ) ? absint( wp_unslash( $_POST['queried_id'] ) ) : ( isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0 );
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$form = method_exists( $record, 'get_form_settings' ) ? (string) $record->get_form_settings( 'form_name' ) : '';

	bizmax_lead_create(
		bizmax_lead_detect_contact( $generic ) + array(
			'form'       => 'Elementor: ' . ( '' !== $form ? $form : __( 'טופס', 'bizmax' ) ),
			'source_url' => $url,
			'source_id'  => $page_id,
			'fields'     => $labeled,
		)
	);
}
add_action( 'elementor_pro/forms/new_record', 'bizmax_leads_from_elementor', 10, 2 );

/**
 * JetFormBuilder: store every successful submission as a lead.
 *
 * @param object $handler    Jet_Form_Builder\Form_Handler.
 * @param bool   $is_success Whether the submission succeeded.
 */
function bizmax_leads_from_jetformbuilder( $handler, $is_success = false ): void {
	if ( ! $is_success || ! apply_filters( 'bizmax_leads_capture_jetformbuilder', true ) ) {
		return;
	}
	// jet_fb_context() is the current API (3.1+); the request handler is the older fallback.
	$context = function_exists( 'jet_fb_context' ) ? jet_fb_context() : null;
	if ( $context && method_exists( $context, 'get_request' ) ) {
		$request = (array) $context->get_request();
	} elseif ( function_exists( 'jet_fb_request_handler' ) ) {
		$request = (array) jet_fb_request_handler()->get_request();
	} else {
		return;
	}

	$skip_types = array( 'submit-field', 'form-break-field', 'heading-field', 'group-break-field', 'conditional-block', 'captcha-container', 'hidden-field' );
	$generic    = array();
	$labeled    = array();
	foreach ( $request as $key => $value ) {
		$key = (string) $key;
		// Internal fields (form id, referrer, nonces, honeypot) start with "_" in JetFormBuilder.
		if ( '' === $key || str_starts_with( $key, '_' ) || in_array( $key, array( 'action', 'jet-form-builder-honeypot' ), true ) ) {
			continue;
		}
		$type  = '';
		$label = '';
		if ( $context && method_exists( $context, 'get_field_type' ) ) {
			try {
				$type  = (string) $context->get_field_type( $key );
				$label = (string) $context->get_setting( 'label', $key );
			} catch ( \Throwable $e ) {
				$type  = '';
				$label = '';
			}
		}
		if ( in_array( $type, $skip_types, true ) ) {
			continue;
		}
		$value = is_array( $value ) ? implode( ', ', array_map( 'strval', array_filter( $value, 'is_scalar' ) ) ) : ( is_scalar( $value ) ? (string) $value : '' );
		$label = '' !== trim( $label ) ? wp_strip_all_tags( $label ) : $key;

		$generic[]         = array(
			'key'   => $key,
			'label' => $label,
			'type'  => str_replace( array( 'text-field', '-field' ), array( '', '' ), $type ),
			'value' => $value,
		);
		$labeled[ $label ] = $value;
	}

	$form_id = ( is_object( $handler ) && method_exists( $handler, 'get_form_id' ) ) ? (int) $handler->get_form_id() : 0;
	$referer = ( is_object( $handler ) && method_exists( $handler, 'get_referrer' ) ) ? (string) $handler->get_referrer() : '';
	if ( '' === $referer ) {
		$referer = (string) ( $request['__refer'] ?? wp_get_referer() );
	}

	bizmax_lead_create(
		bizmax_lead_detect_contact( $generic ) + array(
			'form'       => 'JetFormBuilder: ' . ( $form_id ? get_the_title( $form_id ) : __( 'טופס', 'bizmax' ) ),
			'source_url' => remove_query_arg( array( 'status', 'jfb_nonce' ), $referer ),
			'source_id'  => absint( $request['__queried_post_id'] ?? 0 ),
			'fields'     => $labeled,
		)
	);
}
add_action( 'jet-form-builder/form-handler/after-send', 'bizmax_leads_from_jetformbuilder', 10, 2 );

/**
 * Count leads by status (cached by core in the "counts" group).
 *
 * @return array<string,int>
 */
function bizmax_lead_counts(): array {
	$counts = wp_count_posts( BIZMAX_LEAD_PT );
	$out    = array();
	foreach ( array_keys( bizmax_lead_statuses() ) as $status ) {
		$out[ $status ] = (int) ( $counts->$status ?? 0 );
	}
	return $out;
}

/**
 * Personal data exporter: leads submitted with an email address.
 *
 * @param string $email Email.
 * @param int    $page  Page.
 * @return array{data:array<int,array<string,mixed>>,done:bool}
 */
function bizmax_leads_privacy_export( string $email, int $page = 1 ): array {
	$ids  = bizmax_leads_by_email( $email, $page );
	$data = array();
	foreach ( $ids as $id ) {
		$items = array(
			array(
				'name'  => __( 'שם', 'bizmax' ),
				'value' => get_the_title( $id ),
			),
			array(
				'name'  => __( 'תאריך', 'bizmax' ),
				'value' => get_the_date( 'd/m/Y H:i', $id ),
			),
			array(
				'name'  => __( 'טופס', 'bizmax' ),
				'value' => (string) get_post_meta( $id, '_bz_lead_form', true ),
			),
		);
		foreach ( bizmax_lead_meta_array( $id, '_bz_lead_fields' ) as $label => $value ) {
			$items[] = array(
				'name'  => (string) $label,
				'value' => (string) $value,
			);
		}
		$data[] = array(
			'group_id'    => 'bizmax-leads',
			'group_label' => __( 'פניות מטפסי האתר', 'bizmax' ),
			'item_id'     => 'bizmax-lead-' . $id,
			'data'        => $items,
		);
	}
	return array(
		'data' => $data,
		'done' => count( $ids ) < 50,
	);
}

/**
 * Personal data eraser: delete leads submitted with an email address.
 *
 * @param string $email Email.
 * @param int    $page  Page.
 * @return array{items_removed:bool,items_retained:bool,messages:array<int,string>,done:bool}
 */
function bizmax_leads_privacy_erase( string $email, int $page = 1 ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- always page 1: erased leads leave the result set.
	$ids = bizmax_leads_by_email( $email, 1 );
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	return array(
		'items_removed'  => (bool) $ids,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => count( $ids ) < 50,
	);
}

/**
 * Lead IDs for an email address (50 per page).
 *
 * @param string $email Email.
 * @param int    $page  Page.
 * @return int[]
 */
function bizmax_leads_by_email( string $email, int $page ): array {
	$email = sanitize_email( $email );
	if ( ! is_email( $email ) ) {
		return array();
	}
	return array_map(
		'intval',
		get_posts(
			array(
				'post_type'              => BIZMAX_LEAD_PT,
				'post_status'            => array_merge( array_keys( bizmax_lead_statuses() ), array( 'trash' ) ),
				'posts_per_page'         => 50,
				'paged'                  => max( 1, $page ),
				'fields'                 => 'ids',
				'meta_key'               => '_bz_lead_email', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- privacy request, admin only.
				'meta_value'             => $email, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		)
	);
}

/**
 * Register leads with the core personal data tools (Tools → Export/Erase Personal Data).
 *
 * @param array<string,array<string,mixed>> $tools Exporters or erasers.
 * @return array<string,array<string,mixed>>
 */
function bizmax_leads_register_exporter( array $tools ): array {
	$tools['bizmax-leads'] = array(
		'exporter_friendly_name' => __( 'לידים מטפסי האתר', 'bizmax' ),
		'callback'               => 'bizmax_leads_privacy_export',
	);
	return $tools;
}
add_filter( 'wp_privacy_personal_data_exporters', 'bizmax_leads_register_exporter' );

/**
 * Register the eraser.
 *
 * @param array<string,array<string,mixed>> $tools Erasers.
 * @return array<string,array<string,mixed>>
 */
function bizmax_leads_register_eraser( array $tools ): array {
	$tools['bizmax-leads'] = array(
		'eraser_friendly_name' => __( 'לידים מטפסי האתר', 'bizmax' ),
		'callback'             => 'bizmax_leads_privacy_erase',
	);
	return $tools;
}
add_filter( 'wp_privacy_personal_data_erasers', 'bizmax_leads_register_eraser' );
