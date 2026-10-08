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
import {
	PanelBody,
	Button,
	ButtonGroup,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './editor.scss';
import './style.scss';

const PIKIT_TRIGGER_HREF = '#pikit-open';
const PIKIT_TRIGGER_ID = 'pikit-open';

const PIKIT_STEPS = [
	{ value: '', label: __( 'Default (first page)', 'pikit-widget' ) },
	{ value: 'type', label: __( 'Appointment type', 'pikit-widget' ) },
	{ value: 'location', label: __( 'Location', 'pikit-widget' ) },
	{ value: 'services', label: __( 'Services', 'pikit-widget' ) },
	{ value: 'login', label: __( 'Log in', 'pikit-widget' ) },
	{ value: 'signup', label: __( 'Sign up', 'pikit-widget' ) },
	{ value: 'customer-area', label: __( 'Customer area', 'pikit-widget' ) },
	{ value: 'history', label: __( 'Booking history', 'pikit-widget' ) },
	{ value: 'gift-card', label: __( 'Gift cards', 'pikit-widget' ) },
	{ value: 'shop', label: __( 'Shop', 'pikit-widget' ) },
	{ value: 'membership', label: __( 'Memberships', 'pikit-widget' ) },
];

const PIKIT_ID_FIELDS = [
	{ name: 'branch', label: __( 'Location ID', 'pikit-widget' ) },
	{ name: 'category', label: __( 'Category ID', 'pikit-widget' ) },
	{ name: 'provider', label: __( 'Provider ID', 'pikit-widget' ) },
	{ name: 'service', label: __( 'Service ID', 'pikit-widget' ) },
];

/**
 * Keep only characters allowed in a Pikit ID.
 *
 * @param {string} value Raw input.
 * @return {string}
 */
function cleanId( value ) {
	return value.replace( /[^A-Za-z0-9_-]/g, '' );
}

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
 * "Open widget at" panel: choose the page the widget opens on.
 *
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set attributes callback.
 */
function TargetPanel( { attributes, setAttributes } ) {
	return (
		<PanelBody
			title={ __( 'Open widget at', 'pikit-widget' ) }
			initialOpen={ false }
		>
			<SelectControl
				__nextHasNoMarginBottom
				label={ __( 'Page', 'pikit-widget' ) }
				value={ attributes.step || '' }
				options={ PIKIT_STEPS }
				onChange={ ( value ) => setAttributes( { step: value } ) }
			/>
			{ PIKIT_ID_FIELDS.map( ( field ) => (
				<TextControl
					key={ field.name }
					__nextHasNoMarginBottom
					label={ field.label }
					value={ attributes[ field.name ] || '' }
					onChange={ ( value ) =>
						setAttributes( { [ field.name ]: cleanId( value ) } )
					}
				/>
			) ) }
			<p className="pikit-button-panel-note">
				{ __(
					'All fields are optional. Copy IDs from your Pikit dashboard. Leave empty to open the default first page.',
					'pikit-widget'
				) }
			</p>
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
				<TargetPanel
					attributes={ attributes }
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
