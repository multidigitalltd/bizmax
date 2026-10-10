<?php
/**
 * Home: testimonials carousel (server-rendered; JS rotates).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d        = $args['data'];
$quotes   = array();
$fallback = array( 'portrait-a', 'portrait-b' );

foreach ( (array) $d['items'] as $i => $q ) {
	if ( '' === trim( (string) $q['text'] ) && '' === trim( (string) $q['short'] ) ) {
		continue;
	}
	$img_id   = (int) $q['image'];
	$img_url  = $img_id ? wp_get_attachment_image_url( $img_id, 'bizmax-avatar' ) : '';
	$quotes[] = array(
		'text'  => (string) $q['text'],
		'short' => '' !== trim( (string) $q['short'] ) ? (string) $q['short'] : (string) $q['text'],
		'name'  => (string) $q['name'],
		'role'  => (string) $q['role'],
		'img'   => $img_url ?: BIZMAX_URI . '/assets/img/' . $fallback[ $i % 2 ] . '.webp',
	);
}

if ( ! $quotes ) {
	return;
}

$count = count( $quotes );
$slots = array( $quotes[0], $quotes[ 1 % $count ], $quotes[ 2 % $count ] );
?>
<section class="bz-alumni rv" id="alumni" aria-labelledby="bz-alumni-title" data-bz-alumni>
	<span class="bz-alumni__ring bz-alumni__ring--a" aria-hidden="true"></span>
	<span class="bz-alumni__ring bz-alumni__ring--b" aria-hidden="true"></span>
	<span class="bz-alumni__glyph" aria-hidden="true">”</span>

	<div class="bz-wrap">
		<h2 class="bz-h2 bz-h2--light" id="bz-alumni-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'orange' ); ?></h2>
		<div class="bz-alumni__bar">
			<p class="bz-alumni__sub"><?php echo esc_html( $d['subtitle'] ); ?></p>
			<?php if ( $count > 1 ) : ?>
				<div class="bz-alumni__controls">
					<button type="button" class="bz-alumni__btn" data-bz-next aria-label="<?php esc_attr_e( 'ההמלצה הבאה', 'bizmax' ); ?>"><span aria-hidden="true">&lt;</span></button>
					<button type="button" class="bz-alumni__btn" data-bz-prev aria-label="<?php esc_attr_e( 'ההמלצה הקודמת', 'bizmax' ); ?>"><span aria-hidden="true">&gt;</span></button>
				</div>
			<?php endif; ?>
		</div>
		<p class="screen-reader-text" role="status" aria-live="polite" data-bz-status></p>

		<div class="bz-alumni__grid<?php echo $count < 3 ? ' bz-alumni__grid--' . (int) $count : ''; ?>">
			<figure class="bz-glass bz-glass--lg" data-bz-slot="0">
				<svg class="bz-glass__quote" width="46" height="34" viewBox="0 0 46 34" fill="none" aria-hidden="true" focusable="false"><path d="M0 34V20C0 8 6 1 18 0v8c-5 1-8 4-8 9h8v17H0zm28 0V20C28 8 34 1 46 0v8c-5 1-8 4-8 9h8v17H28z" fill="#F6A81C" transform="scale(-1,1) translate(-46,0)"/></svg>
				<blockquote class="bz-glass__text" data-bz-text><?php echo esc_html( $slots[0]['text'] ); ?></blockquote>
				<figcaption class="bz-glass__author">
					<img class="bz-glass__avatar" src="<?php echo esc_url( $slots[0]['img'] ); ?>" width="60" height="60" alt="" loading="lazy" decoding="async" data-bz-img>
					<span><span class="bz-glass__name" data-bz-name><?php echo esc_html( $slots[0]['name'] ); ?></span><span class="bz-glass__role" data-bz-role><?php echo esc_html( $slots[0]['role'] ); ?></span></span>
				</figcaption>
			</figure>
			<?php if ( $count > 1 ) : ?>
				<div class="bz-alumni__side">
					<?php for ( $i = 1; $i <= min( 2, $count - 1 ); $i++ ) : ?>
						<figure class="bz-glass bz-glass--sm" data-bz-slot="<?php echo (int) $i; ?>">
							<blockquote class="bz-glass__text" data-bz-text><?php echo esc_html( $slots[ $i ]['short'] ); ?></blockquote>
							<figcaption class="bz-glass__author">
								<img class="bz-glass__avatar" src="<?php echo esc_url( $slots[ $i ]['img'] ); ?>" width="44" height="44" alt="" loading="lazy" decoding="async" data-bz-img>
								<span><span class="bz-glass__name" data-bz-name><?php echo esc_html( $slots[ $i ]['name'] ); ?></span><span class="bz-glass__role" data-bz-role><?php echo esc_html( $slots[ $i ]['role'] ); ?></span></span>
							</figcaption>
						</figure>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<script type="application/json" data-bz-quotes><?php echo wp_json_encode( $quotes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>
</section>
