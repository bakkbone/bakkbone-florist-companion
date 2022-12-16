<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPetals
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

/**
 * BkfPetals
**/
class BkfPetals{
  
  function __construct() {
		$bkfoptions = get_option("bkf_options_setting");
		if($bkfoptions["bkf_petals"] == 1) {add_filter('manage_edit-shop_order_columns', array($this, 'bkf_petals_col_init'), 10, 1 ) ;};
		if($bkfoptions["bkf_petals"] == 1) {add_action( 'manage_shop_order_posts_custom_column' , array($this, 'bkf_petals_col'), 10, 2 ); };
    add_action( 'admin_head', array($this, 'bkf_hide_reject') );
  }
  
  // Add actions column to orders list
  
	function bkf_petals_col_init( $columns ) {
			$columns['petals'] = __( 'Petals Actions', 'woocommerce' );
			return $columns;
	}

  // Populate actions column
  
	function bkf_petals_col( $column, $post_id ) {
		$bkfoptions = get_option("bkf_options_setting");
			if ( $column == 'petals' ) {
				$order = wc_get_order( $post_id );
				if ( $order->has_status( 'new' ) ) {
					echo '<a href="https://api.bakkbone.au/webhook/' . esc_html($bkfoptions["bkf_petals_member_number"]) . '/accept?id=' . esc_html( $post_id ) . '"><img src="/wp-content/plugins/bakkbone-florist-companion/incl/img/Accept.png" width="30px" title="Accept" /></a>&nbsp;<a href="/reject?id=' . $post_id . '"><img src="/wp-content/plugins/bakkbone-florist-companion/incl/img/Reject.png" width="30px" title="Reject" /></a>';
			}
		}
	}
  
  	function bkf_hide_reject() {
	echo '<style type="text/css" id="bkf_hide_reject">a.wc-action-button.reject,a.wc-action-button.new,a.wc-action-button.accept { display:none !important; }</style>';
	}
  
  
}
