# NetPlus Computers - Secure E-Commerce Rebuild Plan

## Project Overview
Rebuilding netpluscomputers.lk with enhanced security, modern aesthetics, and full content management flexibility while maintaining the easy-to-use WordPress interface you're familiar with.

## Technology Stack

### Core Platform
- **WordPress 6.x** (Latest stable version)
- **WooCommerce** (E-commerce engine)
- **Elementor Pro** (Drag-and-drop page builder for layout control)

### Security Layer (Critical - Addressing Previous Bot Issues)
- **Wordfence Security Premium** - Firewall & malware scanner
- **Cloudflare Pro** - WAF, DDoS protection, bot management
- **Google reCAPTCHA v3** - Invisible spam protection on all forms
- **WP Cerber Security** - Additional hardening layer
- **Two-Factor Authentication** - For admin/manager accounts
- **Loginizer** - Limit login attempts, block suspicious IPs

### Essential Plugins
- **WooCommerce** - Product management, cart, checkout
- **Elementor Pro** - Visual page builder (move products, change grids, resize icons)
- **WooCommerce Product Filter** - Advanced filtering for computer parts
- **YITH WooCommerce Wishlist** - Customer wishlists
- **Customer Reviews for WooCommerce** - Photo reviews, verified buyer badges
- **Joinchat** or **Click to Chat** - WhatsApp integration
- **WP Rocket** - Performance optimization
- **Smush Pro** - Image optimization
- **UpdraftPlus Premium** - Automated backups
- **Role Editor** - Custom user roles (Admin, Manager, Customer)
- **WooCommerce Multistep Checkout** - Improved checkout experience

### Sri Lankan Integrations
- **PayHere** or **DirectPay** - Local payment gateway (Visa, Mastercard, Genie, eZ Cash)
- **PickMe Delivery** or **PromptX** - Local delivery integration
- **Sri Lanka Post** - Shipping rate calculator

## User Roles & Permissions

### Administrator (You)
- Full access to all settings
- Plugin/theme management
- User role assignment
- Financial reports
- System configuration

### Manager (Staff)
- Add/edit/delete products
- Manage orders (view, update status, print invoices)
- Respond to reviews
- Publish blog posts
- View sales reports
- **NO access to**: plugin installation, user deletion, payment settings

### Customer (Public Users)
- Browse products
- Add to cart & checkout
- Leave reviews (after purchase verification)
- Track orders
- Manage profile
- View order history

## Security Implementation Checklist

### Before Migration
1. [ ] Backup existing website completely
2. [ ] Export all products, customers, orders from old site
3. [ ] Document current URL structure for redirects

### Fresh Installation Steps
1. [ ] Clean WordPress install (no carryover from old site)
2. [ ] Install SSL certificate (force HTTPS)
3. [ ] Configure Cloudflare DNS with WAF rules
4. [ ] Set up database with strong credentials
5. [ ] Change default wp-admin URL (e.g., /np-admin-secure)
6. [ ] Disable XML-RPC
7. [ ] Remove wp-version from headers
8. [ ] Implement file permission hardening

### Ongoing Security Measures
1. [ ] Daily automated backups (off-site storage)
2. [ ] Weekly malware scans
3. [ ] Monthly security audits
4. [ ] Automatic plugin/theme updates (with staging test)
5. [ ] IP whitelisting for admin access (optional)
6. [ ] Database prefix change from wp_ to custom
7. [ ] Disable file editing in dashboard
8. [ ] Implement Content Security Policy (CSP) headers

## Design & Layout Features

### Homepage Elements (Customizable via Elementor)
- Hero banner with promotions
- Featured product categories (Motherboards, Keyboards, Webcams, etc.)
- Best sellers grid
- New arrivals section
- Blog post previews
- Trust badges (secure payment, warranty, delivery)
- WhatsApp floating button

### Product Page Features
- Multiple high-res images with zoom
- Detailed specifications table
- Stock availability indicator
- Related products carousel
- Customer reviews with ratings
- WhatsApp "Ask Question" button
- Add to cart / Buy now buttons
- Delivery estimator for Sri Lanka regions

### Category Pages
- Filter by: Brand, Price, Specifications, Availability
- Grid/List view toggle
- Sort by: Price, Popularity, Newest, Rating
- Pagination or infinite scroll

### Blog Section
- Tech news & updates
- Product guides & tutorials
- Category filtering
- Search functionality
- Social sharing buttons
- Newsletter signup

## Admin Panel Capabilities

### Product Management (No Coding Required)
- Add/Edit products through visual form
- Upload multiple images (drag & drop)
- Set prices, sale prices, stock quantities
- Create product variations (e.g., different RAM sizes)
- Assign categories & tags
- Write descriptions with visual editor
- Set SEO meta titles & descriptions
- Schedule product publishing
- Bulk import/export via CSV

### Layout Control (Elementor Features)
- Drag any element anywhere on pages
- Change product grid columns (2, 3, 4, 5)
- Resize product cards, icons, buttons
- Modify colors, fonts, spacing globally
- Create custom headers/footers
- Build promotional landing pages
- Mobile-responsive editing (separate controls for mobile/tablet)
- Save sections as templates for reuse

### Order Management
- View all orders with filters
- Update order status (Processing → Shipped → Delivered)
- Print invoices & packing slips
- Send automated email notifications
- Process refunds
- Export order data

### Customer Management
- View customer list
- See order history per customer
- Manual order creation for phone orders
- Customer role assignment

### Analytics Dashboard
- Sales reports (daily, weekly, monthly)
- Top-selling products
- Customer acquisition sources
- Cart abandonment rate
- Revenue by category

## Migration Strategy

### Phase 1: Preparation (Week 1)
- Set up staging environment
- Install fresh WordPress + security plugins
- Configure Cloudflare
- Test security measures

### Phase 2: Data Migration (Week 2)
- Import products from old site
- Migrate customer accounts (with password reset emails)
- Transfer blog posts
- Set up 301 redirects for old URLs

### Phase 3: Design & Customization (Week 3)
- Build homepage with Elementor
- Create product page template
- Design category pages
- Build blog layout
- Configure header/footer
- Set up WhatsApp integration

### Phase 4: Testing (Week 4)
- Test all user flows (browse, add to cart, checkout)
- Verify payment gateway integration
- Test on multiple devices/browsers
- Security penetration testing
- Load speed optimization
- Form spam testing

### Phase 5: Launch (Week 5)
- Final backup of old site
- Point domain to new site
- Monitor closely for 48 hours
- Submit sitemap to Google
- Announce relaunch to customers

## Performance Optimization

### Speed Targets
- Homepage load: < 2 seconds
- Product page load: < 2.5 seconds
- Mobile performance score: 90+ (Google PageSpeed)

### Optimization Techniques
- WebP image format conversion
- Lazy loading for images
- Minified CSS/JS
- Browser caching
- CDN via Cloudflare
- Database optimization (weekly cleanup)
- Object caching (Redis/Memcached if hosting allows)

## Budget Estimate (Annual Costs)

| Item | Cost (USD) | Notes |
|------|-----------|-------|
| WordPress | Free | Open source |
| WooCommerce | Free | Core plugin |
| Elementor Pro | $59/year | Single site license |
| Wordfence Premium | $99/year | Security firewall |
| Cloudflare Pro | $240/year | $20/month |
| WP Rocket | $59/year | Caching plugin |
| UpdraftPlus Premium | $70/year | Backups |
| PayHere Setup | ~$50 one-time | Sri Lankan payment gateway |
| Theme (Optional) | $59 one-time | If not building from scratch |
| **Total First Year** | **~$576** | Excluding developer time |
| **Total Subsequent Years** | **~$526** | Renewals only |

## Maintenance Requirements

### Weekly Tasks
- Check security scan reports
- Review failed login attempts
- Verify backups completed successfully
- Check for plugin updates

### Monthly Tasks
- Update all plugins (after testing on staging)
- Review and respond to customer reviews
- Analyze sales reports
- Test checkout process
- Clean database (transients, spam comments)

### Quarterly Tasks
- Full security audit
- Performance review
- Content audit (update old blog posts)
- Review user roles and permissions
- Test disaster recovery (restore from backup)

## Success Metrics

### Security Goals
- Zero successful bot attacks
- No malware infections
- 100% uptime (via Cloudflare)
- All forms protected by reCAPTCHA

### Business Goals
- 40% faster page load than previous site
- Mobile-friendly score: 95+
- Conversion rate improvement: 25%
- Reduced cart abandonment: 30%
- Customer review collection: 50+ reviews in first 3 months

## Next Steps

1. **Confirm this plan meets your requirements**
2. **Purchase required plugin licenses**
3. **Set up staging environment on your cPanel**
4. **Begin Phase 1: Preparation**
5. **Schedule migration during low-traffic period**

---

## Contact Information for Sri Lankan Services

### Payment Gateways
- **PayHere**: https://payhere.lk/ | support@payhere.lk
- **DirectPay**: https://directpay.lk/ | info@directpay.lk
- **Genie**: https://genie.lk/ (for e-wallet integration)

### Delivery Partners
- **PickMe Delivery**: https://pickme.lk/delivery
- **PromptX**: https://promptx.lk/
- **Sri Lanka Post**: https://www.slpost.gov.lk/

### Hosting Optimization
Since you're on cPanel, ensure your hosting plan includes:
- PHP 8.1 or higher
- MySQL 8.0 or MariaDB 10.5+
- Minimum 2GB RAM allocation for WordPress
- SSD storage
- Daily backups (in addition to UpdraftPlus)

---

**Document Version**: 1.0  
**Created**: 2024  
**For**: NetPlus Computers (netpluscomputers.lk)  
**Status**: Ready for Implementation
