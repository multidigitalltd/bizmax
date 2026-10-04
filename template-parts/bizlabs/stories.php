<?php
/**
 * BizLabs: success stories.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d     = $args['data'];
$items = array_values( array_filter( (array) $d['items'], static fn( $bz_story ) => '' !== trim( (string) $bz_story['name'] ) ) );
?>
<section class="bz-bl-stories bz-bl-tint rv" id="lstories" aria-labelledby="bz-bl-stories-title">
	<div class="bz-wrap">
		<h2 class="bz-h2" id="bz-bl-stories-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
		<?php if ( '' !== trim( $d['subtitle'] ) ) : ?>
			<p class="bz-bl-sub bz-bl-sub--sm"><?php echo esc_html( $d['subtitle'] ); ?></p>
		<?php endif; ?>
		<ul class="bz-bl-stories__grid" role="list">
			<?php foreach ( $items as $bz_i => $bz_story ) : ?>
				<?php
				$bz_logo = bizmax_row_image(
					$bz_story,
					'medium',
					array(
						'alt'     => sprintf( /* translators: %s: company name. */ __( 'הלוגו של %s', 'bizmax' ), $bz_story['name'] ),
						'loading' => 'lazy',
					),
					'logo'
				);
				?>
				<li class="bz-story">
					<div class="bz-story__head">
						<div>
							<h3 class="bz-story__name" dir="ltr" lang="en"><?php echo esc_html( $bz_story['name'] ); ?></h3>
							<?php if ( '' !== trim( $bz_story['former'] ) ) : ?>
								<p class="bz-story__former"><?php echo esc_html( $d['former_label'] ); ?> <span dir="ltr" lang="en"><?php echo esc_html( $bz_story['former'] ); ?></span></p>
							<?php endif; ?>
						</div>
						<span class="bz-story__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $bz_i + 1 ) ); ?></span>
					</div>
					<?php if ( '' !== trim( $bz_story['founders'] ) ) : ?>
						<p class="bz-story__founders"><span class="bz-story__bar" aria-hidden="true"></span><span><span class="screen-reader-text"><?php esc_html_e( 'מייסדים:', 'bizmax' ); ?> </span><?php echo esc_html( $bz_story['founders'] ); ?></span></p>
					<?php endif; ?>
					<?php bizmax_text( $bz_story['text'], 'bz-story__text' ); ?>
					<?php if ( '' !== $bz_logo ) : ?>
						<span class="bz-story__logo"><?php echo $bz_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
