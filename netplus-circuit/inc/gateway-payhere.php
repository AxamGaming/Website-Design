<?php
/**
 * PayHere payment gateway for WooCommerce (Sri Lanka).
 *
 * Supports Visa / Master / Amex cards, eZ Cash, mCash, FRIMI, QR — everything
 * PayHere Checkout offers. Settings live in WooCommerce → Settings → Payments
 * → "Credit / Debit Card (PayHere)".
 *
 * Flow: checkout → redirect to PayHere Checkout (live/sandbox) → customer pays
 * → PayHere posts to our notify URL (server-to-server, MD5-hash verified) →
 * order marked paid → customer returned to Thank-You page.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the gateway with WooCommerce.
 *
 * @param array $methods Gateway classes.
 * @return array
 */
function np_register_payhere_gateway( $methods ) {
	$methods[] = 'NP_PayHere_Gateway';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'np_register_payhere_gateway' );

/**
 * Gateway class (loaded on plugins_loaded so WC classes exist).
 */
function np_load_payhere_gateway_class() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	/**
	 * NP PayHere Gateway.
	 */
	class NP_PayHere_Gateway extends WC_Payment_Gateway {

		/**
		 * Constructor: id, title, settings form.
		 */
		public function __construct() {
			$this->id                 = 'payhere';
			$this->has_fields         = false;
			$this->method_title       = __( 'Credit / Debit Card (PayHere)', 'netplus-circuit' );
			$this->method_description = __( 'Accept Visa, Master, Amex, eZ Cash, mCash & FRIMI through PayHere (payhere.lk). Create a free PayHere merchant account, then enter your Merchant ID and Merchant Secret below (PayHere → Settings → Domains & Credentials). Use Test Mode first with a sandbox account.', 'netplus-circuit' );
			$this->supports           = array( 'products' );

			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->get_option( 'title', __( 'Credit / Debit Card', 'netplus-circuit' ) );
			$this->description = $this->get_option( 'description' );
			$this->test_mode   = 'yes' === $this->get_option( 'test_mode', 'no' );
			$this->enabled     = $this->get_option( 'enabled', 'no' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_api_np_payhere_notify', array( $this, 'handle_notify' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_note' ) );
		}

		/**
		 * Admin settings fields (WooCommerce → Settings → Payments → PayHere).
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'         => array(
					'title'   => __( 'Enable / Disable', 'netplus-circuit' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable PayHere card payments', 'netplus-circuit' ),
					'default' => 'no',
				),
				'title'           => array(
					'title'       => __( 'Title at checkout', 'netplus-circuit' ),
					'type'        => 'text',
					'description' => __( 'What customers see as the payment method name.', 'netplus-circuit' ),
					'default'     => __( 'Credit / Debit Card', 'netplus-circuit' ),
				),
				'description'     => array(
					'title'       => __( 'Description at checkout', 'netplus-circuit' ),
					'type'        => 'textarea',
					'default'     => __( 'Pay securely with Visa, Master, Amex, eZ Cash or mCash via PayHere. You will be redirected to PayHere to complete payment.', 'netplus-circuit' ),
				),
				'test_mode'       => array(
					'title'   => __( 'Test (sandbox) mode', 'netplus-circuit' ),
					'type'    => 'checkbox',
					'label'   => __( 'Use PayHere sandbox for testing', 'netplus-circuit' ),
					'default' => 'no',
				),
				'merchant_id'     => array(
					'title'       => __( 'Merchant ID', 'netplus-circuit' ),
					'type'        => 'text',
					'description' => __( 'PayHere → Settings → Business / Merchant ID.', 'netplus-circuit' ),
					'default'     => '',
				),
				'merchant_secret' => array(
					'title'       => __( 'Merchant Secret', 'netplus-circuit' ),
					'type'        => 'password',
					'description' => __( 'PayHere → Settings → Domains & Credentials → Merchant Secret. Kept encrypted in your database.', 'netplus-circuit' ),
					'default'     => '',
				),
				'order_prefix'    => array(
					'title'   => __( 'Order ID prefix', 'netplus-circuit' ),
					'type'    => 'text',
					'default' => 'NP',
				),
			);
		}

		/**
		 * Whether the gateway is ready (configured).
		 *
		 * @return bool
		 */
		public function is_available() {
			if ( 'yes' !== $this->enabled ) {
				return false;
			}
			return (bool) $this->get_option( 'merchant_id' );
		}

		/**
		 * Payment page content.
		 */
		public function payment_fields() {
			$desc = $this->get_description();
			if ( $desc ) {
				echo '<p style="font-size:.9rem;color:var(--np-muted);">' . wp_kses_post( wpautop( $desc ) ) . '</p>';
			}
			echo '<p style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">';
			foreach ( array( 'VISA', 'MASTER', 'AMEX', 'eZ Cash', 'mCash', 'FRIMI' ) as $np_brand ) {
				echo '<span class="np-pay-chip" style="background:var(--np-bg);color:var(--np-ink-3);border:1px solid var(--np-line);">' . esc_html( $np_brand ) . '</span>';
			}
			echo '</p>';
		}

		/**
		 * Checkout URL (live or sandbox).
		 *
		 * @return string
		 */
		protected function checkout_url() {
			return $this->test_mode
				? 'https://sandbox.payhere.lk/pay/checkout'
				: 'https://www.payhere.lk/pay/checkout';
		}

		/**
		 * Build the PayHere hash.
		 *
		 * @param string $order_id Order reference.
		 * @param string $amount   Amount formatted 2dp.
		 * @return string
		 */
		protected function build_hash( $order_id, $amount ) {
			$secret = (string) $this->get_option( 'merchant_secret' );
			return strtoupper( md5(
				(string) $this->get_option( 'merchant_id' ) .
				$order_id .
				$amount .
				'LKR' .
				strtoupper( md5( $secret ) )
			) );
		}

		/**
		 * Redirect to PayHere.
		 *
		 * @param int $order_id Order ID.
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return array( 'result' => 'failure' );
			}

			$prefix   = (string) $this->get_option( 'order_prefix', 'NP' );
			$ref      = $prefix . $order_id;
			$amount   = number_format( (float) $order->get_total(), 2, '.', '' );
			$address  = $order->get_billing_address_1() ? $order->get_billing_address_1() : 'N/A';
			$city     = $order->get_billing_city() ? $order->get_billing_city() : 'N/A';

			$args = array(
				'merchant_id'      => (string) $this->get_option( 'merchant_id' ),
				'return_url'       => $this->get_return_url( $order ),
				'cancel_url'       => $order->get_cancel_order_url(),
				'notify_url'       => add_query_arg( 'wc-api', 'NP_PayHere_Notify', home_url( '/' ) ),
				'order_id'         => $ref,
				'items'            => sprintf(
					/* translators: %s: site name */
					__( 'Order %s at %s', 'netplus-circuit' ),
					$ref,
					get_bloginfo( 'name' )
				),
				'currency'         => 'LKR',
				'amount'           => $amount,
				'hash'             => $this->build_hash( $ref, $amount ),
				'first_name'       => $order->get_billing_first_name() ? $order->get_billing_first_name() : 'Customer',
				'last_name'        => $order->get_billing_last_name() ? $order->get_billing_last_name() : '-',
				'email'            => $order->get_billing_email(),
				'phone'            => preg_replace( '/[^0-9+]/', '', $order->get_billing_phone() ),
				'address'          => $address,
				'city'             => $city,
				'country'          => 'Sri Lanka',
				'delivery_address' => $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $address,
				'delivery_city'    => $order->get_shipping_city() ? $order->get_shipping_city() : $city,
				'delivery_country' => 'Sri Lanka',
			);

			WC()->cart->empty_cart();
			$order->update_status( 'on-hold', __( 'Awaiting PayHere payment confirmation.', 'netplus-circuit' ) );
			$order->add_meta_data( '_np_payhere_ref', $ref, true );

			return array(
				'result'   => 'success',
				'redirect' => add_query_arg( $args, $this->checkout_url() ),
			);
		}

		/**
		 * Server-to-server notification from PayHere (hash-verified).
		 */
		public function handle_notify() {
			// phpcs:disable WordPress.Security.NonceVerification -- verified via PayHere MD5 signature instead.
			$merchant_id = isset( $_POST['merchant_id'] ) ? sanitize_text_field( wp_unslash( $_POST['merchant_id'] ) ) : '';
			$order_id    = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : '';
			$payhere_amt = isset( $_POST['payhere_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['payhere_amount'] ) ) : '';
			$status_code = isset( $_POST['status_code'] ) ? sanitize_text_field( wp_unslash( $_POST['status_code'] ) ) : '';
			$md5sig      = isset( $_POST['md5sig'] ) ? sanitize_text_field( wp_unslash( $_POST['md5sig'] ) ) : '';
			// phpcs:enable

			if ( ! $order_id || ! $md5sig ) {
				status_header( 400 );
				exit;
			}

			$secret = (string) $this->get_option( 'merchant_secret' );
			$local_sig = strtoupper( md5(
				$merchant_id .
				$order_id .
				$payhere_amt .
				$status_code .
				strtoupper( md5( $secret ) )
			) );

			if ( ! hash_equals( $local_sig, $md5sig ) ) {
				status_header( 403 );
				exit;
			}

			$ref      = (string) $order_id;
			$order_id_num = (int) preg_replace( '/^[A-Za-z]+/', '', $ref );
			$order    = wc_get_order( $order_id_num );

			if ( ! $order ) {
				status_header( 404 );
				exit;
			}

			// Never downgrade a paid order.
			if ( $order->is_paid() ) {
				echo 'OK';
				exit;
			}

			if ( '2' === (string) $status_code ) { // Success.
				$order->add_order_note(
					sprintf(
						/* translators: %s: payhere order ref */
						__( 'PayHere payment confirmed (ref %s).', 'netplus-circuit' ),
						isset( $_POST['payhere_payment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['payhere_payment_id'] ) ) : $ref // phpcs:ignore WordPress.Security.NonceVerification
					)
				);
				$order->payment_complete( $ref );
			} elseif ( in_array( (string) $status_code, array( '-1', '-2', '-3' ), true ) ) {
				$order->update_status( 'failed', __( 'PayHere reported payment cancellation / failure.', 'netplus-circuit' ) );
			} else {
				$order->update_status( 'on-hold', __( 'PayHere payment pending.', 'netplus-circuit' ) );
			}

			echo 'OK';
			exit;
		}

		/**
		 * Thank-you page note.
		 */
		public function thankyou_note() {
			echo '<div class="woocommerce-info">' . esc_html__( 'Thank you! Your order is confirmed as soon as PayHere verifies the payment — this usually takes a few seconds. You can pay by card, eZ Cash, mCash or FRIMI on the PayHere page.', 'netplus-circuit' ) . '</div>';
		}
	}
}
add_action( 'plugins_loaded', 'np_load_payhere_gateway_class', 20 );

/**
 * Enable COD by default on activation (Sri Lanka default) — user can disable
 * anytime in WooCommerce → Settings → Payments.
 */
function np_default_payment_gateways() {
	$cod = get_option( 'woocommerce_cod_settings', null );
	if ( null === $cod ) {
		update_option( 'woocommerce_cod_settings', array(
			'enabled' => 'yes',
			'title'   => __( 'Cash on Delivery', 'netplus-circuit' ),
		) );
	}
	$bacs = get_option( 'woocommerce_bacs_settings', null );
	if ( null === $bacs ) {
		update_option( 'woocommerce_bacs_settings', array(
			'enabled' => 'yes',
			'title'   => __( 'Bank Transfer', 'netplus-circuit' ),
		) );
	}
}
add_action( 'after_switch_theme', 'np_default_payment_gateways' );
