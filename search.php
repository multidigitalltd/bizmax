<?php
/**
 * Search results.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="bz-main bz-page">
	<div class="bz-content">
		<h1 class="bz-page__title">
			<?php
			/* translators: %s: search query */
			printf( esc_html__( 'תוצאות חיפוש עבור: %s', 'bizmax' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
			?>
		</h1>
		<?php get_search_form(); ?>
		<?php get_template_part( 'template-parts/loop' ); ?>
	</div>
</main>
<?php
get_footer();
