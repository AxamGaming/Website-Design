<?php
/**
 * Inline SVG icon helper (no icon font dependency, crisp on retina).
 *
 * Usage: echo np_icon( 'cart', 20 );
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'np_icon' ) ) :
	/**
	 * Return an inline SVG icon.
	 *
	 * @param string $name Icon key.
	 * @param int    $size Pixel size.
	 * @return string SVG markup.
	 */
	function np_icon( $name, $size = 20 ) {
		$icons = np_circuit_icons();
		if ( ! isset( $icons[ $name ] ) ) {
			return '';
		}
		return sprintf(
			'<svg class="np-svg np-svg--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
			esc_attr( $name ),
			absint( $size ),
			$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput -- static trusted paths defined below.
		);
	}
endif;

if ( ! function_exists( 'np_circuit_icons' ) ) :
	/**
	 * Icon path registry.
	 *
	 * @return array
	 */
	function np_circuit_icons() {
		return array(
			'cart'        => '<circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.3 12.4a1.8 1.8 0 0 0 1.8 1.5h9.4a1.8 1.8 0 0 0 1.8-1.4L22 7.5H6"/>',
			'user'        => '<path d="M20 21v-2a5 5 0 0 0-5-5H9a5 5 0 0 0-5 5v2"/><circle cx="12" cy="7" r="4"/>',
			'search'      => '<circle cx="11" cy="11" r="7.5"/><path d="m21 21-4.3-4.3"/>',
			'whatsapp'    => '<path fill="currentColor" stroke="none" d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.13-.41-2.15-1.32-.79-.7-1.32-1.57-1.48-1.87-.15-.3-.02-.46.13-.6.13-.14.3-.35.45-.53.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.5 0 1.47 1.07 2.9 1.22 3.1.15.2 2.1 3.2 5.1 4.49.71.3 1.27.49 1.7.63.72.23 1.37.2 1.88.12.58-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35zM12.04 21.5h-.01a9.4 9.4 0 0 1-4.79-1.31l-.34-.2-3.56.93.95-3.47-.22-.36a9.37 9.37 0 0 1-1.44-5c0-5.18 4.22-9.4 9.42-9.4a9.35 9.35 0 0 1 6.65 2.76 9.32 9.32 0 0 1 2.76 6.65c0 5.18-4.23 9.4-9.4 9.4zM20.1 3.89A11.3 11.3 0 0 0 12.04.5C5.8.5.7 5.6.7 11.85c0 2 .52 3.95 1.51 5.67L.6 23.5l6.13-1.6a11.3 11.3 0 0 0 5.39 1.37h.01c6.24 0 11.34-5.1 11.34-11.35 0-3.02-1.18-5.87-3.33-8z"/>',
			'phone'       => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.13.96.36 1.9.7 2.8a2 2 0 0 1-.45 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.45c.9.34 1.84.57 2.8.7A2 2 0 0 1 22 16.9z"/>',
			'mail'        => '<rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 7 9 6 9-6"/>',
			'pin'         => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
			'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
			'truck'       => '<path d="M1 4h14v11H1z"/><path d="M15 8h4l4 4v3h-8"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/>',
			'shield'      => '<path d="M12 2 4 5.5V11c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5.5z"/><path d="m9 12 2 2 4-4.5"/>',
			'refresh'     => '<path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/>',
			'lock'        => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
			'chat'        => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9 9 0 0 1-3.9-.9L3 20.5l1.6-4.4a8.2 8.2 0 0 1-1.1-4.2 8.4 8.4 0 0 1 9-8.4 8.4 8.4 0 0 1 8.5 8z"/>',
			'grid'        => '<rect x="3" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.5"/>',
			'burger'      => '<path d="M4 6h16M4 12h16M4 18h16"/>',
			'close'       => '<path d="M18 6 6 18M6 6l12 12"/>',
			'chevron-d'   => '<path d="m6 9 6 6 6-6"/>',
			'chevron-r'   => '<path d="m9 6 6 6-6 6"/>',
			'chevron-l'   => '<path d="m15 6-6 6 6 6"/>',
			'arrow-r'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'arrow-u'     => '<path d="M12 19V5M6 11l6-6 6 6"/>',
			'heart'       => '<path d="M20.8 5.6a5 5 0 0 0-7.1 0L12 7.3l-1.7-1.7a5 5 0 0 0-7.1 7.1l8.8 8.8 8.8-8.8a5 5 0 0 0 0-7.1z"/>',
			'eye'         => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
			'star'        => '<path fill="currentColor" stroke="none" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01z"/>',
			'bolt'        => '<path d="M13 2 3 14h7l-1 8 11-13h-7z"/>',
			'cpu'         => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
			'laptop'      => '<rect x="4" y="5" width="16" height="11" rx="1.5"/><path d="M2 19h20"/>',
			'monitor'     => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/>',
			'keyboard'    => '<rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M6 14h12"/>',
			'camera'      => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
			'mouse'      => '<rect x="6" y="2.5" width="12" height="19" rx="6"/><path d="M12 6.5v4"/>',
			'printer'     => '<path d="M6 9V3h12v6"/><rect x="2" y="9" width="20" height="8" rx="2"/><path d="M6 14h12v7H6z"/>',
			'drive'       => '<rect x="2" y="13" width="20" height="8" rx="2"/><path d="m6.5 13 2.6-8.4a2 2 0 0 1 1.9-1.4h2a2 2 0 0 1 1.9 1.4L17.5 13"/><circle cx="18" cy="17" r="1"/>',
			'wifi'        => '<path d="M5 12.5a10 10 0 0 1 14 0M8.5 16a5.5 5.5 0 0 1 7 0"/><circle cx="12" cy="19.5" r="1" fill="currentColor"/><path d="M2 9a15 15 0 0 1 20 0"/>',
			'tag'         => '<path d="M20.6 13.4 12 22l-9-9V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.3" fill="currentColor"/>',
			'check'       => '<path d="m4 12.5 5 5L20 6.5"/>',
			'check-c'     => '<circle cx="12" cy="12" r="9.5"/><path d="m8 12.2 2.8 2.8L16.4 9"/>',
			'box'         => '<path d="m21 8-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>',
			'fire'        => '<path d="M12 22c4.4 0 8-2.9 8-7 0-5.4-6-6.5-5-13-3.8 1.4-9 5.6-9 10.9C6 19.1 7.6 22 12 22z"/><path d="M12 22c-1.7 0-3-1.4-3-3.1 0-2.3 3-3.1 3-6 1.5 1.4 4.5 2.6 4.5 5.7 0 1.9-1.6 3.4-4.5 3.4z"/>',
			'fb'          => '<path fill="currentColor" stroke="none" d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H7v3h3v7h3v-7h3l1-3h-4v-2c0-.6.4-1 1-1z"/>',
			'ig'          => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/>',
			'yt'          => '<path fill="currentColor" stroke="none" d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8zM10 15.5v-7l6 3.5z"/>',
			'tiktok'      => '<path fill="currentColor" stroke="none" d="M16.6 2h-3v13.2a2.9 2.9 0 1 1-2.1-2.8V9.3a6.2 6.2 0 1 0 5.1 6.1V8.9a7.2 7.2 0 0 0 4 1.2V7.1a4.2 4.2 0 0 1-4-4.2z"/>',
			'calendar'    => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
			'logout'      => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
			'package'     => '<path d="M16.5 9.4 7.5 4.2"/><path d="m21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>',
			'card'        => '<rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/>',
			'hand-money'  => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 12h.01M18 12h.01"/>',
			'headset'     => '<path d="M3 14v-2a9 9 0 0 1 18 0v2"/><path d="M21 15a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2zM3 15a2 2 0 0 0 2 2h1v-5H5a2 2 0 0 0-2 2z"/><path d="M20 17v1a3 3 0 0 1-3 3h-3"/>',
		);
	}
endif;
