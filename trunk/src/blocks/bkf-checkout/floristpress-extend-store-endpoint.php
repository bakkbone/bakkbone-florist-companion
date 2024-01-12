<?php
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;

class BKF_Extend_Store_Endpoint {
	private static $extend;
	
	const IDENTIFIER = 'floristpress';

	public static function init() {
		self::$extend = Automattic\WooCommerce\StoreApi\StoreApi::container()->get( Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::class );
		self::extend_store();
	}

	public static function extend_store() {
		if ( is_callable( [ self::$extend, 'register_endpoint_data' ] ) ) {
			self::$extend->register_endpoint_data(
				[
					'endpoint'        => CheckoutSchema::IDENTIFIER,
					'namespace'       => self::IDENTIFIER,
					'schema_callback' => [ 'BKF_Extend_Store_Endpoint', 'extend_checkout_schema' ],
					'schema_type'     => ARRAY_A,
				]
			);
		}
	}


	public static function extend_checkout_schema() {
        
        return [
            'cardMessage'   => [
            'description' => __('Card message to accompany gift', 'bakkbone-florist-companion'),
                'type'        => 'string',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
                'arg_options' => [
                    'validate_callback' => function( $value ) {
                        return is_string( $value );
                    },
                ]
            ],
        ];
    }
}
