<?php
/**
 * BizLabs: programme team.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d       = $args['data'];
$members = array_values( array_filter( (array) $d['members'], static fn( $bz_person ) => '' !== trim( (string) $bz_person['name'] ) ) );
?>
<section class="bz-bl-team rv" id="lteam" aria-labelledby="bz-bl-team-title">
	<div class="bz-wrap">
		<h2 class="bz-h2" id="bz-bl-team-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
		<?php if ( '' !== trim( $d['subtitle'] ) ) : ?>
			<p class="bz-bl-sub"><?php echo esc_html( $d['subtitle'] ); ?></p>
		<?php endif; ?>
		<ul class="bz-bl-team__grid" role="list">
			<?php foreach ( $members as $bz_person ) : ?>
				<li class="bz-bl-person">
					<?php if ( (int) $bz_person['image'] ) : ?>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
						echo bizmax_image(
							(int) $bz_person['image'],
							'medium',
							'',
							array(
								'class'   => 'bz-bl-person__img',
								'alt'     => '',
								'loading' => 'lazy',
							)
						);
						?>
					<?php else : ?>
						<span class="bz-bl-person__img bz-bl-person__initials" aria-hidden="true"><?php echo esc_html( bizmax_initials( $bz_person['name'] ) ); ?></span>
					<?php endif; ?>
					<h3 class="bz-bl-person__name"><?php echo esc_html( $bz_person['name'] ); ?></h3>
					<?php if ( '' !== trim( $bz_person['role'] ) ) : ?>
						<p class="bz-bl-person__role"><?php echo esc_html( $bz_person['role'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
