<?php
/**
 * Accessible search form.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

$bz_id = wp_unique_id( 'bz-search-' );
?>
<form role="search" method="get" class="bz-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $bz_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'חיפוש באתר', 'bizmax' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $bz_id ); ?>" class="bz-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'חיפוש…', 'bizmax' ); ?>">
	<button type="submit" class="bz-btn bz-btn--secondary bz-search__btn"><?php esc_html_e( 'חיפוש', 'bizmax' ); ?></button>
</form>
