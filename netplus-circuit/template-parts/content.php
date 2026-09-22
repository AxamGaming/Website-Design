<?php
/**
 * Blog card used in archive/index loops.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

$np_cats = get_the_category();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'np-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="np-post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'np-post-card', array( 'loading' => 'lazy' ) ); ?>
			<?php if ( ! empty( $np_cats ) ) : ?>
				<span class="np-post-card__cat"><?php echo esc_html( $np_cats[0]->name ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>
	<div class="np-post-card__body">
		<div class="np-post-card__meta">
			<?php np_posted_on(); ?>
			<?php np_posted_by(); ?>
		</div>
		<h2 class="np-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="np-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<a class="np-post-card__more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'netplus-circuit' ); ?> <?php echo np_icon( 'arrow-r', 14); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</div>
</article>
