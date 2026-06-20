=== Pikit Booking Widget ===
Contributors: pikit
Tags: booking, appointment, calendar, salon, scheduling
Requires at least: 6.1
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed Pikit online booking on your WordPress site with a branded button, shortcode, and page builder widgets.

== Description ==

**Pikit Booking Widget** connects your WordPress website to [Pikit](https://pikit.io) online booking. Visitors stay on your site while scheduling appointments through the official Pikit booking widget.

This plugin is free to use. A [Pikit](https://pikit.io) account with online booking enabled is required.

= Features =

* Official Pikit embed script enqueued on the front end (loads `book.pikit.io/install/widget.js`)
* Branded **Pikit** admin settings page with setup checklist
* **Gutenberg** block: *Pikit Button* (core Button styling: colors, typography, border, padding, width)
* **Elementor** widget: *Pikit Button* (native Button controls)
* **WPBakery** element: *Pikit Button* (native Button controls)
* Shortcode: `[pikit_book_button]` for any page builder
* SEO-friendly or fast-load embed modes
* Front page or site-wide embed scope
* Compatible with Pikit dashboard **Verify Connection**

= Requirements =

* A Pikit account with online booking enabled
* Your website URL configured in Pikit Business settings (must match this WordPress site, HTTPS recommended)
* WordPress 6.1+ and PHP 7.4+

== Development ==

Source code and build tooling are available on GitHub:

https://github.com/matinshoon/pikit-wp

The Gutenberg block source lives in `src/book-button/`. Run `npm install && npm run build` from the repository root to compile assets into the plugin `build/` directory.

== Installation ==

1. Install and activate the plugin through the **Plugins → Add New** screen in WordPress.
2. In Pikit, open **Business settings → Online booking → Setup & Integration** and copy your **installation code**.
3. In WordPress, open **Pikit** in the admin sidebar, paste your installation code, and save.
4. Add a booking button using the Gutenberg block, Elementor widget, WPBakery element, or shortcode `[pikit_book_button]`.
5. In Pikit, run **Verify Connection** to confirm the widget is live on your site.

== Frequently Asked Questions ==

= Do I need a Pikit account? =

Yes. This plugin embeds the official Pikit booking widget. Sign up at [pikit.io](https://pikit.io) and enable online booking in your dashboard.

= Where do I find my installation code? =

In Pikit: **Business settings → Online booking → Setup & Integration**.

= Why does Verify Connection fail? =

* The website URL in Pikit Business settings must match this WordPress site (including `https://`).
* The installation code in plugin settings must match your Pikit account.
* The embed must be enabled in plugin settings and appear in your page HTML.

= Does this work with Elementor or WPBakery? =

Yes. Search for **Pikit Button** in the Elementor widget panel or WPBakery element list (under the **Pikit** category). You can also use the shortcode in any builder.

= What shortcode attributes are supported? =

`[pikit_book_button text="Book now" style="button" class="pikit-book-button" align="center"]`

* `text` — button or link label
* `style` — `button` or `link`
* `class` — CSS classes on the trigger element
* `align` — wrapper alignment: `left`, `center`, or `right`

= Does this plugin work without Elementor or WPBakery? =

Yes. The Gutenberg block and shortcode work on any theme. Elementor and WPBakery integrations load only when those plugins are active.

== Screenshots ==

1. Pikit settings page with installation code and onboarding steps
2. Gutenberg Pikit Button block in the editor
3. Elementor Pikit Button widget
4. Booking widget opened on the front end

== External services ==

This plugin connects to **Pikit** services to provide online booking.

**Service:** Pikit online booking (https://pikit.io)

**What the service is used for:**

* Loading the official booking widget JavaScript on your website
* Opening the booking flow when a visitor clicks a Pikit Button
* Linking your WordPress site to your Pikit business account via an installation code

**What data is sent and when:**

* When the embed is enabled, your site loads scripts from `https://book.pikit.io` (including your installation code as `window.PIKIT_TOKEN`).
* When a visitor interacts with the booking widget, data is sent directly between the visitor and Pikit according to Pikit’s terms and privacy policy (appointment details, contact information, etc.).
* This plugin stores your installation code and embed settings in the WordPress database on your server. It does not send WordPress user data to Pikit by itself.

**Links:**

* Terms of service: https://pikit.io/terms
* Privacy policy: https://pikit.io/privacy

== Changelog ==

= 1.0.0 =
* Initial public release.
* Official Pikit embed script injection with SEO-friendly and fast-load modes.
* Branded Pikit admin settings page.
* Gutenberg *Pikit Button* block with core Button styling support.
* Elementor *Pikit Button* widget with native Button styling.
* WPBakery *Pikit Button* element with native Button styling.
* Shortcode `[pikit_book_button]`.
* Uninstall cleanup of plugin settings.

== Upgrade Notice ==

= 1.0.0 =
Initial release of Pikit Booking Widget for WordPress.
