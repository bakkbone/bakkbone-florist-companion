import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { TextareaControl, Disabled } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';

import './style.scss';

const { defaultCardMessageText } = getSetting('floristpress_data', '');

export const Edit = ({ attributes, setAttributes }) => {
	const { text } = attributes;
	const blockProps = useBlockProps();
	return (
		<div {...blockProps} style={{ display: 'block' }}>
		<InspectorControls>
		</InspectorControls>
			<div>
				<Disabled>
					<TextareaControl
						label={ __('Card Message', 'bakkbone-florist-companion') }
						className={
							'card-message-textarea'
						}
						value={''}
						required={true}
						help={__(
							"We'll include this with your gift.",
							'bakkbone-florist-companion'
						)}
					/>
				</Disabled>
			</div>
		</div>
	);
};