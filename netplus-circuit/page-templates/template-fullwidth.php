<?php
/**
 * Template Name: Full Width (no sidebar)
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">

	<section class="np-page-hero">
		<div class="np-container">
			<h1 class="np-page-hero__title"><?php the_title(); ?></h1>
			<?php np_breadcrumbs(); ?>
		</div>
	</section>

	<div class="np-container np-section">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content-page' );
		endwhile;
		?>
	</div>

</main>

<?php
get_footer();
