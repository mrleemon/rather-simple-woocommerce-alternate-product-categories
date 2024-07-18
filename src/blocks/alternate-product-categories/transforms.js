/**
 * WordPress dependencies
 */
import {
	createBlock
} from '@wordpress/blocks';

const transforms = {
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
}

export default transforms;
