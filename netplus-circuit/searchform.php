<?php
/**
 * Search form with scope selector (products / posts / everything).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

$np_unique = 'np-search-' . wp_rand( 100, 999 );
?>
<form role="search" method="get" class="search-form np-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $np_unique ); ?>"><?php esc_html_e( 'Search for:', 'netplus-circuit' ); ?></label>
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<select name="post_type" aria-label="<?php esc_attr_e( 'Search scope', 'netplus-circuit' ); ?>">
			<option value="any"><?php esc_html_e( 'All', 'netplus-circuit' ); ?></option>
			<option value="product" <?php selected( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ); ?>><?php esc_html_e( 'Products', 'netplus-circuit' ); ?></option>
			<option value="post" <?php selected( isset( $_GET['post_type'] ) && 'post' === $_GET['post_type'] ); ?>><?php esc_html_e( 'Blog', 'netplus-circuit' ); ?></option>
			<option value="page" <?php selected( isset( $_GET['post_type'] ) && 'page' === $_GET['post_type'] ); ?>><?php esc_html_e( 'Pages', 'netplus-circuit' ); ?></option>
		</select>
	<?php endif; ?>
	<input type="search" id="<?php echo esc_attr( $np_unique ); ?>" class="search-field"
		placeholder="<?php esc_attr_e( 'Search parts, models, brands… (e.g. Dell 3558 keyboard)', 'netplus-circuit' ); ?>"
		value="<?php echo get_search_query() ? esc_attr( get_search_query() ) : ''; ?>" name="s" />
	<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'netplus-circuit' ); ?>">
		<?php echo np_icon( 'search', 18); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<span class="hnp-hsearch-text"><?php esc_html_e( 'Search', 'netplus-circuit' ); ?></span>
	</button>
</form>
