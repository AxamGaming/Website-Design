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
  - **Field:** `Known Bots` **equals** `True` → Action: **Block**
  - **Better Rule:** Enable **Super Bot Fight Mode** (if available) → Set to **Fight**.
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

#### D. Block Direct Access to wp-login.php (CRITICAL)
- Go to **Security** > **WAF** > **Tools** > **Page Rules**.
- Click **Create Page Rule**:
  - **URL Pattern:** `*netpluscomputers.lk/wp-login.php*`
  - **Pick a Setting:** Security Level → **I'm Under Attack!** (or choose **Block**)
  - **Alternative Setting:** Access Rules → **Block**
- Click **Save and Deploy**.
*(Since you will use WPS Hide Login to access via `/np-secure-login-2025`, blocking `/wp-login.php` at Cloudflare level prevents all bot brute-force attempts from consuming your server resources. Bots scanning for wp-login.php will be blocked before reaching your hosting.)*

#### E. Disable XML-RPC Completely
- Go to **Security** > **WAF** > **Tools** > **Page Rules**.
- Create a Page Rule:
  - **URL Pattern:** `*netpluscomputers.lk/xmlrpc.php*`
  - **Setting:** Block
  *(XML-RPC is a common attack vector for WordPress)*

---

## Step 2: Database Cleanup (Fresh Start)
*Time required: 10 minutes*

Since your previous site was compromised, start with a completely clean database.

### 2.1 Backup Old Data (Optional but Recommended)
1. Log in to **phpMyAdmin** via cPanel.
2. Select your database.
3. Click **Export** > **Quick** > **Go**.
4. Save the SQL file locally (in case you need old product data later).

### 2.2 Drop All Tables
1. In phpMyAdmin, select all tables (Check All).
2. Choose **Drop** from the dropdown.
3. Confirm deletion.
   *(This removes all malware, spam users, and corrupted data)*

### 2.3 Create New Database User
1. In cPanel, go to **MySQL Databases**.
2. Create a new database: `netplus_secure_db`.
3. Create a new user with a **strong password** (20+ characters, mix of letters/numbers/symbols).
4. Assign user to database with **ALL PRIVILEGES**.
5. Note down: Database name, username, password (you'll need these for WordPress install).

---

## Step 3: Fresh WordPress Installation
*Time required: 10 minutes*

### 3.1 Install WordPress via Softaculous/cPanel
1. In cPanel, find **Softaculous Apps Installer** or **WordPress Manager**.
2. Click **Install**.
3. Choose your domain: `netpluscomputers.lk`.
4. **IMPORTANT SETTINGS:**
   - **Site Name:** NetPlus Computers
   - **Admin Username:** DO NOT use "admin" (use something unique like `np_master_2025`)
   - **Admin Password:** Generate strong password (save in password manager)
   - **Admin Email:** Your business email
   - **Database Name:** Use the new database created in Step 2.3
   - **Table Prefix:** Change from `wp_` to `npc_` (prevents SQL injection attacks)
5. Click **Install**.

### 3.2 Post-Installation Hardening
Immediately after installation, before logging in:

#### A. Hide Admin URL
1. Install plugin: **WPS Hide Login** (Free).
2. Go to **Settings** > **General**.
3. Change login URL from `/wp-admin` to something secret like `/np-secure-login-2025`.
4. Save and bookmark this URL (you'll need it to log in).

#### B. Disable File Editing
Add this line to `wp-config.php` (via cPanel File Manager):
```php
define('DISALLOW_FILE_EDIT', true);
```
*(Prevents hackers from editing plugin files even if they get in)*

#### C. Limit Login Attempts
1. Install plugin: **Limit Login Attempts Reloaded** (Free).
2. Activate and configure:
   - Max attempts: 3
   - Lockout duration: 1 hour
   - Long lockout after 4 lockouts: 24 hours

---

## Step 4: NinjaFirewall Installation (Full WAF Mode)
*Time required: 10 minutes*

NinjaFirewall intercepts attacks BEFORE WordPress loads, providing server-level protection.

### 4.1 Install Plugin
1. Log in to your hidden admin URL.
2. Go to **Plugins** > **Add New**.
3. Search for **"NinjaFirewall WP Edition"**.
4. Install and Activate.

### 4.2 Configure Full WAF Mode (CRITICAL)
1. During setup wizard, choose **"Full WAF"** mode (NOT WordPress WAF).
   - Full WAF blocks threats before WordPress boots.
   - WordPress WAF only works after WordPress loads (too late for some attacks).
2. Follow the wizard to edit `wp-config.php` or `.htaccess` (the plugin guides you).
3. Once activated, you'll see a firewall status page.

### 4.3 Firewall Settings
Go to **NinjaFirewall** > **Firewall Options**:
- Enable **"Block brute force attacks"**.
- Enable **"Block fake search engines"**.
- Enable **"Block access to sensitive files"** (wp-config.php, .htaccess, etc.).
- Enable **"Scan uploaded files for malware"**.

Go to **NinjaFirewall** > **Live Traffic**:
- Monitor this page for the first week to see blocked attacks.
- You'll be shocked how many bots get stopped!

---

## Step 5: Security Checklist Before Proceeding

Verify these are complete before moving to Phase 1 (Plugin Installation):

- [ ] Cloudflare nameservers updated and active (green cloud icon).
- [ ] SSL certificate active (https:// shows padlock).
- [ ] Old database dropped, new database created with strong credentials.
- [ ] WordPress installed with custom table prefix (`npc_`).
- [ ] Admin username is NOT "admin".
- [ ] Login URL changed from `/wp-admin` to custom URL.
- [ ] `DISALLOW_FILE_EDIT` added to wp-config.php.
- [ ] Limit Login Attempts plugin configured (3 attempts max).
- [ ] NinjaFirewall installed in **Full WAF** mode.
- [ ] XML-RPC disabled via Cloudflare Page Rule.

---

## 🎯 What This Achieves

| Threat | Solution |
|--------|----------|
| Bot spam comments | Cloudflare WAF blocks before reaching server |
| Brute force login attacks | Rate limiting + Limit Login Attempts + NinjaFirewall |
| SQL injection | Custom table prefix + prepared statements |
| Malware uploads | NinjaFirewall scans + file edit disabled |
| DDoS attacks | Cloudflare absorbs traffic spikes |
| Admin panel discovery | Hidden login URL |
| Fake Google bots | NinjaFirewall verifies real search engines |

**Result:** Your previous site failed because bots could directly attack WordPress. This setup blocks 99% of attacks BEFORE they touch your server.

---

## ⚠️ Common Mistakes to Avoid

1. **Skipping Cloudflare setup** - Without this, bots reach your server directly.
2. **Using default wp_ table prefix** - Makes SQL injection easier.
3. **Choosing "admin" as username** - First thing hackers try.
4. **Not using Full WAF mode** - WordPress WAF mode is too slow to stop some attacks.
5. **Forgetting to bookmark hidden login URL** - You'll lock yourself out!

---

## Next Steps

Once all checkboxes above are ticked:
1. Proceed to **IMPLEMENTATION_PLAN.md** Week 2 tasks.
2. Install Elementor Free and other design plugins.
3. Build your store with confidence knowing you're protected.

**Remember:** Security is not optional. Your previous site died because of skipped security steps. Do NOT rush Phase 0.
