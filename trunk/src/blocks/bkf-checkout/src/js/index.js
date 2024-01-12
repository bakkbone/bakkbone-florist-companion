import { registerPlugin } from '@wordpress/plugins';

const render = () => {};

registerPlugin('floristpress', {
	render,
	scope: 'woocommerce-checkout',
});
