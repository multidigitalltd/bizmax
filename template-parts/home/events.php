<?php
/**
 * Home: events grid.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d        = $args['data'];
$today    = wp_date( 'Y-m-d' );
$fallback = array( 'space-1', 'space-2', 'space-3', 'space-4' );
$items    = array();

foreach ( (array) $d['items'] as $item ) {
	if ( '' === trim( (string) $item['title'] ) ) {
		continue;
	}
	if ( ! empty( $d['hide_past'] ) && '' !== $item['date'] && $item['date'] < $today ) {
		continue;
	}
	$items[] = $item;
}

if ( ! $items ) {
	return;
}
?>
<section class="bz-events rv" id="events" aria-labelledby="bz-events-title">
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-events__title" id="bz-events-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'blue' ); ?></h2>
		<ul class="bz-events__grid">
			<?php foreach ( $items as $i => $e ) : ?>
				<?php $date = bizmax_hebrew_date_parts( (string) $e['date'] ); ?>
				<li class="bz-event">
					<div class="bz-event__media">
						<?php echo bizmax_image( (int) $e['image'], 'bizmax-card', $fallback[ $i % 4 ], array( 'class' => 'bz-event__img', 'alt' => '', 'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 25vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( '' !== $date['day'] ) : ?>
							<time class="bz-event__date" datetime="<?php echo esc_attr( $date['iso'] ); ?>"><span class="bz-event__day"><?php echo esc_html( $date['day'] ); ?></span><span class="bz-event__month"><?php echo esc_html( $date['month'] ); ?></span></time>
						<?php endif; ?>
					</div>
					<div class="bz-event__body">
						<?php if ( '' !== trim( (string) $e['meta'] ) ) : ?>
							<p class="bz-event__meta"><?php bizmax_icon( 'clock', 14 ); ?><span><?php echo esc_html( $e['meta'] ); ?></span></p>
						<?php endif; ?>
						<h3 class="bz-event__title"><?php echo esc_html( $e['title'] ); ?></h3>
						<?php bizmax_text( $e['text'], 'bz-event__text' ); ?>
						<?php if ( '' !== $e['url'] ) : ?>
							<a class="bz-event__link" href="<?php echo esc_url( $e['url'] ); ?>"><?php esc_html_e( 'מעבר לאירוע', 'bizmax' ); ?> <span class="arr" aria-hidden="true">&gt;&gt;</span><span class="screen-reader-text"> – <?php echo esc_html( $e['title'] ); ?></span></a>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
