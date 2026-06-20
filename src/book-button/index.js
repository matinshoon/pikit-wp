/**
 * Pikit Button block — styling matches the core Button block.
 *
 * @package Pikit_Booking_Widget
 */

import {
	registerBlockType,
	getColorClassName,
	getGradientClass,
} from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	BlockControls,
	AlignmentControl,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles,
	__experimentalGetElementClassName,
} from '@wordpress/block-editor';
import { PanelBody, Button, ButtonGroup } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './editor.scss';
import './style.scss';

const PIKIT_TRIGGER_HREF = '#pikit-open';
const PIKIT_TRIGGER_ID = 'pikit-open';

/**
 * Join class names (clsx-style).
 *
 * @param {...*} parts Class strings or { class: bool } maps.
 * @return {string}
 */
function clsx( ...parts ) {
	const classes = [];

	for ( const part of parts ) {
		if ( ! part ) {
			continue;
		}

		if ( typeof part === 'string' ) {
			classes.push( part );
			continue;
		}

		if ( typeof part === 'object' ) {
			for ( const [ key, value ] of Object.entries( part ) ) {
				if ( value ) {
					classes.push( key );
				}
			}
		}
	}

	return classes.join( ' ' );
}

/**
 * Width panel (same options as core Button).
 *
 * @param {Object}   props                 Component props.
 * @param {number}   props.selectedWidth   Current width percentage.
 * @param {Function} props.setAttributes   Set attributes callback.
 */
function WidthPanel( { selectedWidth, setAttributes } ) {
	return (
		<PanelBody title={ __( 'Settings', 'pikit-widget' ) }>
			<p className="pikit-button-panel-note">
				{ __(
					'This button always opens the Pikit booking widget. The link is fixed to #pikit-open.',
					'pikit-widget'
				) }
			</p>
			<p className="components-base-control__label">
				{ __( 'Button width', 'pikit-widget' ) }
			</p>
			<ButtonGroup aria-label={ __( 'Button width', 'pikit-widget' ) }>
				{ [ 25, 50, 75, 100 ].map( ( widthValue ) => (
					<Button
						key={ widthValue }
						size="small"
						variant={
							widthValue === selectedWidth ? 'primary' : undefined
						}
						onClick={ () =>
							setAttributes( {
								width:
									selectedWidth === widthValue
										? undefined
										: widthValue,
							} )
						}
					>
						{ widthValue }%
					</Button>
				) ) }
			</ButtonGroup>
		</PanelBody>
	);
}

/**
 * Editor component.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set attributes.
 * @param {string}   props.className     Block class name.
 */
function Edit( { attributes, setAttributes, className } ) {
	const { textAlign, style, text, width } = attributes;

	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const spacingProps = getSpacingClassesAndStyles( attributes );
	const shadowProps = getShadowClassesAndStyles( attributes );

	const blockProps = useBlockProps( {
		className: clsx( 'wp-block-button', {
			[ `has-custom-width wp-block-button__width-${ width }` ]: width,
			[ 'has-custom-font-size' ]: style?.typography?.fontSize,
		} ),
	} );

	const linkClassName = clsx(
		className,
		'wp-block-button__link',
		'pikit-book-button',
		colorProps.className,
		borderProps.className,
		{
			[ `has-text-align-${ textAlign }` ]: textAlign,
			'no-border-radius': style?.border?.radius === 0,
		},
		__experimentalGetElementClassName( 'button' )
	);

	const linkStyle = {
		...borderProps.style,
		...colorProps.style,
		...spacingProps.style,
		...shadowProps.style,
	};

	return (
		<>
			<BlockControls group="block">
				<AlignmentControl
					value={ textAlign }
					onChange={ ( nextAlign ) =>
						setAttributes( { textAlign: nextAlign } )
					}
				/>
			</BlockControls>
			<InspectorControls>
				<WidthPanel
					selectedWidth={ width }
					setAttributes={ setAttributes }
				/>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="a"
					href={ PIKIT_TRIGGER_HREF }
					id={ PIKIT_TRIGGER_ID }
					aria-label={ __( 'Button text', 'pikit-widget' ) }
					placeholder={ __( 'Book now', 'pikit-widget' ) }
					value={ text }
					onChange={ ( value ) => setAttributes( { text: value } ) }
					onClick={ ( event ) => event.preventDefault() }
					withoutInteractiveFormatting
					className={ linkClassName }
					style={ linkStyle }
					identifier="text"
				/>
			</div>
		</>
	);
}

/**
 * Save markup (matches core Button serialization + Pikit trigger).
 *
 * @param {Object} props            Block props.
 * @param {Object} props.attributes Block attributes.
 * @param {string} props.className  Block class name.
 */
function save( { attributes, className } ) {
	const { textAlign, fontSize, style, text, width, backgroundColor, textColor, gradient } =
		attributes;

	const borderProps = getBorderClassesAndStyles( attributes );
	const colorProps = getColorClassesAndStyles( attributes );
	const spacingProps = getSpacingClassesAndStyles( attributes );
	const shadowProps = getShadowClassesAndStyles( attributes );

	const backgroundClass = getColorClassName( 'background-color', backgroundColor );
	const textClass = getColorClassName( 'color', textColor );
	const gradientClass = getGradientClass( gradient );

	const buttonClasses = clsx(
		'wp-block-button__link',
		'pikit-book-button',
		colorProps.className,
		borderProps.className,
		backgroundClass,
		textClass,
		gradientClass,
		{
			[ `has-text-align-${ textAlign }` ]: textAlign,
			'no-border-radius': style?.border?.radius === 0,
		},
		__experimentalGetElementClassName( 'button' )
	);

	const buttonStyle = {
		...borderProps.style,
		...colorProps.style,
		...spacingProps.style,
		...shadowProps.style,
	};

	const wrapperClasses = clsx( className, 'wp-block-button', {
		[ `has-custom-width wp-block-button__width-${ width }` ]: width,
		[ 'has-custom-font-size' ]: fontSize || style?.typography?.fontSize,
	} );

	return (
		<div { ...useBlockProps.save( { className: wrapperClasses } ) }>
			<RichText.Content
				tagName="a"
				className={ buttonClasses }
				style={ buttonStyle }
				href={ PIKIT_TRIGGER_HREF }
				id={ PIKIT_TRIGGER_ID }
				value={ text }
			/>
		</div>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
