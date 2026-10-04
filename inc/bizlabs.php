<?php
/**
 * BizLabs page (template-bizlabs.php): helpers, chart markup, assets and the one-time draft page.
 *
 * Content lives in the page itself (meta "_bizmax_bizlabs", schema in inc/bizlabs-schema.php) and
 * is edited in the "תוכן עמוד ביזלאבס" box, exactly like the home page.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print multi-paragraph text: a blank line starts a new paragraph, single newlines become <br>.
 *
 * @param string $text  Stored text (basic HTML allowed).
 * @param string $css_class Paragraph class.
 */
function bizmax_paragraphs( string $text, string $css_class = '' ): void {
	foreach ( preg_split( '/\R\s*\R/u', trim( $text ) ) as $para ) {
		if ( '' !== trim( $para ) ) {
			bizmax_text( $para, $css_class );
		}
	}
}

/**
 * Image of a repeater row: the chosen image, else the bundled asset, else nothing.
 *
 * @param array<string,mixed>  $row   Row (image / asset / logo keys).
 * @param string               $size  Image size.
 * @param array<string,string> $attr  Attributes.
 * @param string               $field Image field key.
 */
function bizmax_row_image( array $row, string $size, array $attr, string $field = 'image' ): string {
	return bizmax_image( (int) ( $row[ $field ] ?? 0 ), $size, (string) ( $row['asset'] ?? '' ), $attr );
}

/**
 * Initials for a person without a photo ("אליהו דינוביץ" → "אד").
 *
 * @param string $name Name.
 */
function bizmax_initials( string $name ): string {
	$parts = preg_split( '/[\s\-]+/u', trim( (string) preg_replace( '/^(Dr\.|ד"ר)\s*/u', '', $name ) ) );
	$out   = '';
	foreach ( array_slice( array_filter( (array) $parts ), 0, 2 ) as $part ) {
		$out .= mb_substr( $part, 0, 1 );
	}
	return mb_strtoupper( $out );
}

/**
 * Donut chart (SVG). Rendered complete on the server; bizlabs.js only animates and adds hover.
 *
 * @param array<int,array<string,mixed>> $rows  Rows with label, value, color.
 * @param string                         $chart Chart key.
 */
function bizmax_donut( array $rows, string $chart ): void {
	$rows  = array_values( array_filter( $rows, static fn( $r ) => (float) ( $r['value'] ?? 0 ) > 0 ) );
	$total = array_sum( array_map( static fn( $r ) => (float) $r['value'], $rows ) );
	if ( $total <= 0 ) {
		return;
	}
	$r   = 70;
	$c   = 2 * M_PI * $r;
	$acc = 0.0;

	printf( '<svg class="bz-donut" viewBox="0 0 200 200" aria-hidden="true" focusable="false" data-bz-donut="%s">', esc_attr( $chart ) );
	foreach ( $rows as $i => $row ) {
		$len = (float) $row['value'] / $total * $c;
		printf(
			'<circle cx="100" cy="100" r="%1$d" fill="none" stroke="%2$s" stroke-width="34" stroke-dasharray="%3$s %4$s" stroke-dashoffset="%5$s" transform="rotate(-90 100 100)" data-len="%3$s" data-i="%6$d" data-label="%7$s" data-pct="%8$s" style="--i:%6$d"/>',
			(int) $r,
			esc_attr( (string) ( sanitize_hex_color( (string) $row['color'] ) ?? '#1B3764' ) ),
			esc_attr( number_format( $len, 3, '.', '' ) ),
			esc_attr( number_format( $c - $len, 3, '.', '' ) ),
			esc_attr( number_format( -$acc, 3, '.', '' ) ),
			(int) $i,
			esc_attr( (string) $row['label'] ),
			esc_attr( bizmax_pct( (float) $row['value'] ) )
		);
		$acc += $len;
	}
	echo '<text class="bz-donut__label" x="100" y="94" text-anchor="middle"></text><text class="bz-donut__pct" x="100" y="116" text-anchor="middle"></text>';
	echo '</svg>';
}

/**
 * Percent label ("21.7%", "24%").
 *
 * @param float $value Value.
 */
function bizmax_pct( float $value ): string {
	return rtrim( rtrim( number_format( $value, 1, '.', '' ), '0' ), '.' ) . '%';
}

/**
 * Legend for a donut chart.
 *
 * @param array<int,array<string,mixed>> $rows  Rows.
 * @param string                         $css_class Extra class.
 */
function bizmax_chart_legend( array $rows, string $css_class = '' ): void {
	echo '<ul class="bz-legend ' . esc_attr( $css_class ) . '">';
	foreach ( $rows as $i => $row ) {
		if ( (float) ( $row['value'] ?? 0 ) <= 0 ) {
			continue;
		}
		printf(
			'<li data-i="%1$d"><span class="bz-legend__dot" style="background:%2$s" aria-hidden="true"></span><span dir="auto">%3$s %4$s</span></li>',
			(int) $i,
			esc_attr( (string) ( sanitize_hex_color( (string) $row['color'] ) ?? '#1B3764' ) ),
			esc_html( (string) $row['label'] ),
			esc_html( bizmax_pct( (float) $row['value'] ) )
		);
	}
	echo '</ul>';
}

/**
 * Enqueue the BizLabs page assets (the shared page script handles reveal, counters and the form).
 */
function bizmax_bizlabs_assets(): void {
	if ( ! bizmax_is_bizlabs_template() ) {
		return;
	}
	wp_enqueue_style( 'bizmax-bizlabs', bizmax_asset( 'css/bizlabs.css' ), array( 'bizmax-main' ), BIZMAX_VERSION );
	wp_enqueue_script( 'bizmax-home', bizmax_asset( 'js/home.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );
	wp_enqueue_script( 'bizmax-bizlabs', bizmax_asset( 'js/bizlabs.js' ), array(), BIZMAX_VERSION, array( 'strategy' => 'defer' ) );
	wp_localize_script( 'bizmax-home', 'bizmaxHome', bizmax_page_script_config() );
}
add_action( 'wp_enqueue_scripts', 'bizmax_bizlabs_assets', 20 );

/**
 * Preload the BizLabs hero image (the largest element above the fold).
 */
function bizmax_bizlabs_preload(): void {
	if ( ! bizmax_is_bizlabs_template() ) {
		return;
	}
	$hero = bizmax_page_get( 'bizlabs', (int) get_queried_object_id() )['hero'];
	if ( ! (int) $hero['image'] ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( BIZMAX_URI . '/assets/img/bizlabs/hero.webp' ) );
	}
}
add_action( 'wp_head', 'bizmax_bizlabs_preload', 2 );

/**
 * Create the BizLabs page once, as a draft, so it can be reviewed before it goes live.
 * Runs on the first admin visit after the update; never touches an existing page.
 */
function bizmax_maybe_create_bizlabs_page(): void {
	if ( (int) get_option( 'bizmax_bizlabs_setup', 0 ) >= 1 || ! current_user_can( 'publish_pages' ) ) {
		return;
	}
	update_option( 'bizmax_bizlabs_setup', 1, false );

	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one-time admin check.
			'meta_value'     => 'template-bizlabs.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		return;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => 'ביזלאבס',
			'post_name'    => 'bizlabs-program',
			'post_content' => '',
		),
		true
	);
	if ( ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_wp_page_template', 'template-bizlabs.php' );
	}
}
add_action( 'admin_init', 'bizmax_maybe_create_bizlabs_page' );
