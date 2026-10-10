<?php
/**
 * Cloudflare Turnstile on the theme's contact form, through the "Simple Cloudflare Turnstile"
 * plugin (simple-cloudflare-turnstile). The plugin owns the keys and every setting (theme,
 * language, appearance, whitelist, failsafe); the theme only renders its widget and verifies
 * the token with the plugin's own functions. Without the plugin, or without keys, the form
 * works as before (nonce, honeypot, timing check and rate limit stay in place either way).
 *
 * The widget is static markup and the token is produced in the browser, so cached pages
 * (LiteSpeed, Cloudflare) stay safe.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether Turnstile should protect the contact form.
 */
function bizmax_turnstile_enabled(): bool {
	$enabled = function_exists( 'cfturnstile_field_show' )
		&& function_exists( 'cfturnstile_check' )
		&& '' !== (string) get_option( 'cfturnstile_key' )
		&& '' !== (string) get_option( 'cfturnstile_secret' )
		&& (bool) bizmax_mod( 'bizmax_turnstile' );

	/**
	 * Filter whether the theme contact form uses Cloudflare Turnstile.
	 *
	 * @param bool $enabled Enabled.
	 */
	return (bool) apply_filters( 'bizmax_turnstile_enabled', $enabled );
}

/**
 * Print the Turnstile widget inside a form (nothing when disabled or the visitor is whitelisted).
 *
 * @param string $form Form identifier, sent to Cloudflare as the widget "action" (analytics).
 */
function bizmax_turnstile_field( string $form = 'bizmax-contact' ): void {
	if ( ! bizmax_turnstile_enabled() ) {
		return;
	}
	echo '<div class="bz-form__turnstile" data-bz-turnstile>';
	cfturnstile_field_show( '', '', $form, '-' . sanitize_html_class( $form ), 'bz-turnstile' );
	echo '</div>';
}

/**
 * Verify the Turnstile response sent with a REST request.
 *
 * The contact form posts JSON, while the plugin reads its failsafe markers from $_POST, so the
 * plugin's fields are copied over for this request only (the plugin sanitizes what it reads).
 *
 * @param WP_REST_Request $request Request.
 * @param string          $form    Form identifier.
 * @return true|WP_Error
 */
function bizmax_turnstile_verify( WP_REST_Request $request, string $form = 'bizmax-contact' ) {
	if ( ! bizmax_turnstile_enabled() ) {
		return true;
	}

	foreach ( array( 'cf-turnstile-response', 'cfturnstile_failsafe', 'g-recaptcha-response' ) as $field ) {
		$value = $request->get_param( $field );
		if ( is_string( $value ) && '' !== $value ) {
			$_POST[ $field ] = sanitize_text_field( $value ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- REST route verifies its own nonce in permission_callback.
		}
	}

	$token  = sanitize_text_field( (string) $request->get_param( 'cf-turnstile-response' ) );
	$result = cfturnstile_check( $token, $form );

	if ( ! empty( $result['success'] ) ) {
		return true;
	}

	$message = (string) get_option( 'cfturnstile_error_message' );
	if ( '' === trim( $message ) ) {
		$message = __( 'אימות האבטחה של Cloudflare לא הושלם. נסו שוב.', 'bizmax' );
	}
	return new WP_Error( 'bizmax_turnstile', wp_strip_all_tags( $message ), array( 'status' => 403 ) );
}
