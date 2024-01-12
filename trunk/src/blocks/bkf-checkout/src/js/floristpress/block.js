import { useEffect, useState, useCallback } from '@wordpress/element';
import { TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { debounce } from 'lodash';

export const Block = ({ checkoutExtensionData, extensions }) => {
	const { setExtensionData } = checkoutExtensionData;
	const debouncedSetExtensionData = useCallback(
		debounce((namespace, key, value) => {
			setExtensionData(namespace, key, value);
		}, 1000),
		[setExtensionData]
	);

	const validationErrorId = 'floristpress-value';

	const { setValidationErrors, clearValidationError } = useDispatch(
		'wc/store/validation'
	);

	const validationError = useSelect((select) => {
		const store = select('wc/store/validation');
		return store.getValidationError( validationErrorId );
	});
	const [
		currentCardMessage,
		setCurrentCardMessage,
	] = useState('');

	useEffect(() => {
		setExtensionData('floristpress', 'currentCardMessage', currentCardMessage);
		if ( currentCardMessage !== '' ) {
			if ( validationError ) {
				clearValidationError( validationErrorId );
			}
			return;
		}
		setValidationErrors( {
			[ validationErrorId ]: {
				message: __( 'Please enter your card message.', 'bakkbone-florist-companion' ),
				hidden: true,
			},
		} );
	},  [
		clearValidationError,
		currentCardMessage,
		setValidationErrors,
		validationErrorId,
		currentCardMessage,
		debouncedSetExtensionData,
		validationError,
	]);
	
	wp.data.dispatch('wc/store/checkout').__internalSetUseShippingAsBilling(true);
	
	return (
		<div className="wp-block-card-message">
			<TextareaControl
				label={ __('Card Message', 'bakkbone-florist-companion') }
				className={
					'card-message-textarea' +
					( validationError?.hidden === false
						? ' has-error'
						: '' )
				}
				onChange={ ( e ) => {
					setCurrentCardMessage( e );
				} }
				value={ currentCardMessage }
				required={ true }
				help={ __(
					"We'll include this with your gift.",
					'bakkbone-florist-companion'
				) }
			/>
			{ validationError?.hidden ? null : (
				<div className="wc-block-components-validation-error">
					{ validationError?.message }
				</div>
			) }
		</div>
	);
};