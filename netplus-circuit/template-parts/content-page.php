<?php
/**
 * Default page layout.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'np-single' ); ?>>
	<?php if ( ! is_front_page() ) : ?>
		<header class="np-single__header">
			<h1 class="np-single__title entry-title"><?php the_title(); ?></h1>
		</header>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() && ! is_front_page() ) : ?>
		<div class="np-single__thumb">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content np-entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'netplus-circuit' ),
			'after'  => '</div>',
		) );
		?>
	</div>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<?php comments_template(); ?>
	<?php endif; ?>
</article>
