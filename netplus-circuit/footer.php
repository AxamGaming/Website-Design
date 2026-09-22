<?php
/**
 * Theme footer: widget columns, bottom bar, floating WhatsApp, back-to-top,
 * cookie notice and toast container.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

$np_elementor_footer = false;
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) {
	$np_elementor_footer = true;
}

if ( ! np_header_is_elementor_mode() && ! $np_elementor_footer ) :

	$np_about    = get_theme_mod( 'np_footer_about', '' );
	$np_phone    = get_theme_mod( 'np_phone', '' );
	$np_email    = get_theme_mod( 'np_email', '' );
	$np_address  = get_theme_mod( 'np_address', '' );
	$np_wa_link  = np_whatsapp_link();
	$np_payments = get_theme_mod( 'np_footer_payment_note', '' );
	$np_socials  = array(
		'fb'     => get_theme_mod( 'np_social_fb', '' ),
		'ig'     => get_theme_mod( 'np_social_ig', '' ),
		'yt'     => get_theme_mod( 'np_social_yt', '' ),
		'tiktok' => get_theme_mod( 'np_social_tiktok', '' ),
	);
	$np_has_socials = (bool) array_filter( $np_socials );
	?>

	<footer id="colophon" class="np-footer">
		<div class="np-container">
			<div class="np-footer__top">

				<!-- Column 1: brand -->
				<div class="np-footer__col">
					<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
						<?php dynamic_sidebar( 'footer-1' ); ?>
					<?php else : ?>
						<div class="np-footer__brand">
							<?php if ( has_custom_logo() ) : ?>
								<?php the_custom_logo(); ?>
							<?php else : ?>
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
									<span class="np-logo__text" style="color:#fff;">Net<span>Plus</span></span>
								</a>
							<?php endif; ?>
							<?php if ( $np_about ) : ?>
								<p class="np-footer__about"><?php echo esc_html( $np_about ); ?></p>
							<?php endif; ?>
							<?php if ( $np_has_socials ) : ?>
								<div class="np-footer__social">
									<?php if ( $np_socials['fb'] ) : ?><a href="<?php echo esc_url( $np_socials['fb'] ); ?>" target="_blank" rel="noopener" aria-label="Facebook"><?php echo np_icon( 'fb', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><?php endif; ?>
									<?php if ( $np_socials['ig'] ) : ?><a href="<?php echo esc_url( $np_socials['ig'] ); ?>" target="_blank" rel="noopener" aria-label="Instagram"><?php echo np_icon( 'ig', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><?php endif; ?>
									<?php if ( $np_socials['yt'] ) : ?><a href="<?php echo esc_url( $np_socials['yt'] ); ?>" target="_blank" rel="noopener" aria-label="YouTube"><?php echo np_icon( 'yt', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><?php endif; ?>
									<?php if ( $np_socials['tiktok'] ) : ?><a href="<?php echo esc_url( $np_socials['tiktok'] ); ?>" target="_blank" rel="noopener" aria-label="TikTok"><?php echo np_icon( 'tiktok', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( $np_payments ) : ?>
								<div class="np-footer__pay">
									<?php
									// Live reflection of the payment methods enabled in WooCommerce.
									if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->payment_gateways() ) {
										$np_gws = WC()->payment_gateways()->get_available_payment_gateways();
										foreach ( $np_gws as $np_gw ) {
											echo '<span class="np-pay-chip">' . esc_html( $np_gw->get_title() ) . '</span>';
										}
									}
									foreach ( array_map( 'trim', explode( ',', $np_payments ) ) as $np_pay ) {
										if ( '' === $np_pay ) {
											continue;
										}
										echo '<span class="np-pay-chip" style="opacity:.75;">' . esc_html( $np_pay ) . '</span>';
									}
									?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Column 2: quick links -->
				<div class="np-footer__col">
					<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
						<?php dynamic_sidebar( 'footer-2' ); ?>
					<?php else : ?>
						<h4 class="np-footer__title"><?php esc_html_e( 'Quick Links', 'netplus-circuit' ); ?></h4>
						<?php
						if ( has_nav_menu( 'footer' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'np-footer__links',
								'depth'          => 1,
							) );
						} else {
							echo '<ul class="np-footer__links">';
							$np_footer_pages = get_pages( array( 'number' => 7, 'sort_column' => 'menu_order' ) );
							foreach ( (array) $np_footer_pages as $np_fp ) {
								echo '<li><a href="' . esc_url( get_permalink( $np_fp ) ) . '">' . esc_html( $np_fp->post_title ) . '</a></li>';
							}
							$np_blog_id2 = (int) get_option( 'page_for_posts' );
							if ( $np_blog_id2 ) {
								echo '<li><a href="' . esc_url( get_permalink( $np_blog_id2 ) ) . '">' . esc_html__( 'Tech Blog', 'netplus-circuit' ) . '</a></li>';
							}
							echo '</ul>';
						}
						?>
					<?php endif; ?>
				</div>

				<!-- Column 3: shop categories -->
				<div class="np-footer__col">
					<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
						<?php dynamic_sidebar( 'footer-3' ); ?>
					<?php else : ?>
						<h4 class="np-footer__title"><?php esc_html_e( 'Shop', 'netplus-circuit' ); ?></h4>
						<ul class="np-footer__links">
							<?php
							if ( class_exists( 'WooCommerce' ) ) {
								$np_footer_cats = get_terms( array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => true,
									'number'     => 7,
									'orderby'    => 'count',
									'order'      => 'DESC',
								) );
								if ( ! is_wp_error( $np_footer_cats ) ) {
									foreach ( $np_footer_cats as $np_fcat ) {
										echo '<li><a href="' . esc_url( get_term_link( $np_fcat ) ) . '">' . esc_html( $np_fcat->name ) . '</a></li>';
									}
								}
								echo '<li><a href="' . esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ) . '">' . esc_html__( 'All Products', 'netplus-circuit' ) . '</a></li>';
								echo '<li><a href="' . esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#' ) . '">' . esc_html__( 'My Cart', 'netplus-circuit' ) . '</a></li>';
							} else {
								echo '<li><span class="np-muted">' . esc_html__( 'Activate WooCommerce', 'netplus-circuit' ) . '</span></li>';
							}
							?>
						</ul>
					<?php endif; ?>
				</div>

				<!-- Column 4: contact -->
				<div class="np-footer__col">
					<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
						<?php dynamic_sidebar( 'footer-4' ); ?>
					<?php else : ?>
						<h4 class="np-footer__title"><?php esc_html_e( 'Get in Touch', 'netplus-circuit' ); ?></h4>
						<ul class="np-footer__contact">
							<?php if ( $np_address ) : ?>
								<li><?php echo np_icon( 'pin', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $np_address ); ?></span></li>
							<?php endif; ?>
							<?php if ( $np_phone ) : ?>
								<li><?php echo np_icon( 'phone', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $np_phone ) ); ?>"><?php echo esc_html( $np_phone ); ?></a></li>
							<?php endif; ?>
							<?php if ( $np_wa_link ) : ?>
								<li><?php echo np_icon( 'whatsapp', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="<?php echo esc_url( $np_wa_link ); ?>" target="_blank" rel="noopener" style="color:#6ee7a0;"><?php esc_html_e( 'Chat on WhatsApp', 'netplus-circuit' ); ?></a></li>
							<?php endif; ?>
							<?php if ( $np_email ) : ?>
								<li><?php echo np_icon( 'mail', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="mailto:<?php echo esc_attr( antispambot( $np_email ) ); ?>"><?php echo esc_html( antispambot( $np_email ) ); ?></a></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</div>

			</div>
		</div>

		<div class="np-footer__bottom">
			<div class="np-container np-footer__bottom-inner">
				<span>
					<?php
					$np_copyright = get_theme_mod( 'np_copyright', '' );
					if ( $np_copyright ) {
						echo wp_kses_post( $np_copyright );
					} else {
						printf(
							/* translators: 1: year, 2: site name. */
							esc_html__( '© %1$s %2$s. All rights reserved.', 'netplus-circuit' ),
							esc_html( gmdate( 'Y' ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
					}
					?>
				</span>
				<span class="np-footer__bottom-links">
					<?php
					$np_privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
					if ( $np_privacy_id ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $np_privacy_id ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'netplus-circuit' ); ?></a>
					<?php endif; ?>
					<?php
					$np_terms_page = get_page_by_path( 'terms-and-conditions' );
					if ( $np_terms_page ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $np_terms_page ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'netplus-circuit' ); ?></a>
					<?php endif; ?>
					<?php
					$np_refund_page = get_page_by_path( 'refund_returns' );
					if ( ! $np_refund_page ) {
						$np_refund_page = get_page_by_path( 'refunds' );
					}
					if ( $np_refund_page ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $np_refund_page ) ); ?>"><?php esc_html_e( 'Refund Policy', 'netplus-circuit' ); ?></a>
					<?php endif; ?>
					<span><?php esc_html_e( 'Built in Sri Lanka 🇱🇰', 'netplus-circuit' ); ?></span>
				</span>
			</div>
		</div>
	</footer>

<?php endif; // footer mode ?>

	<?php get_template_part( 'template-parts/whatsapp-float' ); ?>

	<div class="np-toast-wrap" id="np-toast-wrap" aria-live="polite"></div>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
