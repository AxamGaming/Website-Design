<?php
/**
 * Template Name: NetPlus Home Sections
 * Template Post Type: page
 *
 * Renders the coded homepage sections (flash sale, promos, trust bar, ribbon,
 * product rows, newsletter, FAQ, blog) on any page.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main np-front">

	<?php get_template_part( 'template-parts/hero' ); ?>

	<div class="np-container">
		<?php get_template_part( 'template-parts/flash-sale' ); ?>
		<section class="np-section"><?php get_template_part( 'template-parts/promo-banners' ); ?></section>
	</div>

	<?php get_template_part( 'template-parts/trust-bar' ); ?>

	<div class="np-container">
		<?php get_template_part( 'template-parts/promo-strip' ); ?>

		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<section class="np-section">
				<?php
				np_section_heading( __( 'New Arrivals', 'netplus-circuit' ) );
				$np_new_ids = np_get_product_ids_by_type( 'new', 10 );
				if ( np_opt_on( 'np_carousels', true ) ) {
					np_product_carousel( $np_new_ids, __( 'New arrivals', 'netplus-circuit' ) );
				} else {
					echo '<div class="np-products np-products--4">';
					np_render_loop_cards( array_slice( $np_new_ids, 0, 8 ) );
					echo '</div>';
				}
				?>
			</section>
			<section class="np-section" style="padding-top:0;"><?php get_template_part( 'template-parts/featured-products' ); ?></section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/newsletter' ); ?>
		<?php get_template_part( 'template-parts/wa-strip' ); ?>
		<section class="np-section" style="padding-top:8px;"><?php get_template_part( 'template-parts/faq' ); ?></section>
		<section class="np-section" style="padding-top:0;"><?php get_template_part( 'template-parts/blog-preview' ); ?></section>
	</div>

</main>

<?php
get_footer();
