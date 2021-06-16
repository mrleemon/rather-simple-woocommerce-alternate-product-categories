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
	icon: 'category',
	category: 'widgets',
    keywords: [ __( 'woocommerce' ), __( 'products' ), __( 'categories' ) ],
    supports: {
        html: false,
    },
	attributes: {
		count: {
			type: 'boolean',
			default: false,
		},
		dropdown: {
			type: 'boolean',
			default: false,
		},
	},
    transforms: {
        from: [
            {
                type: 'block',
                blocks: [ 'occ/alternate-product-categories' ],
                isMatch: ( { idBase, instance } ) => {
                    if ( ! instance?.raw ) {
                        // Can't transform if raw instance is not shown in REST API.
                        return false;
                    }
                    return idBase === 'rswapc';
                },
                transform: ( { instance } ) => {
                    const transformedBlock = createBlock(
                        'occ/alternate-product-categories',
                        transform ? transform( instance.raw ) : undefined
                    );
                    if ( ! instance.raw?.title ) {
                        return transformedBlock;
                    }
                    return [
                        createBlock( 'core/heading', {
                            content: instance.raw.title,
                        } ),
                        transformedBlock,
                    ];
                },
            },
        ]
    },

	edit: ( props ) => {
		const attributes = props.attributes;

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
							'Settings',
							'rather-simple-woocommerce-alternate-product-categories'
						) }
					>
                        <ToggleControl
                            label={ __(
                                'Show product counts',
                                'rather-simple-woocommerce-alternate-product-categories'
                            ) }
                            checked={ !! attributes.count }
                            onChange={ toggleCount }
                        />
                        <ToggleControl
                            label={ __(
                                'Show as dropdown',
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
