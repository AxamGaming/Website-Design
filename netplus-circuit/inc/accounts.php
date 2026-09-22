<?php
/**
 * Accounts & purchase rules.
 *
 *  - Guests can browse + add to cart, but MUST sign in / sign up to checkout
 *    (toggle in Customizer → Checkout Rules).
 *  - Every new registration becomes a **customer** — only an administrator can
 *    promote accounts (quick links on Users screen + User Role Editor).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   Login-gated checkout
   ========================================================================== */

/**
 * Is the login gate enabled? (default ON)
 */
function np_login_gate_on() {
	return np_opt_on( 'np_require_login_checkout', true );
}

/**
 * Redirect guests away from checkout to the login/register page.
 */
add_action( 'template_redirect', 'np_gate_checkout_redirect' );
function np_gate_checkout_redirect() {
	if ( ! np_login_gate_on() || ! function_exists( 'is_checkout' ) ) {
		return;
	}
	if ( is_checkout() && ! is_user_logged_in() && ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
		$target = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
		$return = add_query_arg(
			'np_redirect_to',
			rawurlencode( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/' ) ),
			$target
		);
		wp_safe_redirect( $return );
		exit;
	}
}

/**
 * WooCommerce-level enforcement as well (belt & braces).
 */
add_filter( 'woocommerce_checkout_registration_required', function ( $required ) {
	return np_login_gate_on() ? true : $required;
} );

/**
 * Friendly notice on the cart for guests.
 */
add_action( 'woocommerce_before_cart', 'np_cart_login_notice' );
function np_cart_login_notice() {
	if ( ! np_login_gate_on() || is_user_logged_in() ) {
		return;
	}
	$msg  = get_theme_mod( 'np_login_gate_message', __( 'Please sign in (or create a free account) to proceed to checkout. You can keep adding items to your cart as a guest.', 'netplus-circuit' ) );
	$link = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
	echo '<div class="woocommerce-info np-login-gate">' . np_icon( 'lock', 16 ) . ' ' . esc_html( $msg ) . ' <a class="np-btn np-btn--primary np-btn--sm" style="margin-left:10px;" href="' . esc_url( add_query_arg( 'np_redirect_to', rawurlencode( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '' ), $link ) ) . '">' . esc_html__( 'Sign in / Register', 'netplus-circuit' ) . '</a></div>';
}

/**
 * After login/registration send the shopper back to checkout.
 *
 * @param string           $redirect_to Requested redirect.
 * @param string           $requested   Raw requested value.
 * @param WP_User|WP_Error $user        User.
 * @return string
 */
function np_login_redirect_back( $redirect_to, $requested, $user ) {
	// phpcs:ignore WordPress.Security.NonceVerification -- login flow, validated below.
	if ( isset( $_REQUEST['np_redirect_to'] ) ) {
		$target = sanitize_text_field( wp_unslash( $_REQUEST['np_redirect_to'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$target = wp_validate_redirect( $target, '' );
		if ( $target ) {
			return $target;
		}
	}
	return np_login_redirect( $redirect_to, $requested, $user );
}
// Replace the plain role redirect with the gate-aware one.
remove_filter( 'login_redirect', 'np_login_redirect', 10 );
add_filter( 'login_redirect', 'np_login_redirect_back', 10, 3 );

/* ==========================================================================
   Everyone signs up as a customer — only admins promote
   ========================================================================== */

/**
 * Force WooCommerce registrations to the customer role.
 *
 * @param array $data New customer data.
 * @return array
 */
function np_force_customer_role( $data ) {
	$data['role'] = 'customer';
	return $data;
}
add_filter( 'woocommerce_new_customer_data', 'np_force_customer_role' );

/**
 * Safety net: any front-end registration (WP core form too) becomes customer.
 *
 * @param int $user_id New user ID.
 */
function np_front_end_registration_role( $user_id ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return; // Admin-created users keep the chosen role.
	}
	$user = get_userdata( $user_id );
	if ( $user && in_array( 'administrator', (array) $user->roles, true ) ) {
		return;
	}
	if ( $user && ! in_array( 'customer', (array) $user->roles, true ) && ! current_user_can( 'promote_users' ) ) {
		$user->set_role( 'customer' );
	}
}
add_action( 'user_register', 'np_front_end_registration_role', 20 );

/* ==========================================================================
   Quick role controls on the Users screen (admins only)
   ========================================================================== */

/**
 * Add "Make Manager / Make Admin / Make Customer" row actions.
 *
 * @param array   $actions Row actions.
 * @param WP_User $user    User object.
 * @return array
 */
function np_user_row_role_actions( $actions, $user ) {
	if ( ! current_user_can( 'promote_users' ) ) {
		return $actions;
	}
	if ( in_array( 'administrator', (array) $user->roles, true ) && get_current_user_id() === $user->ID ) {
		return $actions; // Never demote yourself by accident.
	}
	$roles = array(
		'customer'      => __( 'Make Customer', 'netplus-circuit' ),
		'store_manager' => __( 'Make Manager', 'netplus-circuit' ),
		'administrator' => __( 'Make Admin', 'netplus-circuit' ),
	);
	foreach ( $roles as $role => $label ) {
		if ( in_array( $role, (array) $user->roles, true ) ) {
			continue;
		}
		$url = wp_nonce_url( admin_url( 'users.php?np_set_role=' . $role . '&user_id=' . $user->ID ), 'np_set_role_' . $user->ID );
		$actions[ 'np_role_' . $role ] = '<a href="' . esc_url( $url ) . '" style="color:#2271b1;">' . esc_html( $label ) . '</a>';
	}
	return $actions;
}
add_filter( 'user_row_actions', 'np_user_row_role_actions', 10, 2 );

/**
 * Handle the quick role change.
 */
function np_handle_set_role() {
	if ( ! isset( $_GET['np_set_role'], $_GET['user_id'] ) ) {
		return;
	}
	$user_id = absint( $_GET['user_id'] );
	$role    = sanitize_key( $_GET['np_set_role'] );
	if ( ! check_admin_referer( 'np_set_role_' . $user_id ) ) {
		return;
	}
	if ( ! current_user_can( 'promote_users' ) ) {
		wp_die( esc_html__( 'You are not allowed to change roles.', 'netplus-circuit' ) );
	}
	$allowed = array( 'customer', 'store_manager', 'store_staff', 'administrator', 'shop_manager' );
	if ( ! in_array( $role, $allowed, true ) ) {
		return;
	}
	$target = get_userdata( $user_id );
	if ( ! $target ) {
		return;
	}
	if ( $user_id === get_current_user_id() && 'administrator' !== $role ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'You cannot change your own role.', 'netplus-circuit' ) . '</p></div>';
		} );
		return;
	}
	$target->set_role( $role );
	add_action( 'admin_notices', function () use ( $target, $role ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(
			sprintf(
				/* translators: 1: user name, 2: role */
				__( '%1$s is now: %2$s', 'netplus-circuit' ),
				$target->display_name,
				translate_user_role( ucwords( str_replace( '_', ' ', $role ) ) )
			)
		) . '</p></div>';
	} );
}
add_action( 'admin_init', 'np_handle_set_role' );
