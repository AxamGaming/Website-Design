# NetPlus Computers - Website Rebuild Implementation Plan
**Strategy: Local-First Development → Secure Migration**

## 🎯 Project Overview
Rebuilding netpluscomputers.lk with a **100% Free WordPress Stack**. 
- **Phase 1-4:** Build entirely on your local computer (Zero risk to live site).
- **Phase 5:** Migrate to live hosting only when approved.
- **Goal:** Solve bot spam issues, enable easy product management, and achieve Elementor-style design control without annual plugin fees.

## ✅ Core Requirements Met
- [x] **Safe Development:** No changes to live database/site until migration.
- [x] Easy product addition/editing (No coding).
- [x] Blog post management with visual editor.
- [x] Multi-role accounts (Admin, Manager, Customer).
- [x] Drag-and-drop page building (Elementor-style).
- [x] Product grid customization (columns, layouts).
- [x] WhatsApp contact integration & Live Chat.
- [x] Sri Lankan payment gateways (PayHere, DirectPay).
- [x] Enterprise-grade security (Cloudflare + NinjaFirewall).
- [x] **Zero annual plugin costs** (~25,000 LKR/year hosting only).

---

## 🛠️ Complete Free Plugin Stack (Local & Live)

### 🎨 Page Building & Design
| Plugin | Purpose | Cost |
|--------|---------|------|
| **Elementor Free** | Core drag-and-drop canvas | Free |
| **Header & Footer Builder** | Custom headers/footers | Free |
| **Essential Addons for Elementor** | Advanced widgets, grids, motion effects | Free |
| **ShopEngine** | WooCommerce builder (Cart, Checkout, Product) | Free |

### 🗃️ Custom Fields & Data
| Plugin | Purpose | Cost |
|--------|---------|------|
| **Pods Framework** | Custom post types, complex fields, relationships | Free |
| **Secure Custom Fields (SCF)** | Simple spec tables (ACF alternative) | Free |

### 👥 Roles & Security
| Plugin | Purpose | Cost |
|--------|---------|------|
| **User Role Editor** | Granular Admin/Manager permissions | Free |
| **NinjaFirewall WP Ed.** | Server-level WAF (Blocks bots before WP loads) | Free |
| **WPS Hide Login** | Hides admin URL from bots | Free |

---

## 📅 Implementation Timeline (5 Weeks)

### Week 1: Local Environment & Security (CURRENT PHASE)
- [ ] **Install LocalWP** on your computer.
- [ ] **Create Local Site** (`netplus-new.local`).
- [ ] **Install Security Plugins** (NinjaFirewall, WPS Hide Login).
- [ ] **Configure Hidden Login URL** (`/np-secure-login-2025`).
- [ ] **Verify** live site is untouched.

### Week 2: Core Installation & Design (Local)
- [ ] Install Elementor Free + Header & Footer Builder.
- [ ] Install Essential Addons (**Disable unused widgets** for speed).
- [ ] Install ShopEngine + WooCommerce.
- [ ] **Design Homepage:** Hero section, Featured Products, Categories.
- [ ] **Design Header/Footer:** Logo, Nav, WhatsApp Button, Cart Icon.
- [ ] **Build Product Templates:** Single product page layout with Spec Tables.

### Week 3: Data Architecture & Content (Local)
- [ ] Configure **Pods Framework** for Computer Parts specs (Socket, RAM, etc.).
- [ ] Create **User Roles**: Admin (Full), Manager (Products/Orders only).
- [ ] **Import Dummy Data:** Add 5-10 sample products and blog posts manually to test layout.
- [ ] Test **Mobile Responsiveness** using LocalWP preview tools.

### Week 4: Functionality & Integrations (Local)
- [ ] **Chat Integration:** Install **Tidio** (Free) or **Click-to-Chat** for WhatsApp.
  - *Reference:* Mimic floating button style from `mdcomputers.lk` and `alphatronic.lk`.
- [ ] **Review System:** Enable WooCommerce native reviews.
- [ ] **Search & Filter:** Configure product filtering by category/price.
- [ ] **Optimization:** Run performance checks (ensure <2s load time locally).

### Week 5: Migration & Go-Live
- [ ] **Backup Live Site:** Full backup of current `netpluscomputers.lk` via cPanel.
- [ ] **Migrate Local to Live:** 
  - Use **All-in-One WP Migration** or **Duplicator** plugin.
  - Export local site package.
  - Install fresh WordPress in a **subdirectory** (`netpluscomputers.lk/new`) on cPanel.
  - Import package to subdirectory.
- [ ] **Test in Subdirectory:** Verify everything works at `.lk/new`.
- [ ] **Cloudflare Setup:** 
  - Switch Nameservers to Cloudflare.
  - Add Rule: Block `/wp-login.php`.
- [ ] **Final Switch:** Move new site from `/new` to root (replacing old site) OR update DNS to point to new location.
- [ ] **Monitor:** Watch Cloudflare analytics for blocked bots.

---

## 💰 Cost Breakdown

| Item | Annual Cost (LKR) |
|------|-------------------|
| Domain renewal | ~3,500 |
| Hosting (cPanel) | ~20,000 |
| **All Plugins** | **FREE** |
| **Total** | **~23,500 LKR/year** |

**Savings:** ~110,000 LKR/year compared to premium stack.

---

## 🔐 Security Features (Bot Spam Solution)

### Multi-Layer Protection
1. **Local Development:** Bots cannot attack what isn't online yet.
2. **NinjaFirewall (Full WAF):** Stops SQL injection and malware uploads immediately upon migration.
3. **Hidden Admin URL:** Bots scanning `/wp-admin` get a 404 error.
4. **Cloudflare (Post-Migration):** Blocks 99% of bot traffic at the DNS level before it hits your server.
5. **Login Limits:** Locks out IPs after 3 failed attempts.

### Why This Solves Your Previous Problem
Your last site was attacked directly because it was exposed. This workflow builds behind a wall (your computer) and only exposes a hardened fortress to the internet when ready.

---

## 🎨 Design Control Features

### What You Can Customize (No Coding)
- ✅ **Move Elements:** Drag products, text, images anywhere.
- ✅ **Grid Control:** Switch between 2, 3, 4 columns instantly.
- ✅ **Styling:** Resize icons, buttons, fonts visually.
- ✅ **Templates:** Save custom product layouts for different categories (e.g., Motherboards vs. Keyboards).

### Design Inspiration References
Use these sites for layout ideas (to be replicated with Elementor):
- **mdcomputers.lk:** Clean category grids, prominent search.
- **alphatronic.lk:** Detailed product specs tables, sidebar filters.
- **scionelectronics.com:** Banner sliders, featured deals section.
- **duino.lk:** Simplified checkout flow, tech blog integration.

---

## 📦 Product & Blog Management Workflow

### Adding Products
1. **Products > Add New**.
2. Enter Title, Price, Description.
3. **Pods Fields:** Fill in technical specs (appears in a neat table on frontend).
4. **Image:** Drag and drop.
5. **Publish**.

### Managing Blogs
1. **Posts > Add New**.
2. Use visual editor for tech updates.
3. Categorize (News, Reviews, Guides).

---

## 🚀 Migration Strategy (Critical Step)

Since we are building locally, follow this exact path to go live:

1. **Preparation:**
   - Ensure local site is perfect.
   - Install **All-in-One WP Migration** (Free) on Local and Live (in a subdirectory `/new`).

2. **Export/Import:**
   - Local: Export to File (.wpress).
   - Live (`netpluscomputers.lk/new`): Import file.
   - *Note:* This overwrites the fresh install in `/new` with your local build.

3. **Search Replace:**
   - The plugin automatically handles URL changes from `local` to `netpluscomputers.lk/new`.

4. **Testing Phase:**
   - Test the site at `netpluscomputers.lk/new`.
   - Check links, images, and login.

5. **Go Live:**
   - **Option A (Safe):** Use a redirect plugin to make `/new` the homepage temporarily.
   - **Option B (Clean):** Once confirmed, ask hosting support to "Move subdirectory to root" or manually swap files.
   - **Activate Cloudflare:** Switch nameservers last to activate global bot protection.

---

## 🆘 Support & Maintenance

- **Local Issues:** Check LocalWP logs.
- **Plugin Help:** Official WordPress.org support forums.
- **Community:** "Elementor Pakistan/India/Sri Lanka" Facebook groups for quick tips.

---

**Next Step:** Open `SETUP_GUIDE_PHASE0.md` and start installing LocalWP. Do not touch your live cPanel yet!
