<?php
/**
 * Site footer (used on every page, including Elementor pages).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$bz_phone     = (string) bizmax_mod( 'bizmax_phone' );
$bz_email     = (string) bizmax_mod( 'bizmax_email' );
$bz_address   = (string) bizmax_mod( 'bizmax_address' );
$bz_map_link  = (string) bizmax_mod( 'bizmax_map_link' );
$bz_map_embed = bizmax_sanitize_map_embed( (string) bizmax_mod( 'bizmax_map_embed' ) );
$bz_map_image = (int) bizmax_mod( 'bizmax_map_image' );
$bz_whatsapp  = bizmax_sanitize_digits( (string) bizmax_mod( 'bizmax_whatsapp' ) );
$bz_privacy   = get_privacy_policy_url();
$bz_a11y      = bizmax_a11y_page_url();
?>

<footer class="bz-footer" id="colophon">
	<div class="bz-wrap bz-footer__inner">
		<div class="bz-footer__logo">
			<?php bizmax_logo( 'bz-footer__logo-link' ); ?>
		</div>

		<?php if ( has_nav_menu( 'footer_1' ) || has_nav_menu( 'footer_2' ) ) : ?>
			<div class="bz-footer__menus">
				<?php foreach ( array( 'footer_1', 'footer_2' ) as $bz_loc ) : ?>
					<?php if ( has_nav_menu( $bz_loc ) ) : ?>
						<nav class="bz-footer__menu" aria-label="<?php echo esc_attr( 'footer_1' === $bz_loc ? __( 'קישורי פוטר', 'bizmax' ) : __( 'קישורי פוטר נוספים', 'bizmax' ) ); ?>">
							<?php bizmax_menu( $bz_loc, 'bz-footer__list', false ); ?>
						</nav>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="bz-footer__map">
			<?php if ( '' !== $bz_map_embed ) : ?>
				<iframe src="<?php echo esc_url( $bz_map_embed ); ?>" width="250" height="112" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( sprintf( /* translators: %s: address */ __( 'מפה – %s', 'bizmax' ), $bz_address ) ); ?>"></iframe>
			<?php else : ?>
				<a href="<?php echo esc_url( $bz_map_link ?: '#' ); ?>" target="_blank" rel="noopener noreferrer" class="bz-footer__map-link">
					<?php echo bizmax_image( $bz_map_image, 'medium', 'map-placeholder', array( 'class' => 'bz-footer__map-img', 'alt' => sprintf( /* translators: %s: address */ __( 'מפה – %s (נפתח בחלון חדש)', 'bizmax' ), $bz_address ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
				</a>
			<?php endif; ?>
		</div>

		<address class="bz-footer__contact">
			<?php if ( '' !== $bz_phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $bz_phone ) ); ?>"><?php bizmax_icon( 'phone' ); ?><span><b><?php esc_html_e( 'טלפון', 'bizmax' ); ?></b> <bdi><?php echo esc_html( $bz_phone ); ?></bdi></span></a>
			<?php endif; ?>
			<?php if ( is_email( $bz_email ) ) : ?>
				<a href="mailto:<?php echo esc_attr( $bz_email ); ?>"><?php bizmax_icon( 'mail' ); ?><span><b><?php esc_html_e( 'מייל', 'bizmax' ); ?></b> <bdi><?php echo esc_html( $bz_email ); ?></bdi></span></a>
			<?php endif; ?>
			<?php if ( '' !== $bz_address ) : ?>
				<span class="bz-footer__line"><?php bizmax_icon( 'map-pin' ); ?><span><b><?php esc_html_e( 'כתובת', 'bizmax' ); ?></b> <?php echo esc_html( $bz_address ); ?></span></span>
			<?php endif; ?>
		</address>
	</div>

	<?php if ( $bz_privacy || $bz_a11y ) : ?>
		<div class="bz-wrap bz-footer__legal">
			<?php if ( $bz_a11y ) : ?>
				<a href="<?php echo esc_url( $bz_a11y ); ?>"><?php esc_html_e( 'הצהרת נגישות', 'bizmax' ); ?></a>
			<?php endif; ?>
			<?php if ( $bz_privacy ) : ?>
				<a href="<?php echo esc_url( $bz_privacy ); ?>"><?php esc_html_e( 'מדיניות הפרטיות', 'bizmax' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( bizmax_mod( 'bizmax_show_whatsapp' ) && '' !== $bz_whatsapp ) : ?>
		<a class="bz-whatsapp" href="<?php echo esc_url( 'https://wa.me/' . $bz_whatsapp ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'שיחה בוואטסאפ (נפתח בחלון חדש)', 'bizmax' ); ?>"><?php bizmax_whatsapp_icon(); ?></a>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
