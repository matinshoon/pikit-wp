<?php
/**
 * Shared Book Now trigger renderer.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders the Pikit book button / link trigger HTML.
 */
class Pikit_Book_Button_Renderer {

	/**
	 * Default button class.
	 */
	const DEFAULT_CLASS = 'pikit-book-button';

	/**
	 * Render trigger markup.
	 *
	 * @param array $args {
	 *     Optional. Display arguments.
	 *
	 *     @type string $text   Button or link text.
	 *     @type string $class  Additional CSS classes.
	 *     @type string $style  `button` or `link`.
	 *     @type string $align  Wrapper alignment class suffix.
	 * }
	 * @return string Escaped HTML.
	 */
	public static function render( array $args = array() ): string {
		$defaults = array(
			'text'  => __( 'Book now', 'pikit-booking-widget' ),
			'class' => self::DEFAULT_CLASS,
			'style' => 'button',
			'align' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$text  = sanitize_text_field( $args['text'] );
		$class = self::sanitize_classes( $args['class'] );
		$style = $args['style'] === 'link' ? 'link' : 'button';
		$align = sanitize_html_class( $args['align'] );

		$wrapper_class = 'pikit-book-button-wrap';
		if ( $align ) {
			$wrapper_class .= ' pikit-book-button-wrap--' . $align;
		}

		$inner = '';

		if ( 'link' === $style ) {
			$inner = sprintf(
				'<a href="#pikit-open" class="%1$s">%2$s</a>',
				esc_attr( $class ),
				esc_html( $text )
			);
		} else {
			$inner = sprintf(
				'<button type="button" id="pikit-open" class="%1$s">%2$s</button>',
				esc_attr( $class ),
				esc_html( $text )
			);
		}

		return sprintf(
			'<div class="%1$s">%2$s</div>',
			esc_attr( $wrapper_class ),
			$inner
		);
	}

	/**
	 * Sanitize a space-separated list of CSS classes.
	 *
	 * @param string $class_string Raw class string.
	 * @return string
	 */
	private static function sanitize_classes( $class_string ) {
		$parts = preg_split( '/\s+/', (string) $class_string, -1, PREG_SPLIT_NO_EMPTY );
		if ( ! $parts ) {
			return self::DEFAULT_CLASS;
		}

		$sanitized = array_filter( array_map( 'sanitize_html_class', $parts ) );
		return $sanitized ? implode( ' ', $sanitized ) : self::DEFAULT_CLASS;
	}
}

/**
 * Render Pikit book trigger (convenience wrapper).
 *
 * @param array $args Display arguments.
 * @return string
 */
function pikit_booking_render_trigger( array $args = array() ): string {
	return Pikit_Book_Button_Renderer::render( $args );
}
