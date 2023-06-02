/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import {
    Disabled,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from "./block.json";

import './style.scss';
import './editor.scss';

const { name } = metadata;

const settings = {
    
    transforms: {
        from: [
            {
                type: 'block',
                blocks: [ 'core/legacy-widget' ],
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
                        {
                            dropdown: instance.raw.dropdown,
                            count: instance.raw.count,
                        }
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

		const toggleDropdown = () => {
			props.setAttributes( { dropdown: ! props.attributes.dropdown } );
		};

		const toggleCount = () => {
			props.setAttributes( { count: ! props.attributes.count } );
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
                                'Show as dropdown',
                                'rather-simple-woocommerce-alternate-product-categories'
                            ) }
                            checked={ !! attributes.dropdown }
                            onChange={ toggleDropdown }
                        />
                        <ToggleControl
                            label={ __(
                                'Show product counts',
                                'rather-simple-woocommerce-alternate-product-categories'
                            ) }
                            checked={ !! attributes.count }
                            onChange={ toggleCount }
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
