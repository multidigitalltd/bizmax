<?php
/**
 * Home: "מרחב צמיחה" journey with stations.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d        = $args['data'];
$stations = array_values( array_filter( (array) $d['stations'], static fn( $s ) => '' !== trim( (string) $s['title'] ) ) );
$fallback = array( 'space-1', 'space-2', 'space-3', 'space-4' );
?>
<section class="bz-meetups rv" id="meetups" aria-labelledby="bz-meetups-title">
	<div class="bz-wrap">
		<div class="bz-meetups__head">
			<div>
				<h2 class="bz-h2" id="bz-meetups-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'orange' ); ?></h2>
				<?php if ( '' !== trim( $d['subheading'] ) ) : ?>
					<h3 class="bz-h3 bz-meetups__sub"><?php echo esc_html( $d['subheading'] ); ?></h3>
				<?php endif; ?>
			</div>
			<?php bizmax_cta( $d['cta_text'], $d['cta_url'], 'secondary', 'bz-meetups__cta' ); ?>
		</div>

		<?php if ( $stations ) : ?>
			<div class="bz-journey">
				<svg class="bz-journey__path" viewBox="0 0 1200 230" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path class="scribble" pathLength="1" d="M1200 140 C 1130 128, 1095 112, 1050 111 C 950 108, 850 65, 750 75 C 640 85, 560 135, 450 127 C 340 118, 250 72, 150 85 C 95 92, 45 105, 0 118" fill="none" stroke="#F6A81C" stroke-width="3.5" stroke-linecap="round"/></svg>
				<ol class="bz-stations">
					<?php foreach ( $stations as $i => $s ) : ?>
						<li class="bz-station bz-station--<?php echo (int) ( $i % 4 + 1 ); ?> <?php echo 0 === $i % 2 ? 'bz-station--odd' : 'bz-station--even'; ?>">
							<div class="bz-station__media">
								<?php echo bizmax_image( (int) $s['image'], 'bizmax-round', $fallback[ $i % 4 ], array( 'class' => 'bz-station__img', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="bz-station__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							</div>
							<h3 class="bz-station__title"><?php echo esc_html( $s['title'] ); ?></h3>
							<?php bizmax_text( $s['text'], 'bz-station__text' ); ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		<?php endif; ?>
	</div>
</section>
