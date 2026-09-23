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
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'דלג לתוכן המרכזי', 'bizmax' ); ?></a>

<header class="bz-header" id="masthead">
	<div class="bz-wrap bz-header__inner">
		<button class="bz-burger" type="button" aria-expanded="false" aria-controls="bz-drawer" aria-label="<?php esc_attr_e( 'פתיחת תפריט', 'bizmax' ); ?>" data-bz-drawer-open>
			<span></span><span></span><span></span>
		</button>
		<nav class="bz-nav" aria-label="<?php esc_attr_e( 'ניווט ראשי', 'bizmax' ); ?>">
			<?php bizmax_menu( 'primary', 'bz-nav__list' ); ?>
		</nav>
		<div class="bz-header__spacer"></div>
		<?php bizmax_logo( 'bz-logo' ); ?>
	</div>
</header>

<div class="bz-drawer" id="bz-drawer" hidden data-bz-drawer>
	<div class="bz-drawer__backdrop" data-bz-drawer-close></div>
	<div class="bz-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="bz-drawer-title" tabindex="-1">
		<div class="bz-drawer__head">
			<span class="bz-drawer__title" id="bz-drawer-title"><?php esc_html_e( 'תפריט', 'bizmax' ); ?></span>
			<button type="button" class="bz-drawer__close" aria-label="<?php esc_attr_e( 'סגירת תפריט', 'bizmax' ); ?>" data-bz-drawer-close><?php bizmax_icon( 'x', 22 ); ?></button>
		</div>
		<nav class="bz-drawer__nav" aria-label="<?php esc_attr_e( 'תפריט האתר', 'bizmax' ); ?>">
			<?php bizmax_menu( has_nav_menu( 'drawer' ) ? 'drawer' : 'primary', 'bz-drawer__list' ); ?>
		</nav>
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
