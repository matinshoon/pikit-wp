# Publish Pikit Booking Widget (approved)

**Status:** Approved  
**Slug:** `pikit-widget`  
**SVN:** https://plugins.svn.wordpress.org/pikit-widget  
**Public page:** https://wordpress.org/plugins/pikit-widget  
**WordPress.org username:** `pikit` (case-sensitive)

## One-time setup

### 1. Generate your SVN password

1. Log in at [wordpress.org/support/users/pikit](https://wordpress.org/support/users/pikit/) (or your profile)
2. Open **Account Security** → set / reset **SVN / Git password**  
   Guide: https://make.wordpress.org/meta/handbook/tutorials-guides/svn-access/

> SVN username is `pikit` — **not** your email.

### 2. Install SVN (Windows)

Already handled if TortoiseSVN is installed with command-line tools. Confirm:

```powershell
svn --version
```

If missing, install TortoiseSVN and ensure “command line client tools” is enabled, then **restart the terminal**.

## Publish version 1.0.1 (code)

From the repo root:

```powershell
cd C:\Users\user\Documents\GitHub\pikit-wp
npm run build
npm run zip
.\scripts\svn-publish.ps1 -Username pikit -Version 1.0.1
```

When prompted, enter your **SVN password**.

The script will:

1. Check out / update `https://plugins.svn.wordpress.org/pikit-widget`
2. Copy `pikit-booking-widget/*` → SVN `trunk/`
3. Copy marketing PNGs from `wordpress-org/assets/` → SVN `assets/` (if present)
4. Commit trunk
5. Create `tags/1.0.1`

Within ~15 minutes the plugin page should show the download. Search indexes can take up to 72 hours.

## Marketing assets (SVN `assets/` — not inside the plugin zip)

| File | Size | Status |
|------|------|--------|
| `icon-128x128.png` | 128×128 | Prepared in `wordpress-org/assets/` |
| `icon-256x256.png` | 256×256 | Prepared in `wordpress-org/assets/` |
| `banner-772x250.png` | 772×250 | Prepared in `wordpress-org/assets/` |
| `banner-1544x500.png` | 1544×500 | Prepared in `wordpress-org/assets/` |
| `screenshot-1.png` … `screenshot-4.png` | ≤1200×900 | **You must capture** from real WP admin / front end |

Screenshot captions (must match `readme.txt`):

1. Pikit settings page with installation code and onboarding steps  
2. Gutenberg Pikit Button block in the editor  
3. Elementor Pikit Button widget  
4. Booking widget opened on the front end  

Put screenshots in `wordpress-org/assets/` then re-run `.\scripts\svn-publish.ps1`.

## Manual SVN (if you prefer TortoiseSVN GUI)

1. Checkout `https://plugins.svn.wordpress.org/pikit-widget` to a folder  
2. Copy contents of `pikit-booking-widget/` into `trunk/`  
3. Copy PNGs into `assets/`  
4. Commit with message `Release 1.0.1: publish Pikit Booking Widget`  
5. Copy `trunk` → `tags/1.0.1` and commit `Tag version 1.0.1`

## Verify live

- https://wordpress.org/plugins/pikit-widget/  
- Confirm Install → version **1.0.1**  
- Confirm icons / banner / screenshots appear after assets commit  

## Future updates

1. Bump version in `pikit-booking-widget.php` **and** `Stable tag` in `readme.txt` (must match)  
2. Add changelog entry  
3. `npm run build && npm run zip`  
4. `.\scripts\svn-publish.ps1 -Version 1.0.2`
