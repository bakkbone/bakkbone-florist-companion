<?php
defined( 'ABSPATH' ) || exit;

function bkf_register_block($registry) {
    require_once __DIR__ . '/floristpress-blocks-integration.php';
	$registry->register( new BKF_Blocks_Integration() );
}

add_action('woocommerce_blocks_loaded', function() {
	add_action( 'woocommerce_blocks_checkout_block_registration', 'bkf_register_block' );
});

function bkf_register_block_category( $categories ) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'floristpress',
                'title' => __( 'FloristPress Blocks', 'bakkbone-florist-companion' ),
            ],
        ]
    );
}

add_action( 'block_categories_all', 'bkf_register_block_category', 10, 2 );


function bkf_checkout_blocks_save( WC_Order $order, WP_REST_Request $request ) {
    $body = json_decode($request->get_body());
    $ext = $body->extensions;
    $values = (array) $ext->floristpress;
    $cm = $values['card_message'];
    $sn = isset($values['shipping_notes']) ? $values['shipping_notes'] : '';
    $order->update_meta_data('_card_message', $cm);
    $order->update_meta_data('_shipping_notes', $sn);
    $order->save();
}
add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'bkf_checkout_blocks_save', 10, 2 );