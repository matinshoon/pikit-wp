<?php
/**
 * Embed script injection.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Injects official Pikit widget loader into wp_head.
 */
class Pikit_Booking_Embed {

	/**
	 * @var Pikit_Booking_Embed|null
	 */
	private static $instance = null;

	/**
	 * @return Pikit_Booking_Embed
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'wp_head', array( $this, 'render_embed_script' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_styles' ) );
	}

	/**
	 * Enqueue minimal front-end styles for book button wrappers.
	 */
	public function enqueue_front_styles() {
		if ( is_admin() || ! Pikit_Booking_Plugin::should_embed_on_current_page() ) {
			return;
		}

		$style_path = PIKIT_BOOKING_WIDGET_DIR . 'build/book-button/style-index.css';
		if ( ! file_exists( $style_path ) ) {
			return;
		}

		wp_enqueue_style(
			'pikit-booking-widget',
			PIKIT_BOOKING_WIDGET_URL . 'build/book-button/style-index.css',
			array(),
			PIKIT_BOOKING_WIDGET_VERSION
		);
	}

	/**
	 * Output embed snippet in head when enabled.
	 */
	public function render_embed_script() {
		if ( is_admin() || ! Pikit_Booking_Plugin::should_embed_on_current_page() ) {
			return;
		}

		$settings = Pikit_Booking_Plugin::get_settings();

		Pikit_Booking_Plugin::print_embed_snippet(
			$settings['installation_code'],
			$settings['load_type']
		);
	}
}
