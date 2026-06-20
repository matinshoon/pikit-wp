<?php
/**
 * Pikit Booking Widget
 *
 * @package     Pikit_Booking_Widget
 * @author      Pikit
 * @copyright   Copyright (c) Pikit
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Pikit Booking Widget
 * Plugin URI:        https://wordpress.org/plugins/pikit-widget/
 * Description:       Embed the Pikit online booking widget on your WordPress site with a Gutenberg block, Elementor widget, WPBakery element, and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Pikit
 * Author URI:        https://pikit.io
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pikit-widget
 */

defined( 'ABSPATH' ) || exit;

define( 'PIKIT_BOOKING_WIDGET_VERSION', '1.0.0' );
define( 'PIKIT_BOOKING_WIDGET_FILE', __FILE__ );
define( 'PIKIT_BOOKING_WIDGET_DIR', plugin_dir_path( __FILE__ ) );
define( 'PIKIT_BOOKING_WIDGET_URL', plugin_dir_url( __FILE__ ) );
define( 'PIKIT_BOOKING_WIDGET_BASENAME', plugin_basename( __FILE__ ) );

define( 'PIKIT_WIDGET_LOADER_URL', 'https://book.pikit.io/install/widget.js' );
define( 'PIKIT_BOOKING_BASE_URL', 'https://book.pikit.io' );
define( 'PIKIT_DASHBOARD_SETUP_URL', 'https://app.pikit.io/business-settings/online-booking/setup-integration' );

define( 'PIKIT_BOOKING_OPTION_KEY', 'pikit_booking_settings' );

require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-plugin.php';

/**
 * Returns the main plugin instance.
 *
 * @return Pikit_Booking_Plugin
 */
function pikit_booking_widget() {
	return Pikit_Booking_Plugin::instance();
}

pikit_booking_widget();
