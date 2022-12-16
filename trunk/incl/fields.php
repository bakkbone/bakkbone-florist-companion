<?php

/**
 * @author BAKKBONE Australia
 * @package BkfFields
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silence is golden");

/**
 * BkfFields
**/
class BkfFields{
	function __construct() {
		add_filter( "woocommerce_shipping_package_name" , array($this, "bkf_shipping_to_delivery"), 10, 3);
		add_filter( "gettext" , array($this, "bkf_translate_reply"));
		add_filter( "ngettext" , array($this, "bkf_translate_reply"));
		add_filter( "woocommerce_billing_fields" , array($this, "bkf_override_billing_fields") );
		add_filter( "woocommerce_shipping_fields" , array($this, "bkf_override_shipping_fields"));
		add_filter( "woocommerce_email_order_meta_fields", array($this, "bkf_notes_email"), 10, 3);
		add_action( "woocommerce_checkout_update_order_meta" , array($this, "bkf_checkout_field_update_order_meta") );
		add_action( "woocommerce_admin_order_data_after_shipping_address", array($this, "bkf_checkout_field_display_admin_order_meta"), 10, 1 );
		add_filter( "woocommerce_checkout_fields" , array($this, "bkf_override_checkout_fields") );
		add_filter( "gform_phone_formats" , array($this, "bkf_au_phone_format") );
		$bkfoptions = get_option("bkf_options_setting");
		if($bkfoptions["bkf_excerpt_pa"] == "1") {add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'bkf_add_excerpt_pa') );};
		add_filter( "woocommerce_product_cross_sells_products_heading", array($this, "bkf_add_cs_heading"), 10, 1 );
		add_filter( 'wcfm_orders_additional_info_column_label', function( $orddd_column_label ) { $orddd_column_label = 'Delivery Date'; return $orddd_column_label;});
		add_filter( 'wcfm_orders_additonal_data_hidden', '__return_false' );
		add_filter( 'wcfm_orders_additonal_data', function( $orddd_column_data, $order_id ) { $orddd_column_data = get_post_meta( $order_id, "orddd_delivery_date", true ); return $orddd_column_data; }, 50, 2);
		add_filter( 'woocommerce_cart_no_shipping_available_html', array($this, 'bkf_noship_message') );
		add_filter( 'woocommerce_no_shipping_available_html', array($this, 'bkf_noship_message') );
		add_action( 'woocommerce_after_checkout_form', array($this, 'bkf_disable_shipping_local_pickup') );
		add_filter( 'woocommerce_checkout_fields', array($this, 'bkf_remove_shipping_checkout_fields') );
		add_action( 'woocommerce_new_order', array($this, 'bkf_link_guest_order', 10, 1 ) );

	}
 
	
	// Change "Shipping" to "Delivery"
	function bkf_shipping_to_delivery($package_name, $i, $package){
	    return sprintf( _nx( 'Delivery', 'Delivery %d', ( $i + 1 ), 'shipping packages', 'shipping-i18n' ), ( $i + 1 ) );
	}

	function bkf_translate_reply($translated) {
	$translated = str_ireplace('Shipping', 'Delivery', $translated);
	$translated = str_ireplace('Ship to a different address?', 'Delivery details', $translated);
	$translated = str_ireplace('Customer provided note:', 'Card Message:', $translated);
	$translated = str_ireplace('Note:', 'Card Message:', $translated);
	return $translated;
	}
	
	// Tidy billing
	function bkf_override_billing_fields( $fields ) {
		$fields['billing_company']['label'] = 'Business Name';
		$fields['billing_state']['label'] = 'State/Territory';
		$fields['billing_postcode']['label'] = 'Postcode';
		$fields['billing_country']['label'] = 'Country';
		     return $fields;
	}
	
	// Tidy shipping
	function bkf_override_shipping_fields( $fields ) {
		$fields['shipping_address_nickname']['priority'] = 1;
		$fields['shipping_address_nickname']['label'] = 'Address nickname';
		$fields['shipping_address_nickname']['description'] = 'Send to someone regularly? An address nickname will help you find this address easily next time. Example: Jessica\'s Work';
	    	$fields['shipping_company']['label'] = 'Business/Hospital/Hotel Name';
		$fields['shipping_company']['description'] = 'For hospitals/hotels/etc., please include ward/room information if known';
		$fields['shipping_state']['label'] = 'State/Territory';
		$fields['shipping_postcode']['label'] = 'Postcode';
		$fields['shipping_country']['label'] = 'Country';
		$fields['shipping_phone'] = array(
	    'label'     => __('Recipient\'s Phone', 'woocommerce'),
	    'placeholder'   => _x('Phone', 'placeholder', 'woocommerce'),
	    'required'  => true,
	    'class'     => array("form-row-wide"),
	    'clear'     => true,
		'type'      => 'tel',
		'validate'  => array( 'phone' ),
	     );
		$fields['shipping_notes'] = array(
	    'label'     => __('Anything we need to know about the address?', 'woocommerce'),
	    'required'  => false,
	    'class'     => array('form-row-wide'),
	    'clear'     => true,
		'type'		=> 'textarea',
		'description' => 'eg. gate code, fence, dog, etc.'
	     );
	     return $fields;
	}
	
	// Add delivery notes to email
	function bkf_notes_email( $fields, $sent_to_admin, $order ) {
		$fields['_shipping_notes'] = array(
			'label' => __( 'Delivery Notes' ),
			'value' => get_post_meta( $order->id, '_shipping_notes', true ),
		);
		return $fields;
	}
	
	// Add delivery notes to order meta
	function bkf_checkout_field_update_order_meta( $order_id ) {
	    if ( ! empty( $_POST['_shipping_notes'] ) ) {
	        update_post_meta( $order_id, 'Delivery Notes', sanitize_text_field( $_POST['_shipping_notes'] ) );
	    }
	}
	
	// Display delivery notes in order meta
	function bkf_checkout_field_display_admin_order_meta($order){
	    echo '<p><strong>'.__('Delivery Notes').':</strong> ' . esc_html( get_post_meta( $order->id, '_shipping_notes', true ) ) . '</p>';
	}
	
	
	// Card message
	function bkf_override_checkout_fields( $fields ) {
	$bkfoptions = get_option("bkf_options_setting");
	if ( !empty( $bkfoptions["bkf_card_length"] ) ) { $bkfcardlength = $bkfoptions["bkf_card_length"];} else { $bkfcardlength = "250" ;};
	     $fields['order']['order_comments']['description'] = 'We\'ll include this with your gift. Maximum '.$bkfcardlength.' characters.' ;
		 $fields['order']['order_comments']['placeholder'] = '';
		 $fields['order']['order_comments']['label'] = 'Card Message';
		 $fields['order']['order_comments']['required'] = true;
		 $fields['order']['order_comments']['maxlength'] = $bkfcardlength;
	     return $fields;
	}
	
	// GF phone formats
	function bkf_au_phone_format( $phone_formats ) {
	    $phone_formats['au'] = array(
	        'label'       => 'Australia Mobile',
	        'mask'        => '9999 999 999',
	        'regex'       => '/^\d{4} \d{3} \d{3}$/',
	        'instruction' => '#### ### ###',
	    );
	    $phone_formats['aul'] = array(
	        'label'       => 'Australia Landline',
	        'mask'        => '99 9999 9999',
	        'regex'       => '/^\d{2} \d{4} \d{4}$/',
	        'instruction' => '## #### ####',
	    );
	    return $phone_formats;
	}
	
	// Add short description on product archive if selected in options
	function bkf_add_excerpt_pa() {    the_excerpt(); }
	
	// Cross-sell headings
	function bkf_add_cs_heading( $string ) {
	$bkfoptions = get_option("bkf_options_setting");
	if( ! empty ($bkfoptions["bkf_cs_heading"] ) ) {$headingtext = $bkfoptions["bkf_cs_heading"];} else {$headingtext = "How about adding...";};
	$string = __( $headingtext, 'woocommerce' );
	return $string;
	}
	
	// No-ship message
	function bkf_noship_message() {
		$bkfoptions = get_option("bkf_options_setting");
		print '<span class="woocommerce-no-shipping-available-html e-checkout-message">' . esc_html( $bkf_options["bkf_noship"] ) . '</span>';
	}
	
	// Hide delivery address on pickup
	function bkf_disable_shipping_local_pickup( $available_gateways ) {
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_shipping = $chosen_methods[0];
		if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {
	?>
	<script type="text/javascript">
	jQuery('#customer_details .col-2').fadeOut();
	</script>
	<?php
		}
	?>
	<script type="text/javascript">
	jQuery('form.checkout').on('change','input[name^="shipping_method"]',function() {
		var val = jQuery( this ).val();
		if (val.match("^local_pickup")) {
			jQuery('#customer_details .col-2').fadeOut();
		} else {
			jQuery('#customer_details .col-2').fadeIn();
		}
	});
	</script>
	<?php
	}
	
	function bkf_remove_shipping_checkout_fields($fields) {
		global $woocommerce;
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_shipping = $chosen_methods[0];
		if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {
			unset($fields['shipping']['shipping_address_nickname']);
        		unset($fields['shipping']['shipping_company']);
		        unset($fields['shipping']['shipping_address_1']);
        		unset($fields['shipping']['shipping_address_2']);
			unset($fields['shipping']['shipping_city']);
        		unset($fields['shipping']['shipping_postcode']);
       			unset($fields['shipping']['shipping_country']);
        		unset($fields['shipping']['shipping_first_name']);
        		unset($fields['shipping']['shipping_last_name']);
        		unset($fields['shipping']['shipping_phone']);
        		unset($fields['shipping']['shipping_state']);
        		unset($fields['shipping']['shipping_notes']);
        		unset($fields['order']['order_comments']);
		}
		return $fields;
	}
		
	// Assign guest order to matching user
	
	function bkf_link_guest_order( $order_id ) {
	$order = new WC_Order($order_id);
	$user = $order->get_user();
	
	if( !$user ){
		//guest order
		$userdata = get_user_by( 'email', $order->get_billing_email() );
		if(isset( $userdata->ID )){
			//registered
			update_post_meta($order_id, '_customer_user', $userdata->ID );
		}else{
			//Guest
		}
	}
}

	
	
	
}
