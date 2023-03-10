<?php

/**
 * @author BAKKBONE Australia
 * @package BkfSuburbs
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");


class BkfSuburbs{

	function __construct()
	{
        $bkffeatures = get_option("bkf_features_setting");
		if($bkffeatures["suburbs_on"] == "1") {
			add_filter( 'woocommerce_package_rates', array( $this, 'bkf_del_suburb_methods' ), 998 , 2 );
		}
	}
	
	function bkf_del_suburb_methods( $rates, $package ) {
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
			
		}		
		global $wpdb;
		$sub = array();
		$subs = $wpdb->get_results(
			"
				SELECT id, method, suburb
				FROM {$wpdb->prefix}bkf_suburbs
			"
		);
		foreach($subs as $suburb){
			$sub[] = array(
				'id'		=>	$suburb->id,
				'method'	=>	$suburb->method,
				'suburb'	=>	$suburb->suburb
			);
		}
		$hassub = array();
		$smid = array();
		foreach($sm as $sms){
			$smid[$sms['rateid']] = array();
			foreach($sub as $subby){
				if($subby['method'] == $sms['rateid']){
					$smid[$sms['rateid']][] = $subby['id'];
				}
			}
			if(empty($smid[$sms['rateid']])){
				$hassub[$sms['rateid']] = array('on' => false, 'id' => '') ;
			} else {
				$hassub[$sms['rateid']] = array('on' => true, 'id' => $sms['rateid']);
			}
		}
		
		$inc_array = array();
		$customer_sub = strtoupper( isset( $_REQUEST['s_city'] ) ? $_REQUEST['s_city'] : ( isset ( $_REQUEST['calc_shipping_city'] ) ? $_REQUEST['calc_shipping_city'] : ( ! empty( $user_city = WC()->customer->get_shipping_city() ) ? $user_city : WC()->countries->get_base_city() ) ) );
		
		$affected_rates = array();
		
		foreach( $rates as $rate_key => $rate ){
			if( $hassub[$rate_key]['on'] == true ){
				$affected_rates[] = $rate_key;
			}
		}
		
		foreach ( $rates as $rate_key => $rate ) {
	
			if(!in_array($rate_key, $affected_rates)){
				continue;
			}else{
				$valid = array();
				foreach($sub as $thissub){
					if($thissub['method'] == $rate_key && strtoupper($thissub['suburb']) == $customer_sub){
						$valid[] = $thissub['id'];
					}
				}
				if(empty($valid)){
					unset( $rates[$rate_key]);
				}else{
				}
				
			}
			
		}

		return $rates;
		
		}
		
		function check( $options_id, $values, $include_or_exclude, $package ) {
			switch ( $options_id ) {
				case 'cities':
					$customer_city = strtoupper( isset( $_REQUEST['s_city'] ) ? $_REQUEST['s_city'] : ( isset ( $_REQUEST['calc_shipping_city'] ) ? $_REQUEST['calc_shipping_city'] : ( ! empty( $user_city = WC()->customer->get_shipping_city() ) ? $user_city : WC()->countries->get_base_city() ) ) );
					$values        = array_map( 'strtoupper', array_map( 'trim', explode( PHP_EOL, $values ) ) );
					return in_array( $customer_city, $values );
				case 'postcodes':
					$customer_postcode = strtoupper( isset( $_REQUEST['s_postcode'] ) ? $_REQUEST['s_postcode'] : ( ! empty( $customer_shipping_postcode = WC()->customer->get_shipping_postcode() ) ? $customer_shipping_postcode : WC()->countries->get_base_postcode() ) );
					$postcodes         = array_map( 'strtoupper', array_map( 'trim', explode( PHP_EOL, $values ) ) );
					return wcj_check_postcode( $customer_postcode, $postcodes );
			}
		}
	
}