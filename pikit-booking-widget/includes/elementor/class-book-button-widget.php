<?php
/**
 * Elementor Pikit Button widget.
 *
 * Uses Elementor's Button_Trait for the same styling controls as the native Button widget.
 *
 * @package Pikit_Booking_Widget
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Includes\Widgets\Traits\Button_Trait;
use Elementor\Plugin;
use Elementor\Widget_Base;

/**
 * Elementor widget that opens the Pikit booking widget on click.
 */
class Pikit_Elementor_Book_Button_Widget extends Widget_Base {

	use Button_Trait;

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'pikit_button';
	}

	/**
	 * Widget title shown in the Elementor panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Pikit Button', 'pikit-booking-widget' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-calendar';
	}

	/**
	 * Widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'pikit', 'general' );
	}

	/**
	 * Keywords for search in the Elementor widget panel.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'pikit', 'button', 'book', 'booking', 'appointment', 'schedule', 'widget' );
	}

	/**
	 * Register controls (content + style matching Elementor Button).
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'Pikit Button', 'pikit-booking-widget' ),
			)
		);

		$this->register_button_content_controls(
			array(
				'button_default_text' => __( 'Book now', 'pikit-booking-widget' ),
			)
		);

		$this->remove_control( 'link' );
		$this->remove_control( 'button_css_id' );

		$this->add_control(
			'custom_classes',
			array(
				'label'       => __( 'CSS Classes', 'pikit-booking-widget' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => Pikit_Book_Button_Renderer::DEFAULT_CLASS,
				'description' => __( 'Extra classes on the button element.', 'pikit-booking-widget' ),
				'separator'   => 'before',
				'ai'          => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'pikit_trigger_info',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Opens the Pikit booking widget when clicked. The trigger link is fixed to <code>#pikit-open</code>.', 'pikit-booking-widget' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Button', 'pikit-booking-widget' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->register_button_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the front end.
	 */
	protected function render() {
		$this->render_button();
	}

	/**
	 * Render button markup with a fixed Pikit booking trigger.
	 *
	 * @param Widget_Base|null $instance Widget instance.
	 */
	protected function render_button( Widget_Base $instance = null ) {
		if ( empty( $instance ) ) {
			$instance = $this;
		}

		$settings = $instance->get_settings_for_display();

		if ( empty( $settings['text'] ) && empty( $settings['selected_icon']['value'] ) ) {
			return;
		}

		$optimized_markup = Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );

		$instance->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

		$instance->add_render_attribute( 'button', 'class', 'elementor-button elementor-button-link' );

		$custom_classes = isset( $settings['custom_classes'] ) ? $settings['custom_classes'] : '';
		if ( '' === trim( (string) $custom_classes ) && ! empty( $settings['custom_class'] ) ) {
			$custom_classes = $settings['custom_class'];
		}
		if ( is_string( $custom_classes ) && '' !== trim( $custom_classes ) ) {
			$instance->add_render_attribute( 'button', 'class', $custom_classes );
		} else {
			$instance->add_render_attribute( 'button', 'class', Pikit_Book_Button_Renderer::DEFAULT_CLASS );
		}

		$instance->add_link_attributes(
			'button',
			array(
				'url'         => '#pikit-open',
				'is_external' => false,
				'nofollow'    => false,
			)
		);
		$instance->add_render_attribute( 'button', 'id', 'pikit-open' );

		if ( ! empty( $settings['size'] ) ) {
			$instance->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
		} else {
			$instance->add_render_attribute( 'button', 'class', 'elementor-size-sm' );
		}

		if ( ! empty( $settings['hover_animation'] ) ) {
			$instance->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}
		?>
		<?php if ( ! $optimized_markup ) : ?>
		<div <?php $instance->print_render_attribute_string( 'wrapper' ); ?>>
		<?php endif; ?>
			<a <?php $instance->print_render_attribute_string( 'button' ); ?>>
				<?php $this->render_text( $instance ); ?>
			</a>
		<?php if ( ! $optimized_markup ) : ?>
		</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Editor preview template (Backbone).
	 */
	protected function content_template() {
		?>
		<#
		if ( '' === settings.text && '' === settings.selected_icon.value ) {
			return;
		}

		const optimized_markup = elementorCommon.config.experimentalFeatures.e_optimized_markup;

		view.addRenderAttribute( 'wrapper', 'class', 'elementor-button-wrapper' );

		view.addRenderAttribute( 'button', 'class', 'elementor-button elementor-button-link' );

		if ( settings.custom_classes ) {
			view.addRenderAttribute( 'button', 'class', settings.custom_classes );
		}

		view.addRenderAttribute( 'button', 'href', '#pikit-open' );
		view.addRenderAttribute( 'button', 'id', 'pikit-open' );

		if ( '' !== settings.size ) {
			view.addRenderAttribute( 'button', 'class', 'elementor-size-' + settings.size );
		} else {
			view.addRenderAttribute( 'button', 'class', 'elementor-size-sm' );
		}

		if ( '' !== settings.hover_animation ) {
			view.addRenderAttribute( 'button', 'class', 'elementor-animation-' + settings.hover_animation );
		}

		view.addRenderAttribute( 'icon', 'class', 'elementor-button-icon' );
		view.addRenderAttribute( 'text', 'class', 'elementor-button-text' );
		view.addInlineEditingAttributes( 'text', 'none' );
		var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );
		#>
		<# if ( ! optimized_markup ) { #>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
		<# } #>
			<a {{{ view.getRenderAttributeString( 'button' ) }}}>
				<span class="elementor-button-content-wrapper">
					<# if ( settings.icon || settings.selected_icon ) { #>
					<span {{{ view.getRenderAttributeString( 'icon' ) }}}>
						<# if ( ( migrated || ! settings.icon ) && iconHTML.rendered ) { #>
							{{{ iconHTML.value }}}
						<# } else { #>
							<i class="{{ settings.icon }}" aria-hidden="true"></i>
						<# } #>
					</span>
					<# } #>
					<# if ( settings.text ) { #>
					<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
					<# } #>
				</span>
			</a>
		<# if ( ! optimized_markup ) { #>
		</div>
		<# } #>
		<?php
	}
}
