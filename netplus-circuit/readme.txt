=== NetPlus Circuit ===
Contributors: netpluscomputers
Tags: e-commerce, blog, custom-colors, custom-logo, custom-menu, featured-images, translation-ready, theme-options, grid-layout
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A modern WooCommerce theme for Sri Lankan computer & electronics stores, with floating WhatsApp support, anti-spam hardening and role-aware accounts.

== Description ==

NetPlus Circuit is a custom-coded WordPress theme designed for NetPlus Computers (Pvt) Ltd —
an online store selling laptop & desktop spare parts (keyboards, batteries, motherboards,
webcams, screens, fans, adapters…) with island-wide delivery, plus a tech blog.

Design influences:
* Clean product grids & category tiles (mdcomputers.lk)
* Tech-blog cards & detailed specification tables (alphatronic.lk)
* Promotional banner sections & deal countdowns (scionelectronics.com)
* Simplified, bot-protected checkout flow (duino.lk)

= Key features =
* Floating WhatsApp button on every page (bottom-right) with greeting bubble
* "Order via WhatsApp" button on every product (pre-filled with product name, price & link)
* Homepage: hero slider, trust bar, category tiles, tabbed product sections
  (New / Featured / Best sellers / Sale), promo banners, Deal of the Week with
  live countdown + stock bar, blog preview, newsletter, WhatsApp support strip
* Shop: sidebar filters, sale % / NEW / low-stock badges, star ratings, AJAX add-to-cart
  with toast notifications and a live header mini-cart
* Product page: highlights list, Specifications tab (meta box + attributes merged),
  Delivery & Warranty tab, assurance chips, related products
* Simplified 2-column checkout with Cash-on-Delivery friendly fields
* Blog: card layout, reading time, author box, related posts, [np_specs] table shortcode
* Accounts: role badges (Admin / Manager / Staff / Customer), role-based login redirects,
  branded & hardened login screen
* Built-in anti-spam: comment honeypot + timing trap, checkout honeypot + timing trap +
  IP rate limit, contact-form protection, newsletter protection
* Hardening: XML-RPC off, version hiding, REST user-enumeration block, ?author= scan block,
  security headers, uploads PHP-execution block, generic login errors
* Elementor friendly: works as-is, or hand header/footer over to Elementor / UAE
* Customizer-driven: colors, hero slides, promos, deal, contact details, footer, security toggles
* LKR-aware price display (hides ".00" on whole numbers)

= Companion plugins (already on your site) =
WooCommerce, Elementor, ShopEngine, Ultimate Addons for Elementor, Essential Addons,
Rank Math SEO, Pods, Secure Custom Fields, User Role Editor, Limit Login Attempts Reloaded,
NinjaFirewall, WPS Hide Login.

== Installation ==

1. In Local (or on cPanel), unzip this theme into wp-content/themes/ so you get
   wp-content/themes/netplus-circuit/
2. In WP Admin go to Appearance -> Themes and activate "NetPlus Circuit".
3. Follow INSTALL.md (included in the theme folder) for the full setup +
   security checklist.

== Frequently Asked Questions ==

= Where do I set my WhatsApp number? =
Customizer -> WhatsApp & Floating Buttons. Enter digits with country code, e.g. 94771234567.

= How do I change the homepage deal product? =
Customizer -> Home Page Sections -> Deal of the Week (enter a product ID, or leave 0
to auto-pick the biggest discount).

= Can I still design pages with Elementor? =
Yes. Build any page with Elementor as usual. If you want an Elementor-built header/footer,
set Customizer -> Header & Top Bar -> Header Behaviour -> "Built with Elementor / UAE".

= Where are newsletter subscribers stored? =
Settings -> NetPlus Subscribers (with CSV export).

== Changelog ==

= 1.1.0 =
* Built-in PayHere gateway (Visa/Master/Amex/eZ Cash/mCash/FRIMI) with hash-verified server notifications
* Cash on Delivery + Bank Transfer enabled by default; footer payment chips reflect enabled gateways
* Login-gated checkout (guests can cart, must sign in to buy) + forced customer role on signup + quick role links
* New palette (graphite/crimson/amber) + 4 customizable font roles (display/UI/body/mono)
* Homepage re-layout to match netpluscomputers.lk: flash sale carousel first, 3 promo banners, discount ribbon, FAQ accordion
* Product carousels, scroll-reveal & micro animations (all toggleable)
* One-click demo catalogue importer with real products/prices/images from netpluscomputers.lk

= 1.0.0 =
* Initial release: full storefront, blog, security hardening, WhatsApp integration.

== Credits ==

* Icons: inline SVG (Feather-style paths, MIT)
* Fonts: Google Fonts — Sora & Inter (SIL Open Font License)
* WooCommerce hook patterns: WooCommerce (GPLv3)
