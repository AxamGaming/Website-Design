<?php
/**
 * Generic sidebar (blog / pages).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

$np_sidebar_id = 'page-sidebar';
if ( is_home() || is_singular( 'post' ) || is_archive() || is_search() ) {
	$np_sidebar_id = 'blog-sidebar';
}

if ( ! is_active_sidebar( $np_sidebar_id ) ) {
	// Sensible defaults so the sidebar is never visually empty.
	?>
	<aside class="np-sidebar widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'netplus-circuit' ); ?>">
		<section class="widget np-sidebar-widget">
			<h3 class="widget-title"><?php esc_html_e( 'Search', 'netplus-circuit' ); ?></h3>
			<?php get_search_form(); ?>
		</section>

		<section class="widget np-sidebar-widget">
			<h3 class="widget-title"><?php esc_html_e( 'Categories', 'netplus-circuit' ); ?></h3>
			<ul>
				<?php
				wp_list_categories( array(
					'title_li'     => '',
					'show_count'   => true,
					'depth'        => 2,
					'number'       => 12,
					'orderby'      => 'count',
					'order'        => 'DESC',
					'hide_empty'   => true,
				) );
				?>
			</ul>
		</section>

		<?php
		$np_wa = np_whatsapp_link( __( 'Hi NetPlus! I have a question.', 'netplus-circuit' ) );
		if ( $np_wa ) :
			?>
			<section class="widget np-sidebar-widget" style="background:linear-gradient(135deg,#1b1f27,#2e3440);border:0;color:#d5ddec;">
				<h3 class="widget-title" style="color:#fff;"><?php esc_html_e( 'Need help?', 'netplus-circuit' ); ?></h3>
				<p style="font-size:.88rem;margin-bottom:14px;"><?php esc_html_e( 'Our technicians answer part-compatibility questions free on WhatsApp.', 'netplus-circuit' ); ?></p>
				<a class="np-btn np-btn--wa np-btn--block np-btn--sm" href="<?php echo esc_url( $np_wa ); ?>" target="_blank" rel="noopener">
					<?php echo np_icon( 'whatsapp', 17); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php esc_html_e( 'Chat now', 'netplus-circuit' ); ?>
				</a>
			</section>
		<?php endif; ?>

		<section class="widget np-sidebar-widget">
			<h3 class="widget-title"><?php esc_html_e( 'Recent Posts', 'netplus-circuit' ); ?></h3>
			<ul>
				<?php
				$np_recent = get_posts( array( 'numberposts' => 5, 'no_found_rows' => true ) );
				foreach ( $np_recent as $np_rp ) {
					echo '<li><a href="' . esc_url( get_permalink( $np_rp ) ) . '">' . esc_html( get_the_title( $np_rp ) ) . '</a></li>';
				}
				?>
			</ul>
		</section>
	</aside>
	<?php
	return;
}
?>
<aside class="np-sidebar widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'netplus-circuit' ); ?>">
	<?php dynamic_sidebar( $np_sidebar_id ); ?>
</aside>
