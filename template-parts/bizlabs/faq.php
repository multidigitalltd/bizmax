<?php
/**
 * BizLabs: FAQ (native <details>, two columns).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$d     = $args['data'];
$items = array_values( array_filter( (array) $d['items'], static fn( $q ) => '' !== trim( (string) $q['question'] ) ) );
$half  = (int) ceil( count( $items ) / 2 );
?>
<section class="bz-bl-faq bz-bl-tint rv" id="lfaq" aria-labelledby="bz-bl-faq-title">
	<div class="bz-wrap">
		<div class="bz-bl-faq__head">
			<div>
				<h2 class="bz-h2" id="bz-bl-faq-title"><span><?php echo esc_html( $d['heading'] ); ?></span><?php bizmax_flower( 'mint' ); ?></h2>
				<?php if ( '' !== trim( $d['subtitle'] ) ) : ?>
					<p class="bz-bl-sub bz-bl-sub--sm bz-bl-sub--flush"><?php echo esc_html( $d['subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( '' !== trim( $d['cta_text'] ) ) : ?>
				<a class="bz-bl-pill" href="<?php echo esc_url( $d['cta_url'] ); ?>"><?php echo esc_html( $d['cta_text'] ); ?> <span class="arr" aria-hidden="true">&gt;&gt;</span></a>
			<?php endif; ?>
		</div>
		<div class="bz-bl-faq__grid">
			<?php foreach ( array( array_slice( $items, 0, $half ), array_slice( $items, $half ) ) as $bz_col ) : ?>
				<div class="bz-bl-faq__col">
					<?php foreach ( $bz_col as $q ) : ?>
						<details class="bz-faq">
							<summary><?php echo esc_html( $q['question'] ); ?></summary>
							<div class="bz-faq__a"><?php bizmax_paragraphs( $q['answer'] ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
