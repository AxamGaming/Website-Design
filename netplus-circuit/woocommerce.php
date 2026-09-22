<?php
/**
 * WooCommerce wrapper — used for shop, product, cart, checkout, account pages.
 *
 * The heavy lifting (product cards, panels, sidebars) is done with hooks in
 * inc/woocommerce.php, so nothing breaks when WooCommerce updates.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main np-woo-main">

	<?php np_woo_page_hero(); ?>

	<div class="np-container np-section">
		<?php woocommerce_content(); ?>
	</div>

</main>

<?php
get_footer();
