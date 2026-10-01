// WordPress dependencies.
import {__} from '@wordpress/i18n';
import {useBlockProps, InspectorControls, BlockControls} from '@wordpress/block-editor';
import {Panel, ToolbarButton, ToolbarGroup} from '@wordpress/components';

// Internal Dependencies.
import {Settings} from "./components/Settings";

const Edit = (props) => {
	const {
		attributes: {source, className},
		setAttributes,
	} = props;

	const blockProps = useBlockProps({
		className: 'custom-class-name'
	});

	return (
		<div {...blockProps}>
			<InspectorControls>
				<Settings {...props} />
			</InspectorControls>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={"edit"}
						label={__('Example', 'block-boilerplate')}
						onClick={() => {

						}}
					/>
				</ToolbarGroup>
			</BlockControls>
			{"placeholder"}
		</div>
	);
};

export default Edit;
