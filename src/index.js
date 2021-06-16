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
	description: __( 'An alternate product categories block.', 'rather-simple-woocommerce-alternate-product-categories' ),
	icon: 'email',
	category: 'embed',
    keywords: [ __( 'email' ), __( 'newsletter' ) ],
    supports: {
        html: false,
        multiple: false,
    },
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		count: {
			type: 'boolean',
			default: false,
		},
		dropdown: {
			type: 'boolean',
			default: false,
		},
	},

	edit: ( props ) => {
		const attributes = props.attributes;

		const setTitle = ( value ) => {
			props.setAttributes( { title: value } );
		};

		const toggleCount = () => {
			props.setAttributes( { count: ! props.attributes.count } );
		};

		const toggleDropdown = () => {
			props.setAttributes( { dropdown: ! props.attributes.dropdown } );
		};

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody
						title={ __(
							'Alternate Product Categories Settings',
							'rather-simple-woocommerce-alternate-product-categories'
						) }
					>
						<TextControl
							label={ __( 'Title', 'rather-simple-woocommerce-alternate-product-categories' ) }
							type="text"
							value={ attributes.title }
							onChange={ setTitle }
						/>
                        <ToggleControl
                            label={ __(
                                'Show Count',
                                'rather-simple-woocommerce-alternate-product-categories'
                            ) }
                            checked={ !! attributes.count }
                            onChange={ toggleCount }
                        />
                        <ToggleControl
                            label={ __(
                                'Show Dropdown',
                                'rather-simple-woocommerce-alternate-product-categories'
                            ) }
                            checked={ !! attributes.dropdown }
                            onChange={ toggleDropdown }
                        />
					</PanelBody>
				</InspectorControls>
				<Disabled>
					<ServerSideRender
						block="occ/alternate-product-categories"
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
