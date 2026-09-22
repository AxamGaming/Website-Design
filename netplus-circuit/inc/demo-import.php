<?php
/**
 * One-click demo catalogue importer.
 *
 * Imports a snapshot of the real NetPlus Computers catalogue (names, SKUs,
 * prices, descriptions, spec tables and product images scraped from
 * netpluscomputers.lk) so the new store starts with genuine content.
 *
 * Admin screen: Appearance → NetPlus Demo Content.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin screen.
 */
function np_demo_import_menu() {
	add_theme_page(
		__( 'NetPlus Demo Content', 'netplus-circuit' ),
		__( 'NetPlus Demo Content', 'netplus-circuit' ),
		'manage_woocommerce',
		'np-demo-import',
		'np_render_demo_import_page'
	);
}
add_action( 'admin_menu', 'np_demo_import_menu' );

/**
 * Render the importer screen.
 */
function np_render_demo_import_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'netplus-circuit' ) );
	}

	// Run import when requested.
	if ( isset( $_GET['np_run_import'] ) && check_admin_referer( 'np_run_import' ) ) {
		$skip_images = isset( $_GET['np_skip_images'] );
		$result      = np_run_demo_import( $skip_images );
		echo '<div class="notice notice-' . ( $result['errors'] ? 'warning' : 'success' ) . '"><p><strong>'
			. esc_html( sprintf(
				/* translators: 1: products, 2: categories, 3: images */
				__( 'Import finished: %1$d products, %2$d categories, %3$d images.', 'netplus-circuit' ),
				$result['products'],
				$result['categories'],
				$result['images']
			) )
			. '</strong><br>' . esc_html( $result['message'] ) . '</p></div>';
	}

	$data     = np_demo_data();
	$count    = count( $data['products'] );
	$run_url  = wp_nonce_url( admin_url( 'themes.php?page=np-demo-import&np_run_import=1' ), 'np_run_import' );
	$skip_url = wp_nonce_url( admin_url( 'themes.php?page=np-demo-import&np_run_import=1&np_skip_images=1' ), 'np_run_import' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'NetPlus Demo Content Importer', 'netplus-circuit' ); ?></h1>
		<p style="max-width:780px;">
			<?php
			printf(
				/* translators: %d: product count */
				esc_html__( 'This imports %d real products from your old store (netpluscomputers.lk): names, SKUs, LKR prices, sale prices, descriptions, specification tables and product photos (downloaded into your Media Library). Categories are created automatically. Existing products with the same SKU are skipped, so the import is safe to run more than once.', 'netplus-circuit' ),
				(int) $count
			);
			?>
		</p>
		<p style="max-width:780px;color:#666;">
			<?php esc_html_e( 'Tip: run it on your Local site first to preview, then again on the live cPanel site after activation. Image download needs an internet connection; if images fail, re-run with "Skip images" and attach photos manually later.', 'netplus-circuit' ); ?>
		</p>
		<h2 class="title" style="margin-top:24px;"><?php esc_html_e( 'Products in this snapshot', 'netplus-circuit' ); ?></h2>
		<table class="widefat striped" style="max-width:900px;">
			<thead><tr><th><?php esc_html_e( 'Product', 'netplus-circuit' ); ?></th><th><?php esc_html_e( 'SKU', 'netplus-circuit' ); ?></th><th><?php esc_html_e( 'Price (LKR)', 'netplus-circuit' ); ?></th><th><?php esc_html_e( 'Category', 'netplus-circuit' ); ?></th></tr></thead>
			<tbody>
			<?php foreach ( $data['products'] as $p ) : ?>
				<tr>
					<td><?php echo esc_html( wp_trim_words( $p['name'], 12 ) ); ?></td>
					<td><code><?php echo esc_html( $p['sku'] ); ?></code></td>
					<td><?php echo esc_html( number_format_i18n( $p['price'] ) ); ?><?php echo ! empty( $p['sale'] ) ? ' → <strong style="color:#b32d2e;">' . esc_html( number_format_i18n( $p['sale'] ) ) . '</strong>' : ''; ?></td>
					<td><?php echo esc_html( $p['category'] ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p style="margin-top:24px;">
			<a class="button button-primary button-hero" href="<?php echo esc_url( $run_url ); ?>">
				<?php esc_html_e( 'Import products + download images', 'netplus-circuit' ); ?>
			</a>
			<a class="button button-hero" href="<?php echo esc_url( $skip_url ); ?>">
				<?php esc_html_e( 'Import without images (fast)', 'netplus-circuit' ); ?>
			</a>
		</p>
	</div>
	<?php
}

/**
 * Load bundled demo JSON.
 *
 * @return array
 */
function np_demo_data() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$file = NP_CIRCUIT_DIR . '/data/demo-products.json';
	$json = file_exists( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	$data = $json ? json_decode( $json, true ) : array();
	$cache = is_array( $data ) && isset( $data['products'] ) ? $data : array( 'products' => array() );
	return $cache;
}

/**
 * Execute the import.
 *
 * @param bool $skip_images Skip image sideloading.
 * @return array{products:int,categories:int,images:int,errors:bool,message:string}
 */
function np_run_demo_import( $skip_images = false ) {
	if ( ! function_exists( 'wc_get_product_id_by_sku' ) ) {
		return array( 'products' => 0, 'categories' => 0, 'images' => 0, 'errors' => true, 'message' => __( 'WooCommerce is not active.', 'netplus-circuit' ) );
	}
	if ( ! $skip_images ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$data       = np_demo_data();
	$made_p     = 0;
	$made_c     = 0;
	$made_i     = 0;
	$cat_cache  = array();
	$img_cache  = array();
	$errors     = false;
	$msg_parts  = array();

	foreach ( $data['products'] as $p ) {
		$sku = sanitize_text_field( $p['sku'] );
		if ( wc_get_product_id_by_sku( $sku ) ) {
			continue; // Already imported.
		}

		// Category.
		$cat_name = sanitize_text_field( $p['category'] );
		if ( ! isset( $cat_cache[ $cat_name ] ) ) {
			$existing = get_term_by( 'name', $cat_name, 'product_cat' );
			if ( $existing ) {
				$cat_cache[ $cat_name ] = (int) $existing->term_id;
			} else {
				$term = wp_insert_term( $cat_name, 'product_cat' );
				if ( ! is_wp_error( $term ) ) {
					$cat_cache[ $cat_name ] = (int) $term['term_id'];
					$made_c++;
				}
			}
		}

		$product = new WC_Product_Simple();
		$product->set_name( sanitize_text_field( $p['name'] ) );
		$product->set_sku( $sku );
		$product->set_regular_price( (string) $p['price'] );
		if ( ! empty( $p['sale'] ) ) {
			$product->set_sale_price( (string) $p['sale'] );
		}
		$product->set_short_description( wp_kses_post( wpautop( $p['short'] ) ) );
		$product->set_description( wp_kses_post( wpautop( $p['description'] ) ) );
		$product->set_status( 'publish' );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_reviews_allowed( true );
		if ( ! empty( $cat_cache[ $cat_name ] ) ) {
			$product->set_category_ids( array( $cat_cache[ $cat_name ] ) );
		}
		$id = $product->save();
		if ( ! $id ) {
			$errors = true;
			continue;
		}
		$made_p++;

		// Meta: spec table + highlights (theme tabs read these).
		update_post_meta( $id, '_np_specs', sanitize_textarea_field( $p['specs'] ) );
		update_post_meta( $id, '_np_highlights', sanitize_textarea_field( $p['highlights'] ) );

		// Images.
		if ( ! $skip_images && ! empty( $p['image'] ) ) {
			$featured = np_sideload_product_image( $p['image'], $id, $img_cache );
			if ( $featured ) {
				set_post_thumbnail( $id, $featured );
				$made_i++;
			} else {
				$errors = true;
			}
			if ( ! empty( $p['gallery'] ) ) {
				$gallery = array();
				foreach ( $p['gallery'] as $g ) {
					$gid = np_sideload_product_image( $g, $id, $img_cache );
					if ( $gid ) {
						$gallery[] = $gid;
						$made_i++;
					}
				}
				if ( $gallery ) {
					$product = wc_get_product( $id );
					$product->set_gallery_image_ids( $gallery );
					$product->save();
				}
			}
		}
	}

	$msg = $errors
		? __( 'Some items or images could not be processed (see notes above). Re-run the importer to retry — duplicates are skipped automatically.', 'netplus-circuit' )
		: __( 'All done! Visit your Shop page to see the catalogue, or the Home page for the product sections.', 'netplus-circuit' );

	return array(
		'products'   => $made_p,
		'categories' => $made_c,
		'images'     => $made_i,
		'errors'     => $errors,
		'message'    => $msg,
	);
}

/**
 * Sideload a remote image into the media library (cached per URL).
 *
 * @param string $url       Remote image URL.
 * @param int    $parent_id Attachment parent post.
 * @param array  $cache     URL => attachment ID cache (by reference).
 * @return int|false Attachment ID.
 */
function np_sideload_product_image( $url, $parent_id, &$cache ) {
	$url = esc_url_raw( $url );
	if ( isset( $cache[ $url ] ) ) {
		return $cache[ $url ];
	}
	// Skip placeholder images from the old site.
	if ( false !== strpos( $url, 'woocommerce-placeholder' ) ) {
		return false;
	}
	$id = media_sideload_image( $url, $parent_id, null, 'id' );
	if ( is_wp_error( $id ) || ! $id ) {
		return false;
	}
	$cache[ $url ] = (int) $id;
	return (int) $id;
}
