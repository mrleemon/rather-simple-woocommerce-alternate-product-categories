/**
 * WordPress dependencies
 */
import {
	Path,
	SVG,
} from '@wordpress/primitives';
import {
	registerBlockType
} from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import Edit from './edit';
import transforms from './transforms';

import './editor.scss';
import './style.scss';

const { name } = metadata;

export const settings = {
	icon: {
        src: <SVG viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
		    <Path d="M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z" />
	        </SVG>,
        foreground: '#ff8a00'
    },

	edit: Edit,
	transforms,
};

registerBlockType(name, settings);
