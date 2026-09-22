<?php
/**
 * Category tiles grid (MD Computers style clean categories).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_cats', true ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$np_limit = (int) get_theme_mod( 'np_cats_limit', 8 );
$np_cats  = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'parent'     => 0,
	'number'     => $np_limit ? $np_limit : 8,
	'orderby'    => 'count',
	'order'      => 'DESC',
) );

if ( is_wp_error( $np_cats ) || empty( $np_cats ) ) {
	return;
}

// Rotate fallback icons when a category has no thumbnail.
$np_icon_keys = array( 'cpu', 'laptop', 'monitor', 'keyboard', 'camera', 'mouse', 'printer', 'drive', 'wifi', 'box', 'tag', 'bolt' );

np_section_heading(
	get_theme_mod( 'np_cats_title', __( 'Shop by Category', 'netplus-circuit' ) ),
	__( 'Genuine parts for every brand — Dell, HP, Lenovo, Asus, Acer & more', 'netplus-circuit' ),
	function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '',
	__( 'All categories', 'netplus-circuit' )
);
?>
<div class="np-cats">
	<?php foreach ( array_values( $np_cats ) as $np_i => $np_cat ) :
		$thumb_id = get_term_meta( $np_cat->term_id, 'thumbnail_id', true );
		$icon_key = $np_icon_keys[ $np_i % count( $np_icon_keys ) ];
		?>
		<a class="np-cat-tile" href="<?php echo esc_url( get_term_link( $np_cat ) ); ?>">
			<span class="np-cat-tile__img">
				<?php if ( $thumb_id ) : ?>
					<?php echo wp_kses_post( wp_get_attachment_image( (int) $thumb_id, 'thumbnail', false, array( 'loading' => 'lazy' ) ) ); ?>
				<?php else : ?>
					<?php echo np_icon( $icon_key, 34 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php endif; ?>
			</span>
			<span class="np-cat-tile__name"><?php echo esc_html( $np_cat->name ); ?></span>
			<span class="np-cat-tile__count">
				<?php echo esc_html( sprintf( /* translators: %s: product count */ _n( '%s item', '%s items', (int) $np_cat->count, 'netplus-circuit' ), number_format_i18n( $np_cat->count ) ) ); ?>
			</span>
		</a>
	<?php endforeach; ?>
</div>
