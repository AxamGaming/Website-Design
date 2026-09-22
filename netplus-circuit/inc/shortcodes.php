<?php
/**
 * Theme shortcodes: spec tables, WhatsApp buttons, contact form.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   [np_specs title="Key Specs"]
   Brand | Asus
   Socket | AM4
   [/np_specs]
   ========================================================================== */

add_shortcode( 'np_specs', 'np_shortcode_specs' );
function np_shortcode_specs( $atts, $content = null ) {
	$atts = shortcode_atts( array( 'title' => '' ), $atts, 'np_specs' );
	$rows = np_get_spec_rows( (string) $content );
	if ( ! $rows ) {
		return '';
	}
	$html = '<table class="np-spec-table">';
	if ( $atts['title'] ) {
		$html .= '<caption>' . esc_html( $atts['title'] ) . '</caption>';
	}
	$html .= '<tbody>';
	foreach ( $rows as $row ) {
		$html .= '<tr><th scope="row">' . esc_html( $row[0] ) . '</th><td>' . esc_html( $row[1] ) . '</td></tr>';
	}
	$html .= '</tbody></table>';
	return $html;
}

/* ==========================================================================
   [np_whatsapp message="..." label="..." style="button|link"]
   ========================================================================== */

add_shortcode( 'np_whatsapp', 'np_shortcode_whatsapp' );
function np_shortcode_whatsapp( $atts ) {
	$atts = shortcode_atts( array(
		'message' => '',
		'label'   => __( 'Chat on WhatsApp', 'netplus-circuit' ),
		'style'   => 'button',
	), $atts, 'np_whatsapp' );

	$url = np_whatsapp_link( $atts['message'] );
	if ( ! $url ) {
		return '<!-- set WhatsApp number in Customizer → WhatsApp & Floating Buttons -->';
	}
	if ( 'link' === $atts['style'] ) {
		return '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $atts['label'] ) . '</a>';
	}
	return '<a class="np-btn np-btn--wa" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">'
		. np_icon( 'whatsapp', 18 ) . esc_html( $atts['label'] ) . '</a>';
}

/* ==========================================================================
   [np_contact_form to="..." title="..."] — built-in protected contact form
   ========================================================================== */

add_shortcode( 'np_contact_form', 'np_shortcode_contact_form' );
function np_shortcode_contact_form( $atts ) {
	$atts = shortcode_atts( array(
		'to'    => get_theme_mod( 'np_email', get_option( 'admin_email' ) ),
		'title' => __( 'Send us a message', 'netplus-circuit' ),
	), $atts, 'np_contact_form' );

	ob_start();
	?>
	<div class="np-contact-form" id="np-contact-form">
		<h3><?php echo esc_html( $atts['title'] ); ?></h3>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="np-ajax-form" data-np-form="contact">
			<input type="hidden" name="action" value="np_contact_form" />
			<?php wp_nonce_field( 'np_contact_form', 'np_contact_nonce' ); ?>
			<input type="hidden" name="np_form_time" value="<?php echo esc_attr( np_bot_time_token() ); ?>" />
			<p class="np-hp-field" aria-hidden="true">
				<label for="np_cf_company">Company (leave empty)</label>
				<input type="text" name="np_cf_company" id="np_cf_company" value="" tabindex="-1" autocomplete="off" />
			</p>
			<input type="hidden" name="np_cf_to" value="<?php echo esc_attr( antispambot( $atts['to'] ) ); ?>" />

			<div class="np-form-row">
				<label for="np_cf_name"><?php esc_html_e( 'Your name *', 'netplus-circuit' ); ?></label>
				<input type="text" id="np_cf_name" name="np_cf_name" required maxlength="80" />
			</div>
			<div class="np-form-row">
				<label for="np_cf_phone"><?php esc_html_e( 'Phone / WhatsApp *', 'netplus-circuit' ); ?></label>
				<input type="tel" id="np_cf_phone" name="np_cf_phone" required maxlength="30" placeholder="07X XXX XXXX" />
			</div>
			<div class="np-form-row">
				<label for="np_cf_email"><?php esc_html_e( 'Email', 'netplus-circuit' ); ?></label>
				<input type="email" id="np_cf_email" name="np_cf_email" maxlength="120" />
			</div>
			<div class="np-form-row">
				<label for="np_cf_subject"><?php esc_html_e( 'Subject', 'netplus-circuit' ); ?></label>
				<select id="np_cf_subject" name="np_cf_subject">
					<option><?php esc_html_e( 'Product enquiry', 'netplus-circuit' ); ?></option>
					<option><?php esc_html_e( 'Order status', 'netplus-circuit' ); ?></option>
					<option><?php esc_html_e( 'Warranty claim', 'netplus-circuit' ); ?></option>
					<option><?php esc_html_e( 'Custom PC build', 'netplus-circuit' ); ?></option>
					<option><?php esc_html_e( 'Other', 'netplus-circuit' ); ?></option>
				</select>
			</div>
			<div class="np-form-row">
				<label for="np_cf_message"><?php esc_html_e( 'Message *', 'netplus-circuit' ); ?></label>
				<textarea id="np_cf_message" name="np_cf_message" required maxlength="2000"></textarea>
			</div>
			<div class="np-form-status" hidden></div>
			<button type="submit" class="np-btn np-btn--primary np-btn--lg">
				<?php echo np_icon( 'mail', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php esc_html_e( 'Send message', 'netplus-circuit' ); ?>
			</button>
			<p class="np-muted" style="font-size:.78rem;margin-top:12px;">
				<?php esc_html_e( 'Prefer instant replies? Message us on WhatsApp — we usually respond within minutes during business hours.', 'netplus-circuit' ); ?>
			</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * AJAX handler for the contact form (works without AJAX too via admin-post).
 */
function np_handle_contact_form() {
	$is_ajax = wp_doing_ajax();

	$nonce = isset( $_POST['np_contact_nonce'] ) ? sanitize_key( $_POST['np_contact_nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'np_contact_form' ) ) {
		np_contact_form_response( false, __( 'Security check failed. Please refresh and try again.', 'netplus-circuit' ), $is_ajax );
	}

	// Honeypot.
	if ( ! empty( $_POST['np_cf_company'] ) ) {
		np_contact_form_response( true, __( 'Thank you! Your message has been sent.', 'netplus-circuit' ), $is_ajax ); // Fake success for bots.
	}
	// Timing trap.
	$elapsed = isset( $_POST['np_form_time'] ) ? np_bot_time_seconds( sanitize_text_field( wp_unslash( $_POST['np_form_time'] ) ) ) : PHP_INT_MAX;
	if ( PHP_INT_MAX === $elapsed || $elapsed < 4 ) {
		np_contact_form_response( true, __( 'Thank you! Your message has been sent.', 'netplus-circuit' ), $is_ajax );
	}
	// Rate limit per IP: 4 messages / hour.
	$rl_key = 'np_cf_' . md5( np_client_ip() );
	$rl     = (int) get_transient( $rl_key );
	if ( $rl >= 4 ) {
		np_contact_form_response( false, __( 'Too many messages sent. Please try again later or use WhatsApp.', 'netplus-circuit' ), $is_ajax );
	}

	$name    = isset( $_POST['np_cf_name'] ) ? sanitize_text_field( wp_unslash( $_POST['np_cf_name'] ) ) : '';
	$phone   = isset( $_POST['np_cf_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['np_cf_phone'] ) ) : '';
	$email   = isset( $_POST['np_cf_email'] ) ? sanitize_email( wp_unslash( $_POST['np_cf_email'] ) ) : '';
	$subject = isset( $_POST['np_cf_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['np_cf_subject'] ) ) : '';
	$message = isset( $_POST['np_cf_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['np_cf_message'] ) ) : '';

	if ( '' === $name || '' === $phone || '' === $message ) {
		np_contact_form_response( false, __( 'Please fill in your name, phone number and message.', 'netplus-circuit' ), $is_ajax );
	}
	if ( substr_count( $message, 'http' ) > 2 ) {
		np_contact_form_response( true, __( 'Thank you! Your message has been sent.', 'netplus-circuit' ), $is_ajax ); // Spam signature → fake success, drop it.
	}

	$to = isset( $_POST['np_cf_to'] ) ? antispambot( sanitize_email( wp_unslash( $_POST['np_cf_to'] ) ) ) : get_option( 'admin_email' );

	$body = "New enquiry from netpluscomputers.lk\n\n"
		. "Name:    {$name}\n"
		. "Phone:   {$phone}\n"
		. "Email:   {$email}\n"
		. "Subject: {$subject}\n\n"
		. "Message:\n{$message}\n\n"
		. '-- Sent via the website contact form';

	$sent = wp_mail(
		$to,
		sprintf( '[%s] %s — %s', get_bloginfo( 'name' ), $subject, $name ),
		$body,
		array( 'Reply-To: ' . ( $email ? $email : 'no-reply@' . wp_parse_url( home_url(), PHP_URL_HOST ) ) )
	);

	set_transient( $rl_key, $rl + 1, HOUR_IN_SECONDS );

	if ( $sent ) {
		np_contact_form_response( true, __( 'Thank you! Your message has been sent — we will reply soon.', 'netplus-circuit' ), $is_ajax );
	}
	np_contact_form_response( false, __( 'Sorry, the message could not be sent. Please WhatsApp us instead.', 'netplus-circuit' ), $is_ajax );
}
add_action( 'wp_ajax_np_contact_form', 'np_handle_contact_form' );
add_action( 'wp_ajax_nopriv_np_contact_form', 'np_handle_contact_form' );
add_action( 'admin_post_np_contact_form', 'np_handle_contact_form' );
add_action( 'admin_post_nopriv_np_contact_form', 'np_handle_contact_form' );

/**
 * Respond to contact form submission.
 *
 * @param bool   $ok      Success flag.
 * @param string $message Message.
 * @param bool   $is_ajax Whether this is an AJAX request.
 */
function np_contact_form_response( $ok, $message, $is_ajax ) {
	if ( $is_ajax ) {
		wp_send_json( array( 'success' => (bool) $ok, 'message' => $message ) );
	}
	// Non-JS fallback: redirect back with query args.
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = add_query_arg( array(
		'np_form' => $ok ? 'sent' : 'error',
		'np_msg'  => rawurlencode( $message ),
	), $redirect );
	$redirect = remove_query_arg( 'action', $redirect );
	wp_safe_redirect( $redirect . '#np-contact-form' );
	exit;
}

/* ==========================================================================
   [np_trust_bar] — reusable trust badges anywhere
   ========================================================================== */

add_shortcode( 'np_trust_bar', function () {
	ob_start();
	get_template_part( 'template-parts/trust-bar' );
	return ob_get_clean();
} );

/* ==========================================================================
   [np_sections] — render the coded homepage sections on any page
   ========================================================================== */

add_shortcode( 'np_sections', function () {
	ob_start();
	?>
	<div class="np-front">
		<?php
		get_template_part( 'template-parts/hero' );
		?>
		<div class="np-container"><?php get_template_part( 'template-parts/flash-sale' ); ?></div>
		<div class="np-container"><section class="np-section"><?php get_template_part( 'template-parts/promo-banners' ); ?></section></div>
		<?php
		get_template_part( 'template-parts/trust-bar' );
		?>
		<div class="np-container">
			<?php get_template_part( 'template-parts/promo-strip' ); ?>
			<section class="np-section"><?php get_template_part( 'template-parts/category-tiles' ); ?></section>
			<section class="np-section" style="padding-top:0;"><?php get_template_part( 'template-parts/featured-products' ); ?></section>
			<?php
			get_template_part( 'template-parts/newsletter' );
			get_template_part( 'template-parts/wa-strip' );
			?>
			<section class="np-section" style="padding-top:8px;"><?php get_template_part( 'template-parts/faq' ); ?></section>
			<section class="np-section" style="padding-top:0;"><?php get_template_part( 'template-parts/blog-preview' ); ?></section>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );
