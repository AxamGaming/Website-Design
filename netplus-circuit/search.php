<?php
/**
 * Search results (posts/pages; product-only searches are handled by WooCommerce).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">

	<section class="np-page-hero">
		<div class="np-container">
			<h1 class="np-page-hero__title">
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Search results for “%s”', 'netplus-circuit' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
			<?php np_breadcrumbs(); ?>
		</div>
	</section>

	<div class="np-container np-section">
		<div class="np-content-grid">
			<div class="np-content-main">
				<?php if ( have_posts() ) : ?>
					<div class="np-blog-list">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content', 'search' );
						endwhile;
						?>
					</div>
					<nav class="np-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'netplus-circuit' ); ?>">
						<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
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
