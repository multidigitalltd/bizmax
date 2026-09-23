<?php
/**
 * Archives (Elementor Pro "archive" location is honoured when defined).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Elementor Pro renders its archive template (when one exists) inside the same <main> landmark
// so the skip link and the drawer's inert handling keep working.
ob_start();
$bz_elementor_location = function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' );
$bz_elementor_output   = ob_get_clean();
?>
<main id="main" class="bz-main<?php echo $bz_elementor_location ? '' : ' bz-page'; ?>">
<?php if ( $bz_elementor_location ) : ?>
	<?php echo $bz_elementor_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor template output. ?>
<?php else : ?>
		<div class="bz-content">
			<header class="bz-entry__header">
				<?php the_archive_title( '<h1 class="bz-page__title">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="bz-entry__meta">', '</div>' ); ?>
			</header>
			<?php get_template_part( 'template-parts/loop' ); ?>
		</div>
<?php endif; ?>
</main>
<?php
get_footer();
