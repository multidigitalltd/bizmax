<?php
/**
 * Site notice popup ("האתר מתחדש"), shown once per visitor on any page.
 *
 * A native modal <dialog> printed after <body>. Whether it was already closed is kept in the
 * visitor's localStorage, so the page HTML stays identical for everyone and fully cacheable.
 * Editing the title or text gives the notice a new id, so it shows again to everyone.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the popup is enabled (Customizer → ביזמקס – הודעה קופצת).
 */
function bizmax_notice_enabled(): bool {
	$enabled = (bool) bizmax_mod( 'bizmax_notice' ) && '' !== trim( (string) bizmax_mod( 'bizmax_notice_text' ) );
	// Not inside the Elementor editor preview.
	if ( $enabled && isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only context check.
		$enabled = false;
	}
	return (bool) apply_filters( 'bizmax_notice_enabled', $enabled );
}

/**
 * Enqueue the popup assets.
 */
function bizmax_notice_enqueue(): void {
	if ( ! bizmax_notice_enabled() ) {
		return;
	}
	wp_enqueue_style( 'bizmax-notice', bizmax_asset( 'css/notice.css' ), array(), BIZMAX_VERSION );
	wp_enqueue_script( 'bizmax-notice', bizmax_asset( 'js/notice.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'bizmax_notice_enqueue' );

/**
 * Print the popup right after <body>.
 */
function bizmax_notice_render(): void {
	if ( ! bizmax_notice_enabled() ) {
		return;
	}
	$title  = (string) bizmax_mod( 'bizmax_notice_title' );
	$text   = (string) bizmax_mod( 'bizmax_notice_text' );
	$button = (string) bizmax_mod( 'bizmax_notice_button' );
	$id     = substr( md5( $title . '|' . $text ), 0, 10 );
	?>
<dialog class="bz-notice" id="bz-notice" data-bz-notice="<?php echo esc_attr( $id ); ?>" aria-labelledby="<?php echo '' !== $title ? 'bz-notice-title' : 'bz-notice-text'; ?>"<?php echo '' !== $title ? ' aria-describedby="bz-notice-text"' : ''; ?>>
	<div class="bz-notice__box">
		<button type="button" class="bz-notice__close" aria-label="<?php esc_attr_e( 'סגירת ההודעה', 'bizmax' ); ?>" data-bz-notice-close><?php bizmax_icon( 'x', 20 ); ?></button>
		<span class="bz-notice__icon"><?php bizmax_icon( 'sparkles', 30 ); ?></span>
		<?php if ( '' !== $title ) : ?>
			<h2 class="bz-notice__title" id="bz-notice-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<p class="bz-notice__text" id="bz-notice-text"><?php echo nl2br( esc_html( $text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped before nl2br. ?></p>
		<button type="button" class="bz-notice__btn" data-bz-notice-close autofocus><?php echo esc_html( '' !== $button ? $button : __( 'הבנתי', 'bizmax' ) ); ?></button>
	</div>
</dialog>
	<?php
}
add_action( 'wp_body_open', 'bizmax_notice_render', 25 );
