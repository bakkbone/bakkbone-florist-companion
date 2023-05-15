<?php

/**
 * @author BAKKBONE Australia
 * @package BkfSuburbs
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfSuburbs{

	function __construct()
	{
		add_filter( 'woocommerce_package_rates', array( $this, 'bkf_del_suburb_methods' ), 998 , 2 );
	}
	
	function bkf_del_suburb_methods( $rates, $package ) {
		$sm = bkf_get_shipping_rates();	
		$customer_sub = strtoupper( isset( $_REQUEST['s_city'] ) ? $_REQUEST['s_city'] : ( isset ( $_REQUEST['calc_shipping_city'] ) ? $_REQUEST['calc_shipping_city'] : ( ! empty( $user_city = WC()->customer->get_shipping_city() ) ? $user_city : WC()->countries->get_base_city() ) ) );
		
		foreach($sm as $smethod){
			if($smethod['hassuburbs']){
				$suburbs = array_map('strtoupper', $smethod['suburbs']);
				
				if(!in_array($customer_sub, $suburbs)){
					unset($rates[$smethod['rateid']]);
				}
			}
		}

		return $rates;
		
		}
	
}