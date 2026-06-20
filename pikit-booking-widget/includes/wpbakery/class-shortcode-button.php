<?php
/**
 * WPBakery shortcode class for the Pikit Button element.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pikit Button shortcode — extends native WPBakery Button rendering.
 */
class Pikit_WPBakery_Shortcode_Button extends WPBakeryShortCode_Vc_Btn {

	/**
	 * Map legacy shortcode attributes from older plugin versions.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @param string|null  $content Shortcode content.
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$atts = is_array( $atts ) ? $atts : array();

		if ( empty( $atts['title'] ) && ! empty( $atts['text'] ) ) {
			$atts['title'] = $atts['text'];
		}

		if ( empty( $atts['el_class'] ) && ! empty( $atts['custom_class'] ) ) {
			$atts['el_class'] = $atts['custom_class'];
		}

		return parent::shortcode( $atts, $content );
	}
}
