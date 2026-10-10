<?php
/**
 * BizLabs: industry board.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d       = $args['data'];
$members = array_values( array_filter( (array) $d['members'], static fn( $bz_member ) => '' !== trim( (string) $bz_member['name'] ) ) );
?>
<section class="bz-bl-board bz-bl-tint rv" id="lboard" aria-labelledby="bz-bl-board-title">
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-bl-h2--gap" id="bz-bl-board-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
		<ul class="bz-bl-board__grid" role="list">
			<?php foreach ( $members as $bz_member ) : ?>
				<li class="bz-bl-member<?php echo (int) $bz_member['image'] ? '' : ' bz-bl-member--empty'; ?>">
					<?php if ( (int) $bz_member['image'] ) : ?>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
						echo bizmax_image(
							(int) $bz_member['image'],
							'large',
							'',
							array(
								'class'   => 'bz-bl-member__img',
								'alt'     => '',
								'loading' => 'lazy',
							)
						);
						?>
					<?php else : ?>
						<span class="bz-bl-member__initials" aria-hidden="true" dir="ltr"><?php echo esc_html( bizmax_initials( $bz_member['name'] ) ); ?></span>
					<?php endif; ?>
					<span class="bz-bl-member__shade" aria-hidden="true"></span>
					<span class="bz-bl-member__accent" aria-hidden="true"></span>
					<div class="bz-bl-member__text" dir="ltr" lang="en">
						<h3 class="bz-bl-member__name"><?php echo esc_html( $bz_member['name'] ); ?></h3>
						<?php if ( '' !== trim( $bz_member['title'] ) ) : ?>
							<p class="bz-bl-member__title"><?php echo esc_html( $bz_member['title'] ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== trim( $bz_member['company'] ) ) : ?>
							<p class="bz-bl-member__company"><?php echo esc_html( $bz_member['company'] ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
