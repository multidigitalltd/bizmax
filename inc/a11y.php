<?php
/**
 * Built-in accessibility panel (WCAG 2.2 AA / ת"י 5568 helper tools).
 *
 * A floating button opens a native modal <dialog> with text size, contrast and
 * readability adjustments. Everything runs in the visitor's browser and is saved in
 * localStorage, so pages stay fully cacheable (LiteSpeed, Cloudflare, Redis): the
 * server output is identical for every visitor and nothing is sent back to the site.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the panel is enabled (Customizer → ביזמקס – נגישות).
 */
function bizmax_a11y_enabled(): bool {
	$enabled = (bool) bizmax_mod( 'bizmax_a11y_panel' );
	// Not inside the Elementor editor preview, where the floating button would cover the canvas being edited.
	if ( $enabled && isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only context check.
		$enabled = false;
	}
	return (bool) apply_filters( 'bizmax_a11y_panel_enabled', $enabled );
}

/**
 * Panel options: key => [label, icon]. Keys become `bz-a11y-{key}` classes on <html>.
 * "contrast" and "light" are mutually exclusive (handled in a11y.js).
 *
 * @return array<string,array{0:string,1:string}>
 */
function bizmax_a11y_options(): array {
	return array(
		'contrast' => array( __( 'ניגודיות גבוהה', 'bizmax' ), 'a11y-contrast' ),
		'light'    => array( __( 'רקע בהיר', 'bizmax' ), 'a11y-light' ),
		'gray'     => array( __( 'גווני אפור', 'bizmax' ), 'a11y-gray' ),
		'links'    => array( __( 'הדגשת קישורים', 'bizmax' ), 'a11y-links' ),
		'headings' => array( __( 'הדגשת כותרות', 'bizmax' ), 'a11y-headings' ),
		'font'     => array( __( 'גופן קריא', 'bizmax' ), 'a11y-font' ),
		'spacing'  => array( __( 'ריווח טקסט', 'bizmax' ), 'a11y-spacing' ),
		'still'    => array( __( 'עצירת אנימציות', 'bizmax' ), 'a11y-still' ),
		'cursor'   => array( __( 'סמן עכבר גדול', 'bizmax' ), 'a11y-cursor' ),
		'focus'    => array( __( 'הדגשת מיקוד מקלדת', 'bizmax' ), 'a11y-focus' ),
	);
}

/**
 * Enqueue the panel assets on every front-end page (the panel is site-wide by design).
 */
function bizmax_a11y_enqueue(): void {
	if ( ! bizmax_a11y_enabled() ) {
		return;
	}
	wp_enqueue_style( 'bizmax-a11y', bizmax_asset( 'css/a11y.css' ), array(), BIZMAX_VERSION );
	wp_enqueue_script( 'bizmax-a11y', bizmax_asset( 'js/a11y.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'bizmax_a11y_enqueue' );

/**
 * Re-apply saved display modes before first paint, so there is no flash of the default look.
 * Only on/off flags are read here; the text size is applied by a11y.js.
 */
function bizmax_a11y_head(): void {
	if ( ! bizmax_a11y_enabled() ) {
		return;
	}
	wp_print_inline_script_tag( '(function(r){try{var s=JSON.parse(localStorage.getItem("bzA11y")||"{}"),k;for(k in s){if(s[k]===true&&/^[a-z]+$/.test(k)){r.classList.add("bz-a11y-"+k)}}}catch(e){}})(document.documentElement);' );
}
add_action( 'wp_head', 'bizmax_a11y_head', 2 );

/**
 * Print the button and the dialog right after <body> (after the skip link), so keyboard
 * users reach it early on every page, including Elementor Canvas pages.
 */
function bizmax_a11y_render(): void {
	if ( ! bizmax_a11y_enabled() ) {
		return;
	}

	$side    = bizmax_sanitize_side( (string) bizmax_mod( 'bizmax_a11y_side' ) );
	$stacked = 'left' === $side && bizmax_mod( 'bizmax_show_whatsapp' ) && '' !== bizmax_sanitize_digits( (string) bizmax_mod( 'bizmax_whatsapp' ) ) && ! bizmax_is_canvas();
	$classes = 'bz-a11y bz-a11y--' . $side . ( $stacked ? ' bz-a11y--stack' : '' );
	$url     = bizmax_a11y_page_url();
	?>
<div class="<?php echo esc_attr( $classes ); ?>" id="bz-a11y" data-bz-a11y
	data-msg-on="<?php /* translators: %s: option name. */ echo esc_attr__( '%s: מופעל', 'bizmax' ); ?>"
	data-msg-off="<?php /* translators: %s: option name. */ echo esc_attr__( '%s: כבוי', 'bizmax' ); ?>"
	data-msg-size="<?php /* translators: %s: percentage. */ echo esc_attr__( 'גודל טקסט: %s', 'bizmax' ); ?>"
	data-msg-reset="<?php echo esc_attr__( 'כל הגדרות הנגישות אופסו', 'bizmax' ); ?>">
	<button type="button" class="bz-a11y__toggle" aria-haspopup="dialog" aria-expanded="false" aria-controls="bz-a11y-panel" data-bz-a11y-open>
		<?php bizmax_a11y_person_icon(); ?>
		<span class="bz-a11y__sr"><?php esc_html_e( 'תפריט נגישות', 'bizmax' ); ?></span>
	</button>
	<dialog class="bz-a11y__panel" id="bz-a11y-panel" aria-labelledby="bz-a11y-title">
		<div class="bz-a11y__head">
			<h2 class="bz-a11y__title" id="bz-a11y-title"><?php esc_html_e( 'התאמות נגישות', 'bizmax' ); ?></h2>
			<button type="button" class="bz-a11y__close" aria-label="<?php esc_attr_e( 'סגירת תפריט הנגישות', 'bizmax' ); ?>" data-bz-a11y-close><?php bizmax_icon( 'x', 20 ); ?></button>
		</div>

		<div class="bz-a11y__size" role="group" aria-labelledby="bz-a11y-size-label">
			<span class="bz-a11y__label" id="bz-a11y-size-label"><?php esc_html_e( 'גודל טקסט', 'bizmax' ); ?></span>
			<button type="button" class="bz-a11y__step" data-bz-a11y-size="-1" aria-label="<?php esc_attr_e( 'הקטנת טקסט', 'bizmax' ); ?>"><?php bizmax_icon( 'minus', 20 ); ?></button>
			<span class="bz-a11y__value" data-bz-a11y-size-value>100%</span>
			<button type="button" class="bz-a11y__step" data-bz-a11y-size="1" aria-label="<?php esc_attr_e( 'הגדלת טקסט', 'bizmax' ); ?>"><?php bizmax_icon( 'plus', 20 ); ?></button>
		</div>

		<ul class="bz-a11y__grid" role="list">
			<?php foreach ( bizmax_a11y_options() as $key => [ $label, $icon ] ) : ?>
				<li>
					<button type="button" class="bz-a11y__opt" aria-pressed="false" data-bz-a11y-opt="<?php echo esc_attr( $key ); ?>">
						<?php bizmax_icon( $icon, 22 ); ?>
						<span class="bz-a11y__opt-label"><?php echo esc_html( $label ); ?></span>
						<span class="bz-a11y__check"><?php bizmax_icon( 'check', 14 ); ?></span>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="bz-a11y__foot">
			<button type="button" class="bz-a11y__reset" data-bz-a11y-reset><?php bizmax_icon( 'rotate-ccw', 18 ); ?><span><?php esc_html_e( 'איפוס הגדרות', 'bizmax' ); ?></span></button>
			<?php if ( '' !== $url ) : ?>
				<a class="bz-a11y__link" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'הצהרת נגישות', 'bizmax' ); ?></a>
			<?php endif; ?>
		</div>
		<p class="bz-a11y__sr" role="status" data-bz-a11y-status></p>
	</dialog>
</div>
	<?php
}
add_action( 'wp_body_open', 'bizmax_a11y_render', 20 );

/**
 * The international accessibility symbol (Material Symbols "accessibility_new", Apache 2.0).
 */
function bizmax_a11y_person_icon(): void {
	echo '<svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M20.5 6c-2.61.7-5.67 1-8.5 1s-5.89-.3-8.5-1L3 8c1.86.5 4 .83 6 1v13h2v-6h2v6h2V9c2-.17 4.14-.5 6-1l-.5-2zM12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg>';
}
