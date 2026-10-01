// WordPress dependencies.
import {__} from '@wordpress/i18n';
import {Panel, PanelBody, PanelRow, TextControl} from '@wordpress/components';
import {useRef, useState} from '@wordpress/element';

/**
 * Settings component : UI component.
 * @returns {JSX.Element}
 * @constructor
 */
export const Settings = (props) => {

	const [inputValue, setInputValue] = useState('');

	// Handle input example.
	const handleInputChange = (value) => {
		setInputValue(value);
	};

	return (
		<Panel title={__('Basic Block', 'block-boilerplate')}>
			<PanelBody>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					style={{width: '100%'}}
					label={__('Input Value', 'block-boilerplate')}
					value={inputValue}
					onChange={handleInputChange}
					help={__('Enter a value to be used in the block.', 'block-boilerplate')}
				/>
			</PanelBody>
		</Panel>
	);
}
