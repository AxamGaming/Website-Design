<?php
/**
 * Theme setup: supports, menus, sidebars, image sizes.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'np_circuit_setup' ) ) :
	/**
	 * Register theme supports and nav menus.
	 */
	function np_circuit_setup() {
		load_theme_textdomain( 'netplus-circuit', NP_CIRCUIT_DIR . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo', array(
			'height'      => 110,
			'width'       => 440,
			'flex-height' => true,
			'flex-width'  => true,
		) );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => 400,
			'single_image_width'    => 700,
			'product_grid'          => array(
				'default_rows'    => 4,
				'default_columns' => 4,
				'min_rows'        => 1,
				'min_columns'     => 2,
				'max_columns'     => 6,
			),
		) );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Elementor plays nicely with this theme.
		add_theme_support( 'elementor' );
		add_theme_support( 'elementor-pro' );

		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu (dark nav bar)', 'netplus-circuit' ),
			'footer'  => esc_html__( 'Footer Quick Links', 'netplus-circuit' ),
			'topbar'  => esc_html__( 'Top Bar Links', 'netplus-circuit' ),
		) );

		add_image_size( 'np-product-card', 480, 480, true );
		add_image_size( 'np-post-card', 640, 360, true );
		add_image_size( 'np-promo-banner', 760, 460, true );
		add_image_size( 'np-hero-slide', 1600, 640, true );
	}
endif;
add_action( 'after_setup_theme', 'np_circuit_setup' );

/**
 * Set content width.
 */
function np_circuit_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'np_circuit_content_width', 1240 );
}
add_action( 'after_setup_theme', 'np_circuit_content_width', 0 );

/**
 * Register widget areas.
 */
function np_circuit_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Shop Sidebar', 'netplus-circuit' ),
		'id'            => 'shop-sidebar',
		'description'   => esc_html__( 'Widgets shown next to product listings (categories, price filter, top rated…).', 'netplus-circuit' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'netplus-circuit' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'Widgets shown next to blog posts and archives.', 'netplus-circuit' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'netplus-circuit' ),
		'id'            => 'page-sidebar',
		'description'   => esc_html__( 'Widgets shown on default page templates.', 'netplus-circuit' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	$footer_areas = 4;
	for ( $i = 1; $i <= $footer_areas; $i++ ) {
		register_sidebar( array(
			/* translators: %d: footer column number. */
			'name'          => sprintf( esc_html__( 'Footer Column %d', 'netplus-circuit' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => esc_html__( 'Add widgets here to replace the default footer column content.', 'netplus-circuit' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="np-footer__title">',
			'after_title'   => '</h4>',
		) );
	}
}
add_action( 'widgets_init', 'np_circuit_widgets_init' );

/**
 * Custom excerpt length and "more".
 */
function np_circuit_excerpt_length( $length ) {
	return is_admin() ? $length : 26;
}
add_filter( 'excerpt_length', 'np_circuit_excerpt_length' );

function np_circuit_excerpt_more( $more ) {
	return is_admin() ? $more : '…';
}
add_filter( 'excerpt_more', 'np_circuit_excerpt_more' );

/**
 * Add a caret + toggle class to menu items that have children (for mobile drawer).
 */
function np_circuit_nav_submenu_css_class( $classes, $item, $args, $depth ) {
	return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'np_circuit_nav_submenu_css_class', 10, 4 );

/**
 * Body classes for layout state.
 */
function np_circuit_body_classes( $classes ) {
	$classes[] = 'np-theme-circuit';
	if ( np_opt_on( 'np_sticky_nav', true ) ) {
		$classes[] = 'np-sticky-enabled';
	}
	if ( np_opt_on( 'np_animations', true ) ) {
		$classes[] = 'np-anim';
	}
	if ( np_opt_on( 'np_anim_cards', true ) ) {
		$classes[] = 'np-anim-cards';
	}
	if ( np_opt_on( 'np_anim_buttons', true ) ) {
		$classes[] = 'np-anim-buttons';
	}
	if ( is_woocommerce() ) {
		$classes[] = 'np-woo';
	}
	if ( ! is_active_sidebar( 'blog-sidebar' ) && ( is_singular( 'post' ) || is_archive() || is_search() ) ) {
		$classes[] = 'np-no-blog-sidebar';
	}
	$header_mode = get_theme_mod( 'np_header_mode', 'coded' );
	if ( 'elementor' === $header_mode ) {
		$classes[] = 'np-header-elementor';
	}
	return $classes;
}
add_filter( 'body_class', 'np_circuit_body_classes' );

/**
 * Pingback header.
 */
function np_circuit_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'np_circuit_pingback_header' );
