<?php
/**
 * Lead center admin screens: list with inline status, filters, bulk actions, CSV export,
 * lead details with notes and status history, menu badge and dashboard widget.
 *
 * Every action checks a nonce and the user's capability for the specific lead.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

// Menu badge: number of new leads.

/**
 * Append the "new leads" count to the admin menu item.
 */
function bizmax_leads_menu_badge(): void {
	global $menu;
	$new = bizmax_lead_counts()['bz_new'] ?? 0;
	if ( ! $new || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=' . BIZMAX_LEAD_PT === $item[2] ) {
			$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod count-%1$d"><span class="pending-count" aria-hidden="true">%1$d</span><span class="screen-reader-text">%2$s</span></span>', $new, esc_html( sprintf( /* translators: %d: count. */ _n( '%d ליד חדש', '%d לידים חדשים', $new, 'bizmax' ), $new ) ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- standard way to add a menu bubble.
			break;
		}
	}
}
add_action( 'admin_menu', 'bizmax_leads_menu_badge', 99 );

// List table.

/**
 * Columns.
 *
 * @return array<string,string>
 */
function bizmax_leads_columns(): array {
	return array(
		'cb'          => '<input type="checkbox" />',
		'title'       => __( 'שם', 'bizmax' ),
		'bz_contact'  => __( 'פרטי קשר', 'bizmax' ),
		'bz_form'     => __( 'טופס', 'bizmax' ),
		'bz_status'   => __( 'סטטוס', 'bizmax' ),
		'bz_received' => __( 'התקבל', 'bizmax' ),
	);
}
add_filter( 'manage_' . BIZMAX_LEAD_PT . '_posts_columns', 'bizmax_leads_columns' );

/**
 * Sortable columns.
 *
 * @param array<string,string> $columns Columns.
 * @return array<string,string>
 */
function bizmax_leads_sortable( array $columns ): array {
	$columns['bz_received'] = 'date';
	$columns['title']       = 'title';
	return $columns;
}
add_filter( 'manage_edit-' . BIZMAX_LEAD_PT . '_sortable_columns', 'bizmax_leads_sortable' );

/**
 * WhatsApp link for an Israeli or international number ('' when not usable).
 *
 * @param string $phone Phone.
 */
function bizmax_lead_whatsapp_url( string $phone ): string {
	$digits = preg_replace( '/\D+/', '', $phone );
	if ( str_starts_with( (string) $digits, '0' ) ) {
		$digits = '972' . substr( $digits, 1 );
	}
	return strlen( (string) $digits ) >= 10 ? 'https://wa.me/' . $digits : '';
}

/**
 * Render a status dropdown.
 *
 * @param int    $id      Lead ID.
 * @param string $current Current status.
 * @param string $label   Accessible label.
 */
function bizmax_lead_status_select( int $id, string $current, string $label ): void {
	printf( '<select class="bz-lead-status" data-id="%1$d" data-status="%2$s" aria-label="%3$s">', (int) $id, esc_attr( $current ), esc_attr( $label ) );
	foreach ( bizmax_lead_statuses() as $status => [ $name ] ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $status ), selected( $current, $status, false ), esc_html( $name ) );
	}
	echo '</select>';
}

/**
 * Column content.
 *
 * @param string $column  Column.
 * @param int    $post_id Lead ID.
 */
function bizmax_leads_column( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'bz_contact':
			$phone = (string) get_post_meta( $post_id, '_bz_lead_phone', true );
			$email = (string) get_post_meta( $post_id, '_bz_lead_email', true );
			if ( '' !== $phone ) {
				$wa = bizmax_lead_whatsapp_url( $phone );
				printf( '<a href="tel:%1$s" dir="ltr">%2$s</a>', esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ), esc_html( $phone ) );
				if ( $wa ) {
					printf( ' · <a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<span class="screen-reader-text"> %3$s</span></a>', esc_url( $wa ), esc_html__( 'וואטסאפ', 'bizmax' ), esc_html__( '(נפתח בחלון חדש)', 'bizmax' ) );
				}
				echo '<br>';
			}
			if ( '' !== $email ) {
				printf( '<a href="mailto:%1$s" dir="ltr">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
			}
			break;

		case 'bz_form':
			$form = (string) get_post_meta( $post_id, '_bz_lead_form', true );
			$url  = (string) get_post_meta( $post_id, '_bz_lead_source_url', true );
			echo esc_html( '' !== $form ? $form : '—' );
			if ( '' !== $url ) {
				$path = (string) wp_parse_url( $url, PHP_URL_PATH );
				printf( '<br><a href="%1$s" target="_blank" rel="noopener noreferrer" class="bz-lead-page" dir="ltr">%2$s</a>', esc_url( $url ), esc_html( urldecode( '' !== $path ? $path : $url ) ) );
			}
			break;

		case 'bz_status':
			bizmax_lead_status_select( $post_id, (string) get_post_status( $post_id ), sprintf( /* translators: %s: lead name. */ __( 'סטטוס הליד: %s', 'bizmax' ), get_the_title( $post_id ) ) );
			break;

		case 'bz_received':
			printf( '<time datetime="%1$s">%2$s</time>', esc_attr( (string) get_the_date( 'c', $post_id ) ), esc_html( (string) get_the_date( 'd/m/Y H:i', $post_id ) ) );
			break;
	}
}
add_action( 'manage_' . BIZMAX_LEAD_PT . '_posts_custom_column', 'bizmax_leads_column', 10, 2 );

/**
 * Row actions: no quick edit (it has no lead statuses) and no "view" (leads are private).
 *
 * @param array<string,string> $actions Actions.
 * @param WP_Post              $post    Post.
 * @return array<string,string>
 */
function bizmax_leads_row_actions( array $actions, WP_Post $post ): array {
	if ( BIZMAX_LEAD_PT === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'], $actions['view'] );
		if ( isset( $actions['edit'] ) ) {
			$actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( (string) get_edit_post_link( $post->ID ) ), esc_html__( 'פרטים והערות', 'bizmax' ) );
		}
	}
	return $actions;
}
add_filter( 'post_row_actions', 'bizmax_leads_row_actions', 10, 2 );

/**
 * Distinct form names, for the filter dropdown.
 *
 * @return string[]
 */
function bizmax_leads_form_names(): array {
	global $wpdb;
	$names = wp_cache_get( 'bizmax_lead_forms', 'bizmax' );
	if ( false === $names ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- cached below; admin only.
		$names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_type = %s AND pm.meta_value <> '' ORDER BY pm.meta_value ASC LIMIT 500",
				'_bz_lead_form',
				BIZMAX_LEAD_PT
			)
		);
		wp_cache_set( 'bizmax_lead_forms', $names, 'bizmax', 5 * MINUTE_IN_SECONDS );
	}
	return array_map( 'strval', (array) $names );
}

/**
 * The form filter currently requested in the list ('' for all).
 */
function bizmax_leads_current_form(): string {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	return isset( $_GET['bz_form'] ) ? sanitize_text_field( wp_unslash( $_GET['bz_form'] ) ) : '';
}

/**
 * Form filter and CSV export above the list.
 *
 * @param string $post_type Post type.
 */
function bizmax_leads_filters( string $post_type ): void {
	if ( BIZMAX_LEAD_PT !== $post_type ) {
		return;
	}
	$current = bizmax_leads_current_form();
	echo '<label class="screen-reader-text" for="bz-form-filter">' . esc_html__( 'סינון לפי טופס', 'bizmax' ) . '</label>';
	echo '<select name="bz_form" id="bz-form-filter"><option value="">' . esc_html__( 'כל הטפסים', 'bizmax' ) . '</option>';
	foreach ( bizmax_leads_form_names() as $name ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $name ), selected( $current, $name, false ), esc_html( $name ) );
	}
	echo '</select>';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	$status = isset( $_GET['post_status'] ) ? sanitize_key( wp_unslash( $_GET['post_status'] ) ) : '';
	$export = wp_nonce_url(
		add_query_arg(
			array_filter(
				array(
					'action'      => 'bizmax_leads_export',
					'post_status' => $status,
					'bz_form'     => $current,
				)
			),
			admin_url( 'admin-post.php' )
		),
		'bizmax_leads_export'
	);
	printf( ' <a class="button" href="%1$s">%2$s</a>', esc_url( $export ), esc_html__( 'ייצוא ל-CSV (Excel)', 'bizmax' ) );
}
add_action( 'restrict_manage_posts', 'bizmax_leads_filters' );

/**
 * Apply the form filter to the list query.
 *
 * @param WP_Query $query Query.
 */
function bizmax_leads_filter_query( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || BIZMAX_LEAD_PT !== $query->get( 'post_type' ) ) {
		return;
	}
	$form = bizmax_leads_current_form();
	if ( '' !== $form ) {
		$query->set( 'meta_key', '_bz_lead_form' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- admin list filter.
		$query->set( 'meta_value', $form ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	}
}
add_action( 'pre_get_posts', 'bizmax_leads_filter_query' );

// Status changes: inline (AJAX), bulk, edit screen, history log.

/**
 * Whether a lead status key is valid.
 *
 * @param string $status Status.
 */
function bizmax_lead_status_valid( string $status ): bool {
	return array_key_exists( $status, bizmax_lead_statuses() );
}

/**
 * Change a lead's status (capability-checked).
 *
 * @param int    $id     Lead ID.
 * @param string $status New status.
 */
function bizmax_lead_set_status( int $id, string $status ): bool {
	if ( BIZMAX_LEAD_PT !== get_post_type( $id ) || ! bizmax_lead_status_valid( $status ) || ! current_user_can( 'edit_post', $id ) ) {
		return false;
	}
	$result = wp_update_post(
		array(
			'ID'          => $id,
			'post_status' => $status,
		),
		true
	);
	return ! is_wp_error( $result );
}

/**
 * AJAX: inline status change from the list.
 */
function bizmax_leads_ajax_status(): void {
	check_ajax_referer( 'bizmax_lead_status', 'nonce' );
	$id     = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
	$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
	if ( ! bizmax_lead_set_status( $id, $status ) ) {
		wp_send_json_error( array( 'message' => __( 'לא ניתן לעדכן את הסטטוס.', 'bizmax' ) ), 403 );
	}
	wp_send_json_success(
		array(
			'status' => $status,
			'new'    => bizmax_lead_counts()['bz_new'] ?? 0,
		)
	);
}
add_action( 'wp_ajax_bizmax_lead_status', 'bizmax_leads_ajax_status' );

/**
 * Bulk actions: set a status for many leads. Bulk edit is removed (it has no lead statuses).
 *
 * @param array<string,string> $actions Actions.
 * @return array<string,string>
 */
function bizmax_leads_bulk_actions( array $actions ): array {
	unset( $actions['edit'] );
	foreach ( bizmax_lead_statuses() as $status => [ $label ] ) {
		/* translators: %s: status name. */
		$actions[ 'bz_set_' . $status ] = sprintf( __( 'סימון כ"%s"', 'bizmax' ), $label );
	}
	return $actions;
}
add_filter( 'bulk_actions-edit-' . BIZMAX_LEAD_PT, 'bizmax_leads_bulk_actions' );

/**
 * Handle bulk status actions (core verifies the list nonce before calling this).
 *
 * @param string $redirect Redirect URL.
 * @param string $action   Action.
 * @param int[]  $ids      Lead IDs.
 */
function bizmax_leads_handle_bulk( string $redirect, string $action, array $ids ): string {
	if ( ! str_starts_with( $action, 'bz_set_' ) ) {
		return $redirect;
	}
	$status = substr( $action, 7 );
	$done   = 0;
	foreach ( $ids as $id ) {
		$done += bizmax_lead_set_status( (int) $id, $status ) ? 1 : 0;
	}
	return add_query_arg( 'bz_updated', $done, $redirect );
}
add_filter( 'handle_bulk_actions-edit-' . BIZMAX_LEAD_PT, 'bizmax_leads_handle_bulk', 10, 3 );

/**
 * Notice after a bulk status change.
 */
function bizmax_leads_bulk_notice(): void {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only count from our redirect.
	if ( ! isset( $_GET['bz_updated'] ) || 'edit-' . BIZMAX_LEAD_PT !== ( get_current_screen()->id ?? '' ) ) {
		return;
	}
	$n = absint( wp_unslash( $_GET['bz_updated'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	/* translators: %d: number of leads. */
	printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( sprintf( _n( 'ליד %d עודכן.', '%d לידים עודכנו.', $n, 'bizmax' ), $n ) ) );
}
add_action( 'admin_notices', 'bizmax_leads_bulk_notice' );

/**
 * Keep leads on lead statuses whatever posts them (edit screen, core actions).
 *
 * @param array<string,mixed> $data    Post data.
 * @param array<string,mixed> $postarr Raw post array.
 * @return array<string,mixed>
 */
function bizmax_leads_guard_status( array $data, array $postarr ): array {
	if ( BIZMAX_LEAD_PT !== ( $data['post_type'] ?? '' ) ) {
		return $data;
	}
	$status = (string) ( $data['post_status'] ?? '' );
	if ( 'trash' !== $status && ! bizmax_lead_status_valid( $status ) ) {
		$previous            = ! empty( $postarr['ID'] ) ? (string) get_post_status( (int) $postarr['ID'] ) : '';
		$data['post_status'] = bizmax_lead_status_valid( $previous ) ? $previous : 'bz_new';
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'bizmax_leads_guard_status', 10, 2 );

/**
 * Status history: who changed what, and when.
 *
 * @param string  $new_status New status.
 * @param string  $old_status Old status.
 * @param WP_Post $post       Lead.
 */
function bizmax_leads_log_status( string $new_status, string $old_status, WP_Post $post ): void {
	if ( BIZMAX_LEAD_PT !== $post->post_type || $new_status === $old_status || 'new' === $old_status ) {
		return;
	}
	$log   = bizmax_lead_meta_array( $post->ID, '_bz_lead_log' );
	$log[] = array(
		't'    => time(),
		'u'    => get_current_user_id(),
		'from' => $old_status,
		'to'   => $new_status,
	);
	update_post_meta( $post->ID, '_bz_lead_log', array_slice( $log, -100 ) );
}
add_action( 'transition_post_status', 'bizmax_leads_log_status', 10, 3 );

// Lead details screen.

/**
 * Replace the publish box with lead boxes.
 */
function bizmax_leads_meta_boxes(): void {
	remove_meta_box( 'submitdiv', BIZMAX_LEAD_PT, 'side' );
	remove_meta_box( 'slugdiv', BIZMAX_LEAD_PT, 'normal' );
	add_meta_box( 'bz-lead-details', __( 'פרטי הפנייה', 'bizmax' ), 'bizmax_lead_box_details', BIZMAX_LEAD_PT, 'normal', 'high' );
	add_meta_box( 'bz-lead-manage', __( 'סטטוס והערות', 'bizmax' ), 'bizmax_lead_box_manage', BIZMAX_LEAD_PT, 'side', 'high' );
	add_meta_box( 'bz-lead-history', __( 'היסטוריית טיפול', 'bizmax' ), 'bizmax_lead_box_history', BIZMAX_LEAD_PT, 'side', 'default' );
}
add_action( 'add_meta_boxes_' . BIZMAX_LEAD_PT, 'bizmax_leads_meta_boxes' );

/**
 * Details box: everything the visitor sent.
 *
 * @param WP_Post $post Lead.
 */
function bizmax_lead_box_details( WP_Post $post ): void {
	$rows = array(
		__( 'התקבל', 'bizmax' ) => get_the_date( 'd/m/Y H:i', $post ),
		__( 'טופס', 'bizmax' )  => (string) get_post_meta( $post->ID, '_bz_lead_form', true ),
	);
	echo '<table class="widefat striped bz-lead-table"><tbody>';
	foreach ( $rows as $label => $value ) {
		printf( '<tr><th scope="row">%1$s</th><td>%2$s</td></tr>', esc_html( $label ), esc_html( (string) $value ) );
	}
	$url = (string) get_post_meta( $post->ID, '_bz_lead_source_url', true );
	if ( '' !== $url ) {
		printf( '<tr><th scope="row">%1$s</th><td><a href="%2$s" target="_blank" rel="noopener noreferrer" dir="ltr">%3$s</a></td></tr>', esc_html__( 'נשלח מהעמוד', 'bizmax' ), esc_url( $url ), esc_html( urldecode( $url ) ) );
	}
	foreach ( bizmax_lead_meta_array( $post->ID, '_bz_lead_fields' ) as $label => $value ) {
		printf( '<tr><th scope="row">%1$s</th><td>%2$s</td></tr>', esc_html( (string) $label ), nl2br( esc_html( (string) $value ) ) );
	}
	$mail = (string) get_post_meta( $post->ID, '_bz_lead_mail', true );
	if ( 'failed' === $mail ) {
		printf( '<tr><th scope="row">%1$s</th><td><strong>%2$s</strong></td></tr>', esc_html__( 'מייל התראה', 'bizmax' ), esc_html__( 'שליחת המייל נכשלה – הפנייה נשמרה כאן.', 'bizmax' ) );
	}
	echo '</tbody></table>';

	$phone = (string) get_post_meta( $post->ID, '_bz_lead_phone', true );
	$email = (string) get_post_meta( $post->ID, '_bz_lead_email', true );
	echo '<p class="bz-lead-actions">';
	if ( '' !== $phone ) {
		printf( '<a class="button" href="tel:%1$s">%2$s <span dir="ltr">%3$s</span></a> ', esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ), esc_html__( 'חיוג', 'bizmax' ), esc_html( $phone ) );
		$wa = bizmax_lead_whatsapp_url( $phone );
		if ( $wa ) {
			printf( '<a class="button" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<span class="screen-reader-text"> %3$s</span></a> ', esc_url( $wa ), esc_html__( 'וואטסאפ', 'bizmax' ), esc_html__( '(נפתח בחלון חדש)', 'bizmax' ) );
		}
	}
	if ( '' !== $email ) {
		printf( '<a class="button" href="mailto:%1$s">%2$s <span dir="ltr">%1$s</span></a>', esc_attr( $email ), esc_html__( 'מייל', 'bizmax' ) );
	}
	echo '</p>';
}

/**
 * Status and notes box (with the save button that replaces the publish box).
 *
 * @param WP_Post $post Lead.
 */
function bizmax_lead_box_manage( WP_Post $post ): void {
	wp_nonce_field( 'bizmax_lead_edit', '_bz_lead_nonce' );
	echo '<p><label for="bz-lead-status-edit"><strong>' . esc_html__( 'סטטוס', 'bizmax' ) . '</strong></label><br>';
	printf( '<select name="post_status" id="bz-lead-status-edit" class="widefat">' );
	foreach ( bizmax_lead_statuses() as $status => [ $label ] ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $status ), selected( $post->post_status, $status, false ), esc_html( $label ) );
	}
	echo '</select></p>';
	echo '<p><label for="bz-lead-notes"><strong>' . esc_html__( 'הערות פנימיות', 'bizmax' ) . '</strong></label>';
	printf( '<textarea name="bz_lead_notes" id="bz-lead-notes" class="widefat" rows="6">%s</textarea></p>', esc_textarea( (string) get_post_meta( $post->ID, '_bz_lead_notes', true ) ) );
	echo '<div class="bz-lead-submit">';
	submit_button( __( 'שמירה', 'bizmax' ), 'primary', 'save', false );
	if ( current_user_can( 'delete_post', $post->ID ) ) {
		printf( ' <a class="submitdelete deletion" href="%1$s">%2$s</a>', esc_url( (string) get_delete_post_link( $post->ID ) ), esc_html__( 'העברה לפח', 'bizmax' ) );
	}
	echo '</div>';
}

/**
 * History box.
 *
 * @param WP_Post $post Lead.
 */
function bizmax_lead_box_history( WP_Post $post ): void {
	$statuses = bizmax_lead_statuses();
	$log      = array_reverse( bizmax_lead_meta_array( $post->ID, '_bz_lead_log' ) );
	// Scrollable when long, so it is a labelled, focusable region (keyboard scrolling).
	printf( '<div class="bz-lead-log" role="region" tabindex="0" aria-label="%s"><ul>', esc_attr__( 'היסטוריית טיפול', 'bizmax' ) );
	foreach ( $log as $entry ) {
		if ( ! is_array( $entry ) || empty( $entry['t'] ) || empty( $entry['to'] ) ) {
			continue;
		}
		$user = ! empty( $entry['u'] ) ? get_userdata( (int) $entry['u'] ) : false;
		$to   = $statuses[ $entry['to'] ?? '' ][0] ?? (string) ( $entry['to'] ?? '' );
		printf(
			'<li><strong>%1$s</strong> · %2$s<br><span class="description">%3$s</span></li>',
			esc_html( $to ),
			esc_html( $user ? $user->display_name : __( 'מערכת', 'bizmax' ) ),
			esc_html( wp_date( 'd/m/Y H:i', (int) ( $entry['t'] ?? 0 ) ) )
		);
	}
	printf( '<li><strong>%1$s</strong><br><span class="description">%2$s</span></li>', esc_html__( 'הפנייה התקבלה', 'bizmax' ), esc_html( get_the_date( 'd/m/Y H:i', $post ) ) );
	echo '</ul></div>';
}

/**
 * Save notes from the details screen.
 *
 * @param int $post_id Lead ID.
 */
function bizmax_lead_save_notes( int $post_id ): void {
	if ( ! isset( $_POST['_bz_lead_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_bz_lead_nonce'] ) ), 'bizmax_lead_edit' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$notes = isset( $_POST['bz_lead_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bz_lead_notes'] ) ) : '';
	update_post_meta( $post_id, '_bz_lead_notes', $notes );
}
add_action( 'save_post_' . BIZMAX_LEAD_PT, 'bizmax_lead_save_notes' );

// CSV export.

/**
 * Neutralise spreadsheet formulas in a CSV cell (CSV injection).
 *
 * @param string $value Cell.
 */
function bizmax_csv_cell( string $value ): string {
	return preg_match( '/^[=+\-@\t\r]/', $value ) ? "'" . $value : $value;
}

/**
 * Stream the (filtered) leads as CSV.
 */
function bizmax_leads_export(): void {
	check_admin_referer( 'bizmax_leads_export' );
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'אין לך הרשאה.', 'bizmax' ), '', array( 'response' => 403 ) );
	}
	$status = isset( $_GET['post_status'] ) ? sanitize_key( wp_unslash( $_GET['post_status'] ) ) : '';
	$form   = bizmax_leads_current_form();

	$args = array(
		'post_type'              => BIZMAX_LEAD_PT,
		'post_status'            => bizmax_lead_status_valid( $status ) ? $status : array_keys( bizmax_lead_statuses() ),
		'posts_per_page'         => 200, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- export in batches.
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	);
	if ( '' !== $form ) {
		$args['meta_key']   = '_bz_lead_form'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- admin export.
		$args['meta_value'] = $form; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	}

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=leads-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- streaming download.
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- UTF-8 BOM so Excel shows Hebrew correctly.
	fputcsv( $out, array( 'תאריך', 'שם', 'טלפון', 'מייל', 'סטטוס', 'טופס', 'עמוד', 'הערות', 'כל השדות' ), ',', '"', '\\' );

	$statuses = bizmax_lead_statuses();
	for ( $page = 1; $page <= 500; $page++ ) {
		$ids = get_posts(
			$args + array(
				'paged'  => $page,
				'fields' => 'ids',
			)
		);
		if ( ! $ids ) {
			break;
		}
		foreach ( $ids as $id ) {
			$fields = array();
			foreach ( bizmax_lead_meta_array( $id, '_bz_lead_fields' ) as $label => $value ) {
				$fields[] = $label . ': ' . $value;
			}
			$row = array(
				get_the_date( 'Y-m-d H:i', $id ),
				get_the_title( $id ),
				(string) get_post_meta( $id, '_bz_lead_phone', true ),
				(string) get_post_meta( $id, '_bz_lead_email', true ),
				$statuses[ get_post_status( $id ) ][0] ?? '',
				(string) get_post_meta( $id, '_bz_lead_form', true ),
				urldecode( (string) get_post_meta( $id, '_bz_lead_source_url', true ) ),
				(string) get_post_meta( $id, '_bz_lead_notes', true ),
				implode( ' | ', $fields ),
			);
			fputcsv( $out, array_map( 'bizmax_csv_cell', array_map( 'strval', $row ) ), ',', '"', '\\' );
		}
		if ( count( $ids ) < 200 ) {
			break;
		}
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_bizmax_leads_export', 'bizmax_leads_export' );

// Dashboard widget.

/**
 * Register the dashboard widget for users who can see leads.
 */
function bizmax_leads_dashboard_setup(): void {
	if ( current_user_can( 'edit_others_posts' ) ) {
		wp_add_dashboard_widget( 'bizmax_leads', __( 'לידים', 'bizmax' ), 'bizmax_leads_dashboard_widget' );
	}
}
add_action( 'wp_dashboard_setup', 'bizmax_leads_dashboard_setup' );

/**
 * Dashboard widget: counts by status and the latest new leads.
 */
function bizmax_leads_dashboard_widget(): void {
	$list = admin_url( 'edit.php?post_type=' . BIZMAX_LEAD_PT );
	echo '<ul class="bz-lead-counts">';
	foreach ( bizmax_lead_counts() as $status => $count ) {
		printf( '<li class="bz-lead-count bz-lead-count--%1$s"><a href="%2$s"><strong>%3$d</strong> %4$s</a></li>', esc_attr( $status ), esc_url( add_query_arg( 'post_status', $status, $list ) ), (int) $count, esc_html( bizmax_lead_statuses()[ $status ][0] ) );
	}
	echo '</ul>';

	$recent = get_posts(
		array(
			'post_type'              => BIZMAX_LEAD_PT,
			'post_status'            => 'bz_new',
			'posts_per_page'         => 5,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	if ( $recent ) {
		echo '<h3>' . esc_html__( 'לידים חדשים אחרונים', 'bizmax' ) . '</h3><ul class="bz-lead-recent">';
		foreach ( $recent as $lead ) {
			printf(
				'<li><a href="%1$s">%2$s</a> <span class="description">· %3$s · %4$s</span></li>',
				esc_url( (string) get_edit_post_link( $lead->ID ) ),
				esc_html( get_the_title( $lead ) ),
				esc_html( (string) get_post_meta( $lead->ID, '_bz_lead_form', true ) ),
				esc_html( get_the_date( 'd/m H:i', $lead ) )
			);
		}
		echo '</ul>';
	}
	printf( '<p><a class="button" href="%1$s">%2$s</a></p>', esc_url( $list ), esc_html__( 'לכל הלידים', 'bizmax' ) );
}

// Assets (lead screens and the dashboard only).

/**
 * Enqueue the lead screen styles and the inline-status script.
 */
function bizmax_leads_admin_assets(): void {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'edit-' . BIZMAX_LEAD_PT, BIZMAX_LEAD_PT, 'dashboard' ), true ) ) {
		return;
	}
	wp_enqueue_style( 'bizmax-leads', bizmax_asset( 'css/admin-leads.css' ), array(), BIZMAX_VERSION );
	if ( 'edit-' . BIZMAX_LEAD_PT !== $screen->id ) {
		return;
	}
	wp_enqueue_script( 'bizmax-leads', bizmax_asset( 'js/admin-leads.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );
	$colors = array();
	foreach ( bizmax_lead_statuses() as $status => [ $label ] ) {
		$colors[ $status ] = $label;
	}
	wp_localize_script(
		'bizmax-leads',
		'bizmaxLeads',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bizmax_lead_status' ),
			'labels'  => $colors,
			/* translators: %s: status name. */
			'saved'   => __( 'הסטטוס עודכן ל"%s"', 'bizmax' ),
			'failed'  => __( 'לא ניתן לעדכן את הסטטוס, נסו שוב.', 'bizmax' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'bizmax_leads_admin_assets' );
