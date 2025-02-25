/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Disabled,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

const Edit = (props) => {

	const blockProps = useBlockProps();
	const {
		attributes: { dropdown, count },
		setAttributes,
	} = props;

	const toggleDropdown = () => {
		setAttributes({ dropdown: !props.attributes.dropdown });
	};

	const toggleCount = () => {
		setAttributes({ count: !props.attributes.count });
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
						checked={!!dropdown}
						onChange={toggleDropdown}
					/>
					<ToggleControl
						label={__(
							'Show product counts',
							'rather-simple-woocommerce-alternate-product-categories'
						)}
						checked={!!count}
						onChange={toggleCount}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<Disabled>
					<ServerSideRender
						block="occ/alternate-product-categories"
						attributes={props.attributes}
					/>
				</Disabled>
			</div>
		</>
	);

}

export default Edit;
