<?php
/**
 * Comments template (accessible, minimal).
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="bz-comments" aria-labelledby="bz-comments-title">
	<?php if ( have_comments() ) : ?>
		<h2 class="bz-comments__title" id="bz-comments-title">
			<?php
			$bz_count = get_comments_number();
			/* translators: %s: number of comments */
			printf( esc_html( _n( 'תגובה אחת', '%s תגובות', $bz_count, 'bizmax' ) ), esc_html( number_format_i18n( $bz_count ) ) );
			?>
		</h2>
		<ol class="bz-comments__list">
			<?php wp_list_comments( array( 'style' => 'ol', 'avatar_size' => 44 ) ); ?>
		</ol>
		<?php the_comments_navigation( array( 'screen_reader_text' => __( 'ניווט בין תגובות', 'bizmax' ) ) ); ?>
	<?php else : ?>
		<h2 class="screen-reader-text" id="bz-comments-title"><?php esc_html_e( 'תגובות', 'bizmax' ); ?></h2>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="bz-comments__closed"><?php esc_html_e( 'התגובות סגורות.', 'bizmax' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</section>
