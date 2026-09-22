<?php
/**
 * Newsletter strip (bot-protected, saves to NetPlus Subscribers table).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_newsletter', true ) ) {
	return;
}
?>
<section class="np-section--tight" aria-label="<?php esc_attr_e( 'Newsletter', 'netplus-circuit' ); ?>">
	<div class="np-newsletter">
		<div class="np-newsletter__inner">
			<div>
				<h2 class="np-newsletter__title"><?php esc_html_e( 'Get deals before everyone else', 'netplus-circuit' ); ?></h2>
				<p class="np-newsletter__sub"><?php esc_html_e( 'Join our newsletter for flash sales, new arrivals and exclusive discounts on laptop & PC parts. No spam — only good stuff, maybe twice a month.', 'netplus-circuit' ); ?></p>
			</div>
			<div>
				<form class="np-ajax-form" data-np-form="newsletter" method="post" style="position:relative;">
					<input type="hidden" name="action" value="np_newsletter_subscribe" />
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'np_circuit_ajax' ) ); ?>" />
					<input type="hidden" name="np_form_time" value="<?php echo esc_attr( np_bot_time_token() ); ?>" />
					<p class="np-hp-field" aria-hidden="true">
						<label for="np_nl_hp">Leave empty</label>
						<input type="text" name="np_nl_hp" id="np_nl_hp" value="" tabindex="-1" autocomplete="off" />
					</p>
					<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'netplus-circuit' ); ?>" required maxlength="190" aria-label="<?php esc_attr_e( 'Email address', 'netplus-circuit' ); ?>" />
					<button type="submit"><?php esc_html_e( 'Subscribe', 'netplus-circuit' ); ?></button>
					<div class="np-form-status" hidden></div>
				</form>
				<p class="np-newsletter__note"><?php esc_html_e( 'We respect your inbox. Unsubscribe anytime with one click.', 'netplus-circuit' ); ?></p>
			</div>
		</div>
	</div>
</section>
