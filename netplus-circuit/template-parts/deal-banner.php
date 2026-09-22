<?php
/**
 * Deal of the Week with live countdown (MD Computers style).
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! np_opt_on( 'np_show_deal', true ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$np_product = null;
$np_pid     = (int) get_theme_mod( 'np_deal_product', 0 );

if ( $np_pid ) {
	$np_product = wc_get_product( $np_pid );
}

// Auto-pick: on-sale product with the biggest discount.
if ( ! $np_product ) {
	$np_on_sale = wc_get_products( array(
		'status' => 'publish',
		'on_sale' => true,
		'limit'  => 20,
		'return' => 'ids',
	) );
	$np_best    = 0;
	$np_best_id = 0;
	foreach ( (array) $np_on_sale as $np_sid ) {
		$p = wc_get_product( $np_sid );
		if ( ! $p || ! $p->is_in_stock() ) {
			continue;
		}
		$reg  = (float) $p->get_regular_price();
		$sale = (float) $p->get_sale_price();
		$off  = ( $reg > 0 && $sale > 0 ) ? ( ( $reg - $sale ) / $reg ) * 100 : 0;
		if ( $off > $np_best ) {
			$np_best    = $off;
			$np_best_id = $np_sid;
		}
	}
	if ( $np_best_id ) {
		$np_product = wc_get_product( $np_best_id );
	}
}

if ( ! $np_product ) {
	return;
}

// Countdown end: custom date, else upcoming Sunday 23:59 site-local time.
$np_ends = (string) get_theme_mod( 'np_deal_ends', '' );
try {
	$np_dt = new DateTime( $np_ends ? $np_ends : 'next sunday 23:59:59', wp_timezone() );
	if ( $np_dt->getTimestamp() < time() ) {
		$np_dt = new DateTime( 'next sunday 23:59:59', wp_timezone() );
	}
	$np_iso = $np_dt->format( 'c' ); // includes the +05:30 offset so JS parses it correctly
} catch ( Exception $e ) {
	$np_iso = gmdate( 'c', time() + WEEK_IN_SECONDS );
}

$np_reg     = (float) $np_product->get_regular_price();
$np_sale    = (float) $np_product->get_sale_price();
$np_percent = ( $np_reg > 0 && $np_sale > 0 ) ? round( ( ( $np_reg - $np_sale ) / $np_reg ) * 100 ) : 0;

// Stock progress.
$np_stock_pct = 62; // default "selling fast" look
if ( $np_product->managing_stock() ) {
	$np_stock    = (int) $np_product->get_stock_quantity();
	$np_sold     = (int) $np_product->get_total_sales();
	$np_total    = max( 1, $np_stock + $np_sold );
	$np_stock_pct = min( 95, max( 15, (int) round( ( $np_sold / $np_total ) * 100 ) ) );
}

$np_url   = get_permalink( $np_product->get_id() );
$np_title = get_theme_mod( 'np_deal_title', '' );
$np_note  = get_theme_mod( 'np_deal_note', '' );
$np_wa    = np_whatsapp_link( sprintf(
	/* translators: %s: product name */
	__( 'Hi! I want the deal of the week: %s', 'netplus-circuit' ),
	$np_product->get_name() . ' (' . $np_url . ')'
) );
?>
<section class="np-section--tight" aria-label="<?php esc_attr_e( 'Deal of the week', 'netplus-circuit' ); ?>">
	<div class="np-deal">
		<div class="np-deal__grid">
			<div class="np-deal__media">
				<?php if ( $np_percent ) : ?>
					<span class="np-badge np-badge--sale np-deal__flash" style="font-size:.9rem;padding:7px 16px;">-<?php echo esc_html( $np_percent ); ?>%</span>
				<?php endif; ?>
				<a href="<?php echo esc_url( $np_url ); ?>">
					<?php echo wp_kses_post( $np_product->get_image( 'woocommerce_single', array( 'loading' => 'lazy' ) ) ); ?>
				</a>
			</div>
			<div class="np-deal__body">
				<span class="np-deal__eyebrow"><?php echo np_icon( 'fire', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Deal of the Week', 'netplus-circuit' ); ?></span>
				<h3 class="np-deal__title"><a href="<?php echo esc_url( $np_url ); ?>"><?php echo esc_html( $np_title ? $np_title : $np_product->get_name() ); ?></a></h3>
				<div class="np-deal__prices">
					<span class="np-deal__price"><?php echo wp_kses_post( wc_price( $np_sale ? $np_sale : $np_reg ) ); ?></span>
					<?php if ( $np_sale && $np_reg > $np_sale ) : ?>
						<span class="np-deal__was"><?php echo wp_kses_post( wc_price( $np_reg ) ); ?></span>
					<?php endif; ?>
				</div>
				<div class="np-countdown" data-countdown="<?php echo esc_attr( $np_iso ); ?>" aria-label="<?php esc_attr_e( 'Offer ends in', 'netplus-circuit' ); ?>">
					<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="d">00</div><div class="np-countdown__label"><?php esc_html_e( 'Days', 'netplus-circuit' ); ?></div></div>
					<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="h">00</div><div class="np-countdown__label"><?php esc_html_e( 'Hours', 'netplus-circuit' ); ?></div></div>
					<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="m">00</div><div class="np-countdown__label"><?php esc_html_e( 'Mins', 'netplus-circuit' ); ?></div></div>
					<div class="np-countdown__cell"><div class="np-countdown__num" data-cd="s">00</div><div class="np-countdown__label"><?php esc_html_e( 'Secs', 'netplus-circuit' ); ?></div></div>
				</div>
				<?php if ( $np_note ) : ?>
					<p class="np-deal__stock"><?php echo esc_html( $np_note ); ?></p>
				<?php endif; ?>
				<div>
					<div class="np-deal__bar"><span class="np-deal__bar-fill" style="width:<?php echo esc_attr( $np_stock_pct ); ?>%;"></span></div>
					<p class="np-deal__stock"><?php esc_html_e( 'Selling fast — limited stock!', 'netplus-circuit' ); ?></p>
				</div>
				<div class="np-deal__cta">
					<a class="np-btn np-btn--hot" href="<?php echo esc_url( $np_url ); ?>"><?php esc_html_e( 'Grab this deal', 'netplus-circuit' ); ?> <?php echo np_icon( 'arrow-r', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<?php if ( $np_wa ) : ?>
						<a class="np-btn np-btn--wa" href="<?php echo esc_url( $np_wa ); ?>" target="_blank" rel="noopener"><?php echo np_icon( 'whatsapp', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Ask about it', 'netplus-circuit' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
