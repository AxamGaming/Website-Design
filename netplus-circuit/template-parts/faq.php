<?php
/**
 * FAQ accordion (like the old site's FAQ block) — managed in the Customizer.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_faq', true ) ) {
	return;
}

$np_rows = np_get_spec_rows( (string) get_theme_mod( 'np_faq_items', '' ) );
if ( empty( $np_rows ) ) {
	return;
}

np_section_heading(
	__( 'Frequently Asked Questions', 'netplus-circuit' ),
	__( 'Delivery, warranty, returns & payments — answered', 'netplus-circuit' )
);
?>
<div class="np-faq">
	<?php foreach ( $np_rows as $np_i => $np_row ) : ?>
		<div class="np-faq__item<?php echo 0 === $np_i ? ' is-open' : ''; ?>">
			<button class="np-faq__q" type="button" aria-expanded="<?php echo 0 === $np_i ? 'true' : 'false'; ?>">
				<span><?php echo esc_html( $np_row[0] ); ?></span>
				<?php echo np_icon( 'chevron-d', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
			<div class="np-faq__a"<?php echo 0 === $np_i ? ' style="max-height:300px;"' : ''; ?>>
				<div class="np-faq__a-inner"><?php echo esc_html( $np_row[1] ); ?></div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
