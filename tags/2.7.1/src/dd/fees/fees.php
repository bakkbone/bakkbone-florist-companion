<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFees
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfDdFees{
	
    function __construct(){
		add_action( 'wp_footer', array($this,'date_script'));
		add_action( 'wp_footer', array($this,'timeslot_script'));
		add_action( 'woocommerce_cart_calculate_fees', array($this,'add_ts_fee'), 20, 1 );
		add_action( 'woocommerce_cart_calculate_fees', array($this,'add_wf_fee'), 20, 1 );
		add_action( 'woocommerce_cart_calculate_fees', array($this,'add_ds_fee'), 20, 1 );
    }

	function date_script() {
	    if ( is_checkout() && ! is_wc_endpoint_url() ) :

		    WC()->session->__unset('delivery_date');
		    WC()->session->__unset('delivery_timestamp');
		    WC()->session->__unset('delivery_weekday');
	    ?>
	    <script type="text/javascript" id="dd_ts_ajax">
	    jQuery( function($){
	        jQuery('form.checkout').on('change', '#delivery_date', function(){
	            var ts = jQuery(this).val();
	            jQuery.ajax({
	                type: 'POST',
	                url: wc_checkout_params.ajax_url,
	                data: {
	                    'action': 'woo_get_ajax_data',
	                    'delivery_date': ts,
	                },
	                success: function (result) {
	                    jQuery('body').trigger('update_checkout');
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
	        jQuery('form.checkout').on('change', '#delivery_timeslot', function(){
	            var ts = jQuery(this).val();
	            jQuery.ajax({
	                type: 'POST',
	                url: wc_checkout_params.ajax_url,
	                data: {
	                    'action': 'woo_get_ajax_data',
	                    'delivery_timeslot': ts,
	                },
	                success: function (result) {
	                    jQuery('body').trigger('update_checkout');
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
		
		$ts = bkf_get_timeslots();

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