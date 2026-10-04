<?php
/**
 * Template Name: BizLabs (Bizmax)
 *
 * The BizLabs programme page. Every section reads its content from the page's own meta
 * (edited in the "תוכן עמוד ביזלאבס" box), so nothing here is hard-coded.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bz_content = bizmax_page_get( 'bizlabs', (int) get_the_ID() );
?>
<main id="main" class="bz-main bz-bl">
	<?php
	get_template_part( 'template-parts/bizlabs/hero', null, array( 'data' => $bz_content['hero'] ) );

	foreach ( array( 'tracks', 'about', 'stats', 'team', 'board', 'stories', 'faq', 'data', 'partners', 'form', 'more' ) as $bz_key ) {
		if ( empty( $bz_content[ $bz_key ]['enabled'] ) ) {
			continue;
		}
		get_template_part(
			'template-parts/bizlabs/' . $bz_key,
			null,
			array(
				'data'    => $bz_content[ $bz_key ],
				'page_id' => (int) get_the_ID(),
			)
		);
	}

	// Floating "sign up" button (inside <main> so it belongs to a landmark).
	$bz_head = $bz_content['header'];
	if ( ! empty( $bz_head['cta'] ) && '' !== trim( $bz_head['cta_text'] ) ) {
		$bz_cta_side = ( bizmax_mod( 'bizmax_a11y_panel' ) && 'left' !== bizmax_mod( 'bizmax_a11y_side' ) ) ? ' bz-bl-cta--beside' : '';
		printf(
			'<a class="bz-bl-cta%1$s" href="%2$s">%3$s</a>',
			esc_attr( $bz_cta_side ),
			esc_url( $bz_head['cta_url'] ),
			esc_html( $bz_head['cta_text'] )
		);
	}
	?>
</main>
<?php
get_footer();
