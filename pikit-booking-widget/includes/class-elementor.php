<?php
/**
 * Elementor integration loader.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers Elementor widgets when Elementor is active.
 */
class Pikit_Booking_Elementor {

	/**
	 * @var Pikit_Booking_Elementor|null
	 */
	private static $instance = null;

	/**
	 * Whether Elementor widgets have been registered.
	 *
	 * @var bool
	 */
	private $widgets_registered = false;

	/**
	 * @return Pikit_Booking_Elementor
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
		add_action( 'plugins_loaded', array( $this, 'maybe_boot_elementor' ), 20 );
	}

	/**
	 * Boot Elementor integration after plugins load (defer until Elementor is ready).
	 */
	public function maybe_boot_elementor() {
		if ( ! $this->is_elementor_plugin_active() ) {
			return;
		}

		add_action( 'elementor/loaded', array( $this, 'register_elementor_hooks' ) );

		if ( did_action( 'elementor/loaded' ) ) {
			$this->register_elementor_hooks();
		}
	}

	/**
	 * Whether Elementor hooks were registered.
	 *
	 * @var bool
	 */
	private $hooks_registered = false;

	/**
	 * Register Elementor hooks once the plugin has finished loading.
	 */
	public function register_elementor_hooks() {
		if ( $this->hooks_registered || ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		$this->hooks_registered = true;

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets_legacy' ) );
	}

	/**
	 * Whether the Elementor plugin is installed and active.
	 *
	 * @return bool
	 */
	private function is_elementor_plugin_active() {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'elementor/elementor.php' );
	}

	/**
	 * Whether Elementor widget APIs are available.
	 *
	 * @return bool
	 */
	private function is_elementor_widget_api_ready() {
		return class_exists( '\Elementor\Widget_Base' );
	}

	/**
	 * Load widget class file only when Elementor base widget class exists.
	 */
	private function load_widget_class() {
		if ( ! $this->is_elementor_widget_api_ready() ) {
			return;
		}

		if ( ! trait_exists( '\Elementor\Includes\Widgets\Traits\Button_Trait' ) ) {
			return;
		}

		if ( ! class_exists( 'Pikit_Elementor_Book_Button_Widget', false ) ) {
			require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/elementor/class-book-button-widget.php';
		}
	}

	/**
	 * Register the Pikit category in the Elementor panel.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'pikit',
			array(
				'title' => __( 'Pikit', 'pikit-widget' ),
				'icon'  => 'fa fa-calendar',
			)
		);
	}

	/**
	 * Register widgets (Elementor 3.5+).
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		$this->register_widget_instance( $widgets_manager );
	}

	/**
	 * Register widgets (Elementor legacy hook).
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets_legacy( $widgets_manager ) {
		$this->register_widget_instance( $widgets_manager );
	}

	/**
	 * Register the Pikit Button widget once.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	private function register_widget_instance( $widgets_manager ) {
		if ( $this->widgets_registered || ! $this->is_elementor_widget_api_ready() ) {
			return;
		}

		$this->load_widget_class();

		if ( ! class_exists( 'Pikit_Elementor_Book_Button_Widget', false ) ) {
			return;
		}

		$widget = new Pikit_Elementor_Book_Button_Widget();

		if ( method_exists( $widgets_manager, 'register' ) ) {
			$widgets_manager->register( $widget );
		} elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
			$widgets_manager->register_widget_type( $widget );
		}

		$this->widgets_registered = true;
	}
}
