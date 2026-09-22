<?php
/**
 * Elementor / Ultimate Addons (HFE) compatibility.
 *
 * - Registers Elementor Pro theme locations so Pro headers/footers work.
 * - When "Header/Footer source = Built with Elementor / UAE" is selected in
 *   the Customizer, the theme suppresses its own coded header & footer so
 *   Elementor-built ones (via Elementor Pro or the free Header Footer
 *   Elementor plugin bundled with Ultimate Addons) take over completely.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Elementor theme locations.
 *
 * @param object $elementor_theme_manager Theme manager.
 */
function np_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'np_register_elementor_locations' );

/**
 * Is the theme handing header/footer over to Elementor/UAE?
 *
 * @return bool
 */
function np_header_is_elementor_mode() {
	return 'elementor' === get_theme_mod( 'np_header_mode', 'coded' );
}

/**
 * Mark Elementor-built pages so the theme removes panel wrappers.
 *
 * @param array $classes Body classes.
 * @return array
 */
function np_elementor_body_classes( $classes ) {
	if ( did_action( 'elementor/loaded' ) && function_exists( 'elementor_theme_do_location' ) ) {
		$classes[] = 'np-elementor-ready';
	}
	return $classes;
}
add_filter( 'body_class', 'np_elementor_body_classes' );
