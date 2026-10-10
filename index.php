<?php
/**
 * Fallback template (blog index).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="bz-main bz-page">
	<div class="bz-content">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="bz-page__title"><?php single_post_title(); ?></h1>
		<?php endif; ?>
		<?php get_template_part( 'template-parts/loop' ); ?>
	</div>
</main>
<?php
get_footer();
