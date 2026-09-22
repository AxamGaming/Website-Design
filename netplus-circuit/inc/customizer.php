<?php
/**
 * Theme Customizer options.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function np_circuit_customize_register( $wp_customize ) {

	/* ===== Panels & sections ============================================= */

	$wp_customize->add_panel( 'np_header_panel', array(
		'title'    => __( 'Header & Top Bar', 'netplus-circuit' ),
		'priority' => 20,
	) );
	$wp_customize->add_section( 'np_topbar_section', array(
		'title' => __( 'Top Bar', 'netplus-circuit' ),
		'panel' => 'np_header_panel',
	) );
	$wp_customize->add_section( 'np_header_section', array(
		'title' => __( 'Header Behaviour', 'netplus-circuit' ),
		'panel' => 'np_header_panel',
	) );

	$wp_customize->add_panel( 'np_home_panel', array(
		'title'    => __( 'Home Page Sections', 'netplus-circuit' ),
		'priority' => 21,
	) );
	$wp_customize->add_section( 'np_hero_section', array(
		'title' => __( 'Hero Slider (3 slides)', 'netplus-circuit' ),
		'panel' => 'np_home_panel',
	) );
	$wp_customize->add_section( 'np_home_blocks_section', array(
		'title' => __( 'Section Switches & Titles', 'netplus-circuit' ),
		'panel' => 'np_home_panel',
	) );
	$wp_customize->add_section( 'np_promo_section', array(
		'title' => __( 'Promo Banners (2-up)', 'netplus-circuit' ),
		'panel' => 'np_home_panel',
	) );
	$wp_customize->add_section( 'np_deal_section', array(
		'title' => __( 'Deal of the Week', 'netplus-circuit' ),
		'panel' => 'np_home_panel',
	) );

	$wp_customize->add_panel( 'np_colors_panel', array(
		'title'    => __( 'Theme Colors', 'netplus-circuit' ),
		'priority' => 22,
	) );
	$wp_customize->add_section( 'np_colors_section', array(
		'title' => __( 'Brand Colors', 'netplus-circuit' ),
		'panel' => 'np_colors_panel',
	) );

	$wp_customize->add_section( 'np_typography_section', array(
		'title'    => __( 'Typography (4 font roles)', 'netplus-circuit' ),
		'priority' => 22,
	) );

	$wp_customize->add_section( 'np_motion_section', array(
		'title'    => __( 'Motion & Animations', 'netplus-circuit' ),
		'priority' => 23,
	) );

	$wp_customize->add_section( 'np_checkout_rules', array(
		'title'       => __( 'Checkout Rules', 'netplus-circuit' ),
		'description' => __( 'Control who may complete a purchase and how payments behave.', 'netplus-circuit' ),
		'priority'    => 24,
	) );

	$wp_customize->add_section( 'np_float_section', array(
		'title'    => __( 'WhatsApp & Floating Buttons', 'netplus-circuit' ),
		'priority' => 23,
	) );

	$wp_customize->add_panel( 'np_footer_panel', array(
		'title'    => __( 'Footer & Contact', 'netplus-circuit' ),
		'priority' => 24,
	) );
	$wp_customize->add_section( 'np_footer_section', array(
		'title' => __( 'Footer Content', 'netplus-circuit' ),
		'panel' => 'np_footer_panel',
	) );
	$wp_customize->add_section( 'np_social_section', array(
		'title' => __( 'Social Links', 'netplus-circuit' ),
		'panel' => 'np_footer_panel',
	) );

	$wp_customize->add_section( 'np_security_section', array(
		'title'       => __( 'Security & Anti-Spam', 'netplus-circuit' ),
		'description' => __( 'Theme-level hardening. Combine with your plugins: NinjaFirewall, Limit Login Attempts Reloaded and WPS Hide Login. See INSTALL.md for the full checklist.', 'netplus-circuit' ),
		'priority'    => 25,
	) );

	$wp_customize->add_section( 'np_woo_section', array(
		'title'    => __( 'WooCommerce Display', 'netplus-circuit' ),
		'priority' => 26,
	) );

	/* ===== Helper to add settings fast ==================================== */

	$np_add = function ( $id, $label, $section, $type = 'text', $default = '', $extra = array() ) use ( $wp_customize ) {
		$wp_customize->add_setting( $id, array_merge( array(
			'default'           => $default,
			'sanitize_callback' => 'np_sanitize_by_type',
			'transport'         => 'refresh',
			'type'              => 'theme_mod',
		), $extra ) );
		$control_args = array_merge( array(
			'label'   => $label,
			'section' => $section,
			'type'    => $type,
		), isset( $extra['control'] ) ? $extra['control'] : array() );
		unset( $control_args['choices_fallback'] );
		$wp_customize->add_control( $id . '_control', $control_args );
	};

	/* ===== Top bar ========================================================= */

	$np_add( 'np_topbar_show', __( 'Show top bar', 'netplus-circuit' ), 'np_topbar_section', 'checkbox', true );
	$np_add( 'np_phone', __( 'Hotline number', 'netplus-circuit' ), 'np_topbar_section', 'text', '+94 77 123 4567' );
	$np_add( 'np_email', __( 'Business email', 'netplus-circuit' ), 'np_topbar_section', 'text', 'info@netpluscomputers.lk' );
	$np_add( 'np_topbar_hours', __( 'Opening hours text', 'netplus-circuit' ), 'np_topbar_section', 'text', 'Mon–Sat 9:00 AM – 6:30 PM' );
	$np_add( 'np_announce_text', __( 'Announcement bar text (leave empty to hide)', 'netplus-circuit' ), 'np_topbar_section', 'textarea', '🚚 Island-wide delivery via registered courier · Cash on Delivery available · Genuine parts with warranty' );

	/* ===== Header behaviour ================================================ */

	$np_add( 'np_header_mode', __( 'Header/Footer source', 'netplus-circuit' ), 'np_header_section', 'select', 'coded', array(
		'control' => array(
			'choices' => array(
				'coded'     => __( 'Built-in coded header (recommended)', 'netplus-circuit' ),
				'elementor' => __( 'Built with Elementor / UAE Header-Footer', 'netplus-circuit' ),
			),
		),
	) );
	$np_add( 'np_sticky_nav', __( 'Sticky navigation on scroll', 'netplus-circuit' ), 'np_header_section', 'checkbox', true );
	$np_add( 'np_header_search', __( 'Show header search bar', 'netplus-circuit' ), 'np_header_section', 'checkbox', true );
	$np_add( 'np_tagline_show', __( 'Show tagline under site title (text logo only)', 'netplus-circuit' ), 'np_header_section', 'checkbox', false );

	/* ===== Hero slider ===================================================== */

	for ( $i = 1; $i <= 3; $i++ ) {
		$np_add( 'np_hero_' . $i . '_show', sprintf( /* translators: %d slide number */ __( 'Slide %d — show', 'netplus-circuit' ), $i ), 'np_hero_section', 'checkbox', false );
		$np_add( 'np_hero_' . $i . '_eyebrow', sprintf( __( 'Slide %d — eyebrow (small label)', 'netplus-circuit' ), $i ), 'np_hero_section', 'text', 1 === $i ? __( 'Genuine Laptop & PC Parts', 'netplus-circuit' ) : '' );
		$np_add( 'np_hero_' . $i . '_title', sprintf( __( 'Slide %d — title (use *stars* to highlight a word)', 'netplus-circuit' ), $i ), 'np_hero_section', 'text',
			1 === $i ? __( 'Every part your *computer* needs', 'netplus-circuit' ) : '' );
		$np_add( 'np_hero_' . $i . '_sub', sprintf( __( 'Slide %d — subtitle', 'netplus-circuit' ), $i ), 'np_hero_section', 'textarea',
			1 === $i ? __( 'Keyboards, batteries, screens, fans, motherboards & more — tested, warrantied and delivered anywhere in Sri Lanka.', 'netplus-circuit' ) : '' );
		$np_add( 'np_hero_' . $i . '_btn', sprintf( __( 'Slide %d — button label', 'netplus-circuit' ), $i ), 'np_hero_section', 'text', 1 === $i ? __( 'Shop Now', 'netplus-circuit' ) : '' );
		$np_add( 'np_hero_' . $i . '_link', sprintf( __( 'Slide %d — button link', 'netplus-circuit' ), $i ), 'np_hero_section', 'text', 1 === $i ? '/shop/' : '' );
		$np_add( 'np_hero_' . $i . '_img', sprintf( __( 'Slide %d — background image (optional, gradient used if empty)', 'netplus-circuit' ), $i ), 'np_hero_section', 'image', '' );
	}

	/* ===== Home section switches =========================================== */

	$np_add( 'np_show_trust', __( 'Show trust bar', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_show_cats', __( 'Show category tiles', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_cats_title', __( 'Category section title', 'netplus-circuit' ), 'np_home_blocks_section', 'text', __( 'Shop by Category', 'netplus-circuit' ) );
	$np_add( 'np_cats_limit', __( 'Number of categories', 'netplus-circuit' ), 'np_home_blocks_section', 'number', 8, array( 'control' => array( 'input_attrs' => array( 'min' => 4, 'max' => 16 ) ) ) );
	$np_add( 'np_show_products', __( 'Show product tabs section', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_products_title', __( 'Products section title', 'netplus-circuit' ), 'np_home_blocks_section', 'text', __( 'Trending Products', 'netplus-circuit' ) );
	$np_add( 'np_products_per_tab', __( 'Products per tab', 'netplus-circuit' ), 'np_home_blocks_section', 'number', 8, array( 'control' => array( 'input_attrs' => array( 'min' => 4, 'max' => 20 ) ) ) );
	$np_add( 'np_show_promos', __( 'Show promo banners', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_show_deal', __( 'Show deal of the week', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_show_blog', __( 'Show latest blog posts', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_blog_title', __( 'Blog section title', 'netplus-circuit' ), 'np_home_blocks_section', 'text', __( 'Tech Updates & Guides', 'netplus-circuit' ) );
	$np_add( 'np_show_newsletter', __( 'Show newsletter strip', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_show_wa_strip', __( 'Show WhatsApp support strip', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_carousels', __( 'Show product rows as swipeable carousels (like netpluscomputers.lk) — off = static grid', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_show_promo_strip', __( 'Show discount promo strip', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_promo_strip_text', __( 'Promo strip text', 'netplus-circuit' ), 'np_home_blocks_section', 'text', __( 'Discount up to 30% for first purchase! Use code NETPLUS30 at checkout', 'netplus-circuit' ) );
	$np_add( 'np_promo_strip_link', __( 'Promo strip link', 'netplus-circuit' ), 'np_home_blocks_section', 'text', '/shop/' );
	$np_add( 'np_show_faq', __( 'Show FAQ accordion', 'netplus-circuit' ), 'np_home_blocks_section', 'checkbox', true );
	$np_add( 'np_faq_items', __( 'FAQ items (one per line: Question | Answer)', 'netplus-circuit' ), 'np_home_blocks_section', 'textarea',
		"How long does delivery take? | Colombo district 1-2 working days, other districts 2-4 working days via registered courier.\nDo you offer Cash on Delivery? | Yes — COD is available island-wide on all orders.\nAre your parts genuine? | Every part is original or OEM-grade, tested before dispatch and covered by a 6-month replacement warranty.\nCan I return a part if it doesn't fit? | Unused items in original packaging can be returned within 7 days; wrong-part claims via WhatsApp are handled same-day.\nHow do I check compatibility? | Send your laptop model number or a photo of the old part on WhatsApp — free compatibility check." );

	/* ===== Promo banners =================================================== */

	for ( $i = 1; $i <= 3; $i++ ) {
		$np_add( 'np_promo_' . $i . '_eyebrow', sprintf( __( 'Banner %d — eyebrow', 'netplus-circuit' ), $i ), 'np_promo_section', 'text',
			1 === $i ? __( 'Custom Builds', 'netplus-circuit' ) : ( 2 === $i ? __( 'Laptop Spare Parts', 'netplus-circuit' ) : __( 'Accessories', 'netplus-circuit' ) ) );
		$np_add( 'np_promo_' . $i . '_title', sprintf( __( 'Banner %d — title', 'netplus-circuit' ), $i ), 'np_promo_section', 'text',
			1 === $i ? __( 'Build Your Dream PC', 'netplus-circuit' ) : ( 2 === $i ? __( 'Keyboards · Batteries · Screens', 'netplus-circuit' ) : __( 'Webcams · Speakers · Storage', 'netplus-circuit' ) ) );
		$np_add( 'np_promo_' . $i . '_sub', sprintf( __( 'Banner %d — subtitle', 'netplus-circuit' ), $i ), 'np_promo_section', 'text',
			1 === $i ? __( 'Tell us your budget — we assemble, test & deliver.', 'netplus-circuit' ) : ( 2 === $i ? __( 'Original Dell, HP, Lenovo & Asus parts in stock.', 'netplus-circuit' ) : __( 'Everything to upgrade your setup in one place.', 'netplus-circuit' ) ) );
		$np_add( 'np_promo_' . $i . '_btn', sprintf( __( 'Banner %d — button label', 'netplus-circuit' ), $i ), 'np_promo_section', 'text', __( 'Explore', 'netplus-circuit' ) );
		$np_add( 'np_promo_' . $i . '_link', sprintf( __( 'Banner %d — button link', 'netplus-circuit' ), $i ), 'np_promo_section', 'text', '/shop/' );
		$np_add( 'np_promo_' . $i . '_img', sprintf( __( 'Banner %d — image', 'netplus-circuit' ), $i ), 'np_promo_section', 'image', '' );
		$np_add( 'np_promo_' . $i . '_style', sprintf( __( 'Banner %d — tint', 'netplus-circuit' ), $i ), 'np_promo_section', 'select',
			1 === $i ? 'accent' : ( 2 === $i ? 'hot' : 'dark' ), array(
			'control' => array(
				'choices' => array(
					'dark'   => __( 'Graphite dark', 'netplus-circuit' ),
					'accent' => __( 'Brand accent', 'netplus-circuit' ),
					'hot'    => __( 'Deals orange', 'netplus-circuit' ),
				),
			),
		) );
	}

	/* ===== Deal of the week ================================================ */

	$np_add( 'np_deal_product', __( 'Product ID on deal (0 = auto-pick biggest discount)', 'netplus-circuit' ), 'np_deal_section', 'number', 0, array( 'control' => array( 'input_attrs' => array( 'min' => 0 ) ) ) );
	$np_add( 'np_deal_ends', __( 'Countdown ends at (YYYY-MM-DD HH:MM)', 'netplus-circuit' ), 'np_deal_section', 'text', '' );
	$np_add( 'np_deal_title', __( 'Deal title override (empty = product name)', 'netplus-circuit' ), 'np_deal_section', 'text', '' );
	$np_add( 'np_deal_note', __( 'Note under price (e.g. delivery info)', 'netplus-circuit' ), 'np_deal_section', 'text', __( 'Free delivery within Colombo · Island-wide courier Rs 350', 'netplus-circuit' ) );

	/* ===== Colors ========================================================== */

	$np_color_fields = array(
		'np_color_accent'      => array( __( 'Primary accent (buttons, links)', 'netplus-circuit' ), '#d90429' ),
		'np_color_accent_dark' => array( __( 'Accent (dark/hover)', 'netplus-circuit' ), '#a80321' ),
		'np_color_hot'         => array( __( 'Deals / flash-sale / checkout color', 'netplus-circuit' ), '#f77f00' ),
		'np_color_ink'         => array( __( 'Dark surface (nav bar, footer)', 'netplus-circuit' ), '#1b1f27' ),
		'np_color_sale'        => array( __( 'Sale price & badges', 'netplus-circuit' ), '#d90429' ),
		'np_color_success'     => array( __( 'In-stock / success / NEW badge', 'netplus-circuit' ), '#17a558' ),
		'np_color_bg'          => array( __( 'Page background', 'netplus-circuit' ), '#f6f6f4' ),
	);
	foreach ( $np_color_fields as $np_cid => $np_cfield ) {
		$wp_customize->add_setting( $np_cid, array( 'default' => $np_cfield[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $np_cid . '_control', array(
			'label'   => $np_cfield[0],
			'section' => 'np_colors_section',
		) ) );
	}

	/* ===== Typography ====================================================== */

	$np_font_choices = array(
		'Chakra Petch'  => 'Chakra Petch (techy display)',
		'Rajdhani'      => 'Rajdhani (condensed tech UI)',
		'Sora'          => 'Sora (geometric modern)',
		'Orbitron'      => 'Orbitron (futuristic)',
		'Exo 2'         => 'Exo 2 (sci-fi rounded)',
		'Space Grotesk' => 'Space Grotesk (quirky modern)',
		'Montserrat'    => 'Montserrat (clean corporate)',
		'Poppins'       => 'Poppins (friendly geometric)',
		'Inter'         => 'Inter (neutral UI)',
		'Roboto'        => 'Roboto (classic)',
		'Nunito'        => 'Nunito (rounded soft)',
		'Manrope'       => 'Manrope (modern grotesk)',
		'JetBrains Mono' => 'JetBrains Mono (code/mono)',
		'custom'        => '— Custom Google Font (type below) —',
	);

	$np_font_roles = array(
		'np_font_display' => array( __( 'Display font (hero titles, section titles, prices, countdown)', 'netplus-circuit' ), 'Chakra Petch' ),
		'np_font_head'    => array( __( 'UI font (buttons, badges, nav, card titles)', 'netplus-circuit' ), 'Rajdhani' ),
		'np_font_body'    => array( __( 'Body font (paragraphs, forms)', 'netplus-circuit' ), 'Inter' ),
		'np_font_mono'    => array( __( 'Mono font (SKUs, spec values, order numbers)', 'netplus-circuit' ), 'JetBrains Mono' ),
	);
	foreach ( $np_font_roles as $np_fid => $np_ffield ) {
		$wp_customize->add_setting( $np_fid, array( 'default' => $np_ffield[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $np_fid . '_control', array(
			'label'   => $np_ffield[0],
			'section' => 'np_typography_section',
			'type'    => 'select',
			'choices' => $np_font_choices,
		) );
		$wp_customize->add_setting( $np_fid . '_custom', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $np_fid . '_custom_control', array(
			'label'       => __( 'Custom font family name (used only when "Custom" selected above)', 'netplus-circuit' ),
			'section'     => 'np_typography_section',
			'type'        => 'text',
			'description' => __( 'Any Google Fonts family, e.g. "Barlow Condensed".', 'netplus-circuit' ),
		) );
	}
	$wp_customize->add_setting( 'np_body_size', array( 'default' => 15, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'np_body_size_control', array(
		'label'       => __( 'Base body size (px)', 'netplus-circuit' ),
		'section'     => 'np_typography_section',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 13, 'max' => 19 ),
	) );

	/* ===== Motion ========================================================== */

	$np_add( 'np_animations', __( 'Enable scroll-reveal & micro animations site-wide', 'netplus-circuit' ), 'np_motion_section', 'checkbox', true );
	$np_add( 'np_anim_cards', __( 'Card hover lift + image zoom', 'netplus-circuit' ), 'np_motion_section', 'checkbox', true );
	$np_add( 'np_anim_buttons', __( 'Button shine sweep effect', 'netplus-circuit' ), 'np_motion_section', 'checkbox', true );
	$np_add( 'np_hero_autoplay', __( 'Hero slider autoplay delay (seconds, 0 = manual)', 'netplus-circuit' ), 'np_motion_section', 'number', 6, array( 'control' => array( 'input_attrs' => array( 'min' => 0, 'max' => 20 ) ) ) );

	/* ===== Checkout rules =================================================== */

	$np_add( 'np_require_login_checkout', __( 'Require sign-in to checkout (guests can still add to cart)', 'netplus-circuit' ), 'np_checkout_rules', 'checkbox', true );
	$np_add( 'np_login_gate_message', __( 'Message shown to guests on the cart page', 'netplus-circuit' ), 'np_checkout_rules', 'textarea', __( 'Please sign in (or create a free account) to proceed to checkout. You can keep adding items to your cart as a guest.', 'netplus-circuit' ) );

	/* ===== WhatsApp & floats =============================================== */

	$np_add( 'np_whatsapp_number', __( 'WhatsApp number (with country code, digits only e.g. 94771234567)', 'netplus-circuit' ), 'np_float_section', 'text', '' );
	$np_add( 'np_whatsapp_default_message', __( 'Default pre-filled message', 'netplus-circuit' ), 'np_float_section', 'text', __( 'Hi NetPlus! I need help with a computer part.', 'netplus-circuit' ) );
	$np_add( 'np_wa_float_show', __( 'Show floating WhatsApp button (all pages)', 'netplus-circuit' ), 'np_float_section', 'checkbox', true );
	$np_add( 'np_wa_bubble_text', __( 'Greeting bubble text (empty = no bubble)', 'netplus-circuit' ), 'np_float_section', 'text', __( 'Hello 👋 Need a part? Chat with us!', 'netplus-circuit' ) );
	$np_add( 'np_wa_bubble_delay', __( 'Bubble appears after (seconds)', 'netplus-circuit' ), 'np_float_section', 'number', 5, array( 'control' => array( 'input_attrs' => array( 'min' => 0, 'max' => 60 ) ) ) );
	$np_add( 'np_wa_product_btn', __( 'Show "Order via WhatsApp" button on product pages', 'netplus-circuit' ), 'np_float_section', 'checkbox', true );
	$np_add( 'np_show_totop', __( 'Show back-to-top button', 'netplus-circuit' ), 'np_float_section', 'checkbox', true );
	$np_add( 'np_cookie_show', __( 'Show cookie notice bar', 'netplus-circuit' ), 'np_float_section', 'checkbox', true );
	$np_add( 'np_cookie_text', __( 'Cookie notice text', 'netplus-circuit' ), 'np_float_section', 'textarea', __( 'We use cookies to improve your shopping experience and keep your cart safe. By continuing to browse you agree to our use of cookies.', 'netplus-circuit' ) );

	/* ===== Footer ========================================================== */

	$np_add( 'np_footer_about', __( 'Footer about text', 'netplus-circuit' ), 'np_footer_section', 'textarea', __( 'NetPlus Computers (Pvt) Ltd — your trusted source for genuine laptop & desktop spare parts, accessories and tech in Sri Lanka. Tested parts, honest prices, island-wide delivery.', 'netplus-circuit' ) );
	$np_add( 'np_address', __( 'Store address', 'netplus-circuit' ), 'np_footer_section', 'textarea', 'No. 123, Main Street, Colombo 03, Sri Lanka' );
	$np_add( 'np_map_embed', __( 'Google Maps embed URL (iframe src)', 'netplus-circuit' ), 'np_footer_section', 'text', '' );
	$np_add( 'np_footer_payment_note', __( 'Extra payment chips (comma separated, optional — enabled gateways are listed automatically)', 'netplus-circuit' ), 'np_footer_section', 'text', '' );
	$np_add( 'np_copyright', __( 'Copyright line (empty = auto)', 'netplus-circuit' ), 'np_footer_section', 'text', '' );

	/* ===== Social ========================================================== */

	$np_add( 'np_social_fb', __( 'Facebook URL', 'netplus-circuit' ), 'np_social_section', 'url', '' );
	$np_add( 'np_social_ig', __( 'Instagram URL', 'netplus-circuit' ), 'np_social_section', 'url', '' );
	$np_add( 'np_social_yt', __( 'YouTube URL', 'netplus-circuit' ), 'np_social_section', 'url', '' );
	$np_add( 'np_social_tiktok', __( 'TikTok URL', 'netplus-circuit' ), 'np_social_section', 'url', '' );

	/* ===== Security ======================================================== */

	$np_add( 'np_sec_hide_version', __( 'Hide WordPress version everywhere', 'netplus-circuit' ), 'np_security_section', 'checkbox', true );
	$np_add( 'np_sec_disable_xmlrpc', __( 'Disable XML-RPC (blocks common bot attack vector)', 'netplus-circuit' ), 'np_security_section', 'checkbox', true );
	$np_add( 'np_sec_block_user_enum', __( 'Block user enumeration (REST API + ?author= scans)', 'netplus-circuit' ), 'np_security_section', 'checkbox', true );
	$np_add( 'np_sec_comment_honeypot', __( 'Comment anti-spam (honeypot + timing trap)', 'netplus-circuit' ), 'np_security_section', 'checkbox', true );
	$np_add( 'np_sec_checkout_honeypot', __( 'Checkout anti-bot (honeypot + timing trap + rate limit)', 'netplus-circuit' ), 'np_security_section', 'checkbox', true );
	$np_add( 'np_sec_headers', __( 'Send security headers (X-Frame-Options, X-Content-Type-Options, Referrer-Policy)', 'netplus-circuit' ), 'np_security_section', 'checkbox', true );

	/* ===== WooCommerce display ============================================= */

	$np_add( 'np_woo_trim_zeros', __( 'Hide ".00" on whole-number LKR prices (Rs 1,500 instead of Rs 1,500.00)', 'netplus-circuit' ), 'np_woo_section', 'checkbox', true );
	$np_add( 'np_woo_columns', __( 'Products per row (shop archive, desktop)', 'netplus-circuit' ), 'np_woo_section', 'number', 4, array( 'control' => array( 'input_attrs' => array( 'min' => 3, 'max' => 6 ) ) ) );
	$np_add( 'np_woo_related_count', __( 'Related products count', 'netplus-circuit' ), 'np_woo_section', 'number', 4, array( 'control' => array( 'input_attrs' => array( 'min' => 2, 'max' => 8 ) ) ) );
	$np_add( 'np_woo_new_badge_days', __( 'Show "NEW" badge for products younger than (days, 0 = off)', 'netplus-circuit' ), 'np_woo_section', 'number', 30, array( 'control' => array( 'input_attrs' => array( 'min' => 0, 'max' => 120 ) ) ) );
	$np_add( 'np_delivery_tab_text', __( '"Delivery & Warranty" tab content on product pages', 'netplus-circuit' ), 'np_woo_section', 'textarea', "🚚 Colombo district: 1–2 working days (courier Rs 350)\n📦 Other districts: 2–4 working days (registered courier / SL Post)\n💵 Cash on Delivery available island-wide\n🛡️ All parts carry a replacement warranty — see product description for terms.\n↩️ 7-day return policy on unused items in original packaging." );
}
add_action( 'customize_register', 'np_circuit_customize_register' );

/**
 * Sanitize by control type.
 *
 * @param mixed $value Value.
 * @return mixed
 */
function np_sanitize_by_type( $value ) {
	$customize = '';
	// Determine the setting context from the current filter via debug backtrace is unreliable;
	// instead sanitize defensively based on value type using the setting id passed through.
	return np_sanitize_guess( $value );
}

/**
 * Defensive sanitizer used for text-ish values.
 *
 * @param mixed $value Value.
 * @return mixed
 */
function np_sanitize_guess( $value ) {
	if ( is_bool( $value ) ) {
		return (bool) $value;
	}
	return sanitize_textarea_field( wp_unslash( (string) $value ) );
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Value.
 * @return bool
 */
function np_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Register checkbox-specific sanitizers after settings exist.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function np_circuit_fix_checkbox_sanitizers( $wp_customize ) {
	foreach ( $wp_customize->settings() as $id => $setting ) {
		$control = $wp_customize->get_control( $id . '_control' );
		if ( $control && isset( $control->type ) && in_array( $control->type, array( 'checkbox' ), true ) ) {
			$setting->sanitize_callback    = 'np_sanitize_checkbox';
			$setting->sanitize_js_callback = 'absint';
		}
		if ( $control && isset( $control->type ) && 'url' === $control->type ) {
			$setting->sanitize_callback = 'esc_url_raw';
		}
		if ( $control && isset( $control->type ) && 'number' === $control->type ) {
			$setting->sanitize_callback = 'absint';
		}
		if ( $control && isset( $control->type ) && 'email' === $control->type ) {
			$setting->sanitize_callback = 'sanitize_email';
		}
	}
}
add_action( 'customize_register', 'np_circuit_fix_checkbox_sanitizers', 20 );

/**
 * Live preview bindings.
 */
function np_circuit_customize_preview_js() {
	wp_enqueue_script( 'np-circuit-customizer', NP_CIRCUIT_URI . '/assets/js/customizer-preview.js', array( 'customize-preview' ), NP_CIRCUIT_VERSION, true );
}
add_action( 'customize_preview_init', 'np_circuit_customize_preview_js' );

/**
 * Helpers used by templates to read options with defaults.
 */
if ( ! function_exists( 'np_opt' ) ) :
	/**
	 * Get a theme mod with fallback default.
	 *
	 * @param string $key     Mod key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function np_opt( $key, $default = '' ) {
		return get_theme_mod( $key, $default );
	}
endif;

if ( ! function_exists( 'np_opt_on' ) ) :
	/**
	 * Truthy check for checkbox mods (defaults to ON when unset).
	 *
	 * @param string $key     Mod key.
	 * @param bool   $default Default state.
	 * @return bool
	 */
	function np_opt_on( $key, $default = true ) {
		$raw = get_theme_mod( $key, null );
		if ( null === $raw || '' === $raw ) {
			return $default;
		}
		return (bool) $raw;
	}
endif;
