<?php
/**
 * Template Name: Home (Bizmax)
 *
 * The home page. Every section reads its content from the page's own meta
 * (edited in the "תוכן דף הבית" box), so nothing here is hard-coded.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bz_content = bizmax_home_get( get_the_ID() );
?>
<main id="main" class="bz-main">
	<?php
	get_template_part( 'template-parts/home/hero', null, array( 'data' => $bz_content['hero'] ) );

	foreach ( array( 'coworking', 'deschool', 'bizlabs' ) as $bz_key ) {
		if ( empty( $bz_content[ $bz_key ]['enabled'] ) ) {
			continue;
		}
		get_template_part(
			'template-parts/home/feature',
			null,
			array(
				'key'     => $bz_key,
				'data'    => $bz_content[ $bz_key ],
				'reverse' => 'deschool' === $bz_key,
				'first'   => 'coworking' === $bz_key,
			)
		);
	}

	foreach ( array( 'meetups', 'events', 'about', 'alumni', 'partners', 'more' ) as $bz_key ) {
		if ( empty( $bz_content[ $bz_key ]['enabled'] ) ) {
			continue;
		}
		get_template_part( 'template-parts/home/' . $bz_key, null, array( 'data' => $bz_content[ $bz_key ], 'page_id' => get_the_ID() ) );
	}
	?>
</main>
<?php
get_footer();
