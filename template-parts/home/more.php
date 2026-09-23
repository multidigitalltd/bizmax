<?php
/**
 * Home: "עוד בביזמקס" capsules, contact form and brand stripe.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d       = $args['data'];
$page_id = (int) ( $args['page_id'] ?? 0 );
$privacy = get_privacy_policy_url();
?>
<section class="bz-more rv" id="more" aria-labelledby="bz-more-title">
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-more__title" id="bz-more-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'green' ); ?></h2>

		<div class="bz-more__row">
			<?php $c = $d['capsule_1']; ?>
			<a class="bz-pill bz-pill--dark" href="<?php echo esc_url( $c['url'] ); ?>">
				<span class="bz-pill__text"><?php echo esc_html( $c['text'] ); ?></span>
				<svg width="56" height="56" viewBox="0 0 70 70" aria-hidden="true" focusable="false"><path class="scribble" pathLength="1" d="M18 46 C 14 28, 34 12, 46 20 C 56 27, 46 42, 36 38 C 28 35, 32 24, 42 26" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/></svg>
			</a>

			<?php $c = $d['capsule_2']; ?>
			<a class="bz-pill bz-pill--outline" href="<?php echo esc_url( $c['url'] ); ?>">
				<span class="bz-bars bz-bars--sm" aria-hidden="true"><span class="bz-bars__tall"></span><span class="bz-bars__col"><span class="bz-bars__red"></span><span class="bz-bars__mid"></span></span></span>
				<span class="bz-pill__text bz-pill__text--dark"><?php echo esc_html( $c['text'] ); ?></span>
			</a>

			<?php $c = $d['capsule_3']; ?>
			<a class="bz-pill bz-pill--green" href="<?php echo esc_url( $c['url'] ); ?>">
				<span class="bz-pill__text bz-pill__text--lg"><?php echo esc_html( $c['text'] ); ?></span>
				<svg width="44" height="110" viewBox="0 0 60 150" aria-hidden="true" focusable="false"><path class="scribble" pathLength="1" d="M38 8 C 62 44, 8 70, 30 106 C 48 136, 14 150, 24 168" fill="none" stroke="#e2574c" stroke-width="3.5" stroke-linecap="round"/></svg>
			</a>

			<?php $c = $d['capsule_4']; ?>
			<a class="bz-pill bz-pill--yellow" href="<?php echo esc_url( $c['url'] ); ?>">
				<span class="bz-pill__text bz-pill__text--plain"><b><?php echo esc_html( $c['title'] ); ?></b><br><?php echo esc_html( $c['text'] ); ?></span>
				<svg width="44" height="88" viewBox="0 0 60 120" aria-hidden="true" focusable="false"><path class="scribble" pathLength="1" d="M36 6 C 18 16, 20 30, 32 30 C 43 30, 39 16, 26 23 C 12 32, 15 52, 28 50 C 41 48, 36 34, 23 44 C 12 54, 16 76, 28 74" fill="none" stroke="#1B3764" stroke-width="3" stroke-linecap="round"/></svg>
				<span class="bz-orb bz-orb--sm" aria-hidden="true"><span class="bz-orb__gem bz-orb__gem--yellow"></span></span>
			</a>

			<?php if ( ! empty( $d['show_form'] ) ) : ?>
				<form class="bz-form" id="contact" method="post" action="<?php echo esc_url( rest_url( 'bizmax/v1/contact' ) ); ?>" novalidate data-bz-contact data-page="<?php echo (int) $page_id; ?>" aria-labelledby="bz-form-title">
					<h3 class="bz-form__title" id="bz-form-title"><?php echo esc_html( $d['form_title'] ); ?></h3>

					<div class="bz-form__field">
						<label class="screen-reader-text" for="bz-f-name"><?php esc_html_e( 'שם', 'bizmax' ); ?></label>
						<input class="bz-form__input" type="text" id="bz-f-name" name="name" placeholder="<?php esc_attr_e( 'שם', 'bizmax' ); ?>" autocomplete="name" required aria-required="true" aria-describedby="bz-f-name-err" maxlength="80">
						<p class="bz-form__error" id="bz-f-name-err" hidden></p>
					</div>
					<div class="bz-form__field">
						<label class="screen-reader-text" for="bz-f-email"><?php esc_html_e( 'מייל', 'bizmax' ); ?></label>
						<input class="bz-form__input" type="email" id="bz-f-email" name="email" placeholder="<?php esc_attr_e( 'מייל', 'bizmax' ); ?>" autocomplete="email" inputmode="email" required aria-required="true" aria-describedby="bz-f-email-err" dir="ltr">
						<p class="bz-form__error" id="bz-f-email-err" hidden></p>
					</div>
					<div class="bz-form__field">
						<label class="screen-reader-text" for="bz-f-phone"><?php esc_html_e( 'טלפון', 'bizmax' ); ?></label>
						<input class="bz-form__input" type="tel" id="bz-f-phone" name="phone" placeholder="<?php esc_attr_e( 'טלפון', 'bizmax' ); ?>" autocomplete="tel" inputmode="tel" required aria-required="true" aria-describedby="bz-f-phone-err" dir="ltr">
						<p class="bz-form__error" id="bz-f-phone-err" hidden></p>
					</div>

					<div class="bz-form__hp" aria-hidden="true">
						<label for="bz-f-website"><?php esc_html_e( 'אתר', 'bizmax' ); ?></label>
						<input type="text" id="bz-f-website" name="website" tabindex="-1" autocomplete="off">
					</div>

					<div class="bz-form__field bz-form__field--check">
						<label class="bz-form__check">
							<input type="checkbox" id="bz-f-privacy" name="privacy" value="1" required aria-required="true" aria-describedby="bz-f-privacy-err">
							<span>
								<?php esc_html_e( 'קראתי ואני מאשר/ת את', 'bizmax' ); ?>
								<?php if ( $privacy ) : ?>
									<a href="<?php echo esc_url( $privacy ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'מדיניות הפרטיות', 'bizmax' ); ?><span class="screen-reader-text"> (<?php esc_html_e( 'נפתח בחלון חדש', 'bizmax' ); ?>)</span></a>
								<?php else : ?>
									<?php esc_html_e( 'מדיניות הפרטיות', 'bizmax' ); ?>
								<?php endif; ?>
							</span>
						</label>
						<p class="bz-form__error" id="bz-f-privacy-err" hidden></p>
					</div>

					<button class="bz-form__submit" type="submit"><?php echo esc_html( $d['form_btn'] ); ?></button>
					<p class="bz-form__status" role="status" aria-live="polite" data-bz-status></p>
				</form>
			<?php endif; ?>
		</div>

		<div class="bz-stripe" aria-hidden="true"><span></span><span></span><span></span></div>
	</div>
</section>
