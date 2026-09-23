<?php
/**
 * Home: hero with the floating capsule cluster.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d = $args['data'];
?>
<section class="bz-hero" aria-labelledby="bz-hero-title">
	<div class="bz-wrap bz-hero__inner">
		<div class="bz-hero__text">
			<h1 class="bz-hero__title" id="bz-hero-title">
				<span class="bz-hero__line"><span><?php echo esc_html( $d['title_1'] ); ?></span><?php bizmax_flower( 'blue', 44 ); ?></span>
				<span class="bz-hero__line"><?php echo esc_html( $d['title_2'] ); ?> <span class="hl"><?php echo esc_html( $d['highlight'] ); ?></span></span>
			</h1>
			<?php bizmax_text( $d['text'], 'bz-hero__lead' ); ?>
			<?php bizmax_cta( $d['cta_text'], $d['cta_url'], 'primary' ); ?>
		</div>

		<div class="bz-hero__cluster" aria-hidden="true">
			<div class="bz-hero__canvas">
				<?php $c = $d['capsule_1']; ?>
				<div class="bz-cap bz-cap--a">
					<a class="bz-cap__link" href="<?php echo esc_url( $c['url'] ); ?>" tabindex="-1"></a>
					<div class="bz-cap__text"><?php echo esc_html( $c['text'] ); ?></div>
					<svg width="70" height="70" viewBox="0 0 70 70"><path class="scribble" pathLength="1" d="M18 46 C 14 28, 34 12, 46 20 C 56 27, 46 42, 36 38 C 28 35, 32 24, 42 26" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/></svg>
					<div class="bz-cap__photo"><?php echo bizmax_image( (int) $c['image'], 'bizmax-round', 'photo-office', array( 'alt' => '', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				</div>

				<?php $c = $d['capsule_2']; ?>
				<div class="bz-cap bz-cap--b">
					<a class="bz-cap__link" href="<?php echo esc_url( $c['url'] ); ?>" tabindex="-1"></a>
					<div class="bz-bars bz-bars--lg"><div class="bz-bars__tall"></div><div class="bz-bars__col"><div class="bz-bars__red"></div><div class="bz-bars__mid"></div></div></div>
					<div class="bz-cap__text bz-cap__text--dark"><?php echo esc_html( $c['text'] ); ?></div>
				</div>

				<?php $c = $d['capsule_3']; ?>
				<div class="bz-cap bz-cap--c">
					<a class="bz-cap__link" href="<?php echo esc_url( $c['url'] ); ?>" tabindex="-1"></a>
					<div class="bz-cap__text bz-cap__text--plain"><b><?php echo esc_html( $c['title'] ); ?></b><br><?php echo esc_html( $c['text'] ); ?></div>
					<svg width="60" height="150" viewBox="0 0 60 150"><defs><linearGradient id="bz-sp1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1B3764"/><stop offset="1" stop-color="#2E4F8F"/></linearGradient></defs><path class="scribble" pathLength="1" d="M38 6 C 18 18, 20 34, 34 34 C 46 34, 42 18, 28 26 C 12 36, 16 60, 30 58 C 44 56, 38 40, 24 50 C 10 62, 14 88, 28 86 C 42 84, 36 66, 22 78 C 10 90, 16 116, 30 114" fill="none" stroke="url(#bz-sp1)" stroke-width="3" stroke-linecap="round"/></svg>
					<div class="bz-orb bz-orb--lg"><span class="bz-orb__gem bz-orb__gem--green"></span></div>
				</div>

				<?php $c = $d['capsule_4']; ?>
				<div class="bz-cap bz-cap--d">
					<a class="bz-cap__link" href="<?php echo esc_url( $c['url'] ); ?>" tabindex="-1"></a>
					<div class="bz-cap__text bz-cap__text--dark"><?php echo esc_html( $c['text'] ); ?></div>
					<svg width="70" height="230" viewBox="0 0 70 230"><defs><linearGradient id="bz-wv1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F6A81C"/><stop offset="1" stop-color="#2E4F8F"/></linearGradient></defs><path class="scribble" pathLength="1" d="M40 6 C 66 46, 6 76, 30 116 C 52 152, 8 178, 26 222" fill="none" stroke="url(#bz-wv1)" stroke-width="3.5" stroke-linecap="round"/></svg>
				</div>
			</div>
		</div>
	</div>
</section>
