<?php
/**
 * Promo banners (Scion-style 2-up promotional sections).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_promos', true ) ) {
	return;
}

$np_banners = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$np_title = (string) get_theme_mod( 'np_promo_' . $i . '_title', '' );
	if ( '' === $np_title ) {
		continue;
	}
	$np_img_id = (int) get_theme_mod( 'np_promo_' . $i . '_img', 0 );
	$np_banners[] = array(
		'eyebrow' => (string) get_theme_mod( 'np_promo_' . $i . '_eyebrow', '' ),
		'title'   => $np_title,
		'sub'     => (string) get_theme_mod( 'np_promo_' . $i . '_sub', '' ),
		'btn'     => (string) get_theme_mod( 'np_promo_' . $i . '_btn', '' ),
		'link'    => (string) get_theme_mod( 'np_promo_' . $i . '_link', '' ),
		'style'   => (string) get_theme_mod( 'np_promo_' . $i . '_style', 1 === $i ? 'accent' : 'hot' ),
		'img'     => $np_img_id ? wp_get_attachment_image_url( $np_img_id, 'np-promo-banner' ) : '',
	);
}
if ( empty( $np_banners ) ) {
	return;
}
?>
<section class="np-section--tight" aria-label="<?php esc_attr_e( 'Promotions', 'netplus-circuit' ); ?>">
	<div class="np-promos">
		<?php foreach ( $np_banners as $np_b ) : ?>
			<a class="np-promo np-promo--<?php echo esc_attr( $np_b['style'] ); ?>" href="<?php echo esc_url( $np_b['link'] ? $np_b['link'] : '#' ); ?>"
				<?php echo $np_b['img'] ? ' style="background-image:url(' . esc_url( $np_b['img'] ) . ');"' : ''; ?>>
				<span class="np-promo__content">
					<?php if ( $np_b['eyebrow'] ) : ?>
						<span class="np-promo__eyebrow"><?php echo esc_html( $np_b['eyebrow'] ); ?></span>
					<?php endif; ?>
					<span class="np-promo__title" style="display:block;font-family:var(--np-font-head);font-weight:700;"><?php echo esc_html( $np_b['title'] ); ?></span>
					<?php if ( $np_b['sub'] ) : ?>
						<span class="np-promo__sub" style="display:block;"><?php echo esc_html( $np_b['sub'] ); ?></span>
					<?php endif; ?>
					<?php if ( $np_b['btn'] ) : ?>
						<span class="np-btn np-btn--light np-btn--sm"><?php echo esc_html( $np_b['btn'] ); ?> <?php echo np_icon( 'arrow-r', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
