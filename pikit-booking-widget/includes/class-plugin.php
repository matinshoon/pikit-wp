<?php
/**
 * Main plugin bootstrap.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singleton plugin loader.
 */
final class Pikit_Booking_Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var Pikit_Booking_Plugin|null
	 */
	private static $instance = null;

	/**
	 * @return Pikit_Booking_Plugin
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
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required files.
	 */
	private function load_dependencies() {
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-book-button-renderer.php';
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-settings.php';
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-embed.php';
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-shortcode.php';
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-block.php';
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-elementor.php';
		require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/class-wpbakery.php';
	}

	/**
	 * Register hooks.
	 */
	private function init_hooks() {
		Pikit_Booking_Settings::instance();
		Pikit_Booking_Embed::instance();
		Pikit_Booking_Shortcode::instance();
		Pikit_Booking_Block::instance();
		Pikit_Booking_Elementor::instance();
		Pikit_Booking_WPBakery::instance();
	}

	/**
	 * Get plugin settings with defaults.
	 *
	 * @return array{enabled: bool, installation_code: string, load_type: string, scope: string}
	 */
	public static function get_settings() {
		$defaults = array(
			'enabled'           => true,
			'installation_code' => '',
			'load_type'         => 'SEO_FRIENDLY',
			'scope'             => 'all',
		);

		$stored = get_option( PIKIT_BOOKING_OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$settings = wp_parse_args( $stored, $defaults );

		$settings['enabled'] = (bool) $settings['enabled'];
		$settings['installation_code'] = self::sanitize_installation_code( $settings['installation_code'] );
		$settings['load_type'] = in_array( $settings['load_type'], array( 'SEO_FRIENDLY', 'FAST_LOAD' ), true )
			? $settings['load_type']
			: 'SEO_FRIENDLY';
		$settings['scope'] = in_array( $settings['scope'], array( 'all', 'front_page' ), true )
			? $settings['scope']
			: 'all';

		return $settings;
	}

	/**
	 * Sanitize installation code (alphanumeric only).
	 *
	 * @param string $code Raw installation code.
	 * @return string
	 */
	public static function sanitize_installation_code( $code ) {
		return preg_replace( '/[^a-zA-Z0-9]/', '', (string) $code );
	}

	/**
	 * Whether embed should load on the current request.
	 *
	 * @return bool
	 */
	public static function should_embed_on_current_page() {
		$settings = self::get_settings();

		if ( ! $settings['enabled'] || '' === $settings['installation_code'] ) {
			return false;
		}

		if ( 'front_page' === $settings['scope'] && ! is_front_page() ) {
			return false;
		}

		return true;
	}

	/**
	 * Build embed snippet preview text for the admin settings screen.
	 *
	 * @param string $installation_code Installation code.
	 * @param string $load_type         LOAD_TYPE value.
	 * @return string Human-readable snippet preview (not executed).
	 */
	public static function build_embed_snippet( $installation_code, $load_type ) {
		$token     = self::sanitize_installation_code( $installation_code );
		$load_type = in_array( $load_type, array( 'SEO_FRIENDLY', 'FAST_LOAD' ), true ) ? $load_type : 'SEO_FRIENDLY';

		// Single-quoted format string: %1$s must not appear in double quotes (PHP interpolates $s).
		return sprintf(
			'window.PIKIT_TOKEN = %1$s;' . "\n" . 'window.LOAD_TYPE = %2$s;' . "\n" . '// Enqueued script: %3$s',
			wp_json_encode( $token, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
			wp_json_encode( $load_type, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
			PIKIT_WIDGET_LOADER_URL
		);
	}
}
