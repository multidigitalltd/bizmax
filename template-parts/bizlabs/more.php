<?php
/**
 * BizLabs: "עוד בביזמקס" capsules.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d = $args['data'];
$i = $d['intro'];
?>
<section class="bz-bl-more rv" id="lmore" aria-labelledby="bz-bl-more-title">
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-bl-h2--gap" id="bz-bl-more-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
		<div class="bz-bl-more__row">
			<a class="bz-bl-intro" href="<?php echo esc_url( $i['url'] ); ?>">
				<?php bizmax_flower( 'bright', 44 ); ?>
				<span class="bz-bl-intro__title"><?php echo esc_html( $i['title'] ); ?></span>
				<?php bizmax_text( $i['text'], 'bz-bl-intro__text', 'span' ); ?>
				<span class="bz-bl-intro__link"><?php echo esc_html( $i['link'] ); ?> <span class="arr" aria-hidden="true">&gt;&gt;</span></span>
			</a>

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
			<a class="bz-pill bz-pill--labs" href="<?php echo esc_url( $c['url'] ); ?>">
				<span class="bz-pill__text bz-pill__text--plain"><b><?php echo esc_html( $c['title'] ); ?></b><br><?php echo esc_html( $c['text'] ); ?></span>
				<svg width="44" height="88" viewBox="0 0 60 120" aria-hidden="true" focusable="false"><path class="scribble" pathLength="1" d="M36 6 C 18 16, 20 30, 32 30 C 43 30, 39 16, 26 23 C 12 32, 15 52, 28 50 C 41 48, 36 34, 23 44 C 12 54, 16 76, 28 74" fill="none" stroke="#1B3764" stroke-width="3" stroke-linecap="round"/></svg>
				<span class="bz-orb bz-orb--sm" aria-hidden="true"><span class="bz-orb__gem bz-orb__gem--yellow"></span></span>
			</a>
		</div>
	</div>
</section>
