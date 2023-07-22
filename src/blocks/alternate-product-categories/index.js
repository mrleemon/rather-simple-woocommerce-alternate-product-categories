/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    Disabled,
    PanelBody,
    ToggleControl,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import metadata from './block.json';

import './style.scss';
import './editor.scss';

const { name } = metadata;

const settings = {

    icon: {
        src: <SVG viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
		    <Path d="M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z" />
	        </SVG>,
        foreground: '#ff8a00'
    },

    edit: (props) => {
        const attributes = props.attributes;

        const toggleDropdown = () => {
            props.setAttributes({ dropdown: !props.attributes.dropdown });
        };

        const toggleCount = () => {
            props.setAttributes({ count: !props.attributes.count });
        };

        return (
            <>
                <InspectorControls>
                    <PanelBody
                        title={__(
                            'Settings',
                            'rather-simple-woocommerce-alternate-product-categories'
                        )}
                    >
                        <ToggleControl
                            label={__(
                                'Show as dropdown',
                                'rather-simple-woocommerce-alternate-product-categories'
                            )}
                            checked={!!attributes.dropdown}
                            onChange={toggleDropdown}
                        />
                        <ToggleControl
                            label={__(
                                'Show product counts',
                                'rather-simple-woocommerce-alternate-product-categories'
                            )}
                            checked={!!attributes.count}
                            onChange={toggleCount}
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block="occ/alternate-product-categories"
                        attributes={attributes}
                    />
                </Disabled>
            </>
        );
    },

    transforms: {
        from: [
            {
                type: 'block',
                blocks: ['core/legacy-widget'],
                isMatch: ({ idBase, instance }) => {
                    if (!instance?.raw) {
                        // Can't transform if raw instance is not shown in REST API.
                        return false;
                    }
                    return idBase === 'rswapc';
                },
                transform: ({ instance }) => {
                    const transformedBlock = createBlock(
                        'occ/alternate-product-categories',
                        {
                            dropdown: instance.raw.dropdown,
                            count: instance.raw.count,
                        }
                    );
                    if (!instance.raw?.title) {
                        return transformedBlock;
                    }
                    return [
                        createBlock('core/heading', {
                            content: instance.raw.title,
                        }),
                        transformedBlock,
                    ];
                },
            },
        ]
    },

};

registerBlockType(name, settings);
