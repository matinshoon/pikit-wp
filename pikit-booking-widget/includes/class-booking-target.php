<?php
/**
 * Booking target (deep link) helper.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normalizes the "open the widget at" options and builds the data attributes
 * read by the Pikit loader.
 */
class Pikit_Booking_Target {

	/**
	 * Target keys mapped to their HTML data attribute.
	 */
	const ATTRIBUTES = array(
		'step'     => 'data-pikit-step',
		'branch'   => 'data-pikit-branch',
		'service'  => 'data-pikit-service',
		'provider' => 'data-pikit-provider',
		'category' => 'data-pikit-category',
	);

	/**
	 * Widget pages that can be opened directly.
	 *
	 * @return array Step slug => label.
	 */
	public static function get_steps(): array {
		return array(
			'type'          => __( 'Appointment type', 'pikit-widget' ),
			'location'      => __( 'Location', 'pikit-widget' ),
			'services'      => __( 'Services', 'pikit-widget' ),
			'login'         => __( 'Log in', 'pikit-widget' ),
			'signup'        => __( 'Sign up', 'pikit-widget' ),
			'customer-area' => __( 'Customer area', 'pikit-widget' ),
			'history'       => __( 'Booking history', 'pikit-widget' ),
			'gift-card'     => __( 'Gift cards', 'pikit-widget' ),
			'shop'          => __( 'Shop', 'pikit-widget' ),
			'membership'    => __( 'Memberships', 'pikit-widget' ),
		);
	}

	/**
	 * Sanitize raw target input. Empty and invalid values are dropped.
	 *
	 * @param array $raw Keys: step, branch, service, provider, category.
	 * @return array Only the non-empty, valid values.
	 */
	public static function sanitize( $raw ): array {
		$raw    = is_array( $raw ) ? $raw : array();
		$target = array();

		$step = isset( $raw['step'] ) ? sanitize_key( $raw['step'] ) : '';
		if ( '' !== $step && array_key_exists( $step, self::get_steps() ) ) {
			$target['step'] = $step;
		}

		foreach ( array( 'branch', 'service', 'provider', 'category' ) as $key ) {
			$id = isset( $raw[ $key ] ) && is_scalar( $raw[ $key ] )
				? preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $raw[ $key ] )
				: '';
			if ( '' !== $id ) {
				$target[ $key ] = $id;
			}
		}

		return $target;
	}

	/**
	 * Data attributes for a trigger element, always including data-pikit-open.
	 *
	 * @param array $raw Raw target input.
	 * @return array Attribute name => value.
	 */
	public static function get_attributes( $raw ): array {
		$attributes = array( 'data-pikit-open' => '' );

		foreach ( self::sanitize( $raw ) as $key => $value ) {
			$attributes[ self::ATTRIBUTES[ $key ] ] = $value;
		}

		return $attributes;
	}

	/**
	 * Data attributes as an escaped HTML string (leading space included).
	 *
	 * @param array $raw Raw target input.
	 * @return string
	 */
	public static function get_attributes_html( $raw ): string {
		$html = '';

		foreach ( self::get_attributes( $raw ) as $name => $value ) {
			$html .= '' === $value
				? ' ' . $name
				: sprintf( ' %1$s="%2$s"', $name, esc_attr( $value ) );
		}

		return $html;
	}
}
