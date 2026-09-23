<?php
/**
 * Home: founding partners.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d        = $args['data'];
$fallback = array( 'logo-kemach', 'logo-achim', 'logo-jda', 'logo-jerusalem-heritage' );
$items    = array_values( array_filter( (array) $d['items'], static fn( $p ) => '' !== trim( (string) $p['name'] ) ) );

if ( ! $items ) {
	return;
}
?>
<section class="bz-partners rv" id="partners" aria-labelledby="bz-partners-title">
	<div class="bz-wrap">
		<h2 class="bz-h2 bz-partners__title" id="bz-partners-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'orange' ); ?></h2>
		<ul class="bz-partners__grid">
			<?php foreach ( $items as $i => $p ) : ?>
				<li class="bz-partner">
					<div class="bz-partner__logo">
						<?php echo bizmax_image( (int) $p['logo'], 'medium', $fallback[ $i % 4 ], array( 'alt' => $p['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<h3 class="bz-partner__name">
						<?php if ( '' !== $p['url'] ) : ?>
							<a href="<?php echo esc_url( $p['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $p['name'] ); ?><span class="screen-reader-text"> (<?php esc_html_e( 'נפתח בחלון חדש', 'bizmax' ); ?>)</span></a>
						<?php else : ?>
							<?php echo esc_html( $p['name'] ); ?>
						<?php endif; ?>
					</h3>
					<?php bizmax_text( $p['text'], 'bz-partner__text' ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
