<?php
/**
 * WhatsApp support CTA strip.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_wa_strip', true ) ) {
	return;
}
$np_wa = np_whatsapp_link( __( 'Hi NetPlus! I need help choosing the right part for my device.', 'netplus-circuit' ) );
if ( ! $np_wa ) {
	return;
}
$np_phone = get_theme_mod( 'np_phone', '' );
?>
<section class="np-section--tight" aria-label="<?php esc_attr_e( 'Contact support', 'netplus-circuit' ); ?>">
	<div class="np-wa-strip">
		<div class="np-wa-strip__left">
			<span class="np-wa-strip__icon"><?php echo np_icon( 'whatsapp', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div>
				<h3 class="np-wa-strip__title"><?php esc_html_e( 'Not sure which part fits your device?', 'netplus-circuit' ); ?></h3>
				<p class="np-wa-strip__sub"><?php esc_html_e( 'Send us your model number or a photo on WhatsApp — our technicians reply free of charge, usually within minutes.', 'netplus-circuit' ); ?></p>
			</div>
		</div>
		<div class="np-flex" style="flex-wrap:wrap;">
			<a class="np-btn np-btn--wa" href="<?php echo esc_url( $np_wa ); ?>" target="_blank" rel="noopener">
				<?php echo np_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php esc_html_e( 'Chat on WhatsApp', 'netplus-circuit' ); ?>
			</a>
			<?php if ( $np_phone ) : ?>
				<a class="np-btn np-btn--ghost" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $np_phone ) ); ?>">
					<?php echo np_icon( 'phone', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( $np_phone ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
