<?php
/**
 * WooCommerce integration — product loops, single product, checkout tweaks.
 *
 * All visual changes are done via hooks/filters (no core template overrides),
 * so WooCommerce updates never produce "outdated template" warnings.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   Catalog basics
   ========================================================================== */

/**
 * Products per row on shop archives.
 */
function np_woo_loop_columns() {
	$cols = (int) get_theme_mod( 'np_woo_columns', 4 );
	return $cols ? $cols : 4;
}
add_filter( 'loop_shop_columns', 'np_woo_loop_columns' );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['columns'] = np_woo_loop_columns();
	$args['posts_per_page'] = (int) get_theme_mod( 'np_woo_related_count', 4 );
	return $args;
} );

/**
 * Products per page.
 */
add_filter( 'loop_shop_per_page', function () {
	return np_woo_loop_columns() * 3;
}, 20 );

/**
 * LKR price display: hide trailing .00 for whole numbers.
 */
function np_woo_trim_price_zeros( $return, $price, $args ) {
	if ( ! get_theme_mod( 'np_woo_trim_zeros', true ) ) {
		return $return;
	}
	if ( function_exists( 'get_woocommerce_currency' ) && 'LKR' !== get_woocommerce_currency() ) {
		return $return;
	}
	$num = (float) wp_strip_all_tags( (string) $price );
	if ( floor( $num ) !== $num ) {
		return $return;
	}
	// Remove the decimal portion from formatted HTML (e.g. ".00" / ",00").
	$decimals   = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
	$num_dec    = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2;
	if ( $num_dec > 0 ) {
		$pattern = '/\Q' . $decimals . '\E0{' . $num_dec . '}(?![0-9])/';
		$return  = preg_replace( $pattern, '', $return );
	}
	return $return;
}
add_filter( 'wc_price', 'np_woo_trim_price_zeros', 10, 3 );

/* ==========================================================================
   Product loop card — rebuilt from scratch
   ========================================================================== */

// Remove WooCommerce's default loop pieces.
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

// Card wrapper.
add_action( 'woocommerce_before_shop_loop_item', 'np_loop_card_open', 1 );
function np_loop_card_open() {
	global $product;
	$class = 'np-product-card product type-product';
	if ( $product && $product->is_on_sale() ) {
		$class .= ' on-sale';
	}
	if ( $product && ! $product->is_in_stock() ) {
		$class .= ' outofstock';
	}
	echo '<div class="' . esc_attr( $class ) . '">';
}
add_action( 'woocommerce_after_shop_loop_item', 'np_loop_card_close', 99 );
function np_loop_card_close() {
	echo '</div>';
}

// Media block: link + image + badges + hover actions.
add_action( 'woocommerce_before_shop_loop_item_title', 'np_loop_card_media', 10 );
function np_loop_card_media() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$url = get_permalink( $product->get_id() );
	echo '<div class="np-product-card__media">';
	echo '<div class="np-product-card__badges">';

	if ( $product->is_on_sale() ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		$percent = ( $regular > 0 && $sale > 0 ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;
		echo '<span class="np-badge np-badge--sale">';
		echo $percent ? esc_html( '-' . $percent . '%' ) : esc_html__( 'Sale', 'netplus-circuit' );
		echo '</span>';
	}

	$new_days = (int) get_theme_mod( 'np_woo_new_badge_days', 30 );
	if ( $new_days > 0 ) {
		$created = $product->get_date_created();
		if ( $created && ( time() - $created->getTimestamp() ) < $new_days * DAY_IN_SECONDS ) {
			echo '<span class="np-badge np-badge--new">' . esc_html__( 'New', 'netplus-circuit' ) . '</span>';
		}
	}

	if ( ! $product->is_in_stock() ) {
		echo '<span class="np-badge np-badge--out">' . esc_html__( 'Out of stock', 'netplus-circuit' ) . '</span>';
	} elseif ( $product->managing_stock() && $product->get_stock_quantity() <= 3 ) {
		echo '<span class="np-badge np-badge--hot">' . esc_html__( 'Only few left', 'netplus-circuit' ) . '</span>';
	}

	echo '</div>';

	// Hover actions: quick view (link) + WhatsApp ask.
	echo '<div class="np-product-card__actions">';
	$wa = np_whatsapp_link( sprintf(
		/* translators: %s: product name. */
		__( 'Hi! I am interested in this product: %s', 'netplus-circuit' ),
		$product->get_name() . ' — ' . $url
	) );
	if ( $wa ) {
		echo '<a class="np-icon-btn np-icon-btn--wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr__( 'Ask on WhatsApp', 'netplus-circuit' ) . '" title="' . esc_attr__( 'Ask on WhatsApp', 'netplus-circuit' ) . '">' . np_icon( 'whatsapp', 18 ) . '</a>';
	}
	echo '<a class="np-icon-btn" href="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'View product', 'netplus-circuit' ) . '" title="' . esc_attr__( 'Quick look', 'netplus-circuit' ) . '">' . np_icon( 'eye', 18 ) . '</a>';
	echo '</div>';

	echo '<a href="' . esc_url( $url ) . '" class="woocommerce-LoopProduct-link">';
	echo wp_kses_post( $product->get_image( 'np-product-card', array( 'loading' => 'lazy' ) ) );
	echo '</a>';
	echo '</div>'; // media

	echo '<div class="np-product-card__body">';
	$cats = wc_get_product_category_list( $product->get_id(), ', ', '', '' );
	if ( $cats ) {
		echo '<div class="np-product-card__cat">' . wp_kses_post( wp_strip_all_tags( $cats ) ) . '</div>';
	}
}

// Title.
add_action( 'woocommerce_shop_loop_item_title', 'np_loop_card_title', 10 );
function np_loop_card_title() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<h3 class="np-product-card__title"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . esc_html( $product->get_name() ) . '</a></h3>';
}

// Rating + price.
add_action( 'woocommerce_after_shop_loop_item_title', 'np_loop_card_rating_price', 10 );
function np_loop_card_rating_price() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="np-product-card__rating">';
	$rating = $product->get_average_rating();
	if ( $rating > 0 ) {
		echo '<span class="np-stars" aria-hidden="true">' . esc_html( np_stars_string( $rating ) ) . '</span>';
		$count = $product->get_rating_count();
		echo '<span>(' . esc_html( number_format_i18n( $count ) ) . ')</span>';
	} else {
		echo '<span class="np-muted" style="font-size:.78rem;">' . esc_html__( 'No reviews yet', 'netplus-circuit' ) . '</span>';
	}
	echo '</div>';
	echo '<div class="np-product-card__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';

	// Stock chip.
	if ( $product->managing_stock() ) {
		$qty = (int) $product->get_stock_quantity();
		if ( $qty <= 0 ) {
			echo '<span class="np-stock-chip np-stock-chip--out">' . esc_html__( 'Out of stock', 'netplus-circuit' ) . '</span>';
		} elseif ( $qty <= 5 ) {
			/* translators: %d: stock qty. */
			echo '<span class="np-stock-chip np-stock-chip--low">' . esc_html( sprintf( __( 'Only %d left', 'netplus-circuit' ), $qty ) ) . '</span>';
		} else {
			echo '<span class="np-stock-chip np-stock-chip--in">' . esc_html__( 'In stock', 'netplus-circuit' ) . '</span>';
		}
	} elseif ( $product->is_in_stock() ) {
		echo '<span class="np-stock-chip np-stock-chip--in">' . esc_html__( 'In stock', 'netplus-circuit' ) . '</span>';
	}
}

// Add-to-cart button in a footer row.
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_after_shop_loop_item', 'np_loop_card_atc', 10 );
function np_loop_card_atc() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="np-product-card__foot">';
	woocommerce_template_loop_add_to_cart( array(
		'class' => implode( ' ', array_filter( array(
			'button',
			'product_type_' . $product->get_type(),
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			$product->is_purchasable() && $product->is_in_stock() ? 'np-btn np-btn--primary' : 'np-btn np-btn--dark',
		) ) ),
	) );
	echo '</div>';
	echo '</div>'; // close body opened in media block
}

/**
 * Star string for a rating (★★★★☆).
 *
 * @param float $rating Rating 0-5.
 * @return string
 */
function np_stars_string( $rating ) {
	$full  = (int) round( $rating );
	$stars = str_repeat( '★', min( 5, $full ) );
	$stars .= str_repeat( '☆', max( 0, 5 - $full ) );
	return $stars;
}

/* ==========================================================================
   Single product page
   ========================================================================== */

/**
 * Badges above the title (sale %, new, stock).
 */
add_action( 'woocommerce_single_product_summary', 'np_single_badges', 4 );
function np_single_badges() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="np-sp__badges">';
	if ( $product->is_on_sale() ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		$percent = ( $regular > 0 && $sale > 0 ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;
		echo '<span class="np-badge np-badge--sale">' . ( $percent ? esc_html( __( 'Save', 'netplus-circuit' ) . ' ' . $percent . '%' ) : esc_html__( 'On sale', 'netplus-circuit' ) ) . '</span>';
	}
	$new_days = (int) get_theme_mod( 'np_woo_new_badge_days', 30 );
	if ( $new_days > 0 ) {
		$created = $product->get_date_created();
		if ( $created && ( time() - $created->getTimestamp() ) < $new_days * DAY_IN_SECONDS ) {
			echo '<span class="np-badge np-badge--new">' . esc_html__( 'New arrival', 'netplus-circuit' ) . '</span>';
		}
	}
	echo '<span class="np-badge np-badge--info">' . esc_html__( 'SKU', 'netplus-circuit' ) . ': ' . esc_html( $product->get_sku() ? $product->get_sku() : $product->get_id() ) . '</span>';
	echo '</div>';
}

/**
 * Category line under title.
 */
add_action( 'woocommerce_single_product_summary', 'np_single_cats', 6 );
function np_single_cats() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$cats = wc_get_product_category_list( $product->get_id(), ', ', '<span class="np-sp__cats">' . esc_html__( 'Category:', 'netplus-circuit' ) . ' ', '</span>' );
	echo wp_kses_post( $cats );
}

/**
 * Move the default sale flash (we render our own badges).
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/**
 * Highlights list (from meta box) right after short description.
 */
add_action( 'woocommerce_single_product_summary', 'np_single_highlights', 21 );
function np_single_highlights() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$highlights = get_post_meta( $product->get_id(), '_np_highlights', true );
	if ( ! $highlights ) {
		return;
	}
	echo '<ul class="np-highlights">';
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $highlights ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		echo '<li>' . np_icon( 'check-c', 16 ) . '<span>' . esc_html( $line ) . '</span></li>';
	}
	echo '</ul>';
}

/**
 * WhatsApp order button + call button under Add to Cart.
 */
add_action( 'woocommerce_single_product_summary', 'np_single_whatsapp_row', 31 );
function np_single_whatsapp_row() {
	global $product;
	if ( ! $product || ! get_theme_mod( 'np_wa_product_btn', true ) ) {
		return;
	}
	$number = preg_replace( '/[^0-9]/', '', (string) get_theme_mod( 'np_whatsapp_number', '' ) );
	if ( ! $number ) {
		return;
	}
	$url  = get_permalink( $product->get_id() );
	$price = wp_strip_all_tags( (string) $product->get_price_html() );
	$msg  = sprintf(
		/* translators: 1: product name, 2: price, 3: url */
		__( "Hi NetPlus! I'd like to order:\n📦 %1$s\n💰 %2$s\n🔗 %3$s", 'netplus-circuit' ),
		$product->get_name(),
		$price,
		$url
	);
	echo '<div class="np-sp__buyrow">';
	echo '<a class="np-btn np-btn--wa" href="' . esc_url( 'https://wa.me/' . $number . '?text=' . rawurlencode( $msg ) ) . '" target="_blank" rel="noopener">' . np_icon( 'whatsapp', 18 ) . esc_html__( 'Order via WhatsApp', 'netplus-circuit' ) . '</a>';
	$phone = get_theme_mod( 'np_phone', '' );
	if ( $phone ) {
		echo '<a class="np-btn np-btn--ghost" href="tel:' . esc_attr( preg_replace( '/\s+/', '', $phone ) ) . '">' . np_icon( 'phone', 17 ) . esc_html__( 'Call us', 'netplus-circuit' ) . '</a>';
	}
	echo '</div>';
}

/**
 * Assurance chips under buy row.
 */
add_action( 'woocommerce_single_product_summary', 'np_single_assurances', 32 );
function np_single_assurances() {
	echo '<div class="np-sp__assurances">';
	echo '<div class="np-assurance">' . np_icon( 'truck', 18 ) . '<span>' . esc_html__( 'Island-wide delivery', 'netplus-circuit' ) . '</span></div>';
	echo '<div class="np-assurance">' . np_icon( 'shield', 18 ) . '<span>' . esc_html__( 'Warranty on parts', 'netplus-circuit' ) . '</span></div>';
	echo '<div class="np-assurance">' . np_icon( 'hand-money', 18 ) . '<span>' . esc_html__( 'Cash on delivery', 'netplus-circuit' ) . '</span></div>';
	echo '<div class="np-assurance">' . np_icon( 'refresh', 18 ) . '<span>' . esc_html__( '7-day returns', 'netplus-circuit' ) . '</span></div>';
	echo '</div>';
}

/**
 * Product tabs: add Specifications + Delivery & Warranty, keep Reviews.
 */
add_filter( 'woocommerce_product_tabs', 'np_single_tabs', 20 );
function np_single_tabs( $tabs ) {
	global $product;

	$has_specs = false;
	if ( $product ) {
		$spec_meta = get_post_meta( $product->get_id(), '_np_specs', true );
		$attrs     = $product->get_attributes();
		$has_specs = ( $spec_meta && '' !== trim( (string) $spec_meta ) ) || ! empty( $attrs );
	}

	$new = array();

	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'Description', 'netplus-circuit' );
		$new['description'] = $tabs['description'];
	}

	if ( $has_specs ) {
		$new['np_specs'] = array(
			'title'    => __( 'Specifications', 'netplus-circuit' ),
			'priority' => 15,
			'callback' => 'np_render_specs_tab',
		);
	}

	if ( isset( $tabs['additional_information'] ) ) {
		unset( $tabs['additional_information'] ); // merged into Specifications
	}

	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = sprintf(
			/* translators: %s: review count. */
			__( 'Reviews (%s)', 'netplus-circuit' ),
			$product ? $product->get_review_count() : 0
		);
		$tabs['reviews']['priority'] = 30;
		$new['reviews'] = $tabs['reviews'];
	}

	$delivery = get_theme_mod( 'np_delivery_tab_text', '' );
	if ( $delivery ) {
		$new['np_delivery'] = array(
			'title'    => __( 'Delivery & Warranty', 'netplus-circuit' ),
			'priority' => 40,
			'callback' => 'np_render_delivery_tab',
		);
	}

	return $new;
}

/**
 * Render Specifications tab: meta spec table + attributes table.
 */
function np_render_specs_tab() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$raw = (string) get_post_meta( $product->get_id(), '_np_specs', true );
	$rows = np_get_spec_rows( $raw );
	if ( $rows ) {
		echo '<table class="np-spec-table"><caption>' . esc_html__( 'Technical specifications', 'netplus-circuit' ) . '</caption><tbody>';
		foreach ( $rows as $row ) {
			echo '<tr><th scope="row">' . esc_html( $row[0] ) . '</th><td>' . esc_html( $row[1] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
	$attrs = $product->get_attributes();
	if ( $attrs ) {
		echo '<table class="np-spec-table"><caption>' . esc_html__( 'Attributes', 'netplus-circuit' ) . '</caption><tbody>';
		foreach ( $attrs as $attr ) {
			if ( ! $attr instanceof WC_Product_Attribute ) {
				continue;
			}
			$name  = wc_attribute_label( $attr->get_name() );
			$values = array();
			if ( $attr->is_taxonomy() ) {
				$terms = wp_get_post_terms( $product->get_id(), $attr->get_taxonomy() );
				foreach ( $terms as $term ) {
					$values[] = $term->name;
				}
			} else {
				$values = $attr->get_options();
			}
			if ( $values ) {
				echo '<tr><th scope="row">' . esc_html( $name ) . '</th><td>' . esc_html( implode( ', ', (array) $values ) ) . '</td></tr>';
			}
		}
		echo '</tbody></table>';
	}
}

/**
 * Render Delivery & Warranty tab.
 */
function np_render_delivery_tab() {
	$text = (string) get_theme_mod( 'np_delivery_tab_text', '' );
	echo '<div class="np-delivery-tab">' . wpautop( esc_html( $text ) ) . '</div>';
	$wa = np_whatsapp_link( __( 'Hi NetPlus! I have a question about delivery/warranty.', 'netplus-circuit' ) );
	if ( $wa ) {
		echo '<p><a class="np-btn np-btn--wa np-btn--sm" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener">' . np_icon( 'whatsapp', 16 ) . esc_html__( 'Ask about delivery on WhatsApp', 'netplus-circuit' ) . '</a></p>';
	}
}

/**
 * Related products heading.
 */
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	return $args;
} );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product_summary', 'np_related_products', 20 );
function np_related_products() {
	$count = (int) get_theme_mod( 'np_woo_related_count', 4 );
	echo '<section class="np-related">';
	woocommerce_output_related_products( array( 'posts_per_page' => $count, 'columns' => np_woo_loop_columns() ) );
	echo '</section>';
}

/* ==========================================================================
   Simplified checkout (Duino-style) + bot protection fields
   ========================================================================== */

/**
 * Trim optional fields & reorder for a faster checkout.
 */
add_filter( 'woocommerce_checkout_fields', 'np_simplify_checkout_fields', 20 );
function np_simplify_checkout_fields( $fields ) {
	// Remove rarely used fields (Sri Lankan COD flow).
	$remove = array(
		'billing_company',
		'billing_address_2',
		'shipping_company',
		'shipping_address_2',
		'billing_country', // single-country store; keep if multiple enabled below
	);
	$countries = function_exists( 'WC' ) ? WC()->countries->get_available_countries() : array();
	if ( count( $countries ) > 1 ) {
		$remove = array_diff( $remove, array( 'billing_country' ) );
	}
	foreach ( $remove as $key ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			unset( $fields['billing'][ $key ] ) ;
		}
		if ( isset( $fields['shipping'][ $key ] ) ) {
			unset( $fields['shipping'][ $key ] );
		}
	}

	// Phone always required (couriers need it).
	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['required']    = true;
		$fields['billing']['billing_phone']['priority']    = 25;
		/* translators: placeholder shows format example */
		$fields['billing']['billing_phone']['placeholder'] = '07X XXX XXXX';
	}
	if ( isset( $fields['billing']['billing_first_name'] ) ) {
		$fields['billing']['billing_first_name']['priority'] = 10;
	}
	if ( isset( $fields['billing']['billing_last_name'] ) ) {
		$fields['billing']['billing_last_name']['priority'] = 20;
		$fields['billing']['billing_last_name']['required'] = false;
	}
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['priority'] = 30;
	}
	if ( isset( $fields['billing']['billing_address_1'] ) ) {
		$fields['billing']['billing_address_1']['priority'] = 40;
		$fields['billing']['billing_address_1']['label']    = __( 'Delivery address (street, house no.)', 'netplus-circuit' );
	}
	if ( isset( $fields['billing']['billing_city'] ) ) {
		$fields['billing']['billing_city']['priority'] = 50;
		$fields['billing']['billing_city']['label']    = __( 'City / Town', 'netplus-circuit' );
	}
	if ( isset( $fields['billing']['billing_state'] ) ) {
		$fields['billing']['billing_state']['priority'] = 60;
		$fields['billing']['billing_state']['label']    = __( 'District', 'netplus-circuit' );
	}
	if ( isset( $fields['billing']['billing_postcode'] ) ) {
		$fields['billing']['billing_postcode']['priority'] = 65;
		$fields['billing']['billing_postcode']['required'] = false;
	}
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['placeholder'] = __( 'Delivery notes, landmark, preferred courier… (optional)', 'netplus-circuit' );
		$fields['order']['order_comments']['label']       = __( 'Order notes', 'netplus-circuit' );
	}

	// Anti-bot honeypot + timing fields (see security.php for validation).
	if ( np_opt_on( 'np_sec_checkout_honeypot', true ) ) {
		$fields['np_bot_hp'] = array(
			'type'     => 'text',
			'label'    => __( 'Leave this field empty', 'netplus-circuit' ),
			'required' => false,
			'class'    => array( 'np-hp-field' ),
			'priority' => 1,
		);
		$fields['np_bot_time'] = array(
			'type'     => 'hidden',
			'required' => false,
			'class'    => array( 'np-hp-field' ),
			'priority' => 2,
			'default'  => np_bot_time_token(),
		);
	}
	return $fields;
}

/**
 * Render honeypot CSS-safe (hidden but present in DOM).
 */
add_action( 'woocommerce_before_checkout_form', 'np_checkout_honeypot_style', 5 );
function np_checkout_honeypot_style() {
	if ( np_opt_on( 'np_sec_checkout_honeypot', true ) ) {
		echo '<style>.np-hp-field{position:absolute!important;left:-9999px!important;opacity:0;height:1px;width:1px;overflow:hidden;}</style>';
	}
}

/**
 * Trust chips beside the order review on checkout.
 */
add_action( 'woocommerce_review_order_after_payment', 'np_checkout_trust' );
function np_checkout_trust() {
	echo '<div class="np-checkout-trust">';
	echo '<div class="np-assurance">' . np_icon( 'lock', 16 ) . '<span>' . esc_html__( 'Secure checkout — your data is encrypted', 'netplus-circuit' ) . '</span></div>';
	echo '<div class="np-assurance">' . np_icon( 'truck', 16 ) . '<span>' . esc_html__( 'Fast island-wide delivery', 'netplus-circuit' ) . '</span></div>';
	echo '<div class="np-assurance">' . np_icon( 'hand-money', 16 ) . '<span>' . esc_html__( 'Cash on Delivery available', 'netplus-circuit' ) . '</span></div>';
	echo '</div>';
}

/**
 * Two-column checkout layout wrapper.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_cart() ) {
		$css = '
			form.woocommerce-checkout{display:flex;flex-wrap:wrap;gap:0 26px;align-items:flex-start;}
			form.woocommerce-checkout > *{flex:0 0 100%;}
			form.woocommerce-checkout #customer_details{flex:1 1 calc(58% - 26px);order:2;min-width:0;}
			form.woocommerce-checkout #order_review{flex:1 1 calc(42% - 26px);order:3;min-width:0;}
			form.woocommerce-checkout #order_review_heading{display:none;}
			form.woocommerce-checkout .woocommerce-form-coupon-toggle,form.woocommerce-checkout .checkout_coupon{order:1;}
			form.woocommerce-checkout .col2-set{display:block;}
			form.woocommerce-checkout .col2-set .col-1,form.woocommerce-checkout .col2-set .col-2{width:100%;float:none;}
			#np_order_review_heading{font-size:1.15rem;padding-bottom:12px;border-bottom:2px solid var(--np-line);margin-bottom:16px;}
			@media(max-width:900px){
				form.woocommerce-checkout #customer_details,form.woocommerce-checkout #order_review{flex:0 0 100%;}
				form.woocommerce-checkout #order_review{order:4;}
			}';
		wp_add_inline_style( 'np-circuit-style', $css );
	}
}, 30 );

/**
 * "Your order" heading inside the order review panel.
 */
add_action( 'woocommerce_review_order_before_order_review', function () {
	echo '<h3 id="np_order_review_heading">' . esc_html__( 'Your Order', 'netplus-circuit' ) . '</h3>';
} );

/**
 * Encourage account creation but don't force it.
 */
add_filter( 'woocommerce_checkout_show_terms', '__return_true' );

/* ==========================================================================
   Cart page
   ========================================================================== */

/**
 * Friendly empty-cart content.
 */
add_action( 'woocommerce_cart_is_empty', 'np_empty_cart_message', 5 );
function np_empty_cart_message() {
	echo '<div class="np-empty" style="border:0;padding:30px 0 10px;"><div class="np-empty__code">🛒</div>';
	echo '<h2>' . esc_html__( 'Your cart is empty', 'netplus-circuit' ) . '</h2>';
	echo '<p>' . esc_html__( 'Browse our catalogue of genuine laptop & PC parts — or message us on WhatsApp and we will find the part for you.', 'netplus-circuit' ) . '</p>';
	$wa = np_whatsapp_link( __( 'Hi NetPlus! I am looking for a specific computer part…', 'netplus-circuit' ) );
	if ( $wa ) {
		echo '<a class="np-btn np-btn--wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener">' . np_icon( 'whatsapp', 18 ) . esc_html__( 'Find my part on WhatsApp', 'netplus-circuit' ) . '</a> ';
	}
	echo '<a class="np-btn np-btn--primary" href="' . esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ) . '">' . esc_html__( 'Start shopping', 'netplus-circuit' ) . '</a>';
	echo '</div>';
}
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

/**
 * Cross-sells columns.
 */
add_filter( 'woocommerce_cross_sells_columns', function () {
	return np_woo_loop_columns();
} );
add_filter( 'woocommerce_cross_sells_total', function () {
	return 4;
} );

/* ==========================================================================
   Layout control: remove Woo default wrappers / sidebar / breadcrumb
   ========================================================================== */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Single product: wrap in a white panel.
 */
add_action( 'woocommerce_before_main_content', 'np_single_product_panel_open', 15 );
function np_single_product_panel_open() {
	if ( is_product() ) {
		echo '<div class="np-single-product">';
	}
}
add_action( 'woocommerce_after_main_content', 'np_single_product_panel_close', 5 );
function np_single_product_panel_close() {
	if ( is_product() ) {
		echo '</div>';
	}
}

/**
 * Cart / checkout / account panels.
 */
add_action( 'woocommerce_before_main_content', 'np_woo_panel_open', 16 );
function np_woo_panel_open() {
	if ( is_cart() || is_account_page() ) {
		echo '<div class="np-woo-panel">';
	}
}
add_action( 'woocommerce_after_main_content', 'np_woo_panel_close', 4 );
function np_woo_panel_close() {
	if ( is_cart() || is_account_page() ) {
		echo '</div>';
	}
}

/**
 * Hero for WooCommerce pages (rendered by woocommerce.php).
 */
function np_woo_page_hero() {
	if ( is_product() ) {
		// Breadcrumb-only bar; product title lives inside the panel.
		echo '<section class="np-page-hero np-page-hero--slim"><div class="np-container">';
		np_breadcrumbs();
		echo '</div></section>';
	} elseif ( is_cart() ) {
		np_page_hero( __( 'Shopping Cart', 'netplus-circuit' ) );
	} elseif ( is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
		np_page_hero( __( 'Secure Checkout', 'netplus-circuit' ) );
	} elseif ( is_account_page() ) {
		np_page_hero( __( 'My Account', 'netplus-circuit' ) );
	} elseif ( is_shop() ) {
		np_page_hero( function_exists( 'woocommerce_page_title' ) ? woocommerce_page_title( false ) : __( 'Shop', 'netplus-circuit' ) );
	} elseif ( is_product_taxonomy() ) {
		np_page_hero( wp_strip_all_tags( (string) woocommerce_page_title( false ) ) );
	} elseif ( is_search() ) {
		np_page_hero( sprintf(
			/* translators: %s: search query */
			__( 'Search results for “%s”', 'netplus-circuit' ),
			get_search_query()
		) );
	}
}

/* ==========================================================================
   Archives & misc
   ========================================================================== */

/**
 * Wrap Woo archives in our shop grid with sidebar.
 */
function np_woo_archive_wrapper_open() {
	if ( is_shop() || is_product_taxonomy() ) {
		echo '<div class="np-shop">';
		echo '<aside class="np-shop__sidebar" aria-label="' . esc_attr__( 'Shop filters', 'netplus-circuit' ) . '">';
		if ( is_active_sidebar( 'shop-sidebar' ) ) {
			dynamic_sidebar( 'shop-sidebar' );
		} else {
			np_default_shop_sidebar();
		}
		echo '</aside>';
		echo '<div class="np-shop__main">';
		echo '<div class="np-shop__toolbar">';
	}
}

function np_woo_archive_wrapper_close() {
	if ( is_shop() || is_product_taxonomy() ) {
		echo '</div>'; // main
		echo '</div>'; // shop grid
	}
}

/**
 * Default sidebar content when no widgets assigned.
 */
function np_default_shop_sidebar() {
	echo '<section class="widget np-sidebar-widget"><h3 class="widget-title">' . esc_html__( 'Categories', 'netplus-circuit' ) . '</h3>';
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 14,
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );
	if ( ! is_wp_error( $terms ) && $terms ) {
		echo '<ul class="np-cat-list">';
		foreach ( $terms as $term ) {
			echo '<li' . ( is_tax( 'product_cat', $term->term_id ) ? ' class="current-cat"' : '' ) . '>';
			echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '<span class="count">' . esc_html( $term->count ) . '</span></a>';
			echo '</li>';
		}
		echo '</ul>';
	}
	echo '</section>';

	echo '<section class="widget np-sidebar-widget"><h3 class="widget-title">' . esc_html__( 'Need help choosing?', 'netplus-circuit' ) . '</h3>';
	echo '<p style="font-size:.88rem;color:var(--np-muted);margin-bottom:14px;">' . esc_html__( 'Send us the part number or a photo on WhatsApp — our technicians will confirm compatibility for free.', 'netplus-circuit' ) . '</p>';
	$wa = np_whatsapp_link( __( 'Hi NetPlus! Can you help me find the right part?', 'netplus-circuit' ) );
	if ( $wa ) {
		echo '<a class="np-btn np-btn--wa np-btn--block np-btn--sm" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener">' . np_icon( 'whatsapp', 17 ) . esc_html__( 'Chat on WhatsApp', 'netplus-circuit' ) . '</a>';
	}
	echo '</section>';
}

/**
 * Move result count + ordering into our toolbar.
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'np_woo_toolbar_open', 15 );
function np_woo_toolbar_open() {
	if ( is_shop() || is_product_taxonomy() ) {
		np_woo_archive_wrapper_open();
		echo '<div class="np-shop__count">';
		woocommerce_result_count();
		echo '</div>';
		echo '<div class="np-shop__ordering">';
		woocommerce_catalog_ordering();
		echo '</div>';
	}
}
add_action( 'woocommerce_before_shop_loop', 'np_woo_toolbar_after', 35 );
function np_woo_toolbar_after() {
	if ( is_shop() || is_product_taxonomy() ) {
		np_woo_archive_wrapper_close_toolbar();
	}
}
function np_woo_archive_wrapper_close_toolbar() {
	echo '</div>'; // close toolbar
}
add_action( 'woocommerce_after_shop_loop', 'np_woo_archive_end', 5 );
function np_woo_archive_end() {
	if ( is_shop() || is_product_taxonomy() ) {
		echo '</div></div>'; // main + shop grid
	}
}

/**
 * Single product wrapper handled in woocommerce.php template.
 */

/**
 * "Products" label for shop page title.
 */
add_filter( 'woocommerce_page_title', function ( $title ) {
	return $title;
} );

/**
 * Sale flash text.
 */
add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	$percent = ( $regular > 0 && $sale > 0 ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;
	return '<span class="onsale">' . ( $percent ? '-' . $percent . '%' : esc_html__( 'Sale!', 'netplus-circuit' ) ) . '</span>';
}, 10, 3 );
