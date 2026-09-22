# 🛡️ Phase 0: Local-First Security & Development Setup
**CRITICAL STRATEGY: Build Locally First.**
This phase guides you to set up the new secure website on your **own computer** using a local server. 
- ✅ Your live website (`netpluscomputers.lk`) remains **100% untouched**.
- ✅ Your live database is **never accessed or modified**.
- ✅ You can test security, design, and plugins safely offline.
- ✅ Migration to live hosting happens ONLY when you approve the final design.

---

## Step 1: Install Local Server Environment
*Time required: 15 minutes*

We will use **LocalWP**, the industry standard for local WordPress development. It simulates a cPanel server on your computer.

### 1.1 Download & Install
1. Go to [localwp.com](https://localwp.com/) and download the free version for your OS (Windows/Mac/Linux).
2. Install the application.

### 1.2 Create Your Local Site
1. Open **LocalWP**.
2. Click the **+** button (or "Create a new site").
3. **Choose Method:** Select **"Create a new site"**.
4. **Site Name:** `netplus-new` (This will be your local address: `netplus-new.local`).
5. **Environment:** 
   - **Preferred:** PHP 8.1, MySQL 8.0, Nginx/Apache.
   - *Note: This matches modern hosting standards.*
6. **WordPress Username/Password:** 
   - Create a secure admin user (e.g., `np_admin`, strong password).
   - *Save these credentials!*
7. Click **Add Site**.

### 1.3 Access Your Local Dashboard
1. In LocalWP, click **"WP Admin"** for your new site.
2. Log in with the credentials you just created.
3. **Verify:** You are now on a completely isolated WordPress installation. Your live site is unaffected.

---

## Step 2: Local Security Configuration (Simulation)
*Time required: 15 minutes*

Even though you are local, we install security plugins now to configure the rules. When you migrate to live, these settings transfer automatically.

### 2.1 Install Security Plugins
1. Go to **Plugins > Add New**.
2. Search and Install:
   - **NinjaFirewall WP Edition**
   - **WPS Hide Login**
   - **Limit Login Attempts Reloaded**
3. Activate all three.

### 2.2 Configure WPS Hide Login
1. Go to **Settings > General**.
2. Change Login URL to: `/np-secure-login-2025`.
3. **Bookmark this URL** locally.
   - *Effect:* Even on the live site later, `/wp-admin` will return a 404 error, stopping bots.

### 2.3 Configure NinjaFirewall (Full WAF)
1. Go to **NinjaFirewall** in the dashboard.
2. Run the setup wizard.
3. **Select Mode:** Choose **"Full WAF"** (Standalone).
   - *Note:* LocalWP allows this easily. On some shared hosts, this might require a small config tweak later, but testing it locally confirms compatibility.
4. Enable:
   - Block brute force attacks.
   - Block fake search engines.
   - Scan uploaded files.

### 2.4 Configure Limit Login Attempts
1. Go to **Settings > Limit Login Attempts**.
2. Set Max Retries to **3**.
3. Set Lockout Time to **1 hour**.

---

## Step 3: Prepare for Cloudflare (Live Stage Only)
*Note: Do NOT change nameservers yet. This step is for future reference.*

When you are ready to go live (Phase 5), you will:
1. Sign up for **Cloudflare Free**.
2. Add `netpluscomputers.lk`.
3. **Crucial:** Cloudflare will detect your existing DNS records. As long as you **do not delete** your existing A Record or CNAME, your current live site stays online while you prepare the switch.
4. **Future Rule:** Once the new site is migrated to the main domain, you will add a Cloudflare Page Rule to block `/wp-login.php` entirely.

---

## Step 4: Verification Checklist (Local)

Before proceeding to plugin installation (Phase 1), ensure:
- [ ] Local site loads at `http://netplus-new.local`.
- [ ] You can log in via the hidden URL `.../np-secure-login-2025`.
- [ ] NinjaFirewall status shows "Active".
- [ ] Your live website (`https://netpluscomputers.lk`) still loads normally in a separate browser tab.
- [ ] You have NOT touched your cPanel or live database.

---

## 🎯 Why This Local-First Approach?

| Feature | Benefit |
|---------|---------|
| **Zero Risk** | Impossible to break your live store while building. |
| **Speed** | Local development is instant (no upload/download wait). |
| **Free Testing** | Test premium features (via trials) or complex setups without cost. |
| **Safe Migration** | You only push to live when the site is perfect. |

---

## ⚠️ Important Notes

1. **Payment Gateways:** In Local mode, PayHere/DirectPay will not work because they need a live public URL. 
   - *Solution:* We will use "Cash on Delivery" for testing locally. Real payment integration happens after migration.
2. **Emails:** LocalWP traps emails internally; they won't send to real customers. This is good for testing spam prevention without annoying users.
3. **Migration:** When ready, we will use a plugin like **All-in-One WP Migration** or **Duplicator** to move the local site to your cPanel subdirectory (`netpluscomputers.lk/new`) or main domain.

---

## Next Steps

Once your local environment is secure:
1. Proceed to **IMPLEMENTATION_PLAN.md** (Week 2 tasks).
2. Install Elementor, ShopEngine, and design your store locally.
3. When satisfied, follow the **Migration Guide** (added to Implementation Plan) to go live.

**Remember:** Your live site is safe. You are building in a private sandbox.
