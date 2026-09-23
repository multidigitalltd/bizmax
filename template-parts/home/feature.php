<?php
/**
 * Home: alternating feature row (המתחם / דה-סקול / ביזלאבס).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d       = $args['data'];
$key     = sanitize_key( $args['key'] );
$classes = 'bz-feature rv';
if ( ! empty( $args['reverse'] ) ) {
	$classes .= ' bz-feature--reverse';
}
if ( ! empty( $args['first'] ) ) {
	$classes .= ' bz-feature--first';
}
$placeholders = array( 'coworking' => 'space-1', 'deschool' => 'space-2', 'bizlabs' => 'space-3' );
?>
<section class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $key ); ?>" aria-labelledby="bz-<?php echo esc_attr( $key ); ?>-title">
	<div class="bz-wrap bz-feature__inner">
		<div class="bz-feature__text">
			<h2 class="bz-h2" id="bz-<?php echo esc_attr( $key ); ?>-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( $d['flower'] ); ?></h2>
			<?php if ( '' !== trim( $d['subheading'] ) ) : ?>
				<h3 class="bz-h3"><?php echo wp_kses_post( nl2br( esc_html( $d['subheading'] ) ) ); ?></h3>
			<?php endif; ?>
			<?php bizmax_text( $d['text'], 'bz-text' ); ?>
			<div class="bz-feature__cta"><?php bizmax_cta( $d['cta_text'], $d['cta_url'], 'secondary' ); ?></div>
		</div>
		<div class="bz-feature__frame">
			<?php echo bizmax_image( (int) $d['image'], 'bizmax-feature', $placeholders[ $key ] ?? 'space-1', array( 'class' => 'bz-feature__img', 'alt' => $d['image_alt'], 'sizes' => '(max-width: 1024px) 100vw, 592px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
