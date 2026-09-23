<?php
/**
 * Home: about bento grid with counters.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d = $args['data'];
?>
<section class="bz-about rv" id="about" aria-labelledby="bz-about-title">
	<div class="bz-wrap">
		<div class="bz-about__head">
			<h2 class="bz-h2" id="bz-about-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'orange' ); ?></h2>
			<?php bizmax_text( $d['intro'], 'bz-about__intro' ); ?>
		</div>

		<div class="bz-bento">
			<div class="bz-bento__photo">
				<?php echo bizmax_image( (int) $d['card_image'], 'bizmax-tall', 'photo-office', array( 'class' => 'bz-bento__img', 'alt' => $d['card_title'], 'sizes' => '(max-width: 1024px) 100vw, 420px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="bz-bento__overlay" aria-hidden="true"></div>
				<div class="bz-bento__caption">
					<?php if ( '' !== $d['card_badge'] ) : ?>
						<span class="bz-bento__badge"><?php echo esc_html( $d['card_badge'] ); ?></span>
					<?php endif; ?>
					<h3 class="bz-bento__title"><?php echo esc_html( $d['card_title'] ); ?></h3>
					<?php if ( '' !== $d['card_subtitle'] ) : ?>
						<p class="bz-bento__sub"><?php echo esc_html( $d['card_subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php foreach ( array( 'counter_1' => 'dark', 'counter_2' => 'light' ) as $key => $tone ) : ?>
				<?php $c = $d[ $key ]; ?>
				<div class="bz-counter bz-counter--<?php echo esc_attr( $tone ); ?>">
					<p class="bz-counter__num count" data-count="<?php echo (int) $c['number']; ?>" data-suffix="<?php echo esc_attr( $c['suffix'] ); ?>"><?php echo esc_html( number_format_i18n( (int) $c['number'] ) . $c['suffix'] ); ?></p>
					<p class="bz-counter__text"><?php echo esc_html( $c['text'] ); ?></p>
					<span class="bz-counter__bar" aria-hidden="true"></span>
				</div>
			<?php endforeach; ?>

			<?php foreach ( array( 'link_1' => 'graduation-cap', 'link_2' => 'rocket' ) as $key => $icon ) : ?>
				<?php $l = $d[ $key ]; ?>
				<a class="bz-tile bz-tile--<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $l['url'] ); ?>">
					<span class="bz-tile__icon" aria-hidden="true"><?php bizmax_icon( $icon, 24 ); ?></span>
					<h3 class="bz-tile__title"><?php echo esc_html( $l['title'] ); ?></h3>
					<p class="bz-tile__text"><?php echo esc_html( $l['text'] ); ?></p>
					<span class="bz-tile__cta"><?php echo esc_html( $l['cta'] ); ?> <span class="arr" aria-hidden="true">&gt;&gt;</span></span>
				</a>
			<?php endforeach; ?>
		</div>

		<?php bizmax_text( $d['closing'], 'bz-about__closing' ); ?>
	</div>
</section>
