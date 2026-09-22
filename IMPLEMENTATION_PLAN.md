# NetPlus Computers - Website Rebuild Implementation Plan
**Strategy:** Local-First Development → Secure Migration → Live Launch
**Goal:** Rebuild netpluscomputers.lk with a 100% Free WordPress Stack, solving previous bot spam issues while maintaining easy product/blog management.

## 🎯 Core Requirements Met
- [x] **Safe Development:** Build locally first; zero risk to live site.
- [x] **Easy Management:** Visual product/blog editing (no coding).
- [x] **Security:** Enterprise-grade bot protection (Cloudflare + NinjaFirewall).
- [x] **Design Control:** Drag-and-drop layouts, grid customization, mobile responsive.
- [x] **Features:** Multi-role accounts, WhatsApp chat, Reviews, Sri Lankan Payments.
- [x] **Cost:** ~25,000 LKR/year (Hosting only; all plugins FREE).

---

## 🛠️ Complete Free Plugin Stack (Local & Live)

### 🎨 Page Building & Design
| Plugin | Purpose | Cost |
|--------|---------|------|
| **Elementor Free** | Core drag-and-drop canvas | Free |
| **Header & Footer Builder** | Custom headers/footers | Free |
| **Essential Addons for Elementor** | Advanced widgets, grids, motion effects | Free |
| **ShopEngine** | WooCommerce page builder (Cart, Checkout, Product) | Free |

### 🗃️ Data & Fields
| Plugin | Purpose | Cost |
|--------|---------|------|
| **Pods Framework** | Custom post types, complex product specs | Free |
| **Secure Custom Fields (SCF)** | Simple spec tables (ACF alternative) | Free |

### 👥 Roles & Security
| Plugin | Purpose | Cost |
|--------|---------|------|
| **User Role Editor** | Custom Admin/Manager/Customer roles | Free |
| **NinjaFirewall WP Edition** | Server-level WAF (Full Mode) | Free |
| **WPS Hide Login** | Hide admin URL | Free |
| **Limit Login Attempts** | Brute force protection | Free |

---

## 📅 Implementation Timeline (5 Weeks)

### Week 1: Local Setup & Security Simulation
- [ ] **Install LocalWP** on your computer.
- [ ] **Create Local Site** (`NetPlus-New`).
- [ ] **Install Security Stack:** NinjaFirewall (Full WAF), WPS Hide Login, Limit Login Attempts.
- [ ] **Test Security:** Verify hidden login URL and firewall rules locally.
- [ ] **Install Design Stack:** Elementor, ShopEngine, Essential Addons, Pods.

### Week 2: Local Design & Content Build
- [ ] **Configure WooCommerce:** Set currency to LKR, add shipping zones (Colombo, Kandy, etc.).
- [ ] **Build Header/Footer:** Logo, Nav, Cart Icon, WhatsApp Button.
- [ ] **Design Homepage:** Featured products, categories, blog section.
- [ ] **Customize Product Page:** Layout, Spec tables (using Pods), Reviews.
- [ ] **Create Dummy Content:** Add 5-10 test products and 2 blog posts.

### Week 3: Functionality & Roles
- [ ] **Setup User Roles:** Define "Manager" capabilities (edit products only).
- [ ] **Test Workflows:** 
  - Manager adds product.
  - Customer places order.
  - Admin processes order.
- [ ] **Integrate Chat:** Add Tidio or WhatsApp click-to-chat button.
- [ ] **Payment Gateway:** Install PayHere/DirectPay sandbox mode for testing.

### Week 4: Migration Preparation
- [ ] **Backup Live Site:** Download full backup of current `netpluscomputers.lk` (files + DB).
- [ ] **Create Live Database:** New DB (`netplus_new_db`) and user on cPanel.
- [ ] **Export Local Site:** Use "All-in-One WP Migration" to create export file.
- [ ] **Staging Test (Optional):** Import to `netpluscomputers.lk/new` subfolder for final client approval.

### Week 5: Go-Live & Hardening
- [ ] **Migrate to Root:** Move new site to main domain (replacing old site).
- [ ] **Cloudflare Setup:** 
  - Update Nameservers.
  - Enable "Full Strict" SSL.
  - **Create Page Rules:** Block `/wp-login.php` and `/xmlrpc.php`.
- [ ] **Final Security Check:** Verify NinjaFirewall is active on live server.
- [ ] **Submit Sitemap:** To Google Search Console.
- [ ] **Monitor:** Watch Cloudflare analytics for blocked bot attacks.

---

## 💰 Cost Breakdown (Annual)

| Item | Cost (LKR) | Notes |
|------|------------|-------|
| Domain Renewal | ~3,500 | LK Domain Registry |
| Hosting (cPanel) | ~20,000 | Existing or new provider |
| Cloudflare | 0 | Free Plan sufficient |
| Plugins | 0 | 100% Free Stack |
| **Total** | **~23,500** | **Save ~110,000 vs Premium Stack** |

---

## 🔐 Security Architecture (Bot Spam Solution)

### Layer 1: Cloudflare (DNS Level)
- **Blocks:** Bad bots, DDoS, known attackers.
- **Rule:** Block all access to `/wp-login.php` (since we use hidden URL).
- **Rule:** Block `/xmlrpc.php` completely.

### Layer 2: NinjaFirewall (Server Level)
- **Mode:** Full WAF (loads before WordPress).
- **Blocks:** SQL injection, malicious file uploads, fake Google bots.

### Layer 3: Application Level
- **Hidden Login:** `/np-secure-login-2025` (nobody knows the URL).
- **Login Limits:** Lockout after 3 failed attempts.
- **File Edit Disabled:** `DISALLOW_FILE_EDIT` in wp-config.php.

**Result:** Bots are stopped before they even reach your database.

---

## 🎨 Design Inspiration & References
*Use these sites for layout ideas, color schemes, and feature placement:*
1. **mdcomputers.lk** - Clean product grids, clear categories.
2. **alphatronic.lk** - Tech blog layout, detailed spec tables.
3. **scionelectronics.com** - Banner designs, promotional sections.
4. **duino.lk** - Simplified checkout flow, maker-focused content.

**Key Feature to Replicate:** Floating WhatsApp/Chat button on all pages (bottom-right corner).

---

## 📦 Product Management Workflow (No Coding)
1. **Add New:** Dashboard > Products > Add New.
2. **Details:** Name, Price, Description, Images (Drag & Drop).
3. **Specs:** Use "Product Specs" box (powered by Pods) to enter RAM, Socket, etc.
4. **Publish:** Click Publish. Instantly live.

**Bulk Actions:** Import/Export CSV for mass updates.

---

## 🚀 Migration Checklist (Go-Live Day)
- [ ] Old site backed up.
- [ ] New database created on cPanel.
- [ ] Local site exported.
- [ ] Imported to live server.
- [ ] Permalinks refreshed (Settings > Permalinks > Save).
- [ ] Cloudflare Nameservers updated.
- [ ] Cloudflare Page Rules active.
- [ ] SSL certificate valid.
- [ ] Test Order placed successfully.
- [ ] WhatsApp button working.

---

**Ready to start?** Open `SETUP_GUIDE_PHASE0.md` and install LocalWP today!
