# Pikit Booking Widget

WordPress plugin to embed the [Pikit](https://pikit.io) online booking widget on salon and service business websites.

The **installable plugin** lives in [`pikit-booking-widget/`](pikit-booking-widget/). This repo root holds build tooling only (`src/`, `package.json`, `node_modules/`).

## WordPress.org release

**Version:** 1.0.0

Before submitting to the [WordPress Plugin Directory](https://wordpress.org/plugins/):

1. Run `npm run build && npm run zip`
2. Prepare icons, banner, and screenshots (see [`wordpress-org/assets/README.md`](wordpress-org/assets/README.md))
3. Follow [`wordpress-org/SUBMISSION.md`](wordpress-org/SUBMISSION.md)

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

## License

GPLv2 or later. See [pikit-booking-widget/LICENSE](pikit-booking-widget/LICENSE).
