<?php
/**
 * Theme header: announcement bar, top bar, main header, nav bar, mobile drawer.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">

	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'netplus-circuit' ); ?></a>

<?php
// In Elementor/UAE mode the theme outputs nothing here — the builder injects its own header.
$np_elementor_header = false;
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) {
	$np_elementor_header = true;
}

if ( ! np_header_is_elementor_mode() && ! $np_elementor_header ) :

	$np_show_topbar  = np_opt_on( 'np_topbar_show', true );
	$np_phone        = get_theme_mod( 'np_phone', '' );
	$np_email        = get_theme_mod( 'np_email', '' );
	$np_hours        = get_theme_mod( 'np_topbar_hours', '' );
	$np_announce     = get_theme_mod( 'np_announce_text', '' );
	$np_wa_link      = np_whatsapp_link();
	$np_myaccount    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
	$np_shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	?>

	<?php if ( $np_announce ) : ?>
		<div class="np-topbar__announce"><?php echo wp_kses_post( $np_announce ); ?></div>
	<?php endif; ?>

	<?php if ( $np_show_topbar ) : ?>
		<div class="np-topbar">
			<div class="np-container np-topbar__inner">
				<div class="np-topbar__left">
					<?php if ( $np_phone ) : ?>
						<span class="np-topbar__item">
							<?php echo np_icon( 'phone', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $np_phone ) ); ?>"><?php echo esc_html( $np_phone ); ?></a>
						</span>
					<?php endif; ?>
					<?php if ( $np_email ) : ?>
						<span class="np-topbar__item np-topbar__item--email">
							<?php echo np_icon( 'mail', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<a href="mailto:<?php echo esc_attr( antispambot( $np_email ) ); ?>"><?php echo esc_html( antispambot( $np_email ) ); ?></a>
						</span>
					<?php endif; ?>
					<?php if ( $np_hours ) : ?>
						<span class="np-topbar__item np-topbar__item--email">
							<?php echo np_icon( 'clock', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( $np_hours ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="np-topbar__right">
					<?php if ( $np_wa_link ) : ?>
						<a class="np-topbar__item np-topbar__wa" href="<?php echo esc_url( $np_wa_link ); ?>" target="_blank" rel="noopener">
							<?php echo np_icon( 'whatsapp', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php esc_html_e( 'WhatsApp us', 'netplus-circuit' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( has_nav_menu( 'topbar' ) ) : ?>
						<?php
						wp_nav_menu( array(
							'theme_location' => 'topbar',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
						?>
					<?php endif; ?>
					<?php if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) : ?>
						<a class="np-topbar__item" href="<?php echo esc_url( $np_shop_url ); ?>">
							<?php echo np_icon( 'package', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php esc_html_e( 'Track / Shop', 'netplus-circuit' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<header id="masthead" class="np-header">
		<div class="np-container np-header__main">

			<button class="np-burger" id="np-burger" aria-label="<?php esc_attr_e( 'Open menu', 'netplus-circuit' ); ?>" aria-expanded="false" aria-controls="np-drawer">
				<?php echo np_icon( 'burger', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>

			<div class="np-logo">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="np-logo__text">Net<span>Plus</span></span>
						<?php if ( np_opt_on( 'np_tagline_show', false ) ) : ?>
							<span class="np-logo__tag"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
						<?php endif; ?>
					</a>
					<?php
				}
				?>
			</div>

			<?php if ( np_opt_on( 'np_header_search', true ) ) : ?>
				<div class="np-hsearch hnp-hsearch-hide-sm">
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>

			<div class="np-hactions">

				<?php if ( $np_wa_link ) : ?>
					<a class="np-haction np-haction--wa" href="<?php echo esc_url( $np_wa_link ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'netplus-circuit' ); ?>" title="<?php esc_attr_e( 'Chat on WhatsApp', 'netplus-circuit' ); ?>">
						<span class="np-haction__icon" style="color:var(--np-wa);"><?php echo np_icon( 'whatsapp', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</a>
				<?php endif; ?>

				<div class="np-acc-dd">
					<button class="np-haction" aria-expanded="false" aria-label="<?php esc_attr_e( 'My account', 'netplus-circuit' ); ?>">
						<span class="np-haction__icon"><?php echo np_icon( 'user', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="np-haction__texts">
							<?php if ( is_user_logged_in() ) : ?>
								<span class="np-haction__label"><?php esc_html_e( 'Welcome back', 'netplus-circuit' ); ?></span>
								<span class="np-haction__value"><?php echo esc_html( wp_get_current_user()->display_name ? explode( ' ', wp_get_current_user()->display_name )[0] : __( 'Account', 'netplus-circuit' ) ); ?></span>
							<?php else : ?>
								<span class="np-haction__label"><?php esc_html_e( 'Sign in', 'netplus-circuit' ); ?></span>
								<span class="np-haction__value"><?php esc_html_e( 'Account', 'netplus-circuit' ); ?></span>
							<?php endif; ?>
						</span>
					</button>
					<div class="np-acc-dd__menu">
						<?php if ( is_user_logged_in() ) : ?>
							<div class="np-acc-dd__head">
								<span class="np-acc-dd__name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
								<span class="np-role-chip"><?php echo esc_html( np_user_role_label() ); ?></span>
							</div>
							<?php if ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'edit_posts' ) ) : ?>
								<a href="<?php echo esc_url( admin_url() ); ?>"><?php echo np_icon( 'grid', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Dashboard', 'netplus-circuit' ); ?></a>
							<?php endif; ?>
							<a href="<?php echo esc_url( $np_myaccount ); ?>"><?php echo np_icon( 'user', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'My Account', 'netplus-circuit' ); ?></a>
							<?php if ( function_exists( 'wc_get_account_endpoint_url' ) ) : ?>
								<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php echo np_icon( 'package', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'My Orders', 'netplus-circuit' ); ?></a>
							<?php endif; ?>
							<div class="np-acc-dd__sep"></div>
							<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php echo np_icon( 'logout', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Log out', 'netplus-circuit' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( $np_myaccount ); ?>"><?php echo np_icon( 'user', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Login', 'netplus-circuit' ); ?></a>
							<a href="<?php echo esc_url( $np_myaccount ); ?>"><?php echo np_icon( 'check-c', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Create an account', 'netplus-circuit' ); ?></a>
							<div class="np-acc-dd__sep"></div>
							<a href="<?php echo esc_url( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'orders' ) : $np_myaccount ); ?>"><?php echo np_icon( 'package', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Track my order', 'netplus-circuit' ); ?></a>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<?php
					$np_cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
					$np_cart_total = WC()->cart ? WC()->cart->get_cart_subtotal() : '';
					?>
					<div class="np-cart-dd">
						<a class="np-haction np-haction--cart" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#' ); ?>" aria-label="<?php esc_attr_e( 'View cart', 'netplus-circuit' ); ?>">
							<span class="np-haction__icon"><?php echo np_icon( 'cart', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="np-haction__count np-cart-count<?php echo $np_cart_count ? '' : ' is-hidden'; ?>"><?php echo esc_html( $np_cart_count ); ?></span>
							<span class="np-haction__texts">
								<span class="np-haction__label"><?php esc_html_e( 'Your cart', 'netplus-circuit' ); ?></span>
								<span class="np-haction__value np-cart-subtotal"><?php echo wp_kses_post( $np_cart_total ); ?></span>
							</span>
						</a>
						<div class="np-cart-dd__panel">
							<?php np_cart_dropdown_content(); ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</header>

	<nav class="np-navbar" id="np-navbar" aria-label="<?php esc_attr_e( 'Primary navigation', 'netplus-circuit' ); ?>">
		<div class="np-container np-navbar__inner">

			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<div class="np-depts" id="np-depts">
					<button class="np-depts__toggle" aria-expanded="false" aria-controls="np-depts-panel">
						<span class="burger"><?php echo np_icon( 'grid', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php esc_html_e( 'All Departments', 'netplus-circuit' ); ?>
						<?php echo np_icon( 'chevron-d', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
					<div class="np-depts__panel" id="np-depts-panel">
						<?php
						$np_cats = get_terms( array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => false,
							'parent'     => 0,
							'number'     => 12,
							'orderby'    => 'name',
							'order'      => 'ASC',
						) );
						if ( ! is_wp_error( $np_cats ) && $np_cats ) {
							foreach ( $np_cats as $np_cat ) {
								$thumb_id = get_term_meta( $np_cat->term_id, 'thumbnail_id', true );
								$thumb    = $thumb_id ? wp_get_attachment_image( (int) $thumb_id, 'thumbnail', false, array( 'class' => 'np-dept-thumb', 'loading' => 'lazy' ) ) : '';
								echo '<a href="' . esc_url( get_term_link( $np_cat ) ) . '">';
								if ( $thumb ) {
									echo wp_kses_post( $thumb );
								} else {
									echo '<span class="np-dept-icon">' . np_icon( 'cpu', 18 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
								}
								echo '<span>' . esc_html( $np_cat->name ) . '</span>';
								echo '<span class="np-dept-count">' . esc_html( $np_cat->count ) . '</span>';
								echo '</a>';
							}
						}
						?>
						<a href="<?php echo esc_url( $np_shop_url ); ?>" style="color:var(--np-accent);font-weight:700;">
							<span class="np-dept-icon"><?php echo np_icon( 'arrow-r', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<?php esc_html_e( 'Browse all products', 'netplus-circuit' ); ?>
						</a>
					</div>
				</div>
			<?php endif; ?>

			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'np-nav',
					'depth'          => 3,
					'fallback_cb'    => 'np_circuit_default_menu',
				) );
			} else {
				np_circuit_default_menu();
			}
			?>

			<div class="np-navbar__right">
				<?php if ( $np_wa_link ) : ?>
					<a href="<?php echo esc_url( $np_wa_link ); ?>" target="_blank" rel="noopener">
						<?php echo np_icon( 'headset', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php esc_html_e( 'Support', 'netplus-circuit' ); ?>
					</a>
				<?php endif; ?>
				<?php
				$np_blog_id = (int) get_option( 'page_for_posts' );
				if ( $np_blog_id ) :
					?>
					<a href="<?php echo esc_url( get_permalink( $np_blog_id ) ); ?>">
						<?php echo np_icon( 'bolt', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php esc_html_e( 'Tech Blog', 'netplus-circuit' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</nav>
	<div class="np-nav-spacer" id="np-nav-spacer"></div>

	<!-- Mobile drawer -->
	<div class="np-drawer" id="np-drawer" aria-hidden="true">
		<div class="np-drawer__backdrop" data-np-drawer-close></div>
		<div class="np-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile menu', 'netplus-circuit' ); ?>">
			<div class="np-drawer__head">
				<strong style="font-family:var(--np-font-head);"><?php bloginfo( 'name' ); ?></strong>
				<button class="np-drawer__close" data-np-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'netplus-circuit' ); ?>">
					<?php echo np_icon( 'close', 18); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>
			<div class="np-drawer__body">
				<div class="np-drawer__search"><?php get_search_form(); ?></div>

				<?php
				$np_mobile_menu = has_nav_menu( 'primary' ) ? 'primary' : '';
				if ( $np_mobile_menu ) {
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'np-mnav',
						'depth'          => 3,
					) );
				} else {
					echo '<ul class="np-mnav">';
					echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'netplus-circuit' ) . '</a></li>';
					echo '<li><a href="' . esc_url( $np_shop_url ) . '">' . esc_html__( 'Shop', 'netplus-circuit' ) . '</a></li>';
					if ( $np_blog_id ) {
						echo '<li><a href="' . esc_url( get_permalink( $np_blog_id ) ) . '">' . esc_html__( 'Blog', 'netplus-circuit' ) . '</a></li>';
					}
					echo '</ul>';
				}
				?>

				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<ul class="np-mnav">
						<li><a href="<?php echo esc_url( $np_shop_url ); ?>"><?php echo np_icon( 'grid', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'All Departments', 'netplus-circuit' ); ?></a></li>
						<?php
						if ( ! empty( $np_cats ) && ! is_wp_error( $np_cats ) ) {
							foreach ( array_slice( (array) $np_cats, 0, 8 ) as $np_mcat ) {
								echo '<li><a href="' . esc_url( get_term_link( $np_mcat ) ) . '" style="font-weight:500;color:var(--np-muted);font-size:.88rem;">' . esc_html( $np_mcat->name ) . '</a></li>';
							}
						}
						?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="np-drawer__foot">
				<?php if ( is_user_logged_in() ) : ?>
					<a class="np-btn np-btn--primary np-btn--block np-btn--sm" href="<?php echo esc_url( $np_myaccount ); ?>"><?php esc_html_e( 'My Account', 'netplus-circuit' ); ?></a>
					<a class="np-btn np-btn--ghost np-btn--block np-btn--sm" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Log out', 'netplus-circuit' ); ?></a>
				<?php else : ?>
					<a class="np-btn np-btn--primary np-btn--block np-btn--sm" href="<?php echo esc_url( $np_myaccount ); ?>"><?php esc_html_e( 'Login / Register', 'netplus-circuit' ); ?></a>
				<?php endif; ?>
				<?php if ( $np_wa_link ) : ?>
					<a class="np-btn np-btn--wa np-btn--block np-btn--sm" href="<?php echo esc_url( $np_wa_link ); ?>" target="_blank" rel="noopener"><?php echo np_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'WhatsApp Us', 'netplus-circuit' ); ?></a>
				<?php endif; ?>
				<?php if ( $np_phone ) : ?>
					<a class="np-center np-muted" style="font-size:.84rem;" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $np_phone ) ); ?>"><?php echo esc_html( $np_phone ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php endif; // header mode ?>
