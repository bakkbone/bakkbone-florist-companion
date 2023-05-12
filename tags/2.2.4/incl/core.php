<?php

/**
 * @author BAKKBONE Australia
 * @package BkfCore
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfCore{
	function __construct() {
		$bkffeatures = get_option("bkf_features_setting");
	    if($bkffeatures["disable_order_comments"] == "1"){
	        add_filter('woocommerce_checkout_fields', array($this, 'bkf_remove_order_comments'));
	    }
		if($bkffeatures["excerpt_pa"] == "1") {add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'bkf_add_excerpt_pa') );};
        add_filter( 'plugin_action_links_bakkbone-florist-companion/bakkbone-florist-companion.php', array($this, 'bkf_settings_link') );
		add_filter( "woocommerce_shipping_package_name" , array($this, "bkf_shipping_to_delivery"), 10, 3);
		add_filter( "gettext" , array($this, "bkf_translate_reply"), PHP_INT_MAX, 1 );
		add_filter( "ngettext" , array($this, "bkf_translate_reply"), PHP_INT_MAX, 1 );
		add_filter( "woocommerce_billing_fields" , array($this, "bkf_override_billing_fields") );
		add_filter( "woocommerce_shipping_fields" , array($this, "bkf_override_shipping_fields"));
		add_filter( "woocommerce_email_order_meta_fields", array($this, "bkf_notes_email"), 10, 3);
        add_filter( 'woocommerce_order_details_after_order_table', array($this, 'bkf_ot_thankyou'), PHP_INT_MAX , 1 );
        add_filter( 'woocommerce_order_details_after_customer_details', array($this, 'bkf_cd_thankyou'), PHP_INT_MAX , 1 );
		add_action( "woocommerce_checkout_update_order_meta" , array($this, "bkf_checkout_field_update_order_meta") );
        add_action( 'add_meta_boxes', array($this, 'bkf_cm_metabox_init') );
        add_action( 'save_post', array($this, 'bkf_cm_save_metabox_data') );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'bkf_editable_order_meta_shipping') );
		add_action( 'woocommerce_process_shop_order_meta', array($this, 'bkf_save_general_details') );
		add_filter( "woocommerce_checkout_fields" , array($this, "bkf_override_checkout_fields") );
        if (in_array("gravityforms/gravityforms.php", apply_filters("active_plugins", get_option("active_plugins")))){
            add_filter( "gform_phone_formats" , array($this, "bkf_au_phone_format") ); }
		add_filter( "woocommerce_product_cross_sells_products_heading", array($this, "bkf_add_cs_heading"), 10, 1 );
		add_filter( 'woocommerce_cart_no_shipping_available_html', array($this, 'noship_message') );
		add_filter( 'woocommerce_no_shipping_available_html', array($this, 'noship_message') );
		add_action( 'woocommerce_after_checkout_form', array($this, 'bkf_disable_shipping_local_pickup') );
		add_filter( 'woocommerce_checkout_fields', array($this, 'bkf_remove_shipping_checkout_fields') );
		add_action( 'woocommerce_new_order', array($this, 'bkf_link_guest_order', 10, 1 ) );
	}

    function bkf_remove_order_comments( $checkout_fields ) {
        unset( $checkout_fields[ 'order' ][ 'order_comments' ] );
        return $checkout_fields;
    }

    function bkf_settings_link( $links ) {
    	$url = esc_url( add_query_arg( array( 'page' => 'bkf_options' ), get_admin_url() . 'admin.php' ) );
    	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
    	$bkf_support_link = "<a href='https://wordpress.org/support/plugin/bakkbone-florist-companion/'>" . __( 'Support' ) . '</a>';
    	array_push( $links, $settings_link, $bkf_support_link );
    	return $links;
    }
	
	function bkf_shipping_to_delivery($package_name, $i, $package){
	    return sprintf( _nx( 'Delivery', 'Delivery %d', ( $i + 1 ), 'shipping packages', 'shipping-i18n' ), ( $i + 1 ) );
	}

	function bkf_translate_reply($translated) {
		$translated = str_replace('Shipping', 'Delivery', $translated);
		$translated = str_replace('shipping', 'delivery', $translated);
		$translated = str_ireplace('Ship to a different address?', 'Delivery details', $translated);
		$translated = str_ireplace('Deliver to a different address?', 'Delivery details', $translated);
		return $translated;
	}
	
	function bkf_override_billing_fields( $fields ) {
		$fields['billing_company']['label'] = 'Business Name';
		$fields['billing_state']['label'] = 'State/Territory';
		$fields['billing_postcode']['label'] = 'Postcode';
		$fields['billing_country']['label'] = 'Country';
		return $fields;
	}
	
	function bkf_override_shipping_fields( $fields ) {
		if (in_array("woo-address-book/woocommerce-address-book.php", apply_filters("active_plugins", get_option("active_plugins")))){
		$fields['shipping_address_nickname']['priority'] = 1;
		$fields['shipping_address_nickname']['label'] = __('Address nickname', 'bakkbone-florist-companion');
		$fields['shipping_address_nickname']['description'] = __('Send to someone regularly? An address nickname will help you find this address easily next time. Example: Jessica\'s Work', 'bakkbone-florist-companion');
		}
	    $fields['shipping_company']['label'] = __('Business/Hospital/Hotel Name', 'bakkbone-florist-companion');
		$fields['shipping_company']['description'] = __('For hospitals/hotels/etc., please include ward/room information if known', 'bakkbone-florist-companion');
		$fields['shipping_state']['label'] = __('State/Territory', 'bakkbone-florist-companion');
		$fields['shipping_postcode']['label'] = __('Postcode', 'bakkbone-florist-companion');
		$fields['shipping_country']['label'] = __('Country', 'bakkbone-florist-companion');
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
	
	function bkf_notes_email( $fields, $sent_to_admin, $order ) {
		$fields['_shipping_notes'] = array(
			'label' => __( 'Delivery Notes' ),
			'value' => get_post_meta( $order->get_id(), '_shipping_notes', true ),
		);
		$fields['_card_message'] = array(
			'label' => __( 'Card Message' ),
			'value' => get_post_meta( $order->get_id(), '_card_message', true ),
		);
		return $fields;
	}
	
    function bkf_ot_thankyou ( $order ) {
		$order_id = $order->get_id();
        $cm = get_post_meta( $order_id, '_card_message', true );
		$cmtitle = __('Card Message', 'bakkbone-florist-companion');

        if ( '' !== $cm ) {
        	echo '<p><strong>' . $cmtitle . ':</strong><br>' . $cm . '</p>';
    	}
		
    }
	
    function bkf_cd_thankyou ( $order ) {
		$order_id = $order->get_id();
        $sn = get_post_meta( $order_id, '_shipping_notes', true );
		$sntitle = __('Delivery Notes', 'bakkbone-florist-companion');
        
        if ( '' !== $sn ) {
        	echo '<p><strong>' . $sntitle . ':</strong><br>' . $sn . '</p>';
    	}
		
    }
	
	function bkf_checkout_field_update_order_meta( $order_id ) {
	    if ( ! empty( $_POST['shipping_notes'] ) ) {
	        update_post_meta( $order_id, '_shipping_notes', sanitize_text_field( $_POST['shipping_notes'] ) );
	    }
	    if ( ! empty( $_POST['card_message'] ) ) {
	        update_post_meta( $order_id, '_card_message', sanitize_text_field( $_POST['card_message'] ) );
	    }
	}
	
    function bkf_cm_metabox_init(){
        add_meta_box('bkf_cm', __('Card Message', 'bakkbone-florist-companion'),array($this, 'bkf_cm_metabox_callback'),'shop_order','side','core');
    }
    
    public function bkf_cm_metabox_callback( $post ){
        $cardmessage = get_post_meta( get_the_id(), '_card_message', true );
        echo '<input type="hidden" name="bkf_cm_nonce" value="' . wp_create_nonce() . '">';
        ?><textarea style="font-family:monospace;width:100%;" name="card_message" rows="6" class="card_message input-text form-control" id="card_message" maxlength="<?php echo get_option('bkf_options_setting')['card_length'] ?>" placeholder="Card Message"><?php echo esc_html( $cardmessage ) ?></textarea>
    	<?php  
    }
    
    function bkf_cm_save_metabox_data( $post_id ) {

    if ( ! isset( $_POST[ 'bkf_cm_nonce' ] ) && isset( $_POST['card_message'] ) )
        return $post_id;

    $nonce = $_POST[ 'bkf_cm_nonce' ];

    if ( ! wp_verify_nonce( $nonce ) )
        return $post_id;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if ( ! current_user_can( 'edit_shop_order', $post_id ) && ! current_user_can( 'edit_shop_orders', $post_id ) )
        return $post_id;

    update_post_meta( $post_id, '_card_message', wc_sanitize_textarea( $_POST[ 'card_message' ] ) );
    }

	function bkf_editable_order_meta_shipping( $order ){
	$shippingnotes = $order->get_meta( '_shipping_notes' );
	?>
		<div class="address">
			<p<?php if( empty( $shippingnotes ) ) { echo ' class="none_set"'; } ?>>
	 			<strong>Delivery Notes:</strong>
				<?php echo ! empty( $shippingnotes ) ? $shippingnotes : '' ?>
			</p>
		</div>
		<div class="edit_address">
			<?php
				woocommerce_wp_textarea_input( array(
					'id' => '_shipping_notes',
					'label' => __('Delivery Notes', 'bakkbone-florist-companion'),
					'wrapper_class' => 'form-field-wide',
					'class' => 'input-text',
					'style' => 'width:100%',
					'value' => $shippingnotes,
					'description' => __('Gate code, dog, etc.', 'bakkbone-florist-companion')
				) );
			?>
		</div>
	<?php
    }
    
    function bkf_save_general_details( $order_id ){
	update_post_meta( $order_id, '_shipping_notes', wc_sanitize_textarea( $_POST[ '_shipping_notes' ] ) );
	update_post_meta( $order_id, '_card_message', wc_sanitize_textarea( $_POST[ 'card_message' ] ) );
    }
	
	function bkf_override_checkout_fields( $fields ) {
	$bkfoptions = get_option("bkf_options_setting");
	$bkfcardlength = $bkfoptions["card_length"];
    $has_physical = false; // Initializing

    // Loop through cart items
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( ! $cart_item['data']->is_virtual() ) {
            $has_physical = true; // Stop the loop
            break;
        }
    }
    if( $has_physical ) {
		 $fields['order']['card_message'] = array(
	    'label'     => __('Card Message', 'woocommerce'),
	    'required'  => true,
	    'class'     => array('form-row-wide'),
	    'clear'     => true,
		'type'		=> 'textarea',
		'description' => 'We\'ll include this with your gift. Maximum '.$bkfcardlength.' characters.',
		'maxlength' => $bkfcardlength
		     );
    }
	     return $fields;
	}
	
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
	
	function bkf_add_excerpt_pa() {    the_excerpt(); }
	
	function bkf_add_cs_heading( $string ) {
	$bkfoptions = get_option("bkf_options_setting");
	$headingtext = $bkfoptions["cs_heading"];
	$string = __( $headingtext, 'woocommerce' );
	return $string;
	}
	
	function noship_message() {
		$bkfoptions = get_option("bkf_options_setting");
		print '<span class="woocommerce-no-shipping-available-html e-checkout-message">' . esc_html( $bkfoptions["noship"] ) . '</span>';
	}
	
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
			jQuery('#customer_details .woocommerce-shipping-fields').fadeOut();
		} else {
			jQuery('#customer_details .woocommerce-shipping-fields').fadeIn();
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
		if (in_array("woo-address-book/woocommerce-address-book.php", apply_filters("active_plugins", get_option("active_plugins")))){
		    	unset($fields['shipping']['shipping_address_nickname']);}
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
		}
		return $fields;
	}
		
	function bkf_link_guest_order( $order_id ) {
	$order = new WC_Order($order_id);
	$user = $order->get_user();
	if( !$user ){
		$userdata = get_user_by( 'email', $order->get_billing_email() );
		if(isset( $userdata->ID )){
			update_post_meta($order_id, '_customer_user', $userdata->ID );
		}else{
		}
	}
	}

}