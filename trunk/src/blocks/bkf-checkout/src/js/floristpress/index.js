import { registerBlockType } from '@wordpress/blocks';
import { Icon, postContent } from '@wordpress/icons';

import { Edit } from './edit';
import metadata from './block.json';

registerBlockType(metadata, {
	icon: { src: <Icon icon={ postContent } />, },
	edit: Edit,
});
