<?php
/**
 * Shortcode registration.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * [pikit_book_button] shortcode.
 */
class Pikit_Booking_Shortcode {

	/**
	 * @var Pikit_Booking_Shortcode|null
	 */
	private static $instance = null;

	/**
	 * @return Pikit_Booking_Shortcode
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
		add_shortcode( 'pikit_book_button', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'text'  => __( 'Book now', 'pikit-widget' ),
				'class' => Pikit_Book_Button_Renderer::DEFAULT_CLASS,
				'style' => 'button',
				'align' => '',
			),
			$atts,
			'pikit_book_button'
		);

		return Pikit_Book_Button_Renderer::render(
			array(
				'text'  => $atts['text'],
				'class' => $atts['class'],
				'style' => $atts['style'],
				'align' => $atts['align'],
			)
		);
	}
}
