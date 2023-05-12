<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPetalsOutbound
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfPetalsOutbound{
  
    function __construct() {
      $bkfoptions = get_option("bkf_features_setting");
      if($bkfoptions["petals_on"] == 1) {
          add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'handle_custom_query_var'), 10, 2 );
      };
    }
    
    function handle_custom_query_var( $query, $query_vars ) {
    	if ( ! empty( $query_vars['_petals_on'] ) ) {
    		$query['meta_query'][] = array(
    			'key' => '_petals_on',
    			'value' => esc_attr( $query_vars['_petals_on'] ),
    		);
    	}
    	return $query;
    }

}