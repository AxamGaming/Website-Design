<?php
/**
 * Floating WhatsApp button + back-to-top + cookie notice.
 * Renders on every page (bottom-right), as required.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

$np_wa_link   = np_whatsapp_link();
$np_show_wa   = np_opt_on( 'np_wa_float_show', true ) && $np_wa_link;
$np_show_top  = np_opt_on( 'np_show_totop', true );
$np_show_ck   = np_opt_on( 'np_cookie_show', true );
$np_bubble    = get_theme_mod( 'np_wa_bubble_text', '' );

if ( $np_show_wa || $np_show_top || $np_show_ck ) :
	?>
	<div class="np-float-stack">

		<?php if ( $np_show_top ) : ?>
			<button class="np-totop" id="np-totop" aria-label="<?php esc_attr_e( 'Back to top', 'netplus-circuit' ); ?>">
				<?php echo np_icon( 'arrow-u', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		<?php endif; ?>

		<?php if ( $np_show_wa ) : ?>
			<div class="np-wa-float">
				<?php if ( $np_bubble ) : ?>
					<div class="np-wa-float__bubble" id="np-wa-bubble" data-delay="<?php echo esc_attr( (int) get_theme_mod( 'np_wa_bubble_delay', 5 ) ); ?>">
						<button class="np-wa-float__bubble-close" id="np-wa-bubble-close" aria-label="<?php esc_attr_e( 'Dismiss', 'netplus-circuit' ); ?>">✕</button>
						<strong><?php esc_html_e( 'NetPlus Support', 'netplus-circuit' ); ?></strong>
						<?php echo esc_html( $np_bubble ); ?>
					</div>
				<?php endif; ?>
				<a class="np-wa-float__btn" id="np-wa-float"
					href="<?php echo esc_url( $np_wa_link ); ?>"
					target="_blank" rel="noopener"
					aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'netplus-circuit' ); ?>"
					title="<?php esc_attr_e( 'Chat with us on WhatsApp', 'netplus-circuit' ); ?>">
					<?php echo np_icon( 'whatsapp', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
<?php endif; ?>

<?php if ( $np_show_ck ) : ?>
	<div class="np-cookie" id="np-cookie" role="dialog" aria-label="<?php esc_attr_e( 'Cookie notice', 'netplus-circuit' ); ?>">
		<p>
			<?php echo esc_html( get_theme_mod( 'np_cookie_text', __( 'We use cookies to improve your shopping experience and keep your cart safe. By continuing to browse you agree to our use of cookies.', 'netplus-circuit' ) ) ); ?>
			<?php
			$np_privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
			if ( $np_privacy_id ) :
				?>
				<a href="<?php echo esc_url( get_permalink( $np_privacy_id ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'netplus-circuit' ); ?></a>
			<?php endif; ?>
		</p>
		<div class="np-cookie__btns">
			<button class="np-btn np-btn--primary np-btn--sm" id="np-cookie-accept"><?php esc_html_e( 'Accept', 'netplus-circuit' ); ?></button>
			<button class="np-btn np-btn--ghost np-btn--sm" style="color:#c6d0e4 !important;border-color:rgba(255,255,255,.25);" id="np-cookie-decline"><?php esc_html_e( 'Decline', 'netplus-circuit' ); ?></button>
		</div>
	</div>
<?php endif; ?>
