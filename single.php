<?php
/**
 * Single post (Elementor Pro "single" location is honoured when defined).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) :
	?>
	<main id="main" class="bz-main bz-page">
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
	</main>
	<?php
endif;

get_footer();
