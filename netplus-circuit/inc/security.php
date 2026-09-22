<?php
/**
 * Theme-level security & anti-spam hardening.
 *
 * Works together with the plugins you already run:
 *  - NinjaFirewall (WAF)
 *  - Limit Login Attempts Reloaded (brute force)
 *  - WPS Hide Login (login URL)
 *
 * Everything here can be toggled in Customizer → Security & Anti-Spam.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   Helpers
   ========================================================================== */

if ( ! function_exists( 'np_client_ip' ) ) :
	/**
	 * Best-effort client IP (Cloudflare aware).
	 *
	 * @return string
	 */
	function np_client_ip() {
		$ip = '';
		if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		return $ip ? $ip : '0.0.0.0';
	}
endif;

if ( ! function_exists( 'np_bot_time_token' ) ) :
	/**
	 * Signed timestamp used by timing traps: "timestamp.signature".
	 *
	 * @return string
	 */
	function np_bot_time_token() {
		$t = time();
		return $t . '.' . wp_hash( $t . '|np-time', 'nonce' );
	}
endif;

if ( ! function_exists( 'np_bot_time_seconds' ) ) :
	/**
	 * Seconds elapsed since a timing token was issued (PHP_INT_MAX if invalid/tampered).
	 *
	 * @param string $token Token from np_bot_time_token().
	 * @return int
	 */
	function np_bot_time_seconds( $token ) {
		$parts = explode( '.', (string) $token, 2 );
		if ( 2 !== count( $parts ) ) {
			return PHP_INT_MAX;
		}
		$t = (int) $parts[0];
		if ( ! hash_equals( wp_hash( $t . '|np-time', 'nonce' ), $parts[1] ) ) {
			return PHP_INT_MAX; // Tampered/forged token.
		}
		$elapsed = time() - $t;
		return ( $elapsed >= 0 && $elapsed < YEAR_IN_SECONDS ) ? $elapsed : PHP_INT_MAX;
	}
endif;

/* ==========================================================================
   Version & generator hiding
   ========================================================================== */

if ( np_opt_on( 'np_sec_hide_version', true ) ) {

	add_filter( 'the_generator', '__return_empty_string' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	add_filter( 'style_loader_src', 'np_remove_ver_param', 15 );
	add_filter( 'script_loader_src', 'np_remove_ver_param', 15 );
}

/**
 * Strip ?ver= query strings from WP core assets (mild fingerprint hiding).
 *
 * @param string $src Asset URL.
 * @return string
 */
function np_remove_ver_param( $src ) {
	if ( strpos( $src, 'ver=' ) && strpos( $src, home_url() ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

/* ==========================================================================
   XML-RPC
   ========================================================================== */

if ( np_opt_on( 'np_sec_disable_xmlrpc', true ) ) {
	add_filter( 'xmlrpc_enabled', '__return_false' );
	add_filter( 'xmlrpc_methods', function ( $methods ) {
		unset( $methods['pingback.ping'], $methods['pingback.extendedPingback'] );
		return $methods;
	} );
	// 403 direct hits to xmlrpc.php.
	add_action( 'xmlrpc_rpc_methods', '__return_empty_array' );
}

/* ==========================================================================
   User enumeration protection
   ========================================================================== */

if ( np_opt_on( 'np_sec_block_user_enum', true ) ) {

	// Block ?author= scanning.
	add_action( 'template_redirect', 'np_block_author_scan' );

	// Block REST user list for guests.
	add_filter( 'rest_authentication_errors', 'np_block_rest_users' );

	// Generic login errors (don't reveal whether a username exists).
	add_filter( 'authenticate', 'np_generic_login_errors', 30, 3 );

	// Hide user login in REST responses for guests.
	add_filter( 'rest_prepare_user', function ( $response ) {
		if ( ! is_user_logged_in() ) {
			if ( isset( $response->data['slug'] ) ) {
				unset( $response->data['slug'] );
			}
		}
		return $response;
	} );
}

/**
 * Redirect /?author=1 style scans home.
 */
function np_block_author_scan() {
	if ( isset( $_GET['author'] ) && is_numeric( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}

/**
 * 403 guest requests to /wp-json/wp/v2/users.
 *
 * @param mixed $result Auth result.
 * @return mixed
 */
function np_block_rest_users( $result ) {
	if ( null !== $result ) {
		return $result;
	}
	if ( is_user_logged_in() ) {
		return $result;
	}
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	if ( preg_match( '#/wp/v2/users#', $uri ) ) {
		return new WP_Error(
			'np_rest_users_forbidden',
			__( 'Access denied.', 'netplus-circuit' ),
			array( 'status' => 403 )
		);
	}
	return $result;
}

/**
 * Always answer "invalid username or password".
 *
 * @param WP_User|WP_Error|null $user     Auth result.
 * @param string                $username Username.
 * @param string                $password Password.
 * @return WP_User|WP_Error|null
 */
function np_generic_login_errors( $user, $username, $password ) {
	if ( is_wp_error( $user ) ) {
		$codes = $user->get_error_codes();
		if ( in_array( 'invalid_username', $codes, true ) || in_array( 'incorrect_password', $codes, true ) ) {
			return new WP_Error(
				'np_invalid_login',
				'<strong>' . esc_html__( 'Error:', 'netplus-circuit' ) . '</strong> ' . esc_html__( 'Invalid username or password.', 'netplus-circuit' )
			);
		}
	}
	return $user;
}

/* ==========================================================================
   Security headers
   ========================================================================== */

if ( np_opt_on( 'np_sec_headers', true ) ) {
	add_action( 'send_headers', 'np_send_security_headers' );
}

/**
 * Send baseline security headers (safe set — no CSP so Elementor never breaks).
 */
function np_send_security_headers() {
	if ( headers_sent() || is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
}

/* ==========================================================================
   Comment anti-spam (honeypot + timing trap)
   ========================================================================== */

if ( np_opt_on( 'np_sec_comment_honeypot', true ) ) {

	add_filter( 'comment_form_fields', 'np_comment_honeypot_fields' );
	add_filter( 'preprocess_comment', 'np_comment_bot_check', 1 );
}

/**
 * Inject hidden honeypot + timing fields into the comment form.
 *
 * @param array $fields Comment form fields.
 * @return array
 */
function np_comment_honeypot_fields( $fields ) {
	$hp = '<p class="np-hp-field" aria-hidden="true"><label for="np_website">Website (leave empty)</label>'
		. '<input type="text" name="np_website" id="np_website" value="" tabindex="-1" autocomplete="off" /></p>';
	$tm = '<input type="hidden" name="np_ctime" value="' . esc_attr( np_bot_time_token() ) . '" />';

	// Insert right before the submit field.
	if ( isset( $fields['submit'] ) ) {
		$fields['submit'] = $hp . $tm . $fields['submit'];
	} else {
		$fields['np_bot'] = $hp . $tm;
	}
	return $fields;
}

/**
 * Silently mark bot comments as spam.
 *
 * @param array $commentdata Comment data.
 * @return array
 */
function np_comment_bot_check( $commentdata ) {
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		return $commentdata; // Staff bypass.
	}
	$is_bot = false;

	// Honeypot filled?
	// phpcs:disable WordPress.Security.NonceVerification -- public form, no side effects beyond spam marking.
	if ( ! empty( $_POST['np_website'] ) ) {
		$is_bot = true;
	}
	// Submitted suspiciously fast?
	if ( ! empty( $_POST['np_ctime'] ) ) {
		$elapsed = np_bot_time_seconds( sanitize_text_field( wp_unslash( $_POST['np_ctime'] ) ) );
		if ( $elapsed < 4 ) {
			$is_bot = true;
		}
	}
	// Too many links = classic spam signature.
	$content = isset( $_POST['comment'] ) ? (string) wp_unslash( $_POST['comment'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- only counting links
	if ( substr_count( $content, 'http' ) > 3 ) {
		$is_bot = true;
	}
	// phpcs:enable

	if ( $is_bot ) {
		$commentdata['comment_approved'] = 'spam';
	}
	return $commentdata;
}

/* ==========================================================================
   Checkout anti-bot (honeypot + timing + rate limit)
   ========================================================================== */

if ( np_opt_on( 'np_sec_checkout_honeypot', true ) ) {

	add_action( 'woocommerce_checkout_process', 'np_checkout_bot_check' );
	add_action( 'woocommerce_after_checkout_validation', 'np_checkout_rate_limit', 10, 2 );
}

/**
 * Validate honeypot + minimum submit time on checkout.
 */
function np_checkout_bot_check() {
	if ( is_user_logged_in() && current_user_can( 'manage_woocommerce' ) ) {
		return; // Staff bypass.
	}
	// phpcs:disable WordPress.Security.NonceVerification -- WC validates its own checkout nonce already.
	$hp_filled = ! empty( $_POST['np_bot_hp'] );

	$time_token = isset( $_POST['np_bot_time'] ) ? sanitize_text_field( wp_unslash( $_POST['np_bot_time'] ) ) : '';
	$too_fast   = false;
	if ( $time_token ) {
		$elapsed = np_bot_time_seconds( $time_token );
		// Forged/missing-timing tokens are rejected here (checkout pages are never cached).
		$too_fast = ( PHP_INT_MAX === $elapsed || $elapsed < 4 );
	}
	// phpcs:enable

	if ( $hp_filled || $too_fast ) {
		// Generic message — don't teach the bot which rule caught it.
		wc_add_notice( __( 'Your order could not be verified. Please refresh the page and try again.', 'netplus-circuit' ), 'error' );
	}
}

/**
 * Rate limit checkout submissions per IP (fake-order flood protection).
 *
 * @param array     $data   Posted data.
 * @param WP_Error  $errors Errors object.
 */
function np_checkout_rate_limit( $data, $errors ) {
	if ( is_user_logged_in() ) {
		return;
	}
	$key   = 'np_ck_' . md5( np_client_ip() );
	$hits  = (int) get_transient( $key );
	$limit = 10; // submissions per 10 minutes per IP (guests only)

	if ( $hits >= $limit ) {
		$errors->add(
			'np_rate_limit',
			__( 'Too many checkout attempts from your connection. Please wait 10 minutes or contact us on WhatsApp.', 'netplus-circuit' )
		);
		return;
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );
}

/* ==========================================================================
   Role-aware login/registration redirects & login branding
   ========================================================================== */

/**
 * Staff → dashboard, customers → My Account.
 *
 * @param string           $redirect_to Target URL.
 * @param string           $requested   Requested URL.
 * @param WP_User|WP_Error $user        User.
 * @return string
 */
function np_login_redirect( $redirect_to, $requested, $user ) {
	if ( $user instanceof WP_User ) {
		if ( user_can( $user, 'manage_woocommerce' ) || user_can( $user, 'edit_posts' ) ) {
			return admin_url();
		}
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			return wc_get_page_permalink( 'myaccount' );
		}
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'np_login_redirect', 10, 3 );

/**
 * After registration, customers land on My Account.
 *
 * @param string $redirect Redirect target.
 * @return string
 */
function np_registration_redirect( $redirect ) {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'myaccount' );
	}
	return $redirect;
}
add_filter( 'registration_redirect', 'np_registration_redirect' );

/**
 * Logout → home page.
 */
add_action( 'wp_logout', function () {
	wp_safe_redirect( home_url( '/' ) );
	exit;
} );

/**
 * Branded login screen links.
 */
add_filter( 'login_headerurl', 'home_url' );
add_filter( 'login_headertext', function () {
	return get_bloginfo( 'name' );
} );

/* ==========================================================================
   Misc hardening
   ========================================================================== */

// Disable trackbacks/pingbacks on new posts by default (spam vector).
add_filter( 'pre_option_default_pingback_flag', '__return_zero' );
add_filter( 'pre_option_default_ping_status', '__return_zero' );

// Remove unnecessary dashboard widgets for shop staff (less attack surface & noise).
add_action( 'wp_dashboard_setup', 'np_trim_dashboard', 999 );
function np_trim_dashboard() {
	if ( current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	}
}

// Deny script execution inside uploads (Apache/LiteSpeed .htaccess; ignored on nginx, harmless).
add_action( 'after_switch_theme', 'np_protect_uploads_dir' );
function np_protect_uploads_dir() {
	$uploads = function_exists( 'wp_get_upload_dir' ) ? wp_get_upload_dir() : wp_upload_dir();
	if ( empty( $uploads['basedir'] ) || ! is_dir( $uploads['basedir'] ) ) {
		return;
	}
	$htaccess = $uploads['basedir'] . '/.htaccess';
	if ( file_exists( $htaccess ) ) {
		return;
	}
	$rules = "# NetPlus Circuit: block script execution in uploads\n"
		. "<FilesMatch \"\\.(?i:php|php3|php4|php5|php7|phtml|pl|py|cgi|sh)$\">\n"
		. "  <IfModule mod_authz_core.c>\n"
		. "    Require all denied\n"
		. "  </IfModule>\n"
		. "  <IfModule !mod_authz_core.c>\n"
		. "    Order deny,allow\n"
		. "    Deny from all\n"
		. "  </IfModule>\n"
		. "</FilesMatch>\n";
	@file_put_contents( $htaccess, $rules ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- one-time hardening file.
}
