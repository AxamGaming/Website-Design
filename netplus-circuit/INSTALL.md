# NetPlus Circuit — Installation & Setup Guide

A custom-coded WordPress theme for **NetPlus Computers (Pvt) Ltd** — designed for selling
laptop/PC parts online in Sri Lanka with a tech blog, WhatsApp support and serious anti-bot
protection.

---

## 1. Install (Local by FlyPress)

1. Copy the `netplus-circuit` folder into:
   ```
   /home/jachien/Local Sites/netplus-computers/app/public/wp-content/themes/
   ```
2. Open your local site's WP Admin → **Appearance → Themes** → activate **NetPlus Circuit**.
3. On activation the theme automatically:
   - Creates roles **Store Manager** and **Store Staff** (editable via User Role Editor)
   - Creates missing pages: *About Us, Contact (with protected form), FAQ, Blog*, and
     WooCommerce *Shop / Cart / Checkout / My Account* pages if absent
   - Creates the newsletter table (`wp_np_newsletter`)
   - Writes an `.htaccess` into `wp-content/uploads/` that blocks PHP execution there
     (a classic malware entry point)

## 2. First 10 minutes (Customizer)

Go to **Appearance → Customize**:

| Panel | What to set |
|---|---|
| **WhatsApp & Floating Buttons** | Your WhatsApp number, digits with country code: `94771234567`. This powers the floating button, header chip, product "Order via WhatsApp", deal & 404 CTAs. |
| **Header & Top Bar** | Hotline, email, opening hours, announcement bar text |
| **Home Page Sections** | Hero slides (3), category tiles, product tabs, promo banners, Deal of the Week (product ID or `0` = auto biggest discount + countdown end date), section titles |
| **Footer & Contact** | About text, address, Google Maps embed URL, payment methods, copyright |
| **Social Links** | Facebook / Instagram / YouTube / TikTok |
| **Theme Colors** | Accent blue, dark navy, deals orange — the whole site re-skins |
| **Security & Anti-Spam** | All toggles default ON (recommended) |
| **WooCommerce Display** | Products per row, related count, hide `.00` prices, "Delivery & Warranty" tab text |
| **Site Identity** | Upload your logo (replaces the text logo) |

Then set menus under **Appearance → Menus**:
- **Primary Menu (dark nav bar)** — Home, Shop, Deals, Blog, About, Contact…
- **Footer Quick Links**, **Top Bar Links** (optional)

## 3. WooCommerce settings that matter

- **WooCommerce → Settings → General**: store address Sri Lanka, selling location(s),
  currency **LKR (රු / Rs.)**.
- **Settings → Products → Inventory**: enable "low stock threshold" (e.g. 3) to trigger
  the orange *Only few left* badges.
- **Settings → Payments**: enable **Cash on Delivery** + **Bank Transfer (BACS)**;
  add PayHere/koko when you have merchant accounts.
- **Settings → Shipping**: create zones — e.g. *Colombo district* (flat Rs 350) and
  *Other districts* (flat Rs 400–500), plus *Free shipping* over Rs 20,000 if you like.
- **Settings → Accounts & Privacy**: ✅ allow customers to create an account on checkout
  and on My Account; ✅ allow guest checkout (COD customers expect it).
- Each product gets a **NetPlus Product Extras** meta box (edit product screen):
  - *Key highlights* — one per line, shows as green ticks under the short description
  - *Specification table* — `Label | Value` per line → renders in the **Specifications** tab
    (merged with WooCommerce attributes automatically)

## 4. The product card recipe (for your parts)

For laptop parts like your Dell keyboards/batteries, put the **compatibility list** in the
short description and the numbers in the spec table:

```
Brand | Dell
Part Number | 00WNM6
Compatible Models | Inspiron 3501, 3502, 3505, 5584, 5590, 5593, 7590, 7791
Layout | US Backlit
Condition | Brand New
Warranty | 6 months replacement
```

## 5. Blog with spec tables (Alphatronic style)

In any post/page use the shortcode:

```
[np_specs title="Comparison at a glance"]
Model | Dell Inspiron 3558
Battery | 40Wh 4-cell
Weight | 2.3 kg
[/np_specs]
```

Other shortcodes: `[np_whatsapp label="Chat now" message="Hi!"]`,
`[np_contact_form]`, `[np_trust_bar]`, `[np_sections]` (full homepage sections on any page).

## 6. Anti-bot & security (your #1 pain point — read this)

The theme itself ships with:

| Layer | What it does |
|---|---|
| Comment honeypot + timing trap | Bots fill the invisible field or submit < 4s → silently marked **spam** (they see fake success, learn nothing) |
| Checkout honeypot + timing trap + IP rate limit | Kills fake-order floods; guests limited to 10 checkout attempts / 10 min |
| Contact form & newsletter protection | Honeypot, signed timing token, per-IP rate limits |
| XML-RPC disabled | Closes the most abused WP attack vector |
| REST user enumeration blocked + `?author=` redirect | Bots can't harvest your usernames |
| Generic login errors | Doesn't reveal which usernames exist |
| Security headers | `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` |
| Uploads `.htaccess` | Blocks PHP execution inside `wp-content/uploads` |
| Branded hardened login screen | Pair with **WPS Hide Login** (already installed) — set a custom login URL |

**Do these too (5 minutes, huge impact):**

1. **Your existing plugins** — keep them updated and configured:
   - *NinjaFirewall* → enable "FileGuard" + full WAF mode
   - *Limit Login Attempts Reloaded* → 3 retries, 20 min lockout, GD captcha after 2 retries
   - *WPS Hide Login* → set login slug to something only you know (e.g. `/np-staff-door`)
2. **Cloudflare (free tier)** in front of netpluscomputers.lk:
   - DNS proxied (orange cloud) → hides your server IP
   - Security Level: **High**, Bot Fight Mode: **ON**
   - WAF managed rules + rate limiting rule on `/wp-login.php`, `/wp-admin/`, `/checkout/`, `/wp-json/`
   - "Under Attack Mode" during an active spam wave
   - After enabling, the theme reads `HTTP_CF_CONNECTING_IP` so rate limits use real visitor IPs.
3. **wp-config.php** (cPanel → File Manager) add above `/* That's all, stop editing! */`:
   ```php
   define( 'DISALLOW_FILE_EDIT', true );   // no theme/plugin editor in dashboard
   define( 'WP_AUTO_UPDATE_CORE', 'minor' );
   define( 'DISALLOW_FILE_MODS', false );  // set true once site is stable to lock installs
   ```
4. **Strong passwords + 2FA** for admin accounts (Limit Login Attempts Pro or Wordfence
   Login Security offers free TOTP 2FA). Delete any unused admin accounts.
5. **Backups**: Local → right-click site → Backup; on cPanel use JetBackup weekly +
   download a copy off-server monthly.
6. **Keep everything updated** (core, WooCommerce, Elementor, all plugins). Outdated
   plugins — not themes — are how most WP stores get infected.
7. Optional but recommended for checkout spam waves: **Cloudflare Turnstile** plugin (free,
   privacy-friendly captcha) on login/checkout if bots ever target you again.

## 7. Roles & accounts

| Role | Who | Can do |
|---|---|---|
| Administrator | You | Everything |
| **Store Manager** (created by theme) | Your manager | Products, orders, coupons, reports, blog posts — *no* WP settings/plugins |
| **Store Staff** (created by theme) | Pack-and-ship staff | Update orders & products, moderate comments |
| Customer | Shoppers | Account, orders, addresses, reviews, wishlist |

- Staff land on **wp-admin** after login; customers land on **My Account**.
- The header account menu shows a role chip (Admin/Manager/Customer).
- Fine-tune capabilities with your **User Role Editor** plugin.

## 8. Elementor / ShopEngine

- The theme works out-of-the-box with coded header/footer.
- Want to design the header/footer in Elementor instead? Build them with the
  **Ultimate Addons (Header Footer) builder**, then set
  *Customizer → Header & Top Bar → Header Behaviour → "Built with Elementor / UAE"*.
  The theme then stops rendering its own header/footer and lets the builder take over.
- Elementor **Pro** theme locations are also registered (header/footer/single/archive).
- **ShopEngine** pages (custom product/cart/checkout templates) automatically override
  the theme's WooCommerce rendering — no conflict.
- Any page can be built fully in Elementor; use the **Full Width (no sidebar)** page
  template for clean canvas.

## 9. Deploying to cPanel later

1. Zip the `netplus-circuit` folder → cPanel → File Manager → `public_html/wp-content/themes/` → Upload → Extract.
2. Activate the theme there, re-enter Customizer values (or use Customizer export/import), re-save **Settings → Permalinks**.
3. Set PHP to 8.1+ and raise limits in cPanel → *Select PHP Version → Options*:
   `memory_limit 256M`, `upload_max_filesize 64M`, `post_max_size 64M`, `max_execution_time 300`.
4. Force HTTPS (cPanel → SSL/TLS Status → AutoSSL, then enable "Force HTTPS Redirect").
5. phpMyAdmin: same DB user as before — the theme stores options in the normal WP tables
   plus one custom table `wp_np_newsletter`.

## 10. Performance tips (shared hosting)

- Smush/WebP images before uploading product photos (700–1000px is plenty).
- The theme lazy-loads images and inlines only two Google fonts (Sora + Inter).
- Install a cache plugin (LiteSpeed Cache if your cPanel host uses LiteSpeed — most
  Sri Lankan hosts do; else WP Super Cache). Exclude *Cart, Checkout, My Account* pages
  from caching (WooCommerce does this automatically).
- Keep plugin count lean — you don't need Pods **and** Secure Custom Fields **and**
  ShopEngine's builders all active; deactivate what you don't use (less attack surface).

---

### File map

```
netplus-circuit/
├── style.css               ← entire design system (tokens, components, Woo styles)
├── functions.php           ← bootstrap
├── header.php / footer.php ← coded header (topbar, search, cart, mega-departments) & footer
├── front-page.php          ← coded homepage sections
├── index/single/page/archive/search/404/woocommerce/comments/sidebar/searchform.php
├── inc/                    ← setup, assets, customizer, woocommerce, security,
│                             meta box, shortcodes, newsletter, elementor, activation
├── template-parts/         ← hero, trust bar, category tiles, product tabs, promos,
│                             deal countdown, blog preview, newsletter, WhatsApp float…
├── page-templates/         ← Full Width / Home Sections / Contact
└── assets/                 ← main.js, login.css, editor-style.css, customizer-preview.js
```

Enjoy the rebuild — made for NetPlus, hosted in Sri Lanka. 🇱🇰

---

# v1.1 Update — Payments, Fonts, Layout, Animations, Demo Content

## A. Payments (PayHere cards + Cash on Delivery + Bank Transfer)

All three live in **WooCommerce → Settings → Payments** — no code:

| Method | How to enable |
|---|---|
| **Cash on Delivery** | Enabled by default on activation. Toggle + rename there. |
| **Bank Transfer** | Enabled by default. Add your bank account details in its settings. |
| **Credit / Debit Card (PayHere)** | Built into the theme. Click *Set up*: enter **Merchant ID** + **Merchant Secret** from your PayHere dashboard (payhere.lk → Settings → Domains & Credentials), tick **Test mode** first with a sandbox account. Accepts Visa / Master / Amex / eZ Cash / mCash / FRIMI. |

How it works (same model mdcomputers/alphatronic use): checkout redirects the shopper to
PayHere's secure page; PayHere then confirms the payment server-to-server on a
hash-verified notify URL and the order flips to *Paid* automatically. The footer payment
chips update themselves from whichever gateways you have enabled.

> If you prefer the official *PayHere for WooCommerce* plugin instead, it co-exists fine —
> just disable the theme one (or vice-versa).

## B. Purchase rules (who may buy)

**Customizer → Checkout Rules**
- ✅ *Require sign-in to checkout* (ON by default): guests can browse and add to cart,
  but clicking Checkout sends them to Login/Register and returns them to checkout after
  signing in. The cart page shows your custom notice + sign-in button.
- Every new signup is forced to the **Customer** role — registration can never self-assign
  staff rights.
- **You** decide promotions: *Users* screen → quick links **Make Manager / Make Admin /
  Make Customer** on each row (or edit user → Role, or User Role Editor).
  Roles: Administrator (you) · Store Manager · Store Staff · Customer.

## C. New look — graphite + crimson + amber, 4 font roles

- **Customizer → Theme Colors**: 7 color pickers (accent, hover, deals, dark surface,
  sale, success, page background). Default palette: graphite `#1b1f27`, crimson `#d90429`,
  amber `#f77f00` — chosen to suit PC/laptop parts (ROG-style energy + warm SL retail tones).
- **Customizer → Typography**: four independent font roles with a curated Google-Fonts
  picker (+ "custom" field for any family):
  *Display* (Chakra Petch) → hero/section titles, prices, countdown ·
  *UI* (Rajdhani) → buttons, badges, nav ·
  *Body* (Inter) ·
  *Mono* (JetBrains Mono) → SKUs & spec values. Plus base font size.

## D. Homepage now mirrors netpluscomputers.lk

Order: **Flash Sale band (countdown + product carousel)** → 3 promo banners → trust bar →
discount ribbon → New Arrivals carousel → On-Sale carousel → tabbed product grid →
newsletter + WhatsApp strip → **FAQ accordion** → latest articles.
The old hero slider still exists but is OFF by default (your old site has none) — enable
any slide in *Customizer → Home Page Sections → Hero Slider*.

Everything on it is editable without code:
- Flash sale = products you mark *On sale* (or set a countdown end date in the Deal section)
- Promo banners 1–3: image, eyebrow, title, subtitle, button, link, tint
- Discount ribbon text + link
- FAQ items: `Question | Answer` one per line
- Carousels ON/OFF (*Show product rows as swipeable carousels*)

## E. Animations (Customizer → Motion & Animations)

Scroll-reveal on every section, card hover lift + image zoom, button shine sweep,
flash-sale icon pulse, countdown digit tick, smooth carousel snap-scrolling,
hero autoplay speed. All toggleable; automatically disabled for visitors whose devices
request reduced motion.

## F. Your old catalogue, one click

**Appearance → NetPlus Demo Content** → *Import products + download images*.
Imports 16 real products from netpluscomputers.lk (names, SKUs, LKR prices, sale prices,
descriptions, spec tables, photos into your Media Library) and creates the categories.
Safe to re-run (duplicates skipped). Products/blogs/pages afterwards are added exactly as
before: **Products → Add New**, **Posts → Add New**, Elementor on any page.
