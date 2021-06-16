/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import {
    Disabled,
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';

import './style.scss';
import './editor.scss';

const name = 'occ/alternate-product-categories';

const settings = {
	title: __( 'Alternate Product Categories', 'rather-simple-woocommerce-alternate-product-categories' ),
	description: __( 'A Mailchimp form.', 'rather-simple-woocommerce-alternate-product-categories' ),
	icon: 'email',
	category: 'embed',
    keywords: [ __( 'email' ), __( 'newsletter' ) ],
    supports: {
        html: false,
        multiple: false,
    },
	attributes: {
		url: {
			type: 'string',
			default: '',
		},
		u: {
			type: 'string',
			default: '',
		},
		id: {
			type: 'string',
			default: '',
		},
		firstName: {
			type: 'boolean',
			default: false,
		},
		lastName: {
			type: 'boolean',
			default: false,
		},
	},

	edit: ( props ) => {
		const attributes = props.attributes;

		const setID = ( value ) => {
			props.setAttributes( { id: value } );
		};

		const setURL = ( value ) => {
			props.setAttributes( { url: value } );
		};

		const setU = ( value ) => {
			props.setAttributes( { u: value } );
		};

		const toggleFirstName = () => {
			props.setAttributes( { firstName: ! props.attributes.firstName } );
		};

		const toggleLastName = () => {
			props.setAttributes( { lastName: ! props.attributes.lastName } );
		};

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody
						title={ __(
							'Mailchimp Settings',
							'rather-simple-woocommerce-alternate-product-categories'
						) }
					>
						<TextControl
							label={ __( 'URL', 'rather-simple-woocommerce-alternate-product-categories' ) }
							type="url"
							value={ attributes.url }
							onChange={ setURL }
						/>
						<TextControl
							label={ __( 'U', 'rather-simple-woocommerce-alternate-product-categories' ) }
							type="text"
							value={ attributes.u }
							onChange={ setU }
						/>
						<TextControl
							label={ __( 'ID', 'rather-simple-woocommerce-alternate-product-categories' ) }
							type="text"
							value={ attributes.id }
							onChange={ setID }
						/>
						{ attributes.url && attributes.u && attributes.id && (
							<ToggleControl
								label={ __(
									'Show First Name',
									'rather-simple-woocommerce-alternate-product-categories'
								) }
								checked={ !! attributes.firstName }
								onChange={ toggleFirstName }
							/>
						) }
						{ attributes.url && attributes.u && attributes.id && (
							<ToggleControl
								label={ __(
									'Show Last Name',
									'rather-simple-woocommerce-alternate-product-categories'
								) }
								checked={ !! attributes.lastName }
								onChange={ toggleLastName }
							/>
						) }
					</PanelBody>
				</InspectorControls>
				<Disabled>
					<ServerSideRender
						block="occ/mailchimp"
						attributes={ attributes }
						className={ props.className }
					/>
				</Disabled>
			</Fragment>
		);
	},

	save: () => {
		return null;
	},

};

registerBlockType( name, settings );
