<?php
/**
 * Front page — layout mirrors netpluscomputers.lk:
 * flash sale → promo banners → trust bar → discount ribbon → new arrivals →
 * on-sale row → product tabs → newsletter/WhatsApp → FAQ → blog.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();

// If a static front page has real content or was designed with Elementor, respect it.
$np_front_id     = (int) get_option( 'page_on_front' );
$np_front_custom = false;
if ( $np_front_id ) {
	$np_front_post   = get_post( $np_front_id );
	$np_has_el       = $np_front_post && (bool) get_post_meta( $np_front_id, '_elementor_data', true );
	$np_has_text     = $np_front_post && '' !== trim( (string) $np_front_post->post_content ) && ! has_shortcode( (string) $np_front_post->post_content, 'np_sections' );
	$np_front_custom = $np_has_el || ( $np_has_text && 'page-templates/template-home-sections.php' !== get_page_template_slug( $np_front_id ) );
}

if ( $np_front_custom && have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<main id="primary" class="site-main np-front-custom">
			<?php the_content(); ?>
		</main>
		<?php
	endwhile;
else :
	$np_use_carousel = np_opt_on( 'np_carousels', true ) && function_exists( 'np_product_carousel' );
	?>
	<main id="primary" class="site-main np-front">

		<?php get_template_part( 'template-parts/hero' ); // Optional slider (off by default). ?>

		<div class="np-container">

			<?php get_template_part( 'template-parts/flash-sale' ); ?>

			<section class="np-section" aria-label="<?php esc_attr_e( 'Promotions', 'netplus-circuit' ); ?>">
				<?php get_template_part( 'template-parts/promo-banners' ); ?>
			</section>
		</div>

		<?php get_template_part( 'template-parts/trust-bar' ); ?>

		<div class="np-container">
			<?php get_template_part( 'template-parts/promo-strip' ); ?>

			<?php if ( np_opt_on( 'np_show_products', true ) && class_exists( 'WooCommerce' ) ) : ?>
				<section class="np-section" aria-label="<?php esc_attr_e( 'New arrivals', 'netplus-circuit' ); ?>">
					<?php
					np_section_heading(
						__( 'New Arrivals', 'netplus-circuit' ),
						__( 'Fresh stock landing every week', 'netplus-circuit' ),
						function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '',
						__( 'View all', 'netplus-circuit' )
					);
					$np_new_ids = np_get_product_ids_by_type( 'new', 10 );
					if ( $np_new_ids ) {
						if ( $np_use_carousel ) {
							np_product_carousel( $np_new_ids, __( 'New arrivals', 'netplus-circuit' ) );
						} else {
							echo '<div class="np-products np-products--4">';
							np_render_loop_cards( array_slice( $np_new_ids, 0, 8 ) );
							echo '</div>';
						}
					}
					?>
				</section>

				<section class="np-section" style="padding-top:0;" aria-label="<?php esc_attr_e( 'On sale now', 'netplus-circuit' ); ?>">
					<?php
					np_section_heading(
						__( 'Product on Sale!', 'netplus-circuit' ),
						'',
						function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '',
						__( 'All deals', 'netplus-circuit' )
					);
					$np_sale_ids = np_get_product_ids_by_type( 'sale', 10 );
					if ( $np_sale_ids ) {
						if ( $np_use_carousel ) {
							np_product_carousel( $np_sale_ids, __( 'Products on sale', 'netplus-circuit' ) );
						} else {
							echo '<div class="np-products np-products--4">';
							np_render_loop_cards( array_slice( $np_sale_ids, 0, 8 ) );
							echo '</div>';
						}
					}
					?>
				</section>

				<section class="np-section" style="padding-top:0;" aria-label="<?php esc_attr_e( 'Featured products', 'netplus-circuit' ); ?>">
					<?php get_template_part( 'template-parts/featured-products' ); ?>
				</section>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/newsletter' ); ?>
			<?php get_template_part( 'template-parts/wa-strip' ); ?>

			<section class="np-section" style="padding-top:8px;" aria-label="<?php esc_attr_e( 'FAQ', 'netplus-circuit' ); ?>">
				<?php get_template_part( 'template-parts/faq' ); ?>
			</section>

			<section class="np-section" style="padding-top:0;" aria-label="<?php esc_attr_e( 'From the blog', 'netplus-circuit' ); ?>">
				<?php get_template_part( 'template-parts/blog-preview' ); ?>
			</section>
		</div>

	</main>
	<?php
endif;

get_footer();
