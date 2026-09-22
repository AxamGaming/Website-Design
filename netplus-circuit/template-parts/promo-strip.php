<?php
/**
 * Discount promo ribbon (Scion-style).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_promo_strip', true ) ) {
	return;
}
$np_text = (string) get_theme_mod( 'np_promo_strip_text', '' );
if ( '' === trim( $np_text ) ) {
	return;
}
$np_link = (string) get_theme_mod( 'np_promo_strip_link', '' );
?>
<section class="np-section--tight" style="padding:10px 0 0;" aria-label="<?php esc_attr_e( 'Promotion', 'netplus-circuit' ); ?>">
	<div class="np-promo-strip">
		<span style="display:flex;align-items:center;gap:12px;">
			<?php echo np_icon( 'tag', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span><?php echo esc_html( $np_text ); ?></span>
		</span>
		<?php if ( $np_link ) : ?>
			<a class="np-btn np-btn--sm" href="<?php echo esc_url( $np_link ); ?>">
				<?php esc_html_e( 'Shop the sale', 'netplus-circuit' ); ?>
				<?php echo np_icon( 'arrow-r', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		<?php endif; ?>
	</div>
</section>
