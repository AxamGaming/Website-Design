<?php
/**
 * Latest blog posts preview (Alphatronic tech-blog style cards).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_blog', true ) ) {
	return;
}

$np_posts = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'no_found_rows'  => true,
	'post_status'    => 'publish',
) );

if ( empty( $np_posts ) ) {
	return;
}

$np_blog_id  = (int) get_option( 'page_for_posts' );
$np_blog_url = $np_blog_id ? get_permalink( $np_blog_id ) : get_post_type_archive_link( 'post' );

np_section_heading(
	get_theme_mod( 'np_blog_title', __( 'Tech Updates & Guides', 'netplus-circuit' ) ),
	__( 'Buying guides, repair tips and the latest tech news from our workshop', 'netplus-circuit' ),
	$np_blog_url,
	__( 'Read the blog', 'netplus-circuit' )
);
?>
<div class="np-posts">
	<?php foreach ( $np_posts as $np_p ) :
		setup_postdata( $GLOBALS['post'] = $np_p ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$np_cats = get_the_category( $np_p->ID );
		?>
		<article class="np-post-card">
			<a class="np-post-card__media" href="<?php echo esc_url( get_permalink( $np_p ) ); ?>" aria-hidden="true" tabindex="-1">
				<?php if ( has_post_thumbnail( $np_p ) ) : ?>
					<?php echo get_the_post_thumbnail( $np_p, 'np-post-card', array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.85);"><?php echo np_icon( 'cpu', 56); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $np_cats ) ) : ?>
					<span class="np-post-card__cat"><?php echo esc_html( $np_cats[0]->name ); ?></span>
				<?php endif; ?>
			</a>
			<div class="np-post-card__body">
				<div class="np-post-card__meta">
					<span><?php echo np_icon( 'calendar', 13); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( get_the_date( '', $np_p ) ); ?></span>
					<span><?php echo np_icon( 'clock', 13); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( np_reading_time( $np_p->ID ) ); ?></span>
				</div>
				<h3 class="np-post-card__title"><a href="<?php echo esc_url( get_permalink( $np_p ) ); ?>"><?php echo esc_html( get_the_title( $np_p ) ); ?></a></h3>
				<p class="np-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $np_p ), 20 ) ); ?></p>
				<a class="np-post-card__more" href="<?php echo esc_url( get_permalink( $np_p ) ); ?>">
					<?php esc_html_e( 'Read more', 'netplus-circuit' ); ?> <?php echo np_icon( 'arrow-r', 14); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>
		</article>
	<?php endforeach; ?>
	<?php wp_reset_postdata(); ?>
</div>
