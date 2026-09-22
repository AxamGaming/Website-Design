<?php
/**
 * Single post layout.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">

	<section class="np-page-hero">
		<div class="np-container">
			<?php np_breadcrumbs(); ?>
		</div>
	</section>

	<div class="np-container np-section">
		<div class="np-content-grid">
			<div class="np-content-main">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content-single' );
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				endwhile;
				?>
			</div>
			<?php get_sidebar(); ?>
		</div>
	</div>

</main>

<?php
get_footer();
