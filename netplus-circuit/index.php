<?php
/**
 * Main blog listing / fallback template.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">

	<?php if ( ! is_front_page() ) : ?>
		<?php np_page_hero(); ?>
	<?php endif; ?>

	<div class="np-container np-section">
		<div class="np-content-grid">
			<div class="np-content-main">
				<?php if ( have_posts() ) : ?>
					<div class="np-blog-list">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content', get_post_type() );
						endwhile;
						?>
					</div>

					<nav class="np-pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'netplus-circuit' ); ?>">
						<?php
						the_posts_pagination( array(
							'mid_size'  => 2,
							'prev_text' => np_icon( 'chevron-l', 16 ) . '<span>' . esc_html__( 'Previous', 'netplus-circuit' ) . '</span>',
							'next_text' => '<span>' . esc_html__( 'Next', 'netplus-circuit' ) . '</span>' . np_icon( 'chevron-r', 16 ),
						) );
						?>
					</nav>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content-none' ); ?>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>

</main>

<?php
get_footer();
