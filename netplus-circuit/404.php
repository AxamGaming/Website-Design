<?php
/**
 * 404 template.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();

$np_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$np_wa       = np_whatsapp_link( __( 'Hi NetPlus! I got a 404 error on your website while looking for a product…', 'netplus-circuit' ) );
?>

<main id="primary" class="site-main">
	<div class="np-container np-section">
		<section class="np-empty error-404 not-found">
			<div class="np-empty__code">404</div>
			<h1><?php esc_html_e( 'Oops! This page unplugged itself.', 'netplus-circuit' ); ?></h1>
			<p><?php esc_html_e( 'The page you are looking for was moved, renamed or never existed. Try searching for the part you need — or ask us directly on WhatsApp and we will find it for you.', 'netplus-circuit' ); ?></p>
			<div class="np-empty__search"><?php get_search_form(); ?></div>
			<div class="np-flex" style="justify-content:center;flex-wrap:wrap;">
				<a class="np-btn np-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo np_icon( 'grid', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Back to home', 'netplus-circuit' ); ?></a>
				<a class="np-btn np-btn--dark" href="<?php echo esc_url( $np_shop_url ); ?>"><?php echo np_icon( 'cart', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Browse the shop', 'netplus-circuit' ); ?></a>
				<?php if ( $np_wa ) : ?>
					<a class="np-btn np-btn--wa" href="<?php echo esc_url( $np_wa ); ?>" target="_blank" rel="noopener"><?php echo np_icon( 'whatsapp', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'WhatsApp us', 'netplus-circuit' ); ?></a>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<section class="np-section" aria-label="<?php esc_attr_e( 'Popular products', 'netplus-circuit' ); ?>">
				<?php
				np_section_heading( __( 'Popular right now', 'netplus-circuit' ), '', $np_shop_url, __( 'Shop all', 'netplus-circuit' ) );
				$np_ids = np_get_product_ids_by_type( 'best', 4 );
				if ( $np_ids ) :
					global $product;
					?>
					<div class="np-products np-products--4">
						<?php
						foreach ( $np_ids as $np_id ) {
							$product = wc_get_product( $np_id );
							if ( ! $product ) {
								continue;
							}
							setup_postdata( $GLOBALS['post'] = get_post( $np_id ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
							do_action( 'woocommerce_before_shop_loop_item' );
							do_action( 'woocommerce_before_shop_loop_item_title' );
							do_action( 'woocommerce_shop_loop_item_title' );
							do_action( 'woocommerce_after_shop_loop_item_title' );
							do_action( 'woocommerce_after_shop_loop_item' );
						}
						wp_reset_postdata();
						$product = null;
						?>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
