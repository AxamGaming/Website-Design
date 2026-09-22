<?php
/**
 * Homepage product tabs: Featured / New / Best sellers / On sale.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_products', true ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$np_limit = (int) get_theme_mod( 'np_products_per_tab', 8 );
$np_limit = max( 4, min( 20, $np_limit ) );

$np_tabs = array(
	'new'      => __( 'New Arrivals', 'netplus-circuit' ),
	'featured' => __( 'Featured', 'netplus-circuit' ),
	'best'     => __( 'Best Sellers', 'netplus-circuit' ),
	'sale'     => __( 'On Sale', 'netplus-circuit' ),
);

// Only keep tabs that actually have products.
$np_active_tabs = array();
foreach ( $np_tabs as $np_key => $np_label ) {
	$np_ids = np_get_product_ids_by_type( $np_key, $np_limit );
	if ( ! empty( $np_ids ) ) {
		$np_active_tabs[ $np_key ] = $np_ids;
	}
}
if ( empty( $np_active_tabs ) ) {
	return;
}

$np_tab_buttons = '<div class="np-ptabs" role="tablist">';
$np_first       = true;
foreach ( array_keys( $np_active_tabs ) as $np_key ) {
	$np_tab_buttons .= '<button class="np-ptab' . ( $np_first ? ' is-active' : '' ) . '" role="tab" aria-selected="' . ( $np_first ? 'true' : 'false' ) . '" data-target="np-panel-' . esc_attr( $np_key ) . '">' . esc_html( $np_tabs[ $np_key ] ) . '</button>';
	$np_first = false;
}
$np_tab_buttons .= '</div>';

np_section_heading(
	get_theme_mod( 'np_products_title', __( 'Trending Products', 'netplus-circuit' ) ),
	'',
	function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '',
	__( 'View all products', 'netplus-circuit' ),
	$np_tab_buttons
);
?>
<div class="np-product-panels">
	<?php
	$np_first = true;
	global $product;
	foreach ( $np_active_tabs as $np_key => $np_ids ) :
		?>
		<div class="np-ppanel<?php echo $np_first ? ' is-active' : ''; ?>" id="np-panel-<?php echo esc_attr( $np_key ); ?>" role="tabpanel">
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
		</div>
		<?php
		$np_first = false;
	endforeach;
	?>
</div>
