<?php
/**
 * Enqueue scripts & styles, cart fragments, dynamic CSS variables.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end assets.
 */
function np_circuit_scripts() {
	// Google fonts — families chosen in Customizer → Typography.
	$np_font_url = np_build_fonts_url();
	if ( $np_font_url ) {
		wp_enqueue_style( 'np-circuit-fonts', $np_font_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	wp_enqueue_style( 'np-circuit-style', get_stylesheet_uri(), array(), NP_CIRCUIT_VERSION );

	wp_enqueue_script( 'np-circuit-main', NP_CIRCUIT_URI . '/assets/js/main.js', array(), NP_CIRCUIT_VERSION, true );

	wp_localize_script( 'np-circuit-main', 'npCircuit', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'np_circuit_ajax' ),
		'homeUrl'   => home_url( '/' ),
		'waLink'    => np_whatsapp_link(),
		'i18n'      => array(
			'addedToCart' => __( 'Added to cart', 'netplus-circuit' ),
			'error'       => __( 'Something went wrong. Please try again.', 'netplus-circuit' ),
			'sending'     => __( 'Sending…', 'netplus-circuit' ),
		),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_product() ) {
			wp_enqueue_script( 'wc-single-product' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'np_circuit_scripts' );

/**
 * Build the Google Fonts URL from Customizer typography choices.
 *
 * @return string
 */
function np_build_fonts_url() {
	$roles = array(
		'display' => array( get_theme_mod( 'np_font_display', 'Chakra Petch' ), get_theme_mod( 'np_font_display_custom', '' ), '600;700;800' ),
		'head'    => array( get_theme_mod( 'np_font_head', 'Rajdhani' ), get_theme_mod( 'np_font_head_custom', '' ), '500;600;700' ),
		'body'    => array( get_theme_mod( 'np_font_body', 'Inter' ), get_theme_mod( 'np_font_body_custom', '' ), '400;500;600;700' ),
		'mono'    => array( get_theme_mod( 'np_font_mono', 'JetBrains Mono' ), get_theme_mod( 'np_font_mono_custom', '' ), '400;600' ),
	);
	$families = array();
	foreach ( $roles as $role ) {
		$family = ( 'custom' === $role[0] ) ? trim( (string) $role[1] ) : $role[0];
		if ( ! $family ) {
			continue;
		}
		$key = strtolower( $family );
		if ( isset( $families[ $key ] ) ) {
			// Merge weights for duplicated families.
			$families[ $key ]['weights'] = array_unique( array_merge( $families[ $key ]['weights'], explode( ';', $role[2] ) ) );
			continue;
		}
		$families[ $key ] = array( 'name' => $family, 'weights' => explode( ';', $role[2] ) );
	}
	if ( empty( $families ) ) {
		return '';
	}
	$parts = array();
	foreach ( $families as $fam ) {
		sort( $fam['weights'] );
		$parts[] = 'family=' . str_replace( ' ', '+', $fam['name'] ) . ':wght@' . implode( ';', $fam['weights'] );
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

/**
 * Resolved font family for a role (custom aware).
 *
 * @param string $role display|head|body|mono.
 * @return string
 */
function np_font_family( $role ) {
	$sel    = get_theme_mod( 'np_font_' . $role, '' );
	$custom = trim( (string) get_theme_mod( 'np_font_' . $role . '_custom', '' ) );
	if ( 'custom' === $sel && $custom ) {
		return $custom;
	}
	$defaults = array(
		'display' => 'Chakra Petch',
		'head'    => 'Rajdhani',
		'body'    => 'Inter',
		'mono'    => 'JetBrains Mono',
	);
	return ( $sel && 'custom' !== $sel ) ? $sel : $defaults[ $role ];
}

/**
 * Customizer-driven CSS variables (colors + fonts + sizes).
 */
function np_circuit_custom_colors() {
	$vals = array(
		'--np-accent'      => get_theme_mod( 'np_color_accent', '#d90429' ),
		'--np-accent-dark' => get_theme_mod( 'np_color_accent_dark', '#a80321' ),
		'--np-hot'         => get_theme_mod( 'np_color_hot', '#f77f00' ),
		'--np-hot-dark'    => get_theme_mod( 'np_color_hot', '#f77f00' ),
		'--np-ink'         => get_theme_mod( 'np_color_ink', '#1b1f27' ),
		'--np-sale'        => get_theme_mod( 'np_color_sale', '#d90429' ),
		'--np-green'       => get_theme_mod( 'np_color_success', '#17a558' ),
		'--np-bg'          => get_theme_mod( 'np_color_bg', '#f6f6f4' ),
		'--np-font-display' => '"' . np_font_family( 'display' ) . '", system-ui, sans-serif',
		'--np-font-head'    => '"' . np_font_family( 'head' ) . '", system-ui, sans-serif',
		'--np-font-body'    => '"' . np_font_family( 'body' ) . '", system-ui, sans-serif',
		'--np-font-mono'    => '"' . np_font_family( 'mono' ) . '", ui-monospace, monospace',
	);
	$body_size = (int) get_theme_mod( 'np_body_size', 15 );
	if ( $body_size && 15 !== $body_size ) {
		$vals['--np-body-size'] = $body_size . 'px';
	}
	$vals['--np-accent-soft'] = np_hex_to_rgba( $vals['--np-accent'], 0.09 );

	$css = ':root{';
	foreach ( $vals as $k => $v ) {
		$css .= $k . ':' . $v . ';';
	}
	$css .= '}';
	wp_add_inline_style( 'np-circuit-style', $css );
}
add_action( 'wp_enqueue_scripts', 'np_circuit_custom_colors', 20 );

/**
 * Hex color to rgba() string helper.
 *
 * @param string $hex   Hex color.
 * @param float  $alpha Alpha 0-1.
 * @return string
 */
function np_hex_to_rgba( $hex, $alpha = 1 ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return 'rgba(47,107,255,' . $alpha . ')';
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $alpha . ')';
}

/**
 * WooCommerce cart fragments (keeps header cart count live via AJAX).
 */
function np_circuit_cart_fragment( $fragments ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $fragments;
	}
	$count = WC()->cart->get_cart_contents_count();
	$total = WC()->cart->get_cart_subtotal();

	$fragments['span.np-cart-count']  = '<span class="np-haction__count np-cart-count' . ( $count ? '' : ' is-hidden' ) . '">' . esc_html( $count ) . '</span>';
	$fragments['span.np-cart-subtotal'] = '<span class="np-haction__value np-cart-subtotal">' . wp_kses_post( $total ) . '</span>';

	ob_start();
	np_cart_dropdown_content();
	$fragments['div.np-cart-dd__panel'] = '<div class="np-cart-dd__panel">' . ob_get_clean() . '</div>';

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'np_circuit_cart_fragment' );

/**
 * Inner content of header cart dropdown.
 */
function np_cart_dropdown_content() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		echo '<div class="np-cart-dd__empty">' . esc_html__( 'Your cart is currently empty.', 'netplus-circuit' ) . '</div>';
		echo '<a class="np-btn np-btn--primary np-btn--block np-btn--sm" href="' . esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ) . '">' . esc_html__( 'Start shopping', 'netplus-circuit' ) . '</a>';
		return;
	}
	echo '<div class="np-cart-dd__items">';
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product = $cart_item['data'];
		if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
			continue;
		}
		echo '<div class="np-cart-dd__item">';
		echo '<a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . wp_kses_post( $product->get_image( 'thumbnail', array( 'class' => '' ) ) ) . '</a>';
		echo '<div><div class="np-cart-dd__item-name"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . esc_html( wp_trim_words( $product->get_name(), 8 ) ) . '</a></div>';
		echo '<div class="np-cart-dd__item-qty">' . esc_html( $cart_item['quantity'] ) . ' × ' . wp_kses_post( WC()->cart->get_product_price( $product ) ) . '</div></div>';
		echo '</div>';
	}
	echo '</div>';
	echo '<div class="np-cart-dd__total"><span>' . esc_html__( 'Subtotal', 'netplus-circuit' ) . '</span><span>' . wp_kses_post( WC()->cart->get_cart_subtotal() ) . '</span></div>';
	echo '<div class="np-flex" style="margin-top:10px;gap:8px;">';
	echo '<a class="np-btn np-btn--ghost np-btn--sm" style="flex:1;" href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'View cart', 'netplus-circuit' ) . '</a>';
	echo '<a class="np-btn np-btn--hot np-btn--sm" style="flex:1;" href="' . esc_url( wc_get_checkout_url() ) . '">' . esc_html__( 'Checkout', 'netplus-circuit' ) . '</a>';
	echo '</div>';
}

/**
 * Login page branding CSS.
 */
function np_circuit_login_styles() {
	wp_enqueue_style( 'np-circuit-login', NP_CIRCUIT_URI . '/assets/css/login.css', array(), NP_CIRCUIT_VERSION );
}
add_action( 'login_enqueue_scripts', 'np_circuit_login_styles' );

/**
 * Preconnect for font host.
 */
function np_circuit_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com', 'crossorigin' );
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'np_circuit_resource_hints', 10, 2 );
