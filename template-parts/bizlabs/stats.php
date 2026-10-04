<?php
/**
 * BizLabs: numbers.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d     = $args['data'];
$items = array_values( array_filter( (array) $d['items'], static fn( $bz_stat ) => '' !== trim( (string) $bz_stat['label'] ) ) );
?>
<section class="bz-bl-stats rv" id="lstats" aria-labelledby="bz-bl-stats-title">
	<div class="bz-wrap">
		<div class="bz-bl-stats__head">
			<h2 class="bz-bl-stats__title" id="bz-bl-stats-title"><?php echo esc_html( $d['heading'] ); ?></h2>
			<span class="bz-bl-stats__line" aria-hidden="true"></span>
		</div>
		<ul class="bz-bl-stats__grid" role="list">
			<?php foreach ( $items as $bz_stat ) : ?>
				<?php
				$bz_num    = (int) $bz_stat['number'];
				$bz_suffix = (string) $bz_stat['suffix'];
				$bz_full   = number_format( $bz_num ) . $bz_suffix;
				?>
				<li class="bz-bl-stat<?php echo ! empty( $bz_stat['highlight'] ) ? ' bz-bl-stat--hl' : ''; ?>">
					<p class="bz-bl-stat__num"><span dir="ltr" class="count" data-count="<?php echo (int) $bz_num; ?>" data-suffix="<?php echo esc_attr( $bz_suffix ); ?>" aria-hidden="true"><?php echo esc_html( $bz_full ); ?></span><span class="screen-reader-text"><?php echo esc_html( $bz_full ); ?></span></p>
					<span class="bz-bl-stat__bar" aria-hidden="true"></span>
					<p class="bz-bl-stat__label"><?php echo esc_html( $bz_stat['label'] ); ?></p>
					<?php if ( '' !== trim( $bz_stat['sub'] ) ) : ?>
						<p class="bz-bl-stat__sub"><?php echo esc_html( $bz_stat['sub'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
