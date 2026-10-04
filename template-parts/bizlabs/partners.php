<?php
/**
 * BizLabs: industry partners.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d     = $args['data'];
$logos = array_values( array_filter( (array) $d['logos'], static fn( $l ) => (int) $l['image'] || '' !== (string) $l['asset'] ) );
?>
<section class="bz-bl-partners rv" id="lpartners" aria-labelledby="bz-bl-partners-title">
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-bl-h2--gap" id="bz-bl-partners-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
		<ul class="bz-bl-partners__grid" role="list">
			<?php foreach ( $logos as $l ) : ?>
				<?php
				$bz_img = bizmax_row_image(
					$l,
					'medium',
					array(
						'alt'     => $l['alt'],
						'loading' => 'lazy',
					)
				);
				?>
				<li class="bz-bl-partner">
					<?php if ( '' !== trim( $l['url'] ) ) : ?>
						<a href="<?php echo esc_url( $l['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $bz_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?><span class="screen-reader-text"> (<?php esc_html_e( 'נפתח בחלון חדש', 'bizmax' ); ?>)</span></a>
					<?php else : ?>
						<?php echo $bz_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
