# 🛡️ Phase 0: Local-First Security & Setup Guide
**CRITICAL: This guide sets up your new website LOCALLY on your computer first.**
**Goal:** Build and test the new secure site without touching your live website (`netpluscomputers.lk`) or its database.

---

## 🚀 Why Local-First?
1. **Zero Risk:** Your live site stays 100% untouched. No downtime, no data loss.
2. **Free Testing:** Test security plugins, design changes, and bot protection rules locally before going live.
3. **Speed:** Local development is instant (no upload wait times).
4. **Peace of Mind:** You only migrate to the live server when you are 100% satisfied.

---

## Step 1: Install Local Development Environment
*Time required: 10 minutes*

We will use **LocalWP**, the easiest tool to run WordPress on your computer.

1. **Download LocalWP:**
   - Go to [localwp.com](https://localwp.com/)
   - Download the free version for Windows or Mac.
   - Install and open the application.

2. **Create Your New Site:**
   - Click **"Create a new site"**.
   - **Site Name:** `NetPlus-New` (or `netplus-local`).
   - **Environment:** Choose **"Preferred"** (uses PHP 8.x and MySQL 8).
   - **WordPress Username/Password:** Create a secure admin account (e.g., `admin_np`, strong password).
   - Click **"Add Site"**.

3. **Access Your Local Site:**
   - LocalWP will start your site.
   - Click **"WP Admin"** to log in.
   - Your local site URL will look like: `https://netplus-new.local`
   - **Note:** This site exists ONLY on your computer. The internet cannot see it yet.

---

## Step 2: Local Security Configuration (Simulation)
*Time required: 15 minutes*

Even though it's local, we install security plugins now to test their configuration and ensure they don't conflict with our design stack.

### 2.1 Install Security Plugins
1. Log in to your **Local WP Admin** dashboard.
2. Go to **Plugins > Add New**.
3. Install and Activate:
   - **NinjaFirewall WP Edition**
   - **WPS Hide Login**
   - **Limit Login Attempts Reloaded**

### 2.2 Configure NinjaFirewall (Full WAF Mode)
1. Go to **NinjaFirewall** in the dashboard.
2. Run the setup wizard.
3. **Select "Full WAF Mode"**:
   - LocalWP allows this easily. It edits the local `wp-config.php` automatically.
   - This simulates exactly how it will block bots on the live server later.
4. **Enable Rules:**
   - Block brute force attacks.
   - Block fake search engines.
   - Scan uploaded files.

### 2.3 Hide Login URL
1. Go to **Settings > General**.
2. Find **WPS Hide Login** section.
3. Change login URL from `/wp-admin` to `/np-secure-login-2025`.
4. **Test it:** Try logging out and accessing `/wp-admin`. It should give a 404 error. Access `/np-secure-login-2025` to log back in.
   - *Save this URL!* You will use the same one on the live site later.

---

## Step 3: Install The Free Design Stack (Locally)
*Time required: 20 minutes*

Install the plugins we planned to verify they work together before touching your live server.

1. **Elementor Free** + **Header & Footer Builder**
2. **Essential Addons for Elementor**
   - *Optimization:* Go to settings and disable unused widgets.
3. **ShopEngine** (for WooCommerce building)
4. **WooCommerce** (installed automatically by ShopEngine usually)
5. **Pods Framework** (for custom product fields)
6. **User Role Editor**

**Task:** Create a dummy product and a dummy blog post. Test dragging them around with Elementor. Ensure the "Manager" role (created via User Role Editor) can edit products but not settings.

---

## Step 4: Prepare for Live Migration (The "Go-Live" Plan)
*Do this ONLY when you are ready to replace the old site.*

Once your local site is perfect, follow these steps to move it to `netpluscomputers.lk`:

### 4.1 Export Local Site
1. In LocalWP, right-click your site > **Export** > **Export as Zip**.
2. Save the zip file.

### 4.2 Prepare Live Server (cPanel)
1. **Create New Database:**
   - cPanel > MySQL Databases > Create `netplus_new_db`.
   - Create user `netplus_new_user` with strong password.
   - Assign user to DB with **ALL PRIVILEGES**.
2. **Create Folder (Optional but Recommended):**
   - You can install directly to root (replacing old site) OR to a subfolder (`/new`) first for final testing.
   - *Recommendation:* Install to `/new` subfolder first via Softaculous, then migrate.

### 4.3 Import & Migrate
1. **Install WordPress** on the live server (in `/new` or root).
2. **Install "All-in-One WP Migration" Plugin** on BOTH Local and Live sites.
3. **Export from Local:** Use the plugin to export your local site to a `.wpress` file.
4. **Import to Live:** Upload the `.wpress` file to the live site plugin.
   - *Note:* This overwrites the live WP installation's content but keeps the database credentials you created in step 4.2.

### 4.4 Final Live Security Steps
1. **Cloudflare Setup:**
   - Now update your domain nameservers to Cloudflare.
   - Create Page Rule: `netpluscomputers.lk/wp-login.php` -> **Block**.
   - Create Page Rule: `netpluscomputers.lk/xmlrpc.php` -> **Block**.
2. **Update URLs:**
   - If you migrated from a subfolder, update Site URL in Settings > General to `https://netpluscomputers.lk`.
3. **Test Live:**
   - Verify `netpluscomputers.lk/np-secure-login-2025` works.
   - Verify old site content is gone (if replaced) or accessible via `/old` (if moved).

---

## ✅ Checklist: Before Going Live

- [ ] Local site built and tested successfully.
- [ ] Security plugins configured locally (NinjaFirewall, WPS Hide Login).
- [ ] Design verified on mobile and desktop locally.
- [ ] New database created on cPanel (`netplus_new_db`).
- [ ] Backup of OLD live site downloaded (just in case).
- [ ] Migration plugin used to move local site to live server.
- [ ] Cloudflare nameservers updated.
- [ ] Cloudflare Page Rules blocking `/wp-login.php` and `/xmlrpc.php`.

---

## ⚠️ Important Notes
- **Database Safety:** Your old database (`wp_` prefix) remains untouched until you explicitly overwrite it during migration.
- **Downtime:** Minimal downtime (only during DNS propagation if switching nameservers).
- **Rollback:** If anything goes wrong, you still have the local copy and the old database backup.

**Next Step:** Open LocalWP and start Step 1!
