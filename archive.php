<?php
/**
 * Archives (Elementor Pro "archive" location is honoured when defined).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) :
	?>
	<main id="main" class="bz-main bz-page">
		<div class="bz-content">
			<header class="bz-entry__header">
				<?php the_archive_title( '<h1 class="bz-page__title">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="bz-entry__meta">', '</div>' ); ?>
			</header>
			<?php get_template_part( 'template-parts/loop' ); ?>
		</div>
	</main>
	<?php
endif;

get_footer();
