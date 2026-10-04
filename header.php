<?php
/**
 * Site header (used on every page, including Elementor pages).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
wp_body_open(); // Prints the skip link and the accessibility panel (see inc/setup.php, inc/a11y.php).

// Pages set to Elementor Canvas never show the theme header (see bizmax_is_canvas()).
if ( bizmax_is_canvas() ) {
	return;
}
?>

<header class="bz-header" id="masthead">
	<div class="bz-wrap bz-header__inner">
		<button class="bz-burger" type="button" aria-expanded="false" aria-controls="bz-drawer" aria-label="<?php esc_attr_e( 'פתיחת תפריט', 'bizmax' ); ?>" data-bz-drawer-open>
			<span></span><span></span><span></span>
		</button>
		<?php
		// The BizLabs template has its own in-page navigation and a second (BizLabs) logo.
		$bz_page_head = bizmax_is_bizlabs_template() ? bizmax_page_get( 'bizlabs', (int) get_queried_object_id() )['header'] : null;
		?>
		<?php if ( $bz_page_head ) : ?>
			<?php bizmax_page_nav( $bz_page_head['links'], 'bz-nav__list', __( 'ניווט בעמוד ביזלאבס', 'bizmax' ) ); ?>
		<?php elseif ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="bz-nav" aria-label="<?php esc_attr_e( 'ניווט ראשי', 'bizmax' ); ?>">
				<?php bizmax_menu( 'primary', 'bz-nav__list' ); ?>
			</nav>
		<?php endif; ?>
		<div class="bz-header__spacer"></div>
		<?php if ( $bz_page_head ) : ?>
			<div class="bz-header__logos">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
				echo bizmax_image(
					(int) $bz_page_head['logo'],
					'medium',
					'bizlabs/logo-bizlabs',
					array(
						'class'         => 'bz-header__sublogo',
						'alt'           => $bz_page_head['logo_alt'],
						'fetchpriority' => 'high',
					)
				);
				?>
				<span class="bz-header__divider" aria-hidden="true"></span>
				<?php bizmax_logo( 'bz-logo' ); ?>
			</div>
		<?php else : ?>
			<?php bizmax_logo( 'bz-logo' ); ?>
		<?php endif; ?>
	</div>
</header>

<div class="bz-drawer" id="bz-drawer" hidden data-bz-drawer>
	<div class="bz-drawer__backdrop" data-bz-drawer-close></div>
	<div class="bz-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="bz-drawer-title" tabindex="-1">
		<div class="bz-drawer__head">
			<span class="bz-drawer__title" id="bz-drawer-title"><?php esc_html_e( 'תפריט', 'bizmax' ); ?></span>
			<button type="button" class="bz-drawer__close" aria-label="<?php esc_attr_e( 'סגירת תפריט', 'bizmax' ); ?>" data-bz-drawer-close><?php bizmax_icon( 'x', 22 ); ?></button>
		</div>
		<?php if ( $bz_page_head ) : ?>
			<?php bizmax_page_nav( $bz_page_head['links'], 'bz-drawer__list bz-drawer__list--page', __( 'בעמוד הזה', 'bizmax' ), 'bz-drawer__nav' ); ?>
		<?php endif; ?>
		<?php $bz_drawer_menu = has_nav_menu( 'drawer' ) ? 'drawer' : 'primary'; ?>
		<?php if ( has_nav_menu( $bz_drawer_menu ) ) : ?>
			<nav class="bz-drawer__nav" aria-label="<?php esc_attr_e( 'תפריט האתר', 'bizmax' ); ?>">
				<?php bizmax_menu( $bz_drawer_menu, 'bz-drawer__list' ); ?>
			</nav>
		<?php endif; ?>
		<div class="bz-drawer__contact">
			<?php $bz_phone = (string) bizmax_mod( 'bizmax_phone' ); ?>
			<?php if ( '' !== $bz_phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $bz_phone ) ); ?>"><?php bizmax_icon( 'phone' ); ?><span><?php echo esc_html( $bz_phone ); ?></span></a>
			<?php endif; ?>
			<?php $bz_email = (string) bizmax_mod( 'bizmax_email' ); ?>
			<?php if ( is_email( $bz_email ) ) : ?>
				<a href="mailto:<?php echo esc_attr( $bz_email ); ?>"><?php bizmax_icon( 'mail' ); ?><span><?php echo esc_html( $bz_email ); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
</div>
