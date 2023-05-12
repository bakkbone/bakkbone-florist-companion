<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFees
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdFees{
	
    function __construct(){
		add_action( 'wp_ajax_woo_get_ajax_data', array($this,'woo_get_ajax_data' ));
		add_action( 'wp_ajax_nopriv_woo_get_ajax_data', array($this,'woo_get_ajax_data' ));
		add_action( 'wp_footer', array($this,'date_script'));
		add_action( 'wp_footer', array($this,'timeslot_script'));
		add_action( 'woocommerce_cart_calculate_fees', array($this,'add_ts_fee'), 20, 1 );
		add_action( 'woocommerce_cart_calculate_fees', array($this,'add_wf_fee'), 20, 1 );
		add_action( 'woocommerce_cart_calculate_fees', array($this,'add_ds_fee'), 20, 1 );
    }
	
	function woo_get_ajax_data() {
	    if ( isset($_POST['delivery_timeslot']) ){
	        $ts = sanitize_key( $_POST['delivery_timeslot'] );
	        WC()->session->set('delivery_timeslot', $ts );
	        echo json_encode( $ts );
	    }
	    if ( isset($_POST['delivery_date']) ){
	        $date = sanitize_key( $_POST['delivery_date'] );
			$timestamp = strtotime($date);
			$weekday = strtolower(wp_date("l",$timestamp));
	        WC()->session->set('delivery_date', $date );
	        WC()->session->set('delivery_timestamp', $timestamp );
	        WC()->session->set('delivery_weekday', $weekday );
	        echo json_encode( $date );
	    }
	    if ( isset($_POST['ship_type']) ){
	        $st = sanitize_key( $_POST['ship_type'] );
	        WC()->session->set('ship_type', $st );
			WC_Cache_Helper::get_transient_version( 'shipping', true );
	        echo json_encode( $st );
	    }
	    die();
	}

	function date_script() {
	    if ( is_checkout() && ! is_wc_endpoint_url() ) :

		    WC()->session->__unset('delivery_date');
		    WC()->session->__unset('delivery_timestamp');
		    WC()->session->__unset('delivery_weekday');
	    ?>
	    <script type="text/javascript" id="dd_ts_ajax">
	    jQuery( function($){
	        $('form.checkout').on('change', '#delivery_date', function(){
	            var ts = $(this).val();
	            $.ajax({
	                type: 'POST',
	                url: wc_checkout_params.ajax_url,
	                data: {
	                    'action': 'woo_get_ajax_data',
	                    'delivery_date': ts,
	                },
	                success: function (result) {
	                    $('body').trigger('update_checkout');
	                },
	                error: function(error){
	                    console.log(error);
	                }
	            });
	        });
	    });
	    </script>
	    <?php
	    endif;
	}
	
	function timeslot_script() {
	    if ( is_checkout() && ! is_wc_endpoint_url() ) :

	    WC()->session->__unset('delivery_timeslot');
	    ?>
	    <script type="text/javascript">
	    jQuery( function($){
	        $('form.checkout').on('change', '#delivery_timeslot', function(){
	            var ts = $(this).val();
	            $.ajax({
	                type: 'POST',
	                url: wc_checkout_params.ajax_url,
	                data: {
	                    'action': 'woo_get_ajax_data',
	                    'delivery_timeslot': ts,
	                },
	                success: function (result) {
	                    $('body').trigger('update_checkout');
	                },
	                error: function(error){
	                    console.log(error);
	                }
	            });
	        });
	    });
	    </script>
	    <?php
	    endif;
	}

	function add_ts_fee( $cart ) {
	    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
	        return;
		
		$ts = array();
		global $wpdb;
		$timeslots = $wpdb->get_results(
			"
				SELECT id, method, day, start, end, fee
				FROM {$wpdb->prefix}bkf_dd_timeslots
			"
		);
		foreach($timeslots as $timeslot){
			$ts[] = array(
				'id'		=>	$timeslot->id,
				'method'	=>	$timeslot->method,
				'day'		=>	$timeslot->day,
				'start'		=>	$timeslot->start,
				'end'		=>	$timeslot->end,
				'fee'		=>	$timeslot->fee
			);
		}
		
		$tax = get_option('bkf_ddf_setting')['ddtst'];
		
		if($tax == true){
			$taxable = "true";
		} else {
			$taxable = "false";
		}
		
	    $thets = WC()->session->get( 'delivery_timeslot' );
		$tscol = array_column($ts, 'id');
		$thistsid = array_search($thets, $tscol);
		$thists = $ts[$thistsid];
		
		if($thists !== null && $thists !== ''){
		    if ( $thists['fee'] !== '' && $thists['fee'] !== null ) {
		        $label = __("Time Slot Fee", "bakkbone-florist-companion");
		        $cost  = $thists['fee'];
		    }
		}


	    if ( isset($cost) )
	        $cart->add_fee( $label, $cost, $taxable );
	}
	
	function add_wf_fee( $cart ) {
	    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
	        return;
		
		$tax = get_option('bkf_ddf_setting')['ddwft'];
		
		if($tax == true){
			$taxable = "true";
		} else {
			$taxable = "false";
		}
		
		$setting = get_option('bkf_wf_setting');
	    $wd = WC()->session->get( 'delivery_weekday' );
		
		if($wd !== null && $wd !== ''){
		    if ( $setting[$wd] !== '' && $setting[$wd] !== null ) {
		        $label = ucwords($wd).__(" Surcharge", "bakkbone-florist-companion");
		        $cost  = $setting[$wd];
		    }
		}


	    if ( isset($cost) )
	        $cart->add_fee( $label, $cost, $taxable );
	}
	
	function add_ds_fee( $cart ) {
	    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
	        return;
		
		$tax = get_option('bkf_ddf_setting')['dddft'];
		
		if($tax == true){
			$taxable = "true";
		} else {
			$taxable = "false";
		}
		
		$setting = get_option('bkf_dd_ds_fees');
	    $ts = WC()->session->get( 'delivery_timestamp' );
		
		if($ts !== null && $ts !== ''){
			if(array_key_exists($ts,$setting)){
			    if ( $setting[$ts] !== '' && $setting[$ts] !== null ) {
			        $label = __("Surcharge: ", "bakkbone-florist-companion").stripslashes($setting[$ts]['title']);
			        $cost  = $setting[$ts]['fee'];
				}
			}
		}

	    if ( isset($cost) )
	        $cart->add_fee( $label, $cost, $taxable );
	}
	
}