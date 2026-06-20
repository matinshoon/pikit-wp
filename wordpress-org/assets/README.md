# WordPress.org plugin directory assets

These files are **not** included inside the plugin ZIP. They are uploaded separately to the plugin **SVN `assets/` folder** on WordPress.org after your plugin is approved.

## Required before making the plugin public

| File | Size | Purpose |
|------|------|---------|
| `icon-128x128.png` | 128×128 | Plugin icon (required) |
| `icon-256x256.png` | 256×256 | Retina plugin icon (required) |
| `banner-772x250.png` | 772×250 | Plugin page banner |
| `banner-1544x500.png` | 1544×500 | Retina banner (recommended) |
| `screenshot-1.png` … `screenshot-4.png` | 1200×900 max | Match captions in `readme.txt` |

## Tips

* Use Pikit branding (blue `#054ADA`, logo, booking UI).
* Screenshots should show real plugin UI—not stock photos.
* PNG or JPG only; no animated GIFs for icons/banners.
* File names must match exactly (e.g. `screenshot-1.png`, not `Screenshot1.png`).

## Suggested screenshot content

1. **Settings page** — installation code + onboarding checklist  
2. **Gutenberg** — Pikit Button block in the block editor  
3. **Elementor** — Pikit Button widget with style controls  
4. **Front end** — booking widget open after clicking the button  

Place finished files in this folder, then upload them to SVN `assets/` (see `wordpress-org/SUBMISSION.md`).
