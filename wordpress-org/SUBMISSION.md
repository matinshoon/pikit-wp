# Submitting Pikit Booking Widget to WordPress.org

This guide walks you through publishing **Pikit Booking Widget** on the [WordPress Plugin Directory](https://wordpress.org/plugins/) as a free plugin.

## Before you start

### 1. Create a WordPress.org account

1. Register at [https://login.wordpress.org/register](https://login.wordpress.org/register)
2. Choose a username (e.g. `pikit` or your company account).
3. Update **`Contributors:`** in `pikit-booking-widget/readme.txt` to match that **exact** username.

### 2. Confirm the plugin is ready

From the repo root:

```bash
cd pikit-wp
npm install
npm run build
npm run zip
```

You should get `dist/pikit-booking-widget.zip` (~50–80 KB). The zip must contain only the `pikit-booking-widget/` folder with:

- `pikit-booking-widget.php` (Version **1.0.0**)
- `readme.txt` (Stable tag **1.0.0** — must match plugin version)
- `LICENSE`, `uninstall.php`, `index.php`
- `includes/`, `assets/`, `build/`, `languages/`
- **No** `node_modules/`, `.git/`, or dev tooling

### 3. Prepare marketing assets

Create PNG files listed in [`wordpress-org/assets/README.md`](assets/README.md):

- Plugin icon (128×128 and 256×256)
- Banner (772×250 and 1544×500)
- Four screenshots matching `readme.txt` captions

---

## Step 1 — Submit the plugin for review

1. Log in at [https://wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/)
2. Upload **`dist/pikit-booking-widget.zip`** (or submit the plugin URL if hosted publicly on GitHub with a release asset).
3. Fill in the form:
   - **Plugin name:** Pikit Booking Widget
   - **Short description:** Embed Pikit online booking on your WordPress site.
   - Confirm you have rights to distribute the code (GPLv2+).
   - Confirm the plugin complies with [Plugin Directory Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/).
4. Submit and wait for the review email (typically a few days to a few weeks).

### Common review topics for this plugin

| Topic | How this plugin handles it |
|-------|----------------------------|
| **External services** | Documented in `readme.txt` → *External services* (Pikit / book.pikit.io) |
| **GPL license** | `LICENSE` + plugin header |
| **Unique slug** | Request slug `pikit-booking-widget` |
| **No phone-home tracking** | Only loads Pikit booking scripts when embed is enabled |
| **Prefix / options** | `pikit_` prefix, single option `pikit_booking_settings` |
| **Uninstall** | `uninstall.php` deletes plugin options |
| **Text domain** | `pikit-booking-widget`, loaded on `init` |

If reviewers ask for changes, reply on the review ticket and upload a fixed zip.

---

## Step 2 — Get SVN access (after approval)

WordPress will email you an **SVN repository URL** like:

```
https://plugins.svn.wordpress.org/pikit-booking-widget/
```

Structure:

```
pikit-booking-widget/
├── trunk/          ← development version (what users install from “Development”)
├── tags/1.0.0/     ← each release tag
└── assets/         ← icons, banners, screenshots (NOT inside trunk)
```

### Install SVN

- **Windows:** [TortoiseSVN](https://tortoisesvn.net/) or `svn` via Git Bash / WSL  
- **macOS:** `brew install subversion`  
- **Linux:** `sudo apt install subversion`

### Check out the repository

```bash
svn checkout https://plugins.svn.wordpress.org/pikit-booking-widget/ pikit-booking-widget-svn
cd pikit-booking-widget-svn
```

---

## Step 3 — Upload plugin code to `trunk`

Copy the contents of **`pikit-booking-widget/`** (not the parent repo) into `trunk/`:

```bash
# From pikit-wp repo root (adjust paths for your machine)
rsync -av --delete pikit-booking-widget/ pikit-booking-widget-svn/trunk/ \
  --exclude node_modules --exclude .git
```

On Windows, copy the folder contents manually or use PowerShell:

```powershell
robocopy .\pikit-booking-widget .\pikit-booking-widget-svn\trunk /MIR /XD node_modules .git
```

Then commit:

```bash
cd pikit-booking-widget-svn
svn add --force trunk/*
svn status
svn commit -m "Initial commit: Pikit Booking Widget 1.0.0"
```

---

## Step 4 — Tag release 1.0.0

```bash
svn copy trunk tags/1.0.0
svn commit -m "Tag version 1.0.0"
```

WordPress.org serves the **latest tag** matching `Stable tag:` in `readme.txt` to users who click “Install”.

---

## Step 5 — Upload `assets/` (icons, banners, screenshots)

Copy your PNG files into `assets/`:

```
assets/icon-128x128.png
assets/icon-256x256.png
assets/banner-772x250.png
assets/banner-1544x500.png
assets/screenshot-1.png
assets/screenshot-2.png
assets/screenshot-3.png
assets/screenshot-4.png
```

Commit:

```bash
svn add assets/*
svn commit -m "Add plugin directory assets"
```

---

## Step 6 — Verify the plugin page

Within ~10 minutes, check:

**https://wordpress.org/plugins/pikit-booking-widget/**

Confirm:

- Description and FAQ render correctly  
- Screenshots appear  
- Banner and icon show  
- “Download” installs version 1.0.0  

---

## Releasing future updates

1. Bump version in `pikit-booking-widget.php` **and** `Stable tag` in `readme.txt` (must match).
2. Add a new `== Changelog ==` section for the new version.
3. Run `npm run build && npm run zip`.
4. Copy updated files to SVN `trunk/`, commit.
5. Copy `trunk/` to `tags/1.0.x/`, commit.
6. Wait for WordPress.org to sync (users get updates via WordPress admin).

---

## Optional: Plugin Check before submit

Install the [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin on a test site, run it against **Pikit Booking Widget**, and fix any errors/warnings before submission.

---

## Support links to add on your plugin page

After launch, add in the WordPress.org plugin readme or forum:

- Pikit setup docs: https://pikit.io  
- Support forum: WordPress.org plugin support tab  
- Privacy: https://pikit.io/privacy  

---

## Checklist (print this)

- [ ] WordPress.org account created; username in `readme.txt` Contributors
- [ ] Version **1.0.0** in plugin header and `Stable tag`
- [ ] `npm run build` completed
- [ ] `npm run zip` — zip is small, no node_modules
- [ ] Icons, banner, 4 screenshots prepared
- [ ] Plugin submitted at developers/add
- [ ] Review feedback addressed
- [ ] Code in SVN `trunk/` + tag `tags/1.0.0/`
- [ ] Assets in SVN `assets/`
- [ ] Plugin page verified live
