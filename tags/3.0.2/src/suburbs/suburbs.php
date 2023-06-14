<?php

/**
 * @author BAKKBONE Australia
 * @package BkfSuburbs
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfSuburbs{

	function __construct(){
		add_filter( 'woocommerce_package_rates', array( $this, 'bkf_del_suburb_methods' ), PHP_INT_MAX , 2 );
		add_filter( 'woocommerce_shipping_packages', array($this, 'packages'));
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
	
	function packages($packages){
		do_action('qm/info', $packages);
		return $packages;
	}
	
}

add_filter( 'woocommerce_cart_shipping_packages', 'wp_kama_woocommerce_cart_shipping_packages_filter' );

/**
 * Function for `woocommerce_cart_shipping_packages` filter-hook.
 * 
 * @param  $array 
 *
 * @return 
 */
function wp_kama_woocommerce_cart_shipping_packages_filter( $array ){
		do_action('qm/debug', $array);

	// filter...
	return $array;
}