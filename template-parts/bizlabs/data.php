<?php
/**
 * BizLabs: "קצת על חרדים וסטארטאפים" – four tabs with charts (accessible tabs; charts are SVG/HTML).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d       = $args['data'];
$bz_tabs = array( $d['tab1'], $d['tab2'], $d['tab3'], $d['tab4'] );
$bars    = array_values( array_filter( (array) $d['bars'], static fn( $b ) => '' !== trim( (string) $b['label'] ) ) );
$max     = max( 1, 1, ...array_map( static fn( $b ) => (int) $b['value'], $bars ) );
$funnel  = array_slice( array_values( array_filter( (array) $d['funnel'], static fn( $f ) => '' !== trim( (string) $f['title'] ) ) ), 0, 4 );
?>
<section class="bz-bl-data bz-bl-tint rv" id="linfo" aria-labelledby="bz-bl-data-title" data-bz-data>
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-bl-h2--tabs" id="bz-bl-data-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>

		<div class="bz-bl-tabs" role="tablist" aria-labelledby="bz-bl-data-title">
			<?php foreach ( $bz_tabs as $bz_i => $bz_tab ) : ?>
				<button type="button" class="bz-bl-tab" role="tab" id="bz-bl-tab-<?php echo (int) $bz_i; ?>" aria-controls="bz-bl-panel-<?php echo (int) $bz_i; ?>" aria-selected="<?php echo 0 === $bz_i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $bz_i ? '0' : '-1'; ?>"><?php echo esc_html( $bz_tab ); ?></button>
			<?php endforeach; ?>
		</div>

		<div class="bz-bl-panel" role="tabpanel" id="bz-bl-panel-0" aria-labelledby="bz-bl-tab-0" tabindex="0">
			<div class="bz-bl-panel__row">
				<div class="bz-bl-donut bz-bl-donut--industry"><?php bizmax_donut( (array) $d['industry'], 'industry' ); ?></div>
				<?php bizmax_chart_legend( (array) $d['industry'], 'bz-legend--industry' ); ?>
			</div>
			<ul class="bz-bl-blocks" role="list">
				<?php foreach ( array( 'block_1', 'block_2', 'block_3' ) as $bz_n => $bz_key ) : ?>
					<?php if ( '' !== trim( $d[ $bz_key ] ) ) : ?>
						<li class="bz-bl-block bz-bl-block--<?php echo (int) $bz_n + 1; ?>"><?php echo esc_html( $d[ $bz_key ] ); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="bz-bl-panel" role="tabpanel" id="bz-bl-panel-1" aria-labelledby="bz-bl-tab-1" tabindex="0" hidden>
			<div class="bz-bl-capital">
				<div>
					<h3 class="bz-bl-chart-title"><?php echo esc_html( $d['bars_title'] ); ?> <span><?php echo esc_html( $d['bars_unit'] ); ?></span></h3>
					<ul class="bz-bars-chart" role="list" data-bz-bars>
						<?php foreach ( $bars as $bz_i => $b ) : ?>
							<li class="bz-bars-chart__item" style="--h:<?php echo esc_attr( number_format( (int) $b['value'] / $max * 170, 1, '.', '' ) ); ?>px;--i:<?php echo (int) $bz_i; ?>">
								<span class="bz-bars-chart__value"><?php echo (int) $b['value']; ?><span class="screen-reader-text"> <?php esc_html_e( 'חברות בטווח', 'bizmax' ); ?></span></span>
								<span class="bz-bars-chart__bar" aria-hidden="true"></span>
								<span class="bz-bars-chart__label" dir="ltr"><?php echo esc_html( $b['label'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="bz-bl-totals">
					<p><span class="bz-bl-totals__num" dir="ltr"><?php echo esc_html( $d['total_value'] ); ?></span><span class="bz-bl-totals__label"><?php echo esc_html( $d['total_label'] ); ?></span></p>
					<span class="bz-bl-totals__bar" aria-hidden="true"></span>
					<p><span class="bz-bl-totals__num bz-bl-totals__num--sm" dir="ltr"><?php echo esc_html( $d['avg_value'] ); ?></span><span class="bz-bl-totals__label"><?php echo esc_html( $d['avg_label'] ); ?></span></p>
				</div>
			</div>
		</div>

		<div class="bz-bl-panel" role="tabpanel" id="bz-bl-panel-2" aria-labelledby="bz-bl-tab-2" tabindex="0" hidden>
			<div class="bz-bl-potential">
				<ul class="bz-bl-facts" role="list">
					<?php foreach ( array( 'pot_1', 'pot_2' ) as $bz_key ) : ?>
						<?php if ( '' !== trim( $d[ $bz_key ] ) ) : ?>
							<li class="bz-bl-fact bz-bl-fact--green"><span class="bz-bl-fact__dot" aria-hidden="true"></span><span><?php echo esc_html( $d[ $bz_key ] ); ?>
							<?php
							if ( '' !== trim( $d[ $bz_key . '_note' ] ) ) :
								?>
								<small><?php echo esc_html( $d[ $bz_key . '_note' ] ); ?></small><?php endif; ?></span></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div class="bz-bl-donut bz-bl-donut--potential"><?php bizmax_donut( (array) $d['potential'], 'potential' ); ?></div>
				<ul class="bz-bl-facts" role="list">
					<?php foreach ( array( 'pot_3', 'pot_4' ) as $bz_key ) : ?>
						<?php if ( '' !== trim( $d[ $bz_key ] ) ) : ?>
							<li class="bz-bl-fact"><span class="bz-bl-fact__dot" aria-hidden="true"></span><span><?php echo esc_html( $d[ $bz_key ] ); ?>
							<?php
							if ( '' !== trim( $d[ $bz_key . '_note' ] ) ) :
								?>
								<small><?php echo esc_html( $d[ $bz_key . '_note' ] ); ?></small><?php endif; ?></span></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php bizmax_chart_legend( (array) $d['potential'], 'bz-legend--center' ); ?>
		</div>

		<div class="bz-bl-panel" role="tabpanel" id="bz-bl-panel-3" aria-labelledby="bz-bl-tab-3" tabindex="0" hidden>
			<div class="bz-bl-funnel">
				<div class="bz-bl-funnel__shape" aria-hidden="true">
					<?php foreach ( $funnel as $bz_i => $f ) : ?>
						<span class="bz-bl-funnel__step bz-bl-funnel__step--<?php echo (int) $bz_i + 1; ?>"></span>
					<?php endforeach; ?>
				</div>
				<ol class="bz-bl-funnel__list">
					<?php foreach ( $funnel as $bz_i => $f ) : ?>
						<li class="bz-bl-funnel__item bz-bl-funnel__item--<?php echo (int) $bz_i + 1; ?>">
							<span class="bz-bl-funnel__key" aria-hidden="true"></span>
							<div><h3 class="bz-bl-funnel__title"><?php echo esc_html( $f['title'] ); ?></h3><p class="bz-bl-funnel__text"><?php echo esc_html( $f['text'] ); ?></p></div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
	</div>
</section>
