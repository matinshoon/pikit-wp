<?php
/**
 * WPBakery Page Builder integration.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Pikit Button element in WPBakery (Visual Composer).
 */
class Pikit_Booking_WPBakery {

	/**
	 * Shortcode base used by WPBakery element.
	 */
	const SHORTCODE_BASE = 'pikit_button';

	/**
	 * @var Pikit_Booking_WPBakery|null
	 */
	private static $instance = null;

	/**
	 * @return Pikit_Booking_WPBakery
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
		add_action( 'plugins_loaded', array( $this, 'maybe_boot_wpbakery' ), 20 );
	}

	/**
	 * Boot WPBakery integration when the builder is active.
	 */
	public function maybe_boot_wpbakery() {
		if ( ! $this->is_wpbakery_active() ) {
			return;
		}

		add_filter( 'vc_add_new_category', array( $this, 'register_category' ) );
		add_action( 'vc_before_init', array( $this, 'register_element' ) );
	}

	/**
	 * Whether WPBakery Page Builder is active.
	 *
	 * @return bool
	 */
	private function is_wpbakery_active() {
		if ( defined( 'WPB_VC_VERSION' ) || class_exists( 'Vc_Manager', false ) ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'js_composer/js_composer.php' );
	}

	/**
	 * Whether native WPBakery Button params API is available.
	 *
	 * @return bool
	 */
	private function is_native_button_api_ready() {
		return function_exists( 'vc_btn_element_params' )
			&& class_exists( 'WPBakeryShortCode_Vc_Btn', false );
	}

	/**
	 * Load the WPBakery shortcode class file.
	 */
	private function load_shortcode_class() {
		if ( ! class_exists( 'Pikit_WPBakery_Shortcode_Button', false ) ) {
			require_once PIKIT_BOOKING_WIDGET_DIR . 'includes/wpbakery/class-shortcode-button.php';
		}
	}

	/**
	 * Add Pikit category to the WPBakery element picker.
	 *
	 * @param array $categories Existing categories.
	 * @return array
	 */
	public function register_category( $categories ) {
		$categories[] = array(
			'category'        => 'pikit',
			'category_name'   => __( 'Pikit', 'pikit-booking-widget' ),
			'category_weight' => 999,
		);

		return $categories;
	}

	/**
	 * Param names removed from the native Button field set (Pikit trigger is fixed).
	 *
	 * @return array
	 */
	private function get_removed_button_params() {
		return array(
			'link',
			'custom_onclick',
			'custom_onclick_code',
		);
	}

	/**
	 * Build vc_map params matching the native WPBakery Button element.
	 *
	 * @return array
	 */
	private function get_button_params() {
		if ( ! $this->is_native_button_api_ready() ) {
			return $this->get_fallback_params();
		}

		$btn_config = vc_btn_element_params();
		$params     = isset( $btn_config['params'] ) ? $btn_config['params'] : array();
		$removed    = $this->get_removed_button_params();
		$filtered   = array();

		foreach ( $params as $param ) {
			if ( ! is_array( $param ) || empty( $param['param_name'] ) ) {
				$filtered[] = $param;
				continue;
			}

			if ( in_array( $param['param_name'], $removed, true ) ) {
				continue;
			}

			if ( 'title' === $param['param_name'] ) {
				$param['value'] = __( 'Book now', 'pikit-booking-widget' );
			}

			$filtered[] = $param;
		}

		return $filtered;
	}

	/**
	 * Minimal params when an older WPBakery build lacks vc_btn_element_params().
	 *
	 * @return array
	 */
	private function get_fallback_params() {
		return array(
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Text', 'pikit-booking-widget' ),
				'param_name'  => 'title',
				'value'       => __( 'Book now', 'pikit-booking-widget' ),
				'admin_label' => true,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Alignment', 'pikit-booking-widget' ),
				'param_name' => 'align',
				'value'      => array(
					__( 'Left', 'pikit-booking-widget' )   => 'left',
					__( 'Center', 'pikit-booking-widget' ) => 'center',
					__( 'Right', 'pikit-booking-widget' )  => 'right',
				),
				'std'        => 'left',
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Extra class name', 'pikit-booking-widget' ),
				'param_name'  => 'el_class',
				'value'       => Pikit_Book_Button_Renderer::DEFAULT_CLASS,
			),
		);
	}

	/**
	 * Register vc_map element definition.
	 */
	public function register_element() {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$use_native = $this->is_native_button_api_ready();
		if ( $use_native ) {
			$this->load_shortcode_class();
		}

		$btn_config = $use_native ? vc_btn_element_params() : array();

		$map = array(
			'name'        => __( 'Pikit Button', 'pikit-booking-widget' ),
			'base'        => self::SHORTCODE_BASE,
			'icon'        => $use_native && ! empty( $btn_config['icon'] ) ? $btn_config['icon'] : 'fa fa-calendar',
			'category'    => 'pikit',
			'description' => __( 'Button that opens the Pikit online booking widget.', 'pikit-booking-widget' ),
			'params'      => $this->get_button_params(),
		);

		if ( $use_native ) {
			$map['php_class']      = 'Pikit_WPBakery_Shortcode_Button';
			$map['html_template']  = PIKIT_BOOKING_WIDGET_DIR . 'includes/wpbakery/vc_templates/pikit_button.php';
			$map['js_view']        = isset( $btn_config['js_view'] ) ? $btn_config['js_view'] : 'VcButton3View';
			$map['element_default_class'] = 'vc_do_btn';
			$map['custom_markup']  = '<div class="vc_btn3-container"><button class="vc_general vc_btn3 vc_btn3-size-sm vc_btn3-shape-{{ params.shape }} vc_btn3-style-{{ params.style }} vc_btn3-color-{{ params.color }}">{{{ params.title }}}</button></div>';
		}

		vc_map( $map );

		if ( ! $use_native ) {
			add_shortcode( self::SHORTCODE_BASE, array( $this, 'render_fallback_shortcode' ) );
		}
	}

	/**
	 * Fallback output for WPBakery versions without the native Button API.
	 *
	 * @param array|string $atts    Shortcode attributes.
	 * @param string|null  $content Shortcode content (unused).
	 * @return string
	 */
	public function render_fallback_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'title'     => __( 'Book now', 'pikit-booking-widget' ),
				'text'      => '',
				'el_class'  => Pikit_Book_Button_Renderer::DEFAULT_CLASS,
				'align'     => 'left',
			),
			$atts,
			self::SHORTCODE_BASE
		);

		$text = $atts['title'];
		if ( '' === trim( $text ) && ! empty( $atts['text'] ) ) {
			$text = $atts['text'];
		}

		return Pikit_Book_Button_Renderer::render(
			array(
				'text'  => $text,
				'class' => $atts['el_class'],
				'style' => 'button',
				'align' => $atts['align'],
			)
		);
	}
}
