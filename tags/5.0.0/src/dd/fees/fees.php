<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Fees_Core
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Delivery_Date_Fees_Core{
	
	function __construct(){
		add_action( 'wp_footer', [$this, 'date_script']);
		add_action( 'woocommerce_cart_calculate_fees', [$this, 'add_ts_fee'], 20, 1 );
		add_action( 'woocommerce_cart_calculate_fees', [$this, 'add_wf_fee'], 20, 1 );
		add_action( 'woocommerce_cart_calculate_fees', [$this, 'add_ds_fee'], 20, 1 );
	}

	function date_script() {
		if ( is_checkout() && ! is_wc_endpoint_url() ) :
		?>
		<script type="text/javascript" id="dd_ts_ajax">
		jQuery( function($){
			jQuery('form.checkout').on('change', '#delivery_date, #delivery_timeslot', function(){
				var dd = jQuery('#delivery_date').val();
				var ts = jQuery('#delivery_timeslot').val();
				jQuery.ajax({
					type: 'POST',
					url: wc_checkout_params.ajax_url,
					data: {
						'action': 'bkf_checkout_get_ajax_data',
						'delivery_date': dd,
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
		
		if($tax){
			$taxable = "true";
		} else {
			$taxable = "false";
		}
		
		$thets = WC()->session->get( 'delivery_timeslot' );
		$tscol = array_column($ts, 'id');
		$thistsid = array_search($thets, $tscol);
		$thists = $ts[$thistsid];
		
		$taxobject = new WC_Tax();
		if($tax){
			$taxes = $taxobject->get_rates();
			$rates = array_shift($taxes);
			$item_rate = '0.' . round(array_shift($rates));
			$factor = 1 + $item_rate;
		} else {
			$factor = 1;
		}
		
		if($thists !== null && $thists !== ''){
			if ( $thists['fee'] !== '' && $thists['fee'] !== null ) {
				$label	= esc_html__("Timeslot Fee", "bakkbone-florist-companion");
				$cost	= $thists['fee'] / $factor;
			}
		}


		if ( isset($cost) )
			$cart->add_fee( $label, $cost, $taxable );
	}
	
	function add_wf_fee( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;
		
		$tax = get_option('bkf_ddf_setting')['ddwft'];
		
		if($tax){
			$taxable = "true";
		} else {
			$taxable = "false";
		}
		
		$setting = get_option('bkf_wf_setting');
		$wd = WC()->session->get( 'delivery_weekday' );
		
		$taxobject = new WC_Tax();
		if($tax){
			$taxes = $taxobject->get_rates();
			$rates = array_shift($taxes);
			$item_rate = '0.' . round(array_shift($rates));
			$factor = 1 + $item_rate;
		} else {
			$factor = 1;
		}
		
		if($wd !== null && $wd !== ''){
			if ( $setting[$wd] !== '' && $setting[$wd] !== null ) {
				$label	= esc_html(sprintf(__("%s Surcharge", "bakkbone-florist-companion"),ucwords($wd)));
				$cost	= $setting[$wd] / $factor;
			}
		}


		if ( isset($cost) )
			$cart->add_fee( $label, $cost, $taxable );
	}
	
	function add_ds_fee( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;
		
		$tax = get_option('bkf_ddf_setting')['dddft'];
		
		if($tax){
			$taxable = "true";
		} else {
			$taxable = "false";
		}
		
		$setting = get_option('bkf_dd_ds_fees');
		$ts = WC()->session->get( 'delivery_timestamp' );
		
		$taxobject = new WC_Tax();
		if($tax){
			$taxes = $taxobject->get_rates();
			$rates = array_shift($taxes);
			$item_rate = '0.' . round(array_shift($rates));
			$factor = 1 + $item_rate;
		} else {
			$factor = 1;
		}
		
		if($ts !== null && $ts !== ''){
			if(array_key_exists($ts,$setting)){
				if ( $setting[$ts] !== '' && $setting[$ts] !== null ) {
					$label	= esc_html(sprintf(__("Surcharge: %s", "bakkbone-florist-companion"),stripslashes($setting[$ts]['title'])));
					$cost	= $setting[$ts]['fee'] / $factor;
				}
			}
		}

		if ( isset($cost) )
			$cart->add_fee( $label, $cost, $taxable );
	}
	
}