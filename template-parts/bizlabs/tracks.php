<?php
/**
 * BizLabs: tracks.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d     = $args['data'];
$items = array_values( array_filter( (array) $d['items'], static fn( $t ) => '' !== trim( (string) $t['title'] ) ) );
?>
<section class="bz-bl-tracks bz-bl-tint rv" id="ltracks" aria-labelledby="bz-bl-tracks-title">
	<div class="bz-wrap">
		<h2 class="bz-h2" id="bz-bl-tracks-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
		<?php if ( '' !== trim( $d['subtitle'] ) ) : ?>
			<p class="bz-bl-sub"><?php echo esc_html( $d['subtitle'] ); ?></p>
		<?php endif; ?>
		<ul class="bz-bl-tracks__grid" role="list">
			<?php foreach ( $items as $bz_i => $t ) : ?>
				<li class="bz-trk">
					<p class="bz-trk__top"><span class="bz-trk__kicker"><?php echo esc_html( $d['kicker'] ); ?></span><span class="bz-trk__rule" aria-hidden="true"></span><span class="bz-trk__num"><?php echo esc_html( sprintf( '%02d', $bz_i + 1 ) ); ?></span></p>
					<div>
						<h3 class="bz-trk__title"><?php echo esc_html( $t['title'] ); ?></h3>
						<?php if ( '' !== trim( $t['english'] ) ) : ?>
							<p class="bz-trk__en" dir="ltr" lang="en"><?php echo esc_html( $t['english'] ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== trim( $t['tagline'] ) ) : ?>
							<p class="bz-trk__tag"><?php echo esc_html( $t['tagline'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php bizmax_text( $t['text'], 'bz-trk__text' ); ?>
					<?php if ( '' !== trim( $t['audience'] ) ) : ?>
						<div class="bz-trk__aud">
							<p class="bz-trk__aud-label"><?php echo esc_html( $d['audience_label'] ); ?></p>
							<p class="bz-trk__aud-text"><?php echo esc_html( $t['audience'] ); ?></p>
						</div>
					<?php endif; ?>
					<?php if ( '' !== trim( $d['link_text'] ) && '' !== trim( $t['url'] ) ) : ?>
						<a class="bz-trk__link" href="<?php echo esc_url( $t['url'] ); ?>"><?php echo esc_html( $d['link_text'] ); ?><span class="screen-reader-text"> – <?php echo esc_html( $t['title'] ); ?></span> <span class="arr" aria-hidden="true">&gt;&gt;</span></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
