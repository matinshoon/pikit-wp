# Pikit Booking Widget

WordPress plugin to embed the [Pikit](https://pikit.io) online booking widget on salon and service business websites.

The **installable plugin** lives in [`pikit-booking-widget/`](pikit-booking-widget/). This repo root holds build tooling only (`src/`, `package.json`, `node_modules/`).

## WordPress.org release

**Approved** — slug [`pikit-widget`](https://wordpress.org/plugins/pikit-widget/)  
**Version:** 1.0.2  
**SVN:** https://plugins.svn.wordpress.org/pikit-widget

Publish / update:

```powershell
npm run build
npm run zip
npm run svn:publish
```

Full steps (SVN password, assets, screenshots): [`wordpress-org/PUBLISH.md`](wordpress-org/PUBLISH.md)

The distributable zip is `dist/pikit-booking-widget.zip`.

## Repository layout

```
pikit-wp/
├── pikit-booking-widget/   ← plugin source (SVN trunk contents)
├── src/book-button/        ← Gutenberg block source
├── scripts/                ← build + release helpers
├── wordpress-org/          ← submission guide + asset specs
├── dist/                   ← release zip (gitignored)
└── package.json
```

## Features

- Official Pikit embed script via `wp_head`
- Branded **Pikit** admin settings page
- Gutenberg **Pikit Button** block (core Button styling)
- Elementor **Pikit Button** widget (native Button controls)
- WPBakery **Pikit Button** element (native Button controls)
- Shortcode `[pikit_book_button]`

## Development

Source code: https://github.com/matinshoon/pikit-wp

```bash
cd pikit-wp
npm install
npm run build   # compiles block into pikit-booking-widget/build/
npm run start   # watch mode
npm run zip     # creates dist/pikit-booking-widget.zip
```

**Do not zip the whole repo** — use `npm run zip` only.

### Internationalization

```bash
wp i18n make-pot pikit-booking-widget languages/pikit-booking-widget.pot --domain=pikit-booking-widget
```

## Shortcode

```
[pikit_book_button]
[pikit_book_button text="Book an appointment" style="link" align="center"]
```

## Open the widget at a specific page

By default the widget opens on its first page. Every trigger accepts optional settings to open a specific page instead. All of them are optional; an unknown ID falls back to the default page.

| Setting    | Shortcode attribute | HTML attribute        |
|------------|---------------------|-----------------------|
| Page       | `step`              | `data-pikit-step`     |
| Location   | `branch`            | `data-pikit-branch`   |
| Service    | `service`           | `data-pikit-service`  |
| Provider   | `provider`          | `data-pikit-provider` |
| Category   | `category`          | `data-pikit-category` |

`step` is one of `type`, `location`, `services`, `login`, `signup`, `customer-area`, `history`, `gift-card`, `shop`, `membership`. IDs are copied from your Pikit dashboard.

```
[pikit_book_button text="Book a haircut" service="SERVICE_ID"]
[pikit_book_button text="Our services" step="services" provider="PROVIDER_ID"]
[pikit_book_button text="Gift cards" step="gift-card"]
```

- **Block editor:** select the Pikit Button, then open **Open widget at** in the sidebar.
- **Elementor:** edit the Pikit Button widget, section **Open widget at**.
- **WPBakery:** edit the Pikit Button element, tab **Open widget at**.

Without the plugin, any element with `data-pikit-open` works once the Pikit embed script is on the page:

```html
<a href="#pikit-open" data-pikit-open data-pikit-service="SERVICE_ID">Book now</a>
<button type="button" data-pikit-open data-pikit-step="services" data-pikit-provider="PROVIDER_ID">Our services</button>
```

## License

GPLv2 or later. See [pikit-booking-widget/LICENSE](pikit-booking-widget/LICENSE).
