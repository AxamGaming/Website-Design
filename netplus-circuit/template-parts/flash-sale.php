<?php
/**
 * Flash Sale band: countdown + on-sale products carousel
 * (the signature first section of netpluscomputers.lk).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_deal', true ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$np_ids = np_get_product_ids_by_type( 'sale', 10 );
if ( count( $np_ids ) < 2 ) {
	$np_ids = np_get_product_ids_by_type( 'featured', 10 );
}
if ( empty( $np_ids ) ) {
	return;
}

// Countdown target: Customizer date, else upcoming Sunday 23:59 site time.
$np_ends = (string) get_theme_mod( 'np_deal_ends', '' );
try {
	$np_dt = new DateTime( $np_ends ? $np_ends : 'next sunday 23:59:59', wp_timezone() );
	if ( $np_dt->getTimestamp() < time() ) {
		$np_dt = new DateTime( 'next sunday 23:59:59', wp_timezone() );
	}
	$np_iso = $np_dt->format( 'c' );
} catch ( Exception $e ) {
	$np_iso = gmdate( 'c', time() + WEEK_IN_SECONDS );
}

$np_use_carousel = np_opt_on( 'np_carousels', true );
?>
<section class="np-flash" aria-label="<?php esc_attr_e( 'Flash sale', 'netplus-circuit' ); ?>">
	<div class="np-flash-head">
		<div class="np-flash-head__left">
			<span class="np-flash-head__icon"><?php echo np_icon( 'fire', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div>
				<h2 class="np-flash-head__title"><?php esc_html_e( 'Flash Sale', 'netplus-circuit' ); ?></h2>
				<p class="np-flash-head__sub"><?php esc_html_e( 'Limited stock prices — ends when the timer hits zero', 'netplus-circuit' ); ?></p>
			</div>
		</div>
		<div class="np-countdown" data-countdown="<?php echo esc_attr( $np_iso ); ?>" aria-label="<?php esc_attr_e( 'Sale ends in', 'netplus-circuit' ); ?>">
			<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="d">00</div><div class="np-countdown__label"><?php esc_html_e( 'Days', 'netplus-circuit' ); ?></div></div>
			<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="h">00</div><div class="np-countdown__label"><?php esc_html_e( 'Hours', 'netplus-circuit' ); ?></div></div>
			<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="m">00</div><div class="np-countdown__label"><?php esc_html_e( 'Mins', 'netplus-circuit' ); ?></div></div>
			<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="s">00</div><div class="np-countdown__label"><?php esc_html_e( 'Secs', 'netplus-circuit' ); ?></div></div>
		</div>
	</div>

	<?php if ( $np_use_carousel ) : ?>
		<?php np_product_carousel( $np_ids, __( 'Flash sale products', 'netplus-circuit' ) ); ?>
	<?php else : ?>
		<div class="np-products np-products--4" style="padding-top:6px;">
			<?php np_render_loop_cards( array_slice( $np_ids, 0, 8 ) ); ?>
		</div>
	<?php endif; ?>
</section>
