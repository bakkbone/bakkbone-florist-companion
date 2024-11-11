<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Local_Pickup
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Local_Pickup {
	
	function __construct() {
		add_filter( 'woocommerce_cart_shipping_packages', [$this, 'refresh_cache'], 1 );
		add_action( 'woocommerce_after_checkout_form', [$this, 'bkf_disable_shipping_local_pickup'] );
		add_filter( 'woocommerce_checkout_fields', [$this, 'modify_checkout_fields'] );
	}
	
	function refresh_cache( $packages ) {
		if(bkf_not_nonfloral()){
			foreach ( $packages as $package ) {
				$package['rate_cache'] = wp_rand();
			}
		}
		return $packages;
	}

	function bkf_disable_shipping_local_pickup() {
		
		if(bkf_not_nonfloral()){
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_shipping = $chosen_methods[0];
		if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {
			?>
			<script type="text/javascript" id="pickup_out">
			jQuery('#customer_details .woocommerce-shipping-fields').fadeOut();
			</script>
			<?php
		} else {
			?>
			<script type="text/javascript" id="pickup_in">
			jQuery('#customer_details .woocommerce-shipping-fields').fadeIn();
			</script>
			<?php
		}
		?>
		<script type="text/javascript" id="pickup_fade">
			jQuery('form.checkout').on('change','input[name="shipping_method"]',function() {
				var val = jQuery( this ).val();
				if (val.match("local_pickup")) {
					jQuery('#customer_details .woocommerce-shipping-fields').fadeOut();
				} else {
					jQuery('#customer_details .woocommerce-shipping-fields').fadeIn();
				}
			});
		</script>
		<script type="text/javascript" id="ship_type_field_update">
			jQuery('form.checkout').on('change','input[name="ship_type"]',function() {
				var ele = document.getElementsByName('ship_type[0]');
				for(i = 0; i < ele.length; i++) {
					if(ele[i].checked)
		  			var currentType = ele[i].value;
				}
			});
		</script>
		<?php
		}
	}
	
	function modify_checkout_fields($fields) {
		if(bkf_not_nonfloral()){
			global $woocommerce;
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping = $chosen_methods[0];
			if(bkf_shop_has_pickup() && WC()->cart->needs_shipping_address()){
				$st = WC()->session->get('ship_type', 'delivery');
				$fields['billing']['ship_type'] = array(
					'label'		=>	__('Order type', 'bakkbone-florist-companion'),
					'priority'	=>	999,
					'required'	=>	true,
					'clear'		=>	true,
					'class'		=>	array("form-row-wide", "ship-type-field"),
					'type'		=>	'radio',
					'options'	=>	array(
						'delivery'	=>	__('Delivery', 'bakkbone-florist-companion'),
						'pickup'	=>	__('Collection', 'bakkbone-florist-companion')
					),
					'default'	=>	$st
				);
			}
			
			if ( !empty(strpos( $chosen_shipping, 'pickup' )) ) {
				$fields['shipping']['shipping_company']['required'] = false;
				$fields['shipping']['shipping_address_1']['required'] = false;
				$fields['shipping']['shipping_address_2']['required'] = false;
				$fields['shipping']['shipping_city']['required'] = false;
				$fields['shipping']['shipping_postcode']['required'] = false;
		   		$fields['shipping']['shipping_country']['required'] = false;
				$fields['shipping']['shipping_first_name']['required'] = false;
				$fields['shipping']['shipping_last_name']['required'] = false;
				$fields['shipping']['shipping_phone']['required'] = false;
				$fields['shipping']['shipping_state']['required'] = false;
				$fields['shipping']['shipping_notes']['required'] = false;
				unset($fields['shipping']['shipping_phone']['validate']);
			} else {
				$fields['shipping']['shipping_address_1']['required'] = true;
				$fields['shipping']['shipping_city']['required'] = true;
				$fields['shipping']['shipping_postcode']['required'] = true;
		   		$fields['shipping']['shipping_country']['required'] = true;
				$fields['shipping']['shipping_first_name']['required'] = true;
				$fields['shipping']['shipping_last_name']['required'] = true;
				$fields['shipping']['shipping_phone']['required'] = true;
				$fields['shipping']['shipping_state']['required'] = true;
				$fields['shipping']['shipping_phone']['validate'] = array('phone');
			}
		}
		return $fields;
	}
	
}