<?php
/**
 * Trust bar (Alphatronic-style assurance strip).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_trust', true ) ) {
	return;
}

$np_wa_link = np_whatsapp_link();
?>
<section class="np-trust" aria-label="<?php esc_attr_e( 'Store assurances', 'netplus-circuit' ); ?>">
	<div class="np-container">
		<div class="np-trust__grid">
			<div class="np-trust__item">
				<span class="np-trust__icon"><?php echo np_icon( 'truck', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<div>
					<p class="np-trust__title"><?php esc_html_e( 'Island-wide Delivery', 'netplus-circuit' ); ?></p>
					<p class="np-trust__sub"><?php esc_html_e( 'Courier & SL Post — 1–4 working days', 'netplus-circuit' ); ?></p>
				</div>
			</div>
			<div class="np-trust__item">
				<span class="np-trust__icon"><?php echo np_icon( 'shield', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<div>
					<p class="np-trust__title"><?php esc_html_e( 'Genuine + Warranty', 'netplus-circuit' ); ?></p>
					<p class="np-trust__sub"><?php esc_html_e( 'Tested original parts, replacement warranty', 'netplus-circuit' ); ?></p>
				</div>
			</div>
			<div class="np-trust__item">
				<span class="np-trust__icon"><?php echo np_icon( 'hand-money', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<div>
					<p class="np-trust__title"><?php esc_html_e( 'Cash on Delivery', 'netplus-circuit' ); ?></p>
					<p class="np-trust__sub"><?php esc_html_e( 'Pay when it reaches your door', 'netplus-circuit' ); ?></p>
				</div>
			</div>
			<div class="np-trust__item">
				<?php if ( $np_wa_link ) : ?>
					<a href="<?php echo esc_url( $np_wa_link ); ?>" target="_blank" rel="noopener" style="display:flex;align-items:center;gap:14px;">
						<span class="np-trust__icon"><?php echo np_icon( 'headset', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span>
							<span class="np-trust__title" style="display:block;"><?php esc_html_e( 'Expert Support', 'netplus-circuit' ); ?></span>
							<span class="np-trust__sub" style="display:block;"><?php esc_html_e( 'Free compatibility checks on WhatsApp', 'netplus-circuit' ); ?></span>
						</span>
					</a>
				<?php else : ?>
					<span class="np-trust__icon"><?php echo np_icon( 'headset', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<div>
						<p class="np-trust__title"><?php esc_html_e( 'Expert Support', 'netplus-circuit' ); ?></p>
						<p class="np-trust__sub"><?php esc_html_e( 'Free compatibility checks before you buy', 'netplus-circuit' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
