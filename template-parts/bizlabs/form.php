<?php
/**
 * BizLabs: sign-up form (same secure REST endpoint as the home form; leads are labelled BizLabs).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d       = $args['data'];
$page_id = (int) ( $args['page_id'] ?? 0 );
$privacy = get_privacy_policy_url();
$points  = array_values( array_filter( (array) $d['points'], static fn( $p ) => '' !== trim( (string) $p['text'] ) ) );
$phone   = (string) bizmax_mod( 'bizmax_phone' );
$email   = (string) bizmax_mod( 'bizmax_email' );
?>
<section class="bz-bl-form rv" id="lform" aria-labelledby="bz-bl-form-title">
	<div class="bz-bl-form__inner">
		<div class="bz-bl-form__head">
			<h2 class="bz-bl-form__title" id="bz-bl-form-title"><?php echo esc_html( $d['heading'] ); ?></h2>
			<?php bizmax_text( $d['text'], 'bz-bl-form__text' ); ?>
		</div>

		<?php if ( $points ) : ?>
			<ol class="bz-bl-points">
				<?php foreach ( $points as $bz_i => $p ) : ?>
					<li><span class="bz-bl-points__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $bz_i + 1 ) ); ?></span><span><?php echo esc_html( $p['text'] ); ?></span></li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<?php if ( ! empty( $d['show_contact'] ) && ( '' !== $phone || is_email( $email ) ) ) : ?>
			<p class="bz-bl-form__contact">
				<?php if ( '' !== $phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php bizmax_icon( 'phone', 18 ); ?><span dir="ltr"><?php echo esc_html( $phone ); ?></span></a>
				<?php endif; ?>
				<?php if ( is_email( $email ) ) : ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php bizmax_icon( 'mail', 18 ); ?><span dir="ltr"><?php echo esc_html( $email ); ?></span></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<form class="bz-form bz-bl-form__panel" method="post" action="<?php echo esc_url( rest_url( 'bizmax/v1/contact' ) ); ?>" novalidate data-bz-contact data-page="<?php echo (int) $page_id; ?>" aria-labelledby="bz-bl-form-title">
			<div class="bz-bl-form__grid">
				<div class="bz-form__field">
					<label class="screen-reader-text" for="bz-bl-name"><?php esc_html_e( 'שם מלא', 'bizmax' ); ?></label>
					<input class="bz-form__input" type="text" id="bz-bl-name" name="name" placeholder="<?php esc_attr_e( 'שם מלא', 'bizmax' ); ?>" autocomplete="name" required aria-required="true" aria-describedby="bz-bl-name-err" maxlength="80">
					<p class="bz-form__error" id="bz-bl-name-err" hidden></p>
				</div>
				<div class="bz-form__field">
					<label class="screen-reader-text" for="bz-bl-email"><?php esc_html_e( 'אימייל', 'bizmax' ); ?></label>
					<input class="bz-form__input" type="email" id="bz-bl-email" name="email" placeholder="<?php esc_attr_e( 'אימייל', 'bizmax' ); ?>" autocomplete="email" inputmode="email" required aria-required="true" aria-describedby="bz-bl-email-err" dir="ltr">
					<p class="bz-form__error" id="bz-bl-email-err" hidden></p>
				</div>
				<div class="bz-form__field">
					<label class="screen-reader-text" for="bz-bl-phone"><?php esc_html_e( 'טלפון', 'bizmax' ); ?></label>
					<input class="bz-form__input" type="tel" id="bz-bl-phone" name="phone" placeholder="<?php esc_attr_e( 'טלפון', 'bizmax' ); ?>" autocomplete="tel" inputmode="tel" required aria-required="true" aria-describedby="bz-bl-phone-err" dir="ltr">
					<p class="bz-form__error" id="bz-bl-phone-err" hidden></p>
				</div>
				<button class="bz-form__submit" type="submit"><?php echo esc_html( $d['btn'] ); ?></button>
			</div>

			<div class="bz-form__hp" aria-hidden="true">
				<label for="bz-bl-website"><?php esc_html_e( 'אתר', 'bizmax' ); ?></label>
				<input type="text" id="bz-bl-website" name="website" tabindex="-1" autocomplete="off">
			</div>

			<div class="bz-form__field bz-form__field--check">
				<label class="bz-form__check">
					<input type="checkbox" id="bz-bl-privacy" name="privacy" value="1" required aria-required="true" aria-describedby="bz-bl-privacy-err">
					<span>
						<?php esc_html_e( 'קראתי ואני מאשר/ת את', 'bizmax' ); ?>
						<?php if ( $privacy ) : ?>
							<a href="<?php echo esc_url( $privacy ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'מדיניות הפרטיות', 'bizmax' ); ?><span class="screen-reader-text"> (<?php esc_html_e( 'נפתח בחלון חדש', 'bizmax' ); ?>)</span></a>
						<?php else : ?>
							<?php esc_html_e( 'מדיניות הפרטיות', 'bizmax' ); ?>
						<?php endif; ?>
					</span>
				</label>
				<p class="bz-form__error" id="bz-bl-privacy-err" hidden></p>
			</div>

			<?php bizmax_turnstile_field(); ?>

			<p class="bz-form__status" role="status" aria-live="polite" data-bz-status></p>
		</form>
	</div>
</section>
<div class="bz-wrap bz-bl-stripe-wrap"><div class="bz-stripe" aria-hidden="true"><span></span><span></span><span></span></div></div>
