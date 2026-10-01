/**
 * Block type editor script definition.
 * It will only be enqueued in the context of the editor.
 */
/**
 *  WordPress dependencies
 */
import {cog as icon} from '@wordpress/icons';
import {registerBlockType} from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import Edit from './edit';
import Save from './save';

const {name} = metadata;

const settings = {
	icon,
	edit: Edit,
	save: Save,
};

registerBlockType(name, settings);
