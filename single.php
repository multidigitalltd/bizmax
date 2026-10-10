<?php
/**
 * Single post (Elementor Pro "single" location is honoured when defined).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Elementor Pro renders its single template (when one exists) inside the same <main> landmark
// so the skip link and the drawer's inert handling keep working.
ob_start();
$bz_elementor_location = function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' );
$bz_elementor_output   = ob_get_clean();
?>
<main id="main" class="bz-main<?php echo $bz_elementor_location ? '' : ' bz-page'; ?>">
<?php if ( $bz_elementor_location ) : ?>
	<?php echo $bz_elementor_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor template output. ?>
<?php else : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'bz-content' ); ?>>
				<header class="bz-entry__header">
					<h1 class="bz-page__title"><?php the_title(); ?></h1>
					<p class="bz-entry__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="bz-entry__thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<?php the_content(); ?>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
<?php endif; ?>
</main>
<?php
get_footer();
