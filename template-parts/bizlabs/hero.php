<?php
/**
 * BizLabs: hero.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d = $args['data'];
?>
<section class="bz-bl-hero" id="lhero" aria-labelledby="bz-bl-title">
	<span class="bz-bl-hero__ring" aria-hidden="true"></span>
	<div class="bz-wrap bz-bl-hero__inner">
		<div class="bz-bl-hero__text">
			<h1 class="bz-bl-hero__title" id="bz-bl-title">
				<?php echo esc_html( $d['title_before'] ); ?>
				<?php if ( '' !== trim( $d['highlight'] ) ) : ?>
					<span class="hl"><?php echo esc_html( $d['highlight'] ); ?></span>
				<?php endif; ?>
				<?php echo esc_html( $d['title_after'] ); ?>
			</h1>
			<?php bizmax_paragraphs( $d['text'], 'bz-bl-hero__lead' ); ?>
			<?php if ( '' !== trim( $d['cta_text'] ) ) : ?>
				<a class="bz-bl-btn" href="<?php echo esc_url( $d['cta_url'] ); ?>"><?php echo esc_html( $d['cta_text'] ); ?> <span class="arr" aria-hidden="true">&gt;&gt;</span></a>
			<?php endif; ?>
		</div>

		<div class="bz-bl-hero__visual">
			<div class="bz-bl-hero__frame">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
				echo bizmax_image(
					(int) $d['image'],
					'large',
					'bizlabs/hero',
					array(
						'class'         => 'bz-bl-hero__img',
						'alt'           => $d['image_alt'],
						'fetchpriority' => 'high',
						'sizes'         => '(max-width: 1024px) 100vw, 580px',
					)
				);
				?>
				<div class="bz-bl-hero__fx" aria-hidden="true">
					<span class="bz-bl-hero__vignette"></span>
					<span class="bz-bl-hero__halo"></span>
					<svg class="bz-bl-hero__rays" viewBox="0 0 200 200" focusable="false"><g stroke="rgba(255,236,150,.55)" stroke-width="2" stroke-linecap="round"><path d="M100 26 V6"/><path d="M100 174 V194"/><path d="M26 100 H6"/><path d="M174 100 H194"/><path d="M48 48 L34 34"/><path d="M152 48 L166 34"/><path d="M48 152 L34 166"/><path d="M152 152 L166 166"/></g></svg>
					<?php
					$bz_dx = array( -6, 9, -12, 15, -18, 21, -8, 11, -14 );
					foreach ( $bz_dx as $bz_i => $bz_x ) {
						printf(
							'<span class="bz-bl-spark" style="--dx:%1$dpx;--sz:%2$dpx;--l:%3$s%%;--d:%4$ss;--t:%5$ss;--c:%6$s"></span>',
							(int) $bz_x,
							(int) array( 4, 6, 8 )[ $bz_i % 3 ],
							esc_attr( number_format( 30 + $bz_i * 5.2, 1, '.', '' ) ),
							esc_attr( array( '4.2', '4.8', '5.4', '6.0' )[ $bz_i % 4 ] ),
							esc_attr( number_format( $bz_i * 0.7, 1, '.', '' ) ),
							0 === $bz_i % 3 ? '#F6C43C' : '#2fd68a'
						);
					}
					?>
					<span class="bz-bl-hero__cap bz-bl-hero__cap--line"></span>
					<span class="bz-bl-hero__cap bz-bl-hero__cap--glass"></span>
					<svg class="bz-bl-hero__scribble" width="54" height="90" viewBox="0 0 54 90" focusable="false"><path class="scribble" pathLength="1" d="M8 4 C34 14 4 26 30 36 C4 46 34 58 10 70 C30 78 18 86 26 88" fill="none" stroke="#2E4F8F" stroke-width="3" stroke-linecap="round"/></svg>
					<span class="bz-bl-hero__diamond"></span>
				</div>
			</div>
			<?php if ( '' !== trim( $d['badge'] ) ) : ?>
				<p class="bz-bl-badge bz-bl-hero__badge"><?php bizmax_flower( 'mint', 28 ); ?><span><?php echo esc_html( $d['badge'] ); ?></span></p>
			<?php endif; ?>
		</div>
	</div>
</section>
