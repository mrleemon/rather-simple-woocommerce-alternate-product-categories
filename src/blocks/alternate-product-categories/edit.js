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
import ServerSideRender from '@wordpress/server-side-render';

const Edit = (props) => {

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

}

export default Edit;
