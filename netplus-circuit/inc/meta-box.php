<?php
/**
 * Product meta boxes: Highlights + Spec table (Alphatronic-style).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register meta box on product editor.
 */
function np_add_product_meta_boxes() {
	add_meta_box(
		'np_product_extras',
		__( 'NetPlus Product Extras — Highlights & Specifications', 'netplus-circuit' ),
		'np_render_product_meta_box',
		'product',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'np_add_product_meta_boxes' );

/**
 * Render the meta box.
 *
 * @param WP_Post $post Post object.
 */
function np_render_product_meta_box( $post ) {
	wp_nonce_field( 'np_product_extras', 'np_product_extras_nonce' );

	$highlights = get_post_meta( $post->ID, '_np_highlights', true );
	$specs      = get_post_meta( $post->ID, '_np_specs', true );
	?>
	<style>
		.np-mb textarea { width: 100%; font-family: ui-monospace, Menlo, Consolas, monospace; }
		.np-mb p.description { margin: 6px 0 16px; }
		.np-mb h4 { margin: 14px 0 6px; }
	</style>
	<div class="np-mb">
		<h4><?php esc_html_e( 'Key highlights (one per line)', 'netplus-circuit' ); ?></h4>
		<textarea name="np_highlights" rows="4" placeholder="<?php esc_attr_e( "Original Dell part\n6-month replacement warranty\nShips in 24 hours", 'netplus-circuit' ); ?>"><?php echo esc_textarea( (string) $highlights ); ?></textarea>
		<p class="description"><?php esc_html_e( 'Shown as a green tick list under the short description on the product page.', 'netplus-circuit' ); ?></p>

		<h4><?php esc_html_e( 'Specification table (Label | Value, one per line)', 'netplus-circuit' ); ?></h4>
		<textarea name="np_specs" rows="8" placeholder="<?php esc_attr_e( "Brand | Dell\nCompatible Models | Inspiron 3558, 3559, 5558\nBattery Capacity | 40Wh / 2800mAh\nVoltage | 14.8V\nCondition | Brand New Original", 'netplus-circuit' ); ?>"><?php echo esc_textarea( (string) $specs ); ?></textarea>
		<p class="description"><?php esc_html_e( 'Rendered as a clean spec table in the "Specifications" tab. WooCommerce attributes are merged in automatically.', 'netplus-circuit' ); ?></p>
	</div>
	<?php
}

/**
 * Save meta box values.
 *
 * @param int $post_id Post ID.
 */
function np_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['np_product_extras_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['np_product_extras_nonce'] ), 'np_product_extras' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_product', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['np_highlights'] ) ) {
		update_post_meta(
			$post_id,
			'_np_highlights',
			sanitize_textarea_field( wp_unslash( $_POST['np_highlights'] ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized by textarea_field.
		);
	}
	if ( isset( $_POST['np_specs'] ) ) {
		update_post_meta(
			$post_id,
			'_np_specs',
			sanitize_textarea_field( wp_unslash( $_POST['np_specs'] ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized by textarea_field.
		);
	}
}
add_action( 'save_post_product', 'np_save_product_meta' );
