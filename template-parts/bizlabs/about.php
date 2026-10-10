<?php
/**
 * BizLabs: about (with "read more").
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d         = $args['data'];
$operators = array_values( array_filter( (array) $d['operators'], static fn( $o ) => (int) $o['image'] || '' !== (string) $o['asset'] ) );
?>
<section class="bz-bl-about rv" id="labout" aria-labelledby="bz-bl-about-title">
	<div class="bz-wrap bz-bl-about__inner">
		<div class="bz-bl-about__text">
			<h2 class="bz-h2" id="bz-bl-about-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
			<?php bizmax_paragraphs( $d['lead'], 'bz-bl-about__lead' ); ?>
			<?php if ( '' !== trim( $d['more'] ) ) : ?>
				<div class="bz-bl-about__more" id="bz-bl-about-more" hidden>
					<?php bizmax_paragraphs( $d['more'], 'bz-bl-about__p' ); ?>
				</div>
				<button type="button" class="bz-bl-more-btn" aria-expanded="false" aria-controls="bz-bl-about-more" data-bz-more data-more="<?php echo esc_attr( $d['more_label'] ); ?>" data-less="<?php echo esc_attr( $d['less_label'] ); ?>"><span data-bz-more-label><?php echo esc_html( $d['more_label'] ); ?></span> <span class="arr" aria-hidden="true">&gt;&gt;</span></button>
			<?php endif; ?>
			<?php if ( $operators ) : ?>
				<div class="bz-bl-about__ops">
					<p class="bz-bl-label"><?php echo esc_html( $d['operators_label'] ); ?></p>
					<ul class="bz-bl-chips" role="list">
						<?php foreach ( $operators as $o ) : ?>
							<li class="bz-bl-chip">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
							echo bizmax_row_image(
								$o,
								'medium',
								array(
									'alt'     => $o['alt'],
									'loading' => 'lazy',
								)
							);
							?>
													</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		<div class="bz-bl-about__visual">
			<div class="bz-bl-about__frame">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
				echo bizmax_image(
					(int) $d['image'],
					'large',
					'bizlabs/about',
					array(
						'class'   => 'bz-bl-about__img',
						'alt'     => $d['image_alt'],
						'loading' => 'lazy',
					)
				);
				?>
			</div>
			<?php if ( '' !== trim( $d['badge_title'] ) ) : ?>
				<div class="bz-bl-badge bz-bl-about__badge">
					<?php if ( '' !== trim( $d['badge_num'] ) ) : ?>
						<span class="bz-bl-about__badge-num" aria-hidden="true"><?php echo esc_html( $d['badge_num'] ); ?></span>
					<?php endif; ?>
					<div>
						<p class="bz-bl-about__badge-title"><span class="screen-reader-text"><?php echo esc_html( $d['badge_num'] ); ?> </span><?php echo esc_html( $d['badge_title'] ); ?></p>
						<?php if ( '' !== trim( $d['badge_text'] ) ) : ?>
							<p class="bz-bl-about__badge-text"><?php echo esc_html( $d['badge_text'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
