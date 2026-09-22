<?php
/**
 * Archive templates (categories, tags, authors, dates).
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
				if ( is_category() || is_tag() || is_tax() ) {
					single_term_title();
				} elseif ( is_author() ) {
					the_author();
				} elseif ( is_day() ) {
					echo esc_html( get_the_date() );
				} elseif ( is_month() ) {
					echo esc_html( get_the_date( 'F Y' ) );
				} elseif ( is_year() ) {
					echo esc_html( get_the_date( 'Y' ) );
				} else {
					esc_html_e( 'Archives', 'netplus-circuit' );
				}
				?>
			</h1>
			<?php
			$np_desc = get_the_archive_description();
			if ( $np_desc ) {
				echo '<div style="color:#b9c4d9;margin-top:6px;max-width:640px;">' . wp_kses_post( $np_desc ) . '</div>';
			}
			np_breadcrumbs();
			?>
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
							get_template_part( 'template-parts/content', get_post_type() );
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
