<?php
/**
 * Theme activation: roles, default pages, demo content hints, rewrite flush.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Runs once when the theme is activated.
 */
function np_circuit_after_switch_theme() {

	// 1) Create helper roles (works with User Role Editor — adjust there anytime).
	np_circuit_register_roles();

	// 2) Create essential WooCommerce pages if missing.
	np_circuit_create_pages();

	// 3) Ensure newsletter table exists.
	if ( function_exists( 'np_newsletter_install' ) ) {
		np_newsletter_install();
	}

	// 4) Protect uploads dir.
	if ( function_exists( 'np_protect_uploads_dir' ) ) {
		np_protect_uploads_dir();
	}

	// 5) Flush rewrite rules so /shop/, /product/ etc. resolve.
	flush_rewrite_rules();

	// 6) Flag first-run admin notice.
	set_transient( 'np_circuit_activation_notice', 1, 2 * WEEK_IN_SECONDS );
}
add_action( 'after_switch_theme', 'np_circuit_after_switch_theme' );

/**
 * Register store roles:
 *  - store_manager : full shop control, no WordPress settings access.
 *  - store_staff   : handle orders/products, limited rights.
 * (administrator + WooCommerce customer already exist.)
 */
function np_circuit_register_roles() {
	if ( ! get_role( 'store_manager' ) ) {
		add_role(
			'store_manager',
			__( 'Store Manager', 'netplus-circuit' ),
			array(
				'read'                   => true,
				'edit_posts'             => true,
				'edit_published_posts'   => true,
				'publish_posts'          => true,
				'delete_posts'           => true,
				'delete_published_posts' => true,
				'edit_others_posts'      => true,
				'read_private_posts'     => true,
				'upload_files'           => true,
				'manage_categories'      => true,
				'moderate_comments'      => true,
				// WooCommerce caps (mirror of shop_manager).
				'manage_woocommerce'     => true,
				'view_woocommerce_reports' => true,
				'edit_shop_orders'       => true,
				'edit_others_shop_orders' => true,
				'publish_shop_orders'    => true,
				'read_private_shop_orders' => true,
				'delete_shop_orders'     => true,
				'delete_others_shop_orders' => true,
				'edit_products'          => true,
				'edit_others_products'   => true,
				'publish_products'       => true,
				'read_private_products'  => true,
				'delete_products'        => true,
				'delete_others_products' => true,
				'edit_product_categories' => true,
				'manage_product_terms'   => true,
			)
		);
	}

	if ( ! get_role( 'store_staff' ) ) {
		add_role(
			'store_staff',
			__( 'Store Staff', 'netplus-circuit' ),
			array(
				'read'                 => true,
				'edit_posts'           => true,
				'publish_posts'        => true,
				'upload_files'         => true,
				'moderate_comments'    => true,
				'edit_products'        => true,
				'edit_published_products' => true,
				'publish_products'     => true,
				'read_private_products' => true,
				'edit_shop_orders'     => true,
				'read_private_shop_orders' => true,
				'view_woocommerce_reports' => true,
			)
		);
	}
}

/**
 * Create essential pages if they don't exist.
 */
function np_circuit_create_pages() {
	$pages = array(
		'about-us'  => array(
			'title'   => __( 'About Us', 'netplus-circuit' ),
			'content' => '<!-- wp:paragraph --><p>Welcome to <strong>NetPlus Computers</strong> — your trusted Sri Lankan store for genuine laptop and desktop spare parts.</p><!-- /wp:paragraph -->',
		),
		'contact'   => array(
			'title'   => __( 'Contact Us', 'netplus-circuit' ),
			'content' => '[np_contact_form]',
			'template' => 'page-templates/template-contact.php',
		),
		'faq'       => array(
			'title'   => __( 'FAQ', 'netplus-circuit' ),
			'content' => '<!-- wp:heading --><h2>Delivery</h2><!-- /wp:heading --><!-- wp:paragraph --><p>We deliver island-wide via registered courier. Colombo district orders usually arrive within 1–2 working days.</p><!-- /wp:paragraph -->',
		),
	);

	foreach ( $pages as $slug => $data ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$page_id = wp_insert_post( array(
			'post_title'   => $data['title'],
			'post_name'    => $slug,
			'post_content' => $data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
		if ( $page_id && ! is_wp_error( $page_id ) && ! empty( $data['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $data['template'] );
		}
	}

	// WooCommerce core pages (cart, checkout, my-account, shop) — install if absent.
	if ( class_exists( 'WooCommerce' ) ) {
		foreach ( array( 'shop', 'cart', 'checkout', 'myaccount' ) as $wc_page ) {
			$page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( $wc_page ) : -1;
			if ( $page_id && $page_id > 0 ) {
				continue;
			}
			$titles = array(
				'shop'      => __( 'Shop', 'netplus-circuit' ),
				'cart'      => __( 'Cart', 'netplus-circuit' ),
				'checkout'  => __( 'Checkout', 'netplus-circuit' ),
				'myaccount' => __( 'My Account', 'netplus-circuit' ),
			);
			$new_id = wp_insert_post( array(
				'post_title'  => $titles[ $wc_page ],
				'post_name'   => $wc_page,
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_content' => 'shop' === $wc_page ? '' : '[woocommerce_' . $wc_page . ']',
			) );
			if ( $new_id && ! is_wp_error( $new_id ) ) {
				update_option( 'woocommerce_' . $wc_page . '_page_id', $new_id );
			}
		}
	}

	// Blog page.
	if ( ! get_option( 'page_for_posts' ) ) {
		$blog_id = wp_insert_post( array(
			'post_title'  => __( 'Blog', 'netplus-circuit' ),
			'post_name'   => 'blog',
			'post_status' => 'publish',
			'post_type'   => 'page',
		) );
		if ( $blog_id && ! is_wp_error( $blog_id ) ) {
			update_option( 'page_for_posts', $blog_id );
		}
	}
}

/**
 * Admin notice after activation with a quick-start checklist.
 */
function np_circuit_activation_notice() {
	if ( ! get_transient( 'np_circuit_activation_notice' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$customizer = admin_url( 'customize.php' );
	$dismiss    = wp_nonce_url( admin_url( 'themes.php?np_dismiss_notice=1' ), 'np_dismiss_notice' );
	?>
	<div class="notice notice-success is-dismissible" style="position:relative;">
		<h3 style="margin-bottom:6px;">⚡ NetPlus Circuit is active!</h3>
		<p style="max-width:760px;">
			<strong>Quick start:</strong>
			1) Go to <a href="<?php echo esc_url( $customizer ); ?>">Customize → WhatsApp &amp; Floating Buttons</a> and enter your WhatsApp number (e.g. <code>94771234567</code>).&nbsp;
			2) Set your <em>Hotline, email &amp; address</em> in <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=np_footer_section' ) ); ?>">Customize → Footer &amp; Contact</a>.&nbsp;
			3) Upload your logo in <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ); ?>">Customize → Site Identity</a>.&nbsp;
			4) Assign menus under <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Appearance → Menus</a>.&nbsp;
			5) Read <code>INSTALL.md</code> inside the theme folder for the full setup + security checklist.
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $customizer ); ?>">Open Customizer</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-setup' ) ); ?>">WooCommerce setup</a>
			<a class="button" href="<?php echo esc_url( $dismiss ); ?>">Dismiss</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'np_circuit_activation_notice' );

/**
 * Handle dismiss.
 */
function np_circuit_dismiss_notice() {
	if ( isset( $_GET['np_dismiss_notice'] ) && check_admin_referer( 'np_dismiss_notice' ) ) {
		delete_transient( 'np_circuit_activation_notice' );
		wp_safe_redirect( admin_url( 'themes.php' ) );
		exit;
	}
}
add_action( 'admin_init', 'np_circuit_dismiss_notice' );
