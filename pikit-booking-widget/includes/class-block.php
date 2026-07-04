<?php
/**
 * Gutenberg block registration.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Pikit Button block for the block editor.
 */
class Pikit_Booking_Block {

	/**
	 * Legacy block name (v1.0.0) kept for backwards compatibility.
	 */
	const LEGACY_BLOCK_NAME = 'pikit/book-button';

	/**
	 * @var Pikit_Booking_Block|null
	 */
	private static $instance = null;

	/**
	 * @return Pikit_Booking_Block
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
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register the Pikit block category in the block inserter.
	 *
	 * @param array                    $categories       Block categories.
	 * @param WP_Block_Editor_Context  $editor_context   Editor context.
	 * @return array
	 */
	public function register_block_category( $categories, $editor_context ) {
		if ( empty( $editor_context->post ) ) {
			return $categories;
		}

		array_unshift(
			$categories,
			array(
				'slug'  => 'pikit',
				'title' => __( 'Pikit', 'pikit-widget' ),
				'icon'  => null,
			)
		);

		return $categories;
	}

	/**
	 * Register block type from build output.
	 */
	public function register_block() {
		$block_dir = PIKIT_BOOKING_WIDGET_DIR . 'build/book-button';

		if ( ! file_exists( $block_dir . '/block.json' ) ) {
			return;
		}

		$block_type = register_block_type(
			$block_dir,
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);

		if ( $block_type ) {
			register_block_type(
				self::LEGACY_BLOCK_NAME,
				array(
					'render_callback' => array( $this, 'render_block' ),
				)
			);
		}
	}

	/**
	 * Server-side block render.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Saved block markup.
	 * @param WP_Block $block      Block instance.
	 * @return string
	 */
	public function render_block( $attributes, $content, $block ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$content    = is_string( $content ) ? trim( $content ) : '';

		if ( '' !== $content ) {
			return $this->ensure_pikit_trigger_markup( $content );
		}

		return $this->render_legacy_block( $attributes, $block );
	}

	/**
	 * Force the Pikit booking trigger on anchor elements in saved markup.
	 *
	 * @param string $content Saved block HTML.
	 * @return string
	 */
	private function ensure_pikit_trigger_markup( $content ) {
		if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
			$processor = new WP_HTML_Tag_Processor( $content );

			while ( $processor->next_tag( 'A' ) ) {
				$processor->set_attribute( 'href', '#pikit-open' );
				$processor->set_attribute( 'id', 'pikit-open' );
			}

			return wp_kses_post( $processor->get_updated_html() );
		}

		$updated = preg_replace(
			'/<a\s+/i',
			'<a id="pikit-open" href="#pikit-open" ',
			$content,
			1
		);

		return wp_kses_post( $updated );
	}

	/**
	 * Render legacy dynamic blocks (no saved inner HTML).
	 *
	 * @param array    $attributes Block attributes.
	 * @param WP_Block $block      Block instance.
	 * @return string
	 */
	private function render_legacy_block( $attributes, $block ) {
		$text = '';

		if ( ! empty( $attributes['text'] ) ) {
			$text = $attributes['text'];
		} elseif ( ! empty( $attributes['content'] ) ) {
			$text = $attributes['content'];
		} else {
			$text = __( 'Book now', 'pikit-widget' );
		}

		$wrapper_classes = array( 'wp-block-button' );

		if ( ! empty( $attributes['align'] ) ) {
			$wrapper_classes[] = 'align' . sanitize_html_class( $attributes['align'] );
		}

		if ( ! empty( $attributes['width'] ) ) {
			$wrapper_classes[] = 'has-custom-width';
			$wrapper_classes[] = 'wp-block-button__width-' . (int) $attributes['width'];
		}

		$link_classes = array(
			'wp-block-button__link',
			'wp-element-button',
			Pikit_Book_Button_Renderer::DEFAULT_CLASS,
		);

		if ( ! empty( $attributes['backgroundColor'] ) ) {
			$link_classes[] = 'has-' . sanitize_html_class( $attributes['backgroundColor'] ) . '-background-color';
			$link_classes[] = 'has-background';
		}

		if ( ! empty( $attributes['textColor'] ) ) {
			$link_classes[] = 'has-' . sanitize_html_class( $attributes['textColor'] ) . '-color';
			$link_classes[] = 'has-text-color';
		}

		if ( ! empty( $attributes['gradient'] ) ) {
			$link_classes[] = 'has-' . sanitize_html_class( $attributes['gradient'] ) . '-gradient-background';
		}

		if ( ! empty( $attributes['textAlign'] ) ) {
			$link_classes[] = 'has-text-align-' . sanitize_html_class( $attributes['textAlign'] );
		}

		$link_style = '';
		if ( ! empty( $attributes['style'] ) && function_exists( 'wp_style_engine_get_styles' ) ) {
			$styles = wp_style_engine_get_styles( $attributes['style'] );
			if ( ! empty( $styles['css'] ) ) {
				$link_style = $styles['css'];
			}
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => implode( ' ', $wrapper_classes ),
			)
		);

		return sprintf(
			'<div %1$s><a class="%2$s" href="#pikit-open" id="pikit-open" style="%3$s">%4$s</a></div>',
			$wrapper_attributes,
			esc_attr( implode( ' ', array_unique( $link_classes ) ) ),
			esc_attr( $link_style ),
			wp_kses_post( $text )
		);
	}
}
