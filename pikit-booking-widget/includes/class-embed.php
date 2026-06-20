<?php
/**
 * Embed script injection.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Injects official Pikit widget loader via wp_enqueue_script().
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_assets' ) );
	}

	/**
	 * Enqueue front-end styles and the Pikit widget loader script.
	 *
	 * @return void
	 */
	public function enqueue_front_assets() {
		if ( is_admin() || ! Pikit_Booking_Plugin::should_embed_on_current_page() ) {
			return;
		}

		$this->enqueue_front_styles();

		$settings  = Pikit_Booking_Plugin::get_settings();
		$token     = Pikit_Booking_Plugin::sanitize_installation_code( $settings['installation_code'] );
		$load_type = in_array( $settings['load_type'], array( 'SEO_FRIENDLY', 'FAST_LOAD' ), true )
			? $settings['load_type']
			: 'SEO_FRIENDLY';

		wp_register_script(
			'pikit-widget-loader',
			PIKIT_WIDGET_LOADER_URL,
			array(),
			PIKIT_BOOKING_WIDGET_VERSION,
			false
		);

		wp_add_inline_script(
			'pikit-widget-loader',
			sprintf(
				'window.PIKIT_TOKEN=%1$s;window.LOAD_TYPE=%2$s;',
				wp_json_encode( $token ),
				wp_json_encode( $load_type )
			),
			'before'
		);

		wp_script_add_data( 'pikit-widget-loader', 'async', true );
		wp_enqueue_script( 'pikit-widget-loader' );
	}

	/**
	 * Enqueue minimal front-end styles for book button wrappers.
	 *
	 * @return void
	 */
	private function enqueue_front_styles() {
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
}
