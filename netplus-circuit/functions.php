<?php
/**
 * NetPlus Circuit theme bootstrap.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

define( 'NP_CIRCUIT_VERSION', '1.0.0' );
define( 'NP_CIRCUIT_DIR', get_template_directory() );
define( 'NP_CIRCUIT_URI', get_template_directory_uri() );

/**
 * Include theme modules.
 */
$np_modules = array(
	'inc/theme-setup.php',
	'inc/template-tags.php',
	'inc/icons.php',
	'inc/assets.php',
	'inc/customizer.php',
	'inc/woocommerce.php',
	'inc/gateway-payhere.php',
	'inc/security.php',
	'inc/accounts.php',
	'inc/meta-box.php',
	'inc/shortcodes.php',
	'inc/newsletter.php',
	'inc/demo-import.php',
	'inc/elementor-support.php',
);

foreach ( $np_modules as $np_module ) {
	$np_path = NP_CIRCUIT_DIR . '/' . $np_module;
	if ( file_exists( $np_path ) ) {
		require_once $np_path;
	}
}

/**
 * Theme activation: create pages, roles, flush rewrite rules.
 */
require_once NP_CIRCUIT_DIR . '/inc/activation.php';
