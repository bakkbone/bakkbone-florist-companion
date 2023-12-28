<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Petals_Outbound
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Petals_Outbound {
  
	function __construct() {
	  $bkfoptions = get_option("bkf_features_setting");
	  if($bkfoptions["petals_on"] == 1) {
		  add_filter('woocommerce_order_data_store_cpt_get_orders_query', [$this, 'handle_custom_query_var'], 10, 2 );
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