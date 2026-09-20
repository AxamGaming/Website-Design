# 🛡️ Phase 0: Pre-Installation Security Setup
**CRITICAL: Complete these steps BEFORE installing WordPress or any plugins.**
This phase solves the bot spam issue that destroyed your previous website by blocking attacks at the DNS and server level.

---

## Step 1: Cloudflare Setup (DNS Level Protection)
*Time required: 15-20 minutes*

Cloudflare acts as a shield between the internet and your server. It stops bots before they even touch your hosting.

### 1.1 Create Account & Add Site
1. Go to [Cloudflare.com](https://www.cloudflare.com/) and sign up for a **Free Account**.
2. Click **"Add a Site"** and enter `netpluscomputers.lk`.
3. Select the **Free Plan** (sufficient for bot protection).
4. Click **"Continue"**.

### 1.2 Update Nameservers (Most Important Step)
1. Cloudflare will show you two nameservers (e.g., `bob.ns.cloudflare.com` and `lola.ns.cloudflare.com`).
2. Log in to your **Domain Registrar** (where you bought `netpluscomputers.lk`, e.g., Namecheap, GoDaddy, or LK Domain Registry).
3. Find **"Nameserver Settings"** or **"DNS Management"**.
4. Replace your current nameservers with the two provided by Cloudflare.
5. Save changes. *Note: This can take 15 mins to 24 hours to propagate globally.*

### 1.3 Configure Security Settings (Bot Protection)
Once your domain is active on Cloudflare (status changes to "Active"):

#### A. SSL/TLS Settings
- Go to **SSL/TLS** > **Overview**.
- Select **"Full"** or **"Full (Strict)"** mode.
- Go to **Edge Certificates** and enable **"Always Use HTTPS"**.

#### B. Firewall Rules (Stop Bots)
- Go to **Security** > **WAF**.
- Click **"Create Rule"**:
  - **Rule Name:** `Block Bad Bots`
  - **Field:** `Security Level` **equals** `I'm Under Attack` (Optional, use only if attacked) OR
  - **Field:** `Known Bots` **equals** `True` → Action: **Block** (Careful: blocks some search engines)
  - **Better Rule:** `Super Bot Fight Mode` (If available on your plan) → Set to **Fight**.
- Go to **Security** > **Settings**.
- Set **Security Level** to **Medium** or **High**.
- Enable **Challenge Visitors** when threat score is > 30.

#### C. Rate Limiting (Prevent Brute Force)
- Go to **Security** > **WAF** > **Rate limiting rules**.
- Create a rule:
  - **If:** `URI Path` contains `/wp-login.php` OR `/xmlrpc.php`
  - **And:** `Requests` > 5 per 1 minute
  - **Then:** **Block** for 1 hour.
  *(This stops hackers from guessing your password)*

#### D. Under Attack Mode (Temporary Shield)
- While building the site, go to **Security** > **Settings**.
- Turn **"Under Attack Mode"** to **ON**.
- *This forces every visitor to solve a CAPTCHA before seeing the site. Turn this OFF only when the site is ready for customers.*

---

## Step 2: Prepare cPanel & Database
*Time required: 10 minutes*

Since your previous site had spam issues, we must start with a **CLEAN** database.

### 2.1 Backup Old Site (Optional but Recommended)
1. Log in to **cPanel**.
2. Go to **File Manager**.
3. Rename your current `public_html` folder to `public_html_OLD_BACKUP`.
4. Create a new empty folder named `public_html`.

### 2.2 Clean Database
1. In cPanel, go to **phpMyAdmin**.
2. Select your database (usually named `username_netplus`).
3. **Drop all tables** (Select all tables > Drop).
   *Warning: This deletes all old data. Ensure you have a backup if needed.*
4. You now have an empty database ready for fresh installation.

### 2.3 Create Fresh WordPress User
1. In cPanel, go to **MySQL Databases**.
2. Create a **New User** (e.g., `netplus_admin`).
3. Generate a **Strong Password** (save this in a password manager).
4. **Add User to Database** with **ALL PRIVILEGES**.
   *Never use the default 'root' or 'admin' username.*

---

## Step 3: Install WordPress (Clean Slate)
*Time required: 5 minutes*

Do **NOT** use "Softaculous" or one-click installers if they include demo content. Manual install is cleaner.

1. Download latest WordPress from [wordpress.org](https://wordpress.org/download/).
2. In cPanel **File Manager**, upload the zip file to `public_html`.
3. Extract the zip file. Move all files from the `wordpress` folder to the root of `public_html`.
4. Visit `netpluscomputers.lk` in your browser.
5. Follow the 5-minute installation:
   - **Database Name:** Your existing database name.
   - **Username:** The new user you created in Step 2.3.
   - **Password:** The strong password you generated.
   - **Database Host:** `localhost`
   - **Table Prefix:** Change `wp_` to something random like `np7x_` (Security best practice).
6. **IMPORTANT:** When creating the Admin account:
   - **Username:** Do NOT use "admin". Use something unique like `NetPlusOwner`.
   - **Email:** Use your real business email.
   - **Password:** Use a very strong password.

---

## Step 4: Immediate Post-Install Hardening
*Do this BEFORE logging into the dashboard to add content.*

### 4.1 Disable XML-RPC (Common Bot Entry Point)
1. In cPanel **File Manager**, edit `wp-config.php`.
2. Add this line before `/* That's all, stop editing! */`:
   ```php
   define( 'XMLRPC_REQUEST', false );
   ```

### 4.2 Change Admin URL (Hide Login Page)
*We will do this via plugin in Phase 1, but for now:*
- Do not share your login link (`netpluscomputers.lk/wp-admin`).
- Only access it from your personal computer.

### 4.3 Install NinjaFirewall (First Plugin)
1. Log in to `netpluscomputers.lk/wp-admin`.
2. Go to **Plugins** > **Add New**.
3. Search for **"NinjaFirewall WP Edition"**.
4. Install and Activate.
5. **Setup Wizard:**
   - Choose **"Standalone Mode"** (Maximum security).
   - Let it optimize the rules.
   - Enable **"Block malicious requests"**.

---

## ✅ Checklist: Are You Ready for Phase 1?
- [ ] Cloudflare Nameservers updated and active?
- [ ] "Under Attack Mode" enabled in Cloudflare?
- [ ] Old database tables dropped/cleaned?
- [ ] Fresh WordPress installed with custom table prefix?
- [ ] Admin username is NOT "admin"?
- [ ] XML-RPC disabled in `wp-config.php`?
- [ ] NinjaFirewall installed and active?

**If all checked: Proceed to Phase 1 (Plugin Installation).**
**If not: Do not proceed. Security is compromised.**
