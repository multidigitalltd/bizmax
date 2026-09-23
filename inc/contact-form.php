<?php
/**
 * Contact form: REST endpoints with nonce, honeypot, timing check, rate limiting,
 * full server-side validation and wp_mail delivery.
 *
 * The nonce is fetched lazily by JS (never baked into cached HTML), so the form
 * keeps working behind LiteSpeed / Cloudflare full-page cache.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

const BIZMAX_CONTACT_NONCE = 'bizmax_contact';

/**
 * Register REST routes.
 */
function bizmax_contact_routes(): void {
	register_rest_route(
		'bizmax/v1',
		'/contact/token',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'bizmax_contact_token',
			'permission_callback' => '__return_true', // Public form; the token itself carries no privileges.
		)
	);

	register_rest_route(
		'bizmax/v1',
		'/contact',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'bizmax_contact_submit',
			'permission_callback' => 'bizmax_contact_permission',
			'args'                => array(
				'name'    => array( 'type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
				'email'   => array( 'type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_email' ),
				'phone'   => array( 'type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
				'privacy' => array( 'type' => 'boolean', 'required' => true ),
				'token'   => array( 'type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
				'ts'      => array( 'type' => 'integer', 'required' => true ),
				'website' => array( 'type' => 'string', 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ), // Honeypot.
				'page'    => array( 'type' => 'integer', 'required' => false ),
			),
		)
	);
}
add_action( 'rest_api_init', 'bizmax_contact_routes' );

/**
 * Issue a nonce + timestamp for the form (never cached).
 */
function bizmax_contact_token(): WP_REST_Response {
	$response = new WP_REST_Response(
		array(
			'token' => wp_create_nonce( BIZMAX_CONTACT_NONCE ),
			'ts'    => time(),
		)
	);
	$response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0' );
	return $response;
}

/**
 * Permission: valid nonce and not rate limited.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function bizmax_contact_permission( WP_REST_Request $request ) {
	if ( ! wp_verify_nonce( (string) $request->get_param( 'token' ), BIZMAX_CONTACT_NONCE ) ) {
		return new WP_Error( 'bizmax_bad_token', __( 'פג תוקף הטופס, רעננו את הדף ונסו שוב.', 'bizmax' ), array( 'status' => 403 ) );
	}
	if ( bizmax_contact_is_rate_limited() ) {
		return new WP_Error( 'bizmax_rate_limited', __( 'נשלחו יותר מדי פניות, נסו שוב בעוד מספר דקות.', 'bizmax' ), array( 'status' => 429 ) );
	}
	return true;
}

/**
 * Rate limit: max 5 submissions per IP per 10 minutes (transient; works with object cache).
 */
function bizmax_contact_is_rate_limited(): bool {
	$key   = 'bizmax_cf_' . md5( bizmax_client_ip() . wp_salt( 'nonce' ) );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return true;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

/**
 * Client IP, honouring Cloudflare's header when present.
 */
function bizmax_client_ip(): string {
	$candidates = array( 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR' );
	foreach ( $candidates as $header ) {
		if ( ! empty( $_SERVER[ $header ] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '0.0.0.0';
}

/**
 * Normalize and validate an Israeli phone number. Returns '' when invalid.
 *
 * Accepts 0X-XXXXXXX, 05X-XXXXXXX, +972…, with spaces or dashes.
 *
 * @param string $raw Raw input.
 */
function bizmax_normalize_phone( string $raw ): string {
	$digits = preg_replace( '/\D+/', '', $raw ) ?? '';
	if ( str_starts_with( $digits, '972' ) ) {
		$digits = '0' . substr( $digits, 3 );
	}
	return preg_match( '/^0\d{8,9}$/', $digits ) ? $digits : '';
}

/**
 * Handle a submission.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function bizmax_contact_submit( WP_REST_Request $request ) {
	if ( '' !== (string) $request->get_param( 'website' ) ) {
		// Honeypot filled by a bot: pretend success without sending anything.
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}

	$name    = trim( (string) $request->get_param( 'name' ) );
	$email   = (string) $request->get_param( 'email' );
	$phone   = bizmax_normalize_phone( (string) $request->get_param( 'phone' ) );
	$privacy = rest_sanitize_boolean( $request->get_param( 'privacy' ) );
	$ts      = (int) $request->get_param( 'ts' );

	$errors = array();
	if ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 80 ) {
		$errors['name'] = __( 'נא להזין שם מלא.', 'bizmax' );
	}
	if ( ! is_email( $email ) ) {
		$errors['email'] = __( 'נא להזין כתובת מייל תקינה.', 'bizmax' );
	}
	if ( '' === $phone ) {
		$errors['phone'] = __( 'נא להזין מספר טלפון ישראלי תקין.', 'bizmax' );
	}
	if ( ! $privacy ) {
		$errors['privacy'] = __( 'יש לאשר את מדיניות הפרטיות.', 'bizmax' );
	}
	if ( $ts <= 0 || ( time() - $ts ) < 3 ) {
		// Submitted faster than a human can type: treat as spam.
		$errors['form'] = __( 'השליחה נכשלה, נסו שוב.', 'bizmax' );
	}
	if ( $errors ) {
		return new WP_Error( 'bizmax_invalid', __( 'נא לתקן את השדות המסומנים.', 'bizmax' ), array( 'status' => 422, 'fields' => $errors ) );
	}

	$to = sanitize_email( (string) bizmax_mod( 'bizmax_form_to' ) );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$subject = 'פנייה חדשה מאתר ביזמקס';
	$page_id = (int) $request->get_param( 'page' );
	if ( $page_id > 0 ) {
		$content = bizmax_home_get( $page_id );
		$subject = $content['more']['form_subject'] ?: $subject;
	}

	$body  = sprintf( "%s: %s\n", __( 'שם', 'bizmax' ), $name );
	$body .= sprintf( "%s: %s\n", __( 'מייל', 'bizmax' ), $email );
	$body .= sprintf( "%s: %s\n", __( 'טלפון', 'bizmax' ), $phone );
	$body .= sprintf( "%s: %s\n", __( 'נשלח מ', 'bizmax' ), home_url( '/' ) );
	$body .= sprintf( "%s: %s\n", __( 'תאריך', 'bizmax' ), wp_date( 'd/m/Y H:i' ) );

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, sanitize_text_field( $subject ), $body, $headers );

	/**
	 * Fires after a valid contact submission (CRM/webhook integrations hook here).
	 *
	 * @param array{name:string,email:string,phone:string} $lead Lead data.
	 * @param bool                                          $sent Whether wp_mail reported success.
	 */
	do_action( 'bizmax_contact_submitted', compact( 'name', 'email', 'phone' ), $sent );

	if ( ! $sent ) {
		return new WP_Error( 'bizmax_mail_failed', __( 'השליחה נכשלה, נסו שוב בעוד רגע.', 'bizmax' ), array( 'status' => 500 ) );
	}

	return new WP_REST_Response( array( 'ok' => true ), 200 );
}
