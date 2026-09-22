<?php
/**
 * Home hero slider (3 configurable slides).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

$np_slides = array();
$np_any_hero = false;
for ( $i = 1; $i <= 3; $i++ ) {
	if ( get_theme_mod( 'np_hero_' . $i . '_show', false ) ) {
		$np_any_hero = true;
	}
}
if ( ! $np_any_hero ) {
	return; // Homepage starts with the Flash Sale band (netpluscomputers.lk layout).
}
for ( $i = 1; $i <= 3; $i++ ) {
	if ( ! get_theme_mod( 'np_hero_' . $i . '_show', false ) ) {
		continue;
	}
	$title = (string) get_theme_mod( 'np_hero_' . $i . '_title', '' );
	$img   = (int) get_theme_mod( 'np_hero_' . $i . '_img', 0 );
	if ( ! $title && ! $img ) {
		continue;
	}
	$np_slides[] = array(
		'index'   => $i,
		'eyebrow' => (string) get_theme_mod( 'np_hero_' . $i . '_eyebrow', '' ),
		'title'   => $title,
		'sub'     => (string) get_theme_mod( 'np_hero_' . $i . '_sub', '' ),
		'btn'     => (string) get_theme_mod( 'np_hero_' . $i . '_btn', '' ),
		'link'    => (string) get_theme_mod( 'np_hero_' . $i . '_link', '' ),
		'img'     => $img ? wp_get_attachment_image_url( $img, 'np-hero-slide' ) : '',
	);
}

// Fallback slide when nothing configured.
if ( empty( $np_slides ) ) {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$np_slides[] = array(
		'index'   => 1,
		'eyebrow' => __( 'Genuine Laptop & PC Parts', 'netplus-circuit' ),
		'title'   => __( 'Every part your *computer* needs', 'netplus-circuit' ),
		'sub'     => __( 'Keyboards, batteries, screens, fans & more — tested, warrantied and delivered anywhere in Sri Lanka.', 'netplus-circuit' ),
		'btn'     => __( 'Shop Now', 'netplus-circuit' ),
		'link'    => $shop_url,
		'img'     => '',
	);
}

if ( empty( $np_slides ) ) {
	return;
}
?>
<section class="np-hero" id="np-hero" data-autoplay="6000" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured promotions', 'netplus-circuit' ); ?>">
	<div class="np-hero__track" id="np-hero-track">
		<?php foreach ( $np_slides as $np_s ) :
			// *starred* words become gradient-highlighted.
			$np_title_html = esc_html( $np_s['title'] );
			$np_title_html = preg_replace( '/\*([^*]+)\*/', '<em>$1</em>', $np_title_html );
			$np_style = $np_s['img'] ? ' style="background-image:url(' . esc_url( $np_s['img'] ) . ');"' : '';
			?>
			<div class="np-hero__slide np-hero__slide--<?php echo esc_attr( $np_s['index'] ); ?>"<?php echo $np_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<div class="np-container">
					<div class="np-hero__content">
						<?php if ( $np_s['eyebrow'] ) : ?>
							<span class="np-hero__eyebrow"><?php echo np_icon( 'bolt', 13); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $np_s['eyebrow'] ); ?></span>
						<?php endif; ?>
						<h2 class="np-hero__title"><?php echo wp_kses_post( $np_title_html ); ?></h2>
						<?php if ( $np_s['sub'] ) : ?>
							<p class="np-hero__sub"><?php echo esc_html( $np_s['sub'] ); ?></p>
						<?php endif; ?>
						<?php if ( $np_s['btn'] ) : ?>
							<div class="np-hero__cta">
								<a class="np-btn np-btn--hot np-btn--lg" href="<?php echo esc_url( $np_s['link'] ? $np_s['link'] : '#' ); ?>">
									<?php echo esc_html( $np_s['btn'] ); ?>
									<?php echo np_icon( 'arrow-r', 17); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</a>
								<?php
								$np_hero_wa = np_whatsapp_link( __( 'Hi NetPlus! I saw your website offer and want to know more.', 'netplus-circuit' ) );
								if ( $np_hero_wa ) :
									?>
									<a class="np-btn np-btn--wa np-btn--lg" href="<?php echo esc_url( $np_hero_wa ); ?>" target="_blank" rel="noopener">
										<?php echo np_icon( 'whatsapp', 18); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<?php esc_html_e( 'Ask on WhatsApp', 'netplus-circuit' ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $np_slides ) > 1 ) : ?>
		<button class="np-hero__arrow np-hero__arrow--prev" id="np-hero-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'netplus-circuit' ); ?>"><?php echo np_icon( 'chevron-l', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<button class="np-hero__arrow np-hero__arrow--next" id="np-hero-next" aria-label="<?php esc_attr_e( 'Next slide', 'netplus-circuit' ); ?>"><?php echo np_icon( 'chevron-r', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<div class="np-hero__nav" id="np-hero-dots" role="tablist">
			<?php foreach ( array_keys( $np_slides ) as $np_di ) : ?>
				<button class="np-hero__dot<?php echo 0 === $np_di ? ' is-active' : ''; ?>" data-slide="<?php echo esc_attr( $np_di ); ?>" role="tab" aria-label="<?php echo esc_attr( sprintf( /* translators: %d slide number */ __( 'Slide %d', 'netplus-circuit' ), $np_di + 1 ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
