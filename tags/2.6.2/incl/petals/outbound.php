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
          add_action('wp_ajax_petals_outbound', array($this, 'bkf_petals_outbound_ajax') ); 
          add_action('wp_ajax_nopriv_petals_outbound', array($this, 'bkf_petals_outbound_ajax') );
          add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'bkf_handle_custom_query_var2'), 10, 2 );
      }else{};
    }
    
    function bkf_petals_outbound_ajax(){
        date_default_timezone_set(wp_timezone_string());
        $mn = get_option('bkf_petals_setting')['mn'];
        $password = get_option('bkf_petals_setting')['ppw'];
        $pw = base64_decode($password);
        $rawxml = file_get_contents('php://input');
        $xml = simplexml_load_string($rawxml);
        $symbol = '</strong>: ';
        $xmlarray = json_decode(json_encode((array)$xml), TRUE);
		unset($xmlarray['password']);
		$crtyname = implode($xmlarray['crtyname']);
		$deltime = implode($xmlarray['deltime']);
		$xmlarray['crtyname'] = $crtyname;
		$xmlarray['deltime'] = $deltime;
        $implosion = implode('<br><strong>', array_map(
                    function($k, $v) use($symbol) { 
                        return $k . $symbol . $v;
                    }, 
                    array_keys($xmlarray), 
                    array_values($xmlarray)
                    )
                );
                
        if($xml->recordtype == 1){
	        $product = get_option('bkf_petals_product_setting')['product'];
	        $tax = new WC_Tax();
	        $_product = wc_get_product( $product );
	        if($_product->get_tax_status() == 'taxable'){
	            $taxes = $tax->get_rates($_product->get_tax_class());
	            $rates = array_shift($taxes);
	            $item_rate = '0.' . round(array_shift($rates));
	            $factor = 1 + $item_rate;
	        } else {
	            $factor = 1;
	        }
            $rawdeldate = new DateTime((string)$xml->deldate);
            $deldate = $rawdeldate->format('l, j F Y');
            $order = wc_create_order();
            $item = new WC_Order_Item_Product();
            $item->set_name( (string)$xml->description );
            $item->set_quantity( 1 );
            $item->set_product_id( $product );
            $item->set_subtotal( (string)$xml->tvalue / $factor );
            $item->set_total( (string)$xml->tvalue / $factor );
            $item->update_meta_data( __("Makeup", 'bakkbone-florist-companion'), (string)$xml->makeup );
            $order->add_item( $item );
            $order->calculate_totals();
            $shipping = new WC_Order_Item_Shipping();
            $shipping->set_method_title( __('Delivery Included', 'bakkbone-florist-companion') );
            $shipping->set_total( 0 );
            $order->add_item( $shipping );
            $order->calculate_totals();
            $order->add_meta_data('_card_message',(string)$xml->message);
            $order->add_meta_data('_delivery_date',$deldate);
            $order->add_meta_data('_delivery_timestamp',strtotime($deldate));
            $order->add_meta_data('_shipping_notes',(string)$xml->comments);
            $order->add_meta_data('_petals_on',(string)$xml->sendid);
            $billing = array(
            	'first_name' => _x('Petals Order', 'Billing first name on order', 'bakkbone-florist-companion'),
            	'last_name'  => (string)$xml->sendid,
            	'email'      => ''
            );
            $shipping = array(
            	'first_name' => (string)$xml->recipient,
            	'last_name'  => '',
            	'phone'      => (string)$xml->phone,
            	'address_1'  => (string)$xml->address,
            	'address_2'  => '', 
            	'city'       => (string)$xml->town,
            	'state'      => (string)$xml->state,
            	'postcode'   => (string)$xml->postalcode,
            	'country'    => (string)$xml->crtycode
            );
            $order->set_address( $billing, 'billing');
            $order->set_address( $shipping, 'shipping');
            $order->set_status( 'wc-new' );
            $order->save();
            $note = BKF_PETALS_FULL_TRANSMISSION . '<strong>' . $implosion;
            $order->add_order_note( $note );
            echo '<?xml version="1.0" encoding="UTF-8"?>
                    <message>
                    <recordtype>02</recordtype>
                    <member>'.$mn.'</member>
                    <password>'.$pw.'</password>
                    <notes>Order passed to florist via BAKKBONE Florist Companion for WordPress</notes>
                    <type>100</type>
                    </message>';
        }
            
        if($xml->recordtype == 3){
            $on = (string)$xml->petalsid;
            $order_id = wc_get_orders( array('_petals_on' => $on) );

			$ibonarray = explode(".",$on);
			$ibon = end($ibonarray);
			$args = array(
				'key'		=> '_petals_on',
				'value'		=> $on,
				'compare'	=> '='
			);
			$inboundorder = get_posts(array('post_type' => 'bkf_petals_order', 'meta_query' => array($args)));
			
            $fullnote = BKF_PETALS_FULL_TRANSMISSION . '<strong>' . $implosion;
            $note = __('<strong>Message received from Petals: </strong><br>', 'bakkbone-florist-companion') . (string)$xml->notes;
			
			if(!empty($inboundorder)){
				$comment_author_email  = 'bkf@';
				$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com';
				$comment_author_email  = sanitize_email( $comment_author_email );
				$thisinboundorder = new WP_Post($inboundorder[0]);
				$inboundorder_id = $thisinboundorder->ID;
				$commentargs = array(
					'comment_agent'		=> __('Petals API', 'bakkbone-florist-companion'),
					'comment_type'		=> 'petals_order_note',
					'comment_author'	=> __('Petals Exchange', 'bakkbone-florist-companion'),
					'comment_author_email'	=> $comment_author_email,
					'comment_content'	=> $note . '<br><br>' . $fullnote,
					'comment_post_ID'	=> $inboundorder_id,
					'comment_approved'	=> 1
				);
				$comment = wp_insert_comment($commentargs, true);
	            $wc_emails = WC()->mailer()->get_emails();
	            $wc_emails['WC_Email_Petals_Message']->trigger( $inboundorder_id, $comment );
			}
			if(!empty($order_id)){
				$order = new WC_Order( $order_id[0] );
				            $note = $order->add_order_note( $note );
				            $wc_emails = WC()->mailer()->get_emails();
				            $wc_emails['WC_Email_Petals_Note']->trigger( $order_id[0], $note );
				            $order->add_order_note( $fullnote );
			}
            echo '<?xml version="1.0" encoding="UTF-8"?>
                    <message>
                    <recordtype>02</recordtype>
                    <member>'.$mn.'</member>
                    <password>'.$pw.'</password>
                    <notes>Message passed to florist via BAKKBONE Florist Companion for WordPress</notes>
                    <type>100</type>
                    </message>';
        }
        die();
    }

    function bkf_handle_custom_query_var2( $query, $query_vars ) {
    	if ( ! empty( $query_vars['_petals_on'] ) ) {
    		$query['meta_query'][] = array(
    			'key' => '_petals_on',
    			'value' => esc_attr( $query_vars['_petals_on'] ),
    		);
    	}
    	return $query;
    }

}