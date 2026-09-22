<?php
/**
 * Template tags & helpers.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'np_posted_on' ) ) :
	/**
	 * Post date + reading time meta.
	 */
	function np_posted_on() {
		$time = sprintf(
			'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);
		echo '<span class="posted-on">' . np_icon( 'calendar', 14 ) . $time . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="reading-time">' . np_icon( 'clock', 14 ) . esc_html( np_reading_time() ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
endif;

if ( ! function_exists( 'np_posted_by' ) ) :
	/**
	 * Post author meta.
	 */
	function np_posted_by() {
		echo '<span class="byline">' . np_icon( 'user', 14 ) . '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
endif;

if ( ! function_exists( 'np_reading_time' ) ) :
	/**
	 * Estimate reading time.
	 *
	 * @param int|null $post_id Optional post ID.
	 * @return string
	 */
	function np_reading_time( $post_id = null ) {
		$post    = get_post( $post_id );
		$content = wp_strip_all_tags( (string) ( $post ? $post->post_content : '' ) );
		$words   = max( 1, str_word_count( $content ) );
		$minutes = max( 1, (int) ceil( $words / 200 ) );
		/* translators: %d: number of minutes. */
		return sprintf( _n( '%d min read', '%d min read', $minutes, 'netplus-circuit' ), $minutes );
	}
endif;

if ( ! function_exists( 'np_comment_count_text' ) ) :
	/**
	 * Comment count text for cards.
	 */
	function np_comment_count_text() {
		$count = (int) get_comments_number();
		echo '<span class="comments-link">' . np_icon( 'chat', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 0 === $count ) {
			esc_html_e( 'No comments', 'netplus-circuit' );
		} else {
			/* translators: %s: comment count. */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', $count, 'netplus-circuit' ), number_format_i18n( $count ) ) );
		}
		echo '</span>';
	}
endif;

if ( ! function_exists( 'np_section_heading' ) ) :
	/**
	 * Render a section heading block.
	 *
	 * @param string $title    Heading text.
	 * @param string $sub      Optional sub text.
	 * @param string $link_url Optional "view all" URL.
	 * @param string $link_txt Optional "view all" label.
	 * @param string $extra    Extra HTML injected at right (e.g. tabs).
	 */
	function np_section_heading( $title, $sub = '', $link_url = '', $link_txt = '', $extra = '' ) {
		?>
		<div class="np-sec-head">
			<div>
				<h2 class="np-sec-head__title"><?php echo esc_html( $title ); ?></h2>
				<?php if ( $sub ) : ?>
					<p class="np-sec-head__sub"><?php echo esc_html( $sub ); ?></p>
				<?php endif; ?>
			</div>
			<div class="np-flex" style="gap:16px;flex-wrap:wrap;">
				<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput -- assembled from trusted template parts. ?>
				<?php if ( $link_url ) : ?>
					<a class="np-sec-head__link" href="<?php echo esc_url( $link_url ); ?>">
						<?php echo esc_html( $link_txt ? $link_txt : __( 'View all', 'netplus-circuit' ) ); ?>
						<?php echo np_icon( 'arrow-r', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'np_page_hero' ) ) :
	/**
	 * Inner-page hero with breadcrumb.
	 *
	 * @param string $title Optional title override.
	 */
	function np_page_hero( $title = '' ) {
		if ( ! $title ) {
			if ( is_singular() ) {
				$title = get_the_title();
			} elseif ( is_archive() ) {
				$title = get_the_archive_title();
				$title = preg_replace( '/^(Archives|Category|Tag|Author):\s*/i', '', (string) $title );
			} elseif ( is_search() ) {
				/* translators: %s: search query. */
				$title = sprintf( __( 'Search results for “%s”', 'netplus-circuit' ), get_search_query() );
			} elseif ( is_404() ) {
				$title = __( 'Page not found', 'netplus-circuit' );
			} else {
				$title = get_the_title();
			}
		}
		?>
		<section class="np-page-hero">
			<div class="np-container">
				<h1 class="np-page-hero__title"><?php echo esc_html( wp_strip_all_tags( (string) $title ) ); ?></h1>
				<nav class="np-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'netplus-circuit' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'netplus-circuit' ); ?></a>
					<span class="sep">/</span>
					<span aria-current="page"><?php echo esc_html( wp_strip_all_tags( (string) $title ) ); ?></span>
				</nav>
			</div>
		</section>
		<?php
	}
endif;

if ( ! function_exists( 'np_breadcrumbs' ) ) :
	/**
	 * WooCommerce breadcrumb trail (styled ours).
	 */
	function np_breadcrumbs() {
		$trail = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'netplus-circuit' ) . '</a>' );

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			if ( is_product_category() || is_product() ) {
				$main = get_queried_object();
				if ( is_product() ) {
					$cats = wp_get_post_terms( get_the_ID(), 'product_cat', array( 'orderby' => 'parent', 'order' => 'ASC' ) );
					if ( ! empty( $cats ) ) {
						$trail[] = '<a href="' . esc_url( get_term_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
					}
					$trail[] = '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
				} elseif ( is_product_category() ) {
					$parents = get_term_parents_list( $main->term_id, 'product_cat', array( 'separator' => '|', 'link' => false, 'include_self' => false ) );
					if ( $parents && ! is_wp_error( $parents ) ) {
						foreach ( array_filter( explode( '|', $parents ) ) as $pname ) {
							$pterm = get_term_by( 'name', $pname, 'product_cat' );
							if ( $pterm ) {
								$trail[] = '<a href="' . esc_url( get_term_link( $pterm ) ) . '">' . esc_html( $pterm->name ) . '</a>';
							}
						}
					}
					$trail[] = '<span aria-current="page">' . esc_html( $main->name ) . '</span>';
				}
			} elseif ( is_shop() ) {
				$trail[] = '<span aria-current="page">' . esc_html( woocommerce_page_title( false ) ) . '</span>';
			} elseif ( is_cart() ) {
				$trail[] = '<span aria-current="page">' . esc_html__( 'Cart', 'netplus-circuit' ) . '</span>';
			} elseif ( is_checkout() ) {
				$trail[] = '<a href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'Cart', 'netplus-circuit' ) . '</a>';
				$trail[] = '<span aria-current="page">' . esc_html__( 'Checkout', 'netplus-circuit' ) . '</span>';
			} elseif ( is_account_page() ) {
				$trail[] = '<span aria-current="page">' . esc_html__( 'My Account', 'netplus-circuit' ) . '</span>';
			}
		} elseif ( is_singular( 'post' ) ) {
			$cats = get_the_category();
			if ( ! empty( $cats ) ) {
				$trail[] = '<a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			}
			$trail[] = '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$trail[] = '<span aria-current="page">' . esc_html( single_term_title( '', false ) ) . '</span>';
		} elseif ( is_search() ) {
			/* translators: %s: search query. */
			$trail[] = '<span aria-current="page">' . esc_html( sprintf( __( 'Search: %s', 'netplus-circuit' ), get_search_query() ) ) . '</span>';
		} elseif ( is_singular() ) {
			$trail[] = '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
		} elseif ( is_home() && ! is_front_page() ) {
			$trail[] = '<span aria-current="page">' . esc_html__( 'Blog', 'netplus-circuit' ) . '</span>';
		}

		echo '<nav class="np-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'netplus-circuit' ) . '">' . implode( '<span class="sep">/</span>', $trail ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
endif;

if ( ! function_exists( 'np_product_query' ) ) :
	/**
	 * Build a products grid using WooCommerce loops (safe fallback if Woo missing).
	 *
	 * @param array $args {
	 *     Query args.
	 *     @type string $type    featured|new|best|sale|recent.
	 *     @type int    $limit   Number of products.
	 *     @type string $columns Grid class modifier.
	 * }
	 */
	function np_product_query( $args = array() ) {
		$defaults = array(
			'type'    => 'recent',
			'limit'   => 8,
			'columns' => '',
			'cat'     => '',
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p class="np-muted">' . esc_html__( 'Activate WooCommerce to display products.', 'netplus-circuit' ) . '</p>';
			return;
		}

		$shortcode_map = array(
			'featured' => 'featured_products',
			'new'      => 'recent_products',
			'best'     => 'best_selling_products',
			'sale'     => 'sale_products',
			'recent'   => 'recent_products',
		);
		$shortcode = isset( $shortcode_map[ $args['type'] ] ) ? $shortcode_map[ $args['type'] ] : 'recent_products';

		$sc_args = array(
			'limit'   => absint( $args['limit'] ),
			'columns' => 4,
			'orderby' => 'date',
			'order'   => 'DESC',
		);
		if ( 'best' === $args['type'] ) {
			$sc_args['orderby'] = 'popularity';
		}
		if ( $args['cat'] ) {
			$sc_args['category'] = $args['cat'];
		}

		$attr_string = '';
		foreach ( $sc_args as $k => $v ) {
			$attr_string .= ' ' . $k . '="' . esc_attr( (string) $v ) . '"';
		}

		echo '<div class="np-products-wrap ' . esc_attr( $args['columns'] ? 'np-products--' . $args['columns'] : '' ) . '">';
		echo do_shortcode( '[' . $shortcode . $attr_string . ']' );
		echo '</div>';
	}
endif;

if ( ! function_exists( 'np_get_product_ids_by_type' ) ) :
	/**
	 * Get product IDs for a listing type (used by homepage tabs).
	 *
	 * @param string $type  featured|new|best|sale.
	 * @param int    $limit Number of products.
	 * @return array
	 */
	function np_get_product_ids_by_type( $type, $limit = 8 ) {
		$q = array(
			'status'  => 'publish',
			'limit'   => $limit,
			'orderby' => 'date',
			'order'   => 'DESC',
			'return'  => 'ids',
		);
		switch ( $type ) {
			case 'featured':
				$q['featured'] = true;
				break;
			case 'best':
				$q['orderby']  = 'popularity';
				break;
			case 'sale':
				$q['on_sale']  = true;
				$q['orderby']  = 'date';
				break;
			case 'new':
			default:
				break;
		}
		$ids = wc_get_products( $q );
		// Featured/sale lists can be sparse — top up with recent products.
		if ( count( $ids ) < $limit && in_array( $type, array( 'featured', 'sale', 'best' ), true ) ) {
			$extra = wc_get_products( array(
				'status' => 'publish',
				'limit'  => $limit - count( $ids ),
				'exclude' => $ids,
				'return' => 'ids',
			) );
			$ids = array_merge( $ids, $extra );
		}
		return $ids;
	}
endif;

if ( ! function_exists( 'np_whatsapp_link' ) ) :
	/**
	 * Build a wa.me link.
	 *
	 * @param string $message Pre-filled message.
	 * @return string
	 */
	function np_whatsapp_link( $message = '' ) {
		$number = preg_replace( '/[^0-9]/', '', (string) get_theme_mod( 'np_whatsapp_number', '' ) );
		if ( ! $number ) {
			return '';
		}
		if ( ! $message ) {
			$message = get_theme_mod( 'np_whatsapp_default_message', '' );
		}
		$url = 'https://wa.me/' . $number;
		if ( $message ) {
			$url .= '?text=' . rawurlencode( $message );
		}
		return $url;
	}
endif;

if ( ! function_exists( 'np_user_role_label' ) ) :
	/**
	 * Human-friendly role label for the current/given user.
	 *
	 * @param WP_User|null $user Optional user.
	 * @return string
	 */
	function np_user_role_label( $user = null ) {
		$user = $user ? $user : wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			return '';
		}
		$map = array(
			'administrator'    => __( 'Admin', 'netplus-circuit' ),
			'shop_manager'     => __( 'Manager', 'netplus-circuit' ),
			'store_manager'    => __( 'Manager', 'netplus-circuit' ),
			'editor'           => __( 'Editor', 'netplus-circuit' ),
			'author'           => __( 'Author', 'netplus-circuit' ),
			'customer'         => __( 'Customer', 'netplus-circuit' ),
			'subscriber'       => __( 'Member', 'netplus-circuit' ),
		);
		$roles = (array) $user->roles;
		foreach ( $roles as $role ) {
			if ( isset( $map[ $role ] ) ) {
				return $map[ $role ];
			}
		}
		global $wp_roles;
		$first = reset( $roles );
		if ( $first && isset( $wp_roles->role_names[ $first ] ) ) {
			return translate_user_role( $wp_roles->role_names[ $first ] );
		}
		return __( 'Customer', 'netplus-circuit' );
	}
endif;

if ( ! function_exists( 'np_get_spec_rows' ) ) :
	/**
	 * Parse "Label | Value" textarea content into rows.
	 *
	 * @param string $raw Raw textarea content.
	 * @return array[] [ [label, value], ... ]
	 */
	function np_get_spec_rows( $raw ) {
		$rows  = array();
		$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
		foreach ( (array) $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line || false === strpos( $line, '|' ) ) {
				continue;
			}
			list( $label, $value ) = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( '' !== $label ) {
				$rows[] = array( $label, $value );
			}
		}
		return $rows;
	}
endif;

/**
 * Fallback primary menu (when no menu assigned) — uses pages.
 */
function np_circuit_default_menu() {
	echo '<ul class="np-nav">';
	wp_list_pages( array( 'title_li' => '', 'depth' => 2 ) );
	if ( class_exists( 'WooCommerce' ) ) {
		echo '<li><a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '">' . esc_html__( 'Shop', 'netplus-circuit' ) . '</a></li>';
	}
	echo '</ul>';
}

if ( ! function_exists( 'np_render_loop_cards' ) ) :
	/**
	 * Render product cards through the theme's WooCommerce loop hooks.
	 *
	 * @param array $ids Product IDs.
	 */
	function np_render_loop_cards( $ids ) {
		global $product;
		foreach ( $ids as $np_id ) {
			$product = wc_get_product( $np_id );
			if ( ! $product ) {
				continue;
			}
			setup_postdata( $GLOBALS['post'] = get_post( $np_id ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			do_action( 'woocommerce_before_shop_loop_item' );
			do_action( 'woocommerce_before_shop_loop_item_title' );
			do_action( 'woocommerce_shop_loop_item_title' );
			do_action( 'woocommerce_after_shop_loop_item_title' );
			do_action( 'woocommerce_after_shop_loop_item' );
		}
		wp_reset_postdata();
		$product = null;
	}
endif;

if ( ! function_exists( 'np_product_carousel' ) ) :
	/**
	 * Swipeable product carousel with arrow controls.
	 *
	 * @param array  $ids   Product IDs.
	 * @param string $label Accessible label.
	 */
	function np_product_carousel( $ids, $label = '' ) {
		if ( empty( $ids ) || ! function_exists( 'wc_get_product' ) ) {
			return;
		}
		?>
		<div class="np-carousel" role="region" aria-label="<?php echo esc_attr( $label ? $label : __( 'Product carousel', 'netplus-circuit' ) ); ?>">
			<div class="np-carousel__track">
				<?php np_render_loop_cards( $ids ); ?>
			</div>
			<div class="np-carousel__nav">
				<button class="np-carousel__btn" type="button" data-car="prev" aria-label="<?php esc_attr_e( 'Scroll products left', 'netplus-circuit' ); ?>">
					<?php echo np_icon( 'chevron-l', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
				<button class="np-carousel__btn" type="button" data-car="next" aria-label="<?php esc_attr_e( 'Scroll products right', 'netplus-circuit' ); ?>">
					<?php echo np_icon( 'chevron-r', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>
		</div>
		<?php
	}
endif;
