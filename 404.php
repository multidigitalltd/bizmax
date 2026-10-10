<?php
/**
 * Not found.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="bz-main bz-page">
	<div class="bz-content bz-404">
		<h1 class="bz-page__title"><?php esc_html_e( 'העמוד לא נמצא', 'bizmax' ); ?></h1>
		<p><?php esc_html_e( 'נראה שהעמוד שחיפשתם הועבר או שאינו קיים. אפשר לחזור לדף הבית או לחפש באתר.', 'bizmax' ); ?></p>
		<p><a class="bz-btn bz-btn--secondary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'לדף הבית', 'bizmax' ); ?> <span class="arr" aria-hidden="true">&gt;&gt;</span></a></p>
		<?php get_search_form(); ?>
	</div>
</main>
<?php
get_footer();
