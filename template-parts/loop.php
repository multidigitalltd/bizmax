<?php
/**
 * Generic post loop with pagination.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

if ( ! have_posts() ) {
	echo '<p>' . esc_html__( 'לא נמצאו תוצאות.', 'bizmax' ) . '</p>';
	return;
}
?>
<div class="bz-loop">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'bz-card' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<a class="bz-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'bizmax-card' ); ?></a>
			<?php endif; ?>
			<div class="bz-card__body">
				<h2 class="bz-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="bz-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
				<div class="bz-card__excerpt"><?php the_excerpt(); ?></div>
			</div>
		</article>
	<?php endwhile; ?>
</div>
<?php
the_posts_pagination(
	array(
		'prev_text'          => esc_html__( 'הקודם', 'bizmax' ),
		'next_text'          => esc_html__( 'הבא', 'bizmax' ),
		'screen_reader_text' => esc_html__( 'ניווט בין עמודים', 'bizmax' ),
	)
);
