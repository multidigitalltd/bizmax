<?php
/**
 * Default page template. Elementor pages render their own layout inside <main>;
 * regular pages get a readable content container.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="bz-main bz-page">
	<?php
	while ( have_posts() ) :
		the_post();
		$bz_elementor = 'builder' === get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( $bz_elementor ? 'bz-elementor' : 'bz-content' ); ?>>
			<?php if ( ! $bz_elementor ) : ?>
				<h1 class="bz-page__title"><?php the_title(); ?></h1>
			<?php endif; ?>
			<?php
			the_content();
			wp_link_pages( array( 'before' => '<nav class="bz-pages" aria-label="' . esc_attr__( 'עמודים', 'bizmax' ) . '">', 'after' => '</nav>' ) );
			?>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</main>
<?php
get_footer();
