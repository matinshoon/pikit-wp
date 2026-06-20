<?php
/**
 * Uninstall Pikit Booking Widget.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'pikit_booking_settings' );
