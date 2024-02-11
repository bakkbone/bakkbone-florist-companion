<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Ajax
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Ajax{
	
	function __construct(){
		
		// Order Notifier
		$features = get_option('bkf_features_setting');
		if($features['order_notifier']){
			add_action('wp_ajax_notifier', [$this, 'notifier']);
			add_action('wp_ajax_notifier_status', [$this, 'notifier_status']);
		}
		
		// DD Blocks
		add_action('wp_ajax_bkf_dd_add_closed', [$this, 'dd_add_closed']);
		add_action('wp_ajax_bkf_dd_remove_closed', [$this, 'dd_remove_closed']);
		add_action('wp_ajax_bkf_dd_add_full', [$this, 'dd_add_full']);
		add_action('wp_ajax_bkf_dd_remove_full', [$this, 'dd_remove_full']);
		
		// Calendar Export
		add_action('wp_ajax_bkf_cal_pdf', [$this, 'bkf_cal_pdf']);
		add_action('wp_ajax_bkf_cal_csv', [$this, 'bkf_cal_csv']);
		
		// Category DD Blocks
		add_action('wp_ajax_bkf_cb_add', [$this, 'bkf_cb_add']);
		add_action('wp_ajax_bkf_cb_del', [$this, 'bkf_cb_del']);
		
		// Same-Day per method cutoffs
		add_action('wp_ajax_bkf_sd_add', [$this, 'bkf_sd_add']);
		add_action('wp_ajax_bkf_sd_del', [$this, 'bkf_sd_del']);
		
		// Add/Delete Timeslots
		add_action('wp_ajax_bkf_ts_add', [$this, 'bkf_ts_add']);
		add_action('wp_ajax_bkf_ts_del', [$this, 'bkf_ts_del']);
		
		// Date-specific fees
		add_action('wp_ajax_bkf_dd_add_fee', [$this, 'bkf_dd_add_fee']);
		add_action('wp_ajax_bkf_dd_remove_fee', [$this, 'bkf_dd_remove_fee']);
		
		// Add DD/TS to session in cart
		add_action( 'wp_ajax_bkf_checkout_get_ajax_data', [$this, 'bkf_checkout_get_ajax_data']);
		add_action( 'wp_ajax_nopriv_bkf_checkout_get_ajax_data', [$this, 'bkf_checkout_get_ajax_data']);
		
		// Retrieve TS from session
		add_action( 'wp_ajax_bkf_retrieve_session_ts', [$this, 'bkf_retrieve_session_ts']);
		add_action( 'wp_ajax_nopriv_bkf_retrieve_session_ts', [$this, 'bkf_retrieve_session_ts']);
		
		// PDFs
		add_action( 'wp_ajax_bkfdi', [$this, 'bkfdi']);
		add_action( 'wp_ajax_bkfdw', [$this, 'bkfdw']);
		
		// Petals
		$bkfoptions = get_option("bkf_features_setting");
		if($bkfoptions["petals_on"]) {
			add_action('wp_ajax_petals_msg_frontend', [$this, 'bkf_petals_msg_frontend']);
			add_action('wp_ajax_petals_decision', [$this, 'bkf_petals_decision_ajax']);
			add_action('wp_ajax_petals_msg', [$this, 'bkf_petals_msg']);
			add_action('wp_ajax_petals_outbound', [$this, 'bkf_petals_outbound_ajax']); 
			add_action('wp_ajax_nopriv_petals_outbound', [$this, 'bkf_petals_outbound_ajax']);
			add_action('wp_ajax_bkf_cpp', [$this, 'bkf_cpp']);
		}
		
		// Phone/POS
		add_action('wp_ajax_product_value', [$this, 'product_value']);
		
		// Get all timeslots
		add_action('wp_ajax_bkf_get_timeslots', [$this, 'get_timeslots']);
		
		// Get timeslots for method and weekday
		add_action('wp_ajax_bkf_get_timeslots_for_order', [$this, 'get_timeslots_for_order']);
		
		// Get rates
		add_action('wp_ajax_bkf_rates', [$this, 'bkf_rates']);
		
		// Get delivery rates
		add_action('wp_ajax_bkf_delivery_rates', [$this, 'bkf_delivery_rates']);

		// Get pickup rates
		add_action('wp_ajax_bkf_pickup_rates', [$this, 'bkf_pickup_rates']);
		
		// Check method cost
		add_action('wp_ajax_bkf_method_cost', [$this, 'bkf_method_cost']);

		// Check if products in array are physical
		add_action('wp_ajax_bkf_products_physical', [$this, 'bkf_products_physical']);
		
		// Check if fee applies to weekday
		add_action('wp_ajax_bkf_check_weekday_fee', [$this, 'check_weekday_fee']);
		
		// Check if fee applies to specific date
		add_action('wp_ajax_bkf_check_ds_fee', [$this, 'check_ds_fee']);
		
		// Get fee for timeslot
		add_action('wp_ajax_bkf_get_fee_for_timeslot', [$this, 'get_fee_for_timeslot']);

		// Process phone order
		add_action('wp_ajax_bkf_phone_order', [$this, 'phone_order']);
		
		// Dismiss subscription offer
		add_action('wp_ajax_bkf_sub_notice_dismissed', [$this, 'bkf_sub_notice_dismissed']);
		
		// Resend invoice
		add_action('wp_ajax_bkf_resend_invoice', [$this, 'resend_invoice']);
		
		// Check order is valid
		add_action('wp_ajax_bkf_valid_order', [$this, 'is_valid_order']);
		
		// Search suburbs
		add_action('wp_ajax_bkf_search_suburbs', [$this, 'search_suburbs']);
		
		// Search suburbs
		add_action('wp_ajax_bkf_search_suburbs_frontend', [$this, 'search_suburbs_frontend']);
		add_action('wp_ajax_nopriv_bkf_search_suburbs_frontend', [$this, 'search_suburbs_frontend']);
		
		// Check delivery date
		add_action('wp_ajax_bkf_dd', [$this, 'check_delivery_date']);
		add_action('wp_ajax_nopriv_bkf_dd', [$this, 'check_delivery_date']);
		
	}
	
	function notifier(){
		$timestamp = $_REQUEST['timestamp'];
		$args = array(
			'date_created' => '>=' . $timestamp,
		);
		$orders = wc_get_orders($args);
		$result = [];
		foreach($orders as $order){
			$result[] = array(
				'id' => $order->get_id(),
				'requires_shipping' => $order->needs_shipping_address(),
				'billing_name' => $order->get_formatted_billing_full_name(),
				'shipping_address' => $order->get_formatted_shipping_address(),
				'value' => bkf_currency_symbol().$order->get_total(),
				'delivery_date' => $order->get_meta('_delivery_date'),
				'url' => $order->get_edit_order_url(),
			);
		}
		echo json_encode($result);
		die();
	}
	
	function notifier_status(){
		$status = $_REQUEST['status'];
		$user = $_REQUEST['user'];
		update_user_meta($user, 'bkf_notifier_status', $status);
		die();
	}
	
	function dd_add_closed(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		if(null !== get_option('bkf_dd_closed') && !empty(get_option('bkf_dd_closed'))){
			$option = get_option('bkf_dd_closed');
		} else {
			$option = [];
		}
		$date = $_REQUEST['date'];
		if ($date) {
			$ts = (string)strtotime($date);
			$option[$ts] = $date;
			update_option('bkf_dd_closed', $option);
		}
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}

	function dd_remove_closed(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$option = get_option('bkf_dd_closed');
		$date = $_REQUEST['ts'];
		unset($option[$date]);
		update_option('bkf_dd_closed', $option);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}
	
	function dd_add_full(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		if(null !== get_option('bkf_dd_full') && !empty(get_option('bkf_dd_full'))){
			$option = get_option('bkf_dd_full');
		} else {
			$option = [];
		}
		$date = $_REQUEST['date'];
		if ($date) {
			$ts = (string)strtotime($date);
			$option[$ts] = $date;
			update_option('bkf_dd_full', $option);
		}
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}

	function dd_remove_full(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$option = [];
		$option = get_option('bkf_dd_full');
		$date = $_REQUEST['ts'];
		unset($option[$date]);
		update_option('bkf_dd_full', $option);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}
	
	function bkf_cal_pdf(){
		$start = $_REQUEST['starty'] . '-' . $_REQUEST['startm'] . '-' . $_REQUEST['startd'];
		$end = $_REQUEST['endy'] . '-' . $_REQUEST['endm'] . '-' . $_REQUEST['endd'];
		$pdf = new BKF_PDF_Core();
		$thepdf = $pdf->calendar($start,$end);
		$ct = __('calendar','bakkbone-florist-companion');
		$thepdf->stream($ct.'-'.$start.'-'.esc_html__('to','bakkbone-florist-companion').'-'.$end.'.pdf');
	}

	
	function bkf_cal_csv(){
		$start = $_REQUEST['starty'] . '-' . $_REQUEST['startm'] . '-' . $_REQUEST['startd'];
		$end = $_REQUEST['endy'] . '-' . $_REQUEST['endm'] . '-' . $_REQUEST['endd'];
		$starttime = strtotime($start);
		$endtime = strtotime($end);
		
		$orders = wc_get_orders(array(
			'status' => array('wc-new','wc-accept','wc-processing','wc-completed','wc-scheduled','wc-prepared','wc-collect','wc-out','wc-relayed','wc-processed','wc-collected'),
			'limit' => '999999',
			'orderby' => 'meta_value_num',
			'meta_key' => '_delivery_timestamp',
			'meta_query' => array(
				array('key' => '_delivery_timestamp', 'value' => $starttime, 'compare' => '>='),
				array('key' => '_delivery_timestamp', 'value' => $endtime, 'compare' => '<')
			)
		));
		foreach($orders as $k => $o){
		    if($o->get_meta('_delivery_timestamp', true) < $starttime || $o->get_meta('_delivery_timestamp', true) >= $endtime){
		        unset($orders[$k]);
		    }
		}
		
		$upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'].'/bkfcsv/'.esc_html__('orders','bakkbone-florist-companion').'-'.$start.'-'.esc_html__('to','bakkbone-florist-companion').'-'.$end.'.csv';
		$pdfdir = dirname($filename);
		if (!is_dir($pdfdir))
		{
			mkdir($pdfdir, 0755, true);
		}
		if(file_exists($filename)){
			unlink($filename);
		}
		$afile = fopen($filename, "x+");
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$data = array(
			__('Order ID','bakkbone-florist-companion'),
			$ddtitle,
			__('Items','bakkbone-florist-companion'),
			__('Total','bakkbone-florist-companion'),
			__('Recipient','bakkbone-florist-companion'),
			__('Address','bakkbone-florist-companion'),
			__('Suburb','bakkbone-florist-companion'),
			__('Phone','bakkbone-florist-companion'),
			__('Notes', 'bakkbone-florist-companion')
		);
		fputcsv($afile, $data);
		foreach($orders as $o){
			$items = $o->get_items();
			$list = [];
			foreach($items as $item){
				$list[] = $item->get_quantity() . 'x ' . $item->get_name();
			}
			$sa = [];
			if($o->get_shipping_company() !==null && $o->get_shipping_company() !== ''){
				$sa[] = $o->get_shipping_company();
			}
			if($o->get_shipping_address_1() !==null && $o->get_shipping_address_1() !== ''){
				$sa[] = $o->get_shipping_address_1();
			}
			if($o->get_shipping_address_2() !==null && $o->get_shipping_address_2() !== ''){
				$sa[] = $o->get_shipping_address_2();
			}
			$data = array(
				$o->get_id(),
				$o->get_meta( '_delivery_date', true ),
				implode(", ",$list),
				$o->get_total(),
				$o->get_formatted_shipping_full_name(),
				implode(", ",$sa),
				$o->get_shipping_city(),
				$o->get_shipping_phone(),
				$o->get_meta( '_shipping_notes', true )
			);
			fputcsv($afile, $data);
		}
		fclose($afile);
		
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($filename) . "\""); 
		readfile($filename); 
		die();
	}

	function bkf_cb_add(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$date = $_REQUEST['date'];
		$category = $_REQUEST['category'];
		
		global $wpdb;
		$wpdb->insert(
		$wpdb->prefix.'bkf_dd_catblocks',
			array(
				'date'	=>	$date,
				'category'=>$category
			)
		);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}

	function bkf_cb_del(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix.'bkf_dd_catblocks',
			array(
				'id'	=>	$id
			)
		);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}

	function bkf_sd_add(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$method		= $_REQUEST['method'];
		$day		= $_REQUEST['day'];
		$cutoff		= $_REQUEST['cutoff'];
		$leadtime	= $_REQUEST['leadtime'];
		
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix.'bkf_dd_sameday_methods',
			array(
				'method'	=>	$method,
				'day'		=>	$day,
				'cutoff'	=>	$cutoff,
				'leadtime'	=>	$leadtime
			)
		);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		
		die();		
	}

	function bkf_sd_del(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix.'bkf_dd_sameday_methods',
			array(
				'id'	=>	$id
			)
		);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}
	
	function bkf_ts_add(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$day = $_REQUEST['day'];
		$method = $_REQUEST['method'];
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];
		$fee = $_REQUEST['fee'];
		
		global $wpdb;
		$wpdb->insert($wpdb->prefix.'bkf_dd_timeslots',array(
			'day'	=>	$day,
			'method'=>	$method,
			'start'	=>	$start,
			'end'	=>	$end,
			'fee'	=>	$fee
		));
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}

	function bkf_ts_del(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete($wpdb->prefix.'bkf_dd_timeslots', array('id'	=>	$id));
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();		
	}
	
	function bkf_dd_add_fee(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		if(null !== get_option('bkf_dd_ds_fees') && !empty(get_option('bkf_dd_ds_fees'))){
			$option = get_option('bkf_dd_ds_fees');
		} else {
			$option = [];
		}
		$date = $_REQUEST['date'];
		$ts = (string)strtotime($date);
		$fee = $_REQUEST['fee'];
		$title = $_REQUEST['title'];
		$option[$ts] = array(
			'fee' => $fee,
			'title' => $title
		);
		update_option('bkf_dd_ds_fees', $option);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}

	function bkf_dd_remove_fee(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		$option = get_option('bkf_dd_ds_fees');
		$ts = $_REQUEST['date'];
		unset($option[$ts]);
		update_option('bkf_dd_ds_fees', $option);
			
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}

	function bkf_checkout_get_ajax_data() {
		WC()->session->init();
		$array = [];
		if ( isset($_POST['delivery_timeslot']) ){
			WC()->session->__unset('delivery_timeslot');
			$ts = sanitize_text_field( $_POST['delivery_timeslot'] );
			WC()->session->set('delivery_timeslot', $ts);
			$array['delivery_timeslot'] = WC()->session->get('delivery_timeslot');
		}
		if ( isset($_POST['delivery_date']) ){
			WC()->session->__unset('delivery_date');
			WC()->session->__unset('delivery_timestamp');
			WC()->session->__unset('delivery_weekday');
			$date = sanitize_text_field( $_POST['delivery_date'] );
			$timestamp = strtotime($date);
			$weekday = strtolower(wp_date("l",$timestamp));
			WC()->session->set('delivery_date', $date);
			WC()->session->set('delivery_timestamp', $timestamp);
			WC()->session->set('delivery_weekday', $weekday);
			$array['delivery_date'] = WC()->session->get('delivery_date');
			$array['delivery_timestamp'] = WC()->session->get('delivery_timestamp');
			$array['delivery_weekday'] = WC()->session->get('delivery_weekday');
		}
		if ( isset($_POST['ship_type']) ){
			WC()->session->__unset('ship_type');
			$st = sanitize_text_field( $_POST['ship_type'] );
			WC()->session->set('ship_type', $st);
			$array['ship_type'] = WC()->session->get('ship_type');
		}
		if ( isset($_POST['shipping_notes']) ){
			WC()->session->__unset('shipping_notes');
			$sn = sanitize_textarea_field( $_POST['shipping_notes'] );
			WC()->session->set('shipping_notes', $sn);
			$array['shipping_notes'] = WC()->session->get('shipping_notes');
		}
		if ( isset($_POST['card_message']) ){
			WC()->session->__unset('card_message');
			$cm = sanitize_textarea_field( $_POST['card_message'] );
			WC()->session->set('card_message', $cm);
			$array['card_message'] = WC()->session->get('card_message');
		}
		if (bkf_debug()) {
			bkf_debug_log(sprintf(__('Result of %s(): ', 'bakkbone-florist-companion'), __FUNCTION__).wp_json_encode($array), 'debug');
		}
		echo wp_json_encode($array);
		die();
	}
	
	function bkf_retrieve_session_ts(){
		echo json_encode(WC()->session->get('delivery_timeslot', 'null'));
		die();
	}
	
	function bkfdi(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}

		$order_id = $_REQUEST['order_id'];
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$pdf = new BKF_PDF_Core();
		$thepdf = $pdf->invoice($order_id);
		$thepdf->stream($invtitle.' #'.$order_id.'.pdf');
	}

	function bkfdw(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}

		$order_id = $_REQUEST['order_id'];
		$wstitle = get_option('bkf_pdf_setting')['ws_title'];
		$pdf = new BKF_PDF_Core();
		$thepdf = $pdf->worksheet($order_id);
		$thepdf->stream($wstitle.' #'.$order_id.'.pdf');
	}
	
	function bkf_petals_msg_frontend(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		
		$msgtype	= $_REQUEST['msgtype'];
		$msgbody	= $_REQUEST['msgbody'];
		$orderid	= $_REQUEST['orderid'];
		$order		= wc_get_order($orderid);
		$mn			= get_option('bkf_petals_setting')['mn'];
		$password	= get_option('bkf_petals_setting')['ppw'];
		$pw			= base64_decode($password);		
		$petalsid	= $order->get_meta("_petals_on");
		$url		= 'https://pin.petals.com.au/wconnect/wc.isa?pbo~&ctype=45';
		
		$body = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>03</recordtype>
<type>'.$msgtype.'</type>
<notes>'.$msgbody.'</notes>
</message>';
		
		$response = wp_remote_post($url, array(
			'method'	=> 'POST',
			'headers'   => array('Content-Type' => 'application/xml'),
			'body'	  => $body
			));
			
			$rawxml = $response['body'];
			$xml = simplexml_load_string($rawxml);
			$symbol = '</strong>: ';
			$xmlarray = json_decode(json_encode((array)$xml), TRUE);
			$implosion = implode('<br><strong>', array_map(
						function($k, $v) use($symbol) { 
							return $k . $symbol . $v;
						}, 
						array_keys($xmlarray), 
						array_values($xmlarray)
						)
					);
			if($xml->type == '300'){
				$note = '<strong>'.esc_html__('Message successfully sent to Petals:', 'bakkbone-florist-companion').' </strong><br>' . $msgbody . '<br><br><strong>'.esc_html__('Response from Petals:', 'bakkbone-florist-companion').'</strong> <br><strong>' . $implosion;
				$note = __('<strong>Order sent.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
				$comment_author_email  = 'bkf@';
				$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com';
				$comment_author_email  = sanitize_email( $comment_author_email );
				$commentargs = array(
					'comment_agent'		=> __('Petals API', 'bakkbone-florist-companion'),
					'comment_type'		=> 'petals_order_note',
					'comment_author'	=> __('Petals Exchange', 'bakkbone-florist-companion'),
					'comment_author_email'	=> $comment_author_email,
					'comment_content'	=> $note . '<br><br>' . $fullnote,
					'comment_post_ID'	=> $orderid,
					'comment_approved'	=> 1
				);
				$comment = wp_insert_comment($commentargs, true);
				$wc_emails = WC()->mailer()->get_emails();
				$wc_emails['WC_Email_Petals_Outcome']->trigger( $orderid, $comment );			
			} else {
				$note = '<strong>'.esc_html__('Message FAILED TO SEND to Petals. Your message:', 'bakkbone-florist-companion').' </strong><br>' . $msgbody . '<br><br>'.esc_html__('Response from Petals:', 'bakkbone-florist-companion').' <br><strong>' . $implosion;
				$comment_author_email  = 'bkf@';
				$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com';
				$comment_author_email  = sanitize_email( $comment_author_email );
				$commentargs = array(
					'comment_agent'		=> __('Petals API', 'bakkbone-florist-companion'),
					'comment_type'		=> 'petals_order_note',
					'comment_author'	=> __('Petals Exchange', 'bakkbone-florist-companion'),
					'comment_author_email'	=> $comment_author_email,
					'comment_content'	=> $note . '<br><br>' . $fullnote,
					'comment_post_ID'	=> $orderid,
					'comment_approved'	=> 1
				);
				$comment = wp_insert_comment($commentargs, true);
				$wc_emails = WC()->mailer()->get_emails();
				$wc_emails['WC_Email_Petals_Outcome']->trigger( $orderid, $comment );		
			}

			
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	  $result = json_encode($result);
	  echo $result;
	   }
	   else {		
		  header("Location: ".$_SERVER["HTTP_REFERER"]);
	   }
	   die();
		
	}
	
	function bkf_petals_decision_ajax(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}

		$reasons = array(
			'293' => 'Cannot deliver flowers',
			'294' => 'Don\'t have the required flowers',
			'270' => 'We cannot deliver to this location ever',
			'280' => 'Cannot deliver to this location today',
			'281' => 'Do not have these flowers but could do a florist choice',
			'282' => 'Do not have any flowers to meet delivery date',
			'272' => 'Need more information to deliver this order',
			'283' => 'Do not have this container but could do with a substitution of container',
			'273' => 'Do not do this product ever',
			'274' => 'There is a problem with this address',
			'284' => 'This area is restricted, can go on next run but not this delivery date',
			'285' => 'This area is restricted and can\'t be delivered until next week'
			);
		$mn		 = get_option('bkf_petals_setting')['mn'];
		$password   = get_option('bkf_petals_setting')['ppw'];
		$pw		 = base64_decode($password);
		$petalsid   = $_REQUEST['petalsid'];
		$orderid	= $_REQUEST['orderid'];
		$outcome	= $_REQUEST['outcome'];
		if($outcome == 'reject'){
			$code	   = $_REQUEST['code'];
		}
		$url		= 'https://pin.petals.com.au/wconnect/wc.isa?pbo~&ctype=45';
		if($outcome == 'accept'){
			$body	   = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>10</recordtype>
<type>'.strtoupper($outcome).'</type>
<notes></notes>
</message>';
		}
		if($outcome == 'reject'){
			$body	   = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>10</recordtype>
<type>'.strtoupper($outcome).'</type>
<rejectreason>'.$reasons[$code].'</rejectreason>
<rejectcode>'.$code.'</rejectcode>
<notes></notes>
</message>';			
		}
		$response = wp_remote_post($url, array(
			'method'	=> 'POST',
			'headers'   => array('Content-Type' => 'application/xml'),
			'body'	  => $body
			));
		$order = new WC_Order( $orderid );
		$rawxml = $response['body'];
		$xml = simplexml_load_string($rawxml);
		$symbol = '</strong>: ';
		$xmlarray = json_decode(json_encode((array)$xml), TRUE);
		unset($xmlarray['password']);
		$implosion = implode('<br><strong>', array_map(
					function($k, $v) use($symbol) { 
						return $k . $symbol . $v;
					}, 
					array_keys($xmlarray), 
					array_values($xmlarray)
					)
				);
				
		if($xml->type == '300'){
			if($outcome == 'accept'){
				$note = __('<strong>Order accepted.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
				$ordernote = $order->add_order_note($note);
				$orderstatus = $order->update_status($outcome);
			} else {
				$note = __('<strong>Order rejected.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
				$ordernote = $order->add_order_note($note);
				$order->update_status($outcome);
			}
		} else {
			if($outcome == 'accept'){
				$note = __('<strong>Order acceptance failed.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . $xml->text;
				$ordernote = $order->add_order_note( $note );
			} else {
				$note = __('<strong>Order rejection failed.<br>Response from Petals: </strong><br>', 'bakkbone-florist-companion') . $xml->text;
				$ordernote = $order->add_order_note( $note );
			}
			$wc_emails = WC()->mailer()->get_emails();
			$wc_emails['WC_Email_Petals_Note']->trigger( $order->get_id(), $ordernote );
		}
		
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}
	
	function bkf_petals_msg(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}
		
		$msgtype = $_REQUEST['msgtype'];
		$msgbody = $_REQUEST['msgbody'];
		$orderid = $_REQUEST['orderid'];
		$order = new WC_Order($orderid);
		$mn		 = get_option('bkf_petals_setting')['mn'];
		$password   = get_option('bkf_petals_setting')['ppw'];
		$pw		 = base64_decode($password);		
		$petalsid   = get_post_meta($orderid,"_petals_on");
		$url		= 'https://pin.petals.com.au/wconnect/wc.isa?pbo~&ctype=45';
		
		$body = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>03</recordtype>
<type>'.$msgtype.'</type>
<notes>'.$msgbody.'</notes>
</message>';
		
		$response = wp_remote_post($url, array(
			'method'	=> 'POST',
			'headers'   => array('Content-Type' => 'application/xml'),
			'body'	  => $body
		));
		
		$rawxml = $response['body'];
		$xml = simplexml_load_string($rawxml);
		$symbol = '</strong>: ';
		$xmlarray = json_decode(json_encode((array)$xml), TRUE);
		$implosion = implode('<br><strong>', array_map( function($k, $v) use($symbol) { return $k . $symbol . $v; }, array_keys($xmlarray), array_values($xmlarray)));
		if($xml->type == '300'){
			$note = '<strong>'.esc_html__('Message successfully sent to Petals:', 'bakkbone-florist-companion').' </strong><br>' . $msgbody . '<br><br><strong>'.esc_html__('Response from Petals:', 'bakkbone-florist-companion').'</strong> <br><strong>' . $implosion;
			$ordernote = $order->add_order_note($note);				
		} else {
			$note = '<strong>'.esc_html__('Message FAILED TO SEND to Petals. Your message:', 'bakkbone-florist-companion').' </strong><br>' . $msgbody . '<br><br>'.esc_html__('Response from Petals:', 'bakkbone-florist-companion').' <br><strong>' . $implosion;
			$ordernote = $order->add_order_note($note);				
		}
		$wc_emails = WC()->mailer()->get_emails();
		$wc_emails['WC_Email_Petals_Note']->trigger( $order->get_id(), $ordernote );
		
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}

	function bkf_petals_outbound_ajax(){
		date_default_timezone_set(wp_timezone_string());
		$mn = get_option('bkf_petals_setting')['mn'];
		$password = get_option('bkf_petals_setting')['ppw'];
		$pw = base64_decode($password);
		$rawxml = file_get_contents('php://input');
		$xml = simplexml_load_string($rawxml);
		$symbol = '</strong>: ';
		$plainsymbol = ': ';
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
		$plainimplosion = implode("\n", array_map(
			function($k, $v) use($plainsymbol) { 
				return $k . $plainsymbol . $v;
			}, 
			array_keys($xmlarray), 
			array_values($xmlarray)
			)
		);
		
		if(bkf_debug()){
			bkf_debug_log(__('Petals request received.', 'bakkbone-florist-companion') . "\n" . $plainimplosion, 'debug');
		}
		
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
				'email'	  => ''
			);
			$shipping = array(
				'first_name' => (string)$xml->recipient,
				'last_name'  => '',
				'phone'	  => (string)$xml->phone,
				'address_1'  => (string)$xml->address,
				'address_2'  => '', 
				'city'	   => (string)$xml->town,
				'state'	  => (string)$xml->state,
				'postcode'   => (string)$xml->postalcode,
				'country'	=> (string)$xml->crtycode
			);
			$order->set_address( $billing, 'billing');
			$order->set_address( $shipping, 'shipping');
			$order->payment_complete();
			$order->set_status( 'wc-new' );
			$order->save();
			$note = __('<strong>Full transmission from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
			$order->add_order_note( $note );
			if( ob_get_length() ) {
				ob_clean();
			}
			echo '<?xml version="1.0" encoding="UTF-8"?>
					<message>
					<recordtype>02</recordtype>
					<member>'.$mn.'</member>
					<password>'.$pw.'</password>
					<notes>Order passed to florist via FloristPress</notes>
					<type>100</type>
					</message>';
		} elseif($xml->recordtype == 3){
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
			
			$fullnote = __('<strong>Full transmission from Petals: </strong><br>', 'bakkbone-florist-companion') . '<strong>' . $implosion;
			$note = '<strong>'.esc_html__('Message received from Petals:', 'bakkbone-florist-companion') . ' </strong><br>' . (string)$xml->notes;
			
			if (!empty($inboundorder)) {
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
			} elseif (!empty($order_id)) {
				$order = new WC_Order( $order_id[0] );
							$note = $order->add_order_note( $note );
							$wc_emails = WC()->mailer()->get_emails();
							$wc_emails['WC_Email_Petals_Note']->trigger( $order_id[0], $note );
							$order->add_order_note( $fullnote );
			}
			if( ob_get_length() ) {
				ob_clean();
			}
			echo '<?xml version="1.0" encoding="UTF-8"?>
					<message>
					<recordtype>02</recordtype>
					<member>'.$mn.'</member>
					<password>'.$pw.'</password>
					<notes>Message passed to florist via FloristPress</notes>
					<type>100</type>
					</message>';
		} else {
			bkf_debug_log(__('Invalid request received from Petals. Transmission details included below.', 'bakkbone-florist-companion') . "\n" . $plainimplosion, 'warning');
			if( ob_get_length() ) {
				ob_clean();
			}
			echo '<?xml version "1.0" encoding="UTF-8"?>
				<message>
				<recordtype>02</recordtype>
				<member>'.$mn.'</member>
				<password>'.$pw.'</password>
				<notes>Invalid message received via FloristPress - no action has been taken.</notes>
				<type>500</type>
				</message>';
		}
		die();
	}
	
	function bkf_cpp(){
		$cat = get_option('bkf_petals_product_setting')['cat'];
		$cpp = new WC_Product_Simple();
		$cpp->set_name( __('Petals Network Order', 'bakkbone-florist-companion') );
		$cpp->set_slug( 'petals-network-order' );
		$cpp->set_regular_price( '1.00' );
		$cpp->set_category_ids( [$cat] );
		$cpp->set_catalog_visibility('hidden');
		$cpp->set_status('publish');
		$cpp->save();
		$pid = $cpp->get_id();
		update_option('bkf_petals_product_setting', array_merge(get_option('bkf_petals_product_setting'), ['product' => $pid]));
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}
	
	function product_value(){
		$id = $_REQUEST['id'];
		$product = wc_get_product($id);
		$price = $product->get_price();
		$virtual = $product->is_virtual() ? '1' : '0';
		$priceresult = $price !== null && $price !== '' ? number_format($price,2,'.','') : '';
		$array = [$priceresult, $virtual];
		echo json_encode($array);
		die();
	}
	
	function get_timeslots(){
		$ts = bkf_get_timeslots();
		echo json_encode($ts);
		die();
	}
	
	function get_timeslots_for_order(){
		$method = $_REQUEST['method'];
		$day = $_REQUEST['day'];
		
		$ts = bkf_get_timeslots_for_order($method,$day);
		
		echo json_encode($ts);
		die();
	}
	
	function bkf_rates(){
		$suburb = $_REQUEST['city'];
		$postcode = $_REQUEST['postcode'];
		$state = $_REQUEST['state'];
		$country = $_REQUEST['country'];
		
		$list = bkf_get_shipping_rates_for_location($suburb, $postcode, $state, $country);
		
		echo json_encode($list);
		die();
	}
	
	function bkf_delivery_rates(){
		$suburb = $_REQUEST['city'];
		$postcode = $_REQUEST['postcode'];
		$state = $_REQUEST['state'];
		$country = $_REQUEST['country'];
		
		$list = bkf_get_shipping_rates_for_location($suburb, $postcode, $state, $country);
		
		$result = [];
		
		foreach($list as $item){
			if(!$item['pickup']){
				$shippingtax = new WC_Tax();
				if($item['tax_status'] !== 'none'){
					$shippingtaxes = $shippingtax->get_rates();
					$shippingrates = array_shift($shippingtaxes);
					$shipping_rate = '0.' . round(array_shift($shippingrates));
					$shippingfactor = 1 + $shipping_rate;
				} else {
					$shippingfactor = 1;
				}
				$item['cost'] = ($item['cost'] * $shippingfactor);
				$result[] = $item;
			}
		}
		
		echo json_encode($result);
		die();
	}
	
	function bkf_pickup_rates(){
		$list = bkf_get_shipping_rates();
		
		$result = [];
		
		foreach($list as $item){
			if($item['pickup']){
				$shippingtax = new WC_Tax();
				if($item['tax_status'] !== 'none'){
					$shippingtaxes = $shippingtax->get_rates();
					$shippingrates = array_shift($shippingtaxes);
					$shipping_rate = '0.' . round(array_shift($shippingrates));
					$shippingfactor = 1 + $shipping_rate;
				} else {
					$shippingfactor = 1;
				}
				$item['cost'] = ($item['cost'] * $shippingfactor);
				$result[] = $item;
			}
		}
		
		echo json_encode($result);
		die();
	}
	
	function bkf_method_cost(){
		$method = $_REQUEST['method'];
		$allrates = bkf_get_shipping_rates();
		
		foreach($allrates as $rate){
			$shippingtax = new WC_Tax();
			if($rate['rateid'] === $method){
				if($rate['tax_status'] !== 'none'){
					$shippingtaxes = $shippingtax->get_rates();
					$shippingrates = array_shift($shippingtaxes);
					$shipping_rate = '0.' . round(array_shift($shippingrates));
					$shippingfactor = 1 + $shipping_rate;
				} else {
					$shippingfactor = 1;
				}
				$cost = ($rate['cost'] * $shippingfactor);
			}
		}
		echo $cost;
		die();
	}
	
	function bkf_products_physical(){
		$productids = explode(',',$_REQUEST['id']);
		
		$physical = 'false';
		
		foreach($productids as $id){
			$product = new WC_Product($id);
			if(!$product->is_virtual()){
				$physical = 'true';
			}
		}
		
		echo $physical;
		die();
	}
	
	function check_weekday_fee(){
		$day = $_REQUEST['day'];
		$fee = bkf_check_weekday_fee($day);
		echo $fee;
		die();
	}
	
	function check_ds_fee(){
		$date = $_REQUEST['date'];
		$fee = bkf_check_dd_fee($date);
		if(empty($fee)){
			$result = 'false';
		} else {
			$result = json_encode($fee);
		}
		
		echo $result;
		die();
	}
	
	function get_fee_for_timeslot($inclusive = false){
		$ts = $_REQUEST['ts'];
		
		$all = bkf_get_timeslots_associative();
		$tslot = $all['ts'.$ts];
		
		$fee = $tslot['fee'] == '' || $tslot['fee'] == null ? '0.00' : $tslot['fee'];
				
		echo $fee;
		die();
	}
	
	function phone_order(){
				
		$order = wc_create_order();
		
		$virtual = $_REQUEST['virtualorder'] == 'true' ? true : false;
		$existingCustomer = $_REQUEST['customer_type'] == 'existing' ? true : false;
		$products = $_REQUEST['product'];
		$shippingCost = $_REQUEST['shipping_cost'];
		$timeslotFee = $_REQUEST['timeslot_fee'];
		$paymentOption = $_REQUEST['payment'];
		$destination = $_REQUEST['destination'];
		$pdf = $_REQUEST['pdf'];
				
		foreach($products as $product){
			$pid = $product['product'];
			$tax = new WC_Tax();
			$thisproduct = wc_get_product($pid);
			if($thisproduct->get_tax_status() == 'taxable'){
				$taxes = $tax->get_rates($thisproduct->get_tax_class());
				$rates = array_shift($taxes);
				$item_rate = '0.' . round(array_shift($rates));
				$factor = 1 + $item_rate;
			} else {
				$factor = 1;
			}
			
			$item = new WC_Order_Item_Product();
			$item->set_product($thisproduct);
			$item->set_quantity( 1 );
			$item->set_total($product['value'] / $factor);
			$item->update_meta_data( __("Notes", 'bakkbone-florist-companion'), $product['notes'] );
			$order->add_item($item);
		}
		
		$order->calculate_totals();
		
		if(!$virtual){
			$delivery = $_REQUEST['ordertype'] == 'delivery' ? true : false;
			if($delivery){
				$deliveryDetails = $_REQUEST['delivery'];
				
				$delFirst = $deliveryDetails['first'];
				$delLast = $deliveryDetails['last'];
				$delCompany = $deliveryDetails['company'];
				$delAddr1 = $deliveryDetails['address_1'];
				$delAddr2 = $deliveryDetails['address_2'];
				$delCity = $deliveryDetails['city'];
				$delState = $deliveryDetails['state'];
				$delPostcode = $deliveryDetails['postcode'];
				$delCountry = $deliveryDetails['country'];
				$delPhone = $deliveryDetails['phone'];
				
				$delNotes = $deliveryDetails['notes'];
				$order->add_meta_data('_shipping_notes',$delNotes);
				
				$shipping = array(
					'first_name' => $delFirst,
					'last_name'  => $delLast,
					'company'	 => $delCompany,
					'phone'	  => $delPhone,
					'address_1'  => $delAddr1,
					'address_2'  => $delAddr2, 
					'city'	   => $delCity,
					'state'	  => $delState,
					'postcode'   => $delPostcode,
					'country'	=> $delCountry
				);
				$order->set_address( $shipping, 'shipping');
			}
			
			if(isset($_REQUEST['delivery_timeslot'])){
				$timeslotId = $_REQUEST['delivery_timeslot'];
				$timeslotstring = bkf_get_timeslot_string($timeslotId);
				$order->add_meta_data('_delivery_timeslot_id',$timeslotId);
				$order->add_meta_data('_delivery_timeslot',$timeslotstring);
			}
			
			$cardMessage = $_REQUEST['card_message'];
			$order->add_meta_data('_card_message',$cardMessage);
			
			$deliveryDate = $_REQUEST['delivery_date'];
			$order->add_meta_data('_delivery_date',$deliveryDate);
			$order->add_meta_data('_delivery_timestamp',strtotime($deliveryDate));
			
			$shippingMethod = $_REQUEST['shipping_method'];
			$shippingMethodDetails = bkf_get_shipping_rates_associative()[$shippingMethod];
			
			$shipping = new WC_Order_Item_Shipping();
			$shipping->set_method_id( $shippingMethod );
			$shipping->set_name($shippingMethodDetails['usertitle']);
			
			$shippingtax = new WC_Tax();
			if($shipping->get_tax_status() == 'taxable'){
				$shippingtaxes = $tax->get_rates($shipping->get_tax_class());
				$shippingrates = array_shift($shippingtaxes);
				$shipping_rate = '0.' . round(array_shift($shippingrates));
				$shippingfactor = 1 + $shipping_rate;
			} else {
				$shippingfactor = 1;
			}
			
			$shipping->set_total( $shippingCost / $shippingfactor );
			$order->add_item( $shipping );
			
			if($timeslotFee !== ''){
				$fee = new WC_Order_Item_Fee();
				$feetaxsetting = get_option('bkf_ddf_setting')['ddtst'];
				$feetax = new WC_Tax();
				$feetaxable = $feetaxsetting ? 'taxable' : 'none';
				$fee->set_tax_status($feetaxable);
				
				if($feetaxsetting){
					$feetaxes = $feetax->get_shop_base_rate();
					$feerates = array_shift($feetaxes);
					$fee_rate = '0.' . round(array_shift($feerates));
					$feefactor = 1 + $fee_rate;
				} else {
					$feefactor = 1;
				}
				
				$fee->set_name(__('Timeslot Fee', 'bakkbone-florist-companion'));
				$fee->set_total($timeslotFee / $feefactor);
				
				$order->add_item( $fee );
			}
			
			$order->calculate_totals();
			
		}
		
		if($existingCustomer){
			$cusId = $_REQUEST['customer_id'];
			$order->set_customer_id($cusId);
			
			$cus = new WC_Customer($cusId);
			$billing = $cus->get_billing();
		} else {
			$cus = $_REQUEST['billing'];
			$billing = array(
				'first_name' => $cus['first'],
				'last_name'  => $cus['last'],
				'email'	  => $cus['email'],
				'phone'	  => $cus['phone'],
				'company'	=> $cus['company']
			);
			if($_REQUEST['create_customer']){
				$customeraccount = wc_create_new_customer($cus['email']);
				update_user_meta($customeraccount, "billing_first_name", $cus['first']);
				update_user_meta($customeraccount, "billing_last_name", $cus['last']);
				update_user_meta($customeraccount, "billing_company", $cus['company']);
				update_user_meta($customeraccount, "billing_email", $cus['email']);
				update_user_meta($customeraccount, "billing_phone", $cus['phone']);
				do_action('bkf_phone_order_customer_created', $customeraccount);
			}
		}
		$order->set_address( $billing, 'billing');
		
		$wc_emails = WC()->mailer()->get_emails();
		
		switch($paymentOption){
			case 'paid':
				$order->set_payment_method_title(__('Paid by phone', 'bakkbone-florist-companion'));
				$order->payment_complete();
				do_action('bkf_phone_order_mark_paid', $order->get_id());
				break;
			case 'invoice':
				$order->set_status('invoiced');
				$wc_emails['WC_Email_Customer_Invoice']->trigger( $order->get_id() );
				do_action('bkf_phone_order_send_invoice', $order->get_id());
				break;
			case 'draft':
				$order->set_status('phone-draft');
				do_action('bkf_phone_order_save_draft', $order->get_id());
				break;
		}
		
		$order->save();
		
		switch($destination){
			case 'list':
				$url = admin_url('edit.php?post_type=shop_order');
				break;
			case 'edit':
				$url = $order->get_edit_order_url();
				break;
			case 'new':
				$url = $_SERVER["HTTP_REFERER"];
				break;
			default:
				$url = admin_url('edit.php?post_type=shop_order');
		}
		
		do_action('bkf_phone_order_after_save', $order->get_id());
		
		if($pdf){
			$url .= '&bkf_dw='.$order->get_id();
		}
		
		header("Location: ".$url);
		
		die();
	}
	
	function bkf_sub_notice_dismissed(){
		$uid = $_REQUEST['uid'];
		update_user_meta( $uid, 'bkf_sub_notice_dismissed', 'true', true );
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}
	
	function resend_invoice(){
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "bkf")) {
			exit(__('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));
		}

		$post_id = $_REQUEST['order_id'];
		$post = new WP_Post($post_id);
		$_POST['wc_order_action'] = 'send_order_details';
		WC_Meta_Box_Order_Actions::save($post_id, $post);
		header("Location: ".$_SERVER["HTTP_REFERER"]);
		die();
	}
	
	function is_valid_order(){
		$id = $_REQUEST['id'];
		$order = wc_get_order($id);
		$result = $order == null ? false : true;
		echo json_encode($result);
		die();
	}
	
	function search_suburbs(){
		$string = sanitize_text_field($_REQUEST['query']);
		$suburbs = bkf_get_suburbs();
		$result = [];
		foreach ($suburbs as $suburb => $methods) {
			if (stristr($suburb, $string)) {
				$methodarray = [];
				foreach ($methods as $method) {
					$methodarray[] = [
						'enabled'			=> $method['enabled'],
						'instanceid'		=> $method['instanceid'],
						'settings'			=> $method['settings'],
						'rateid'			=> $method['rateid'],
						'usertitle'			=> $method['usertitle'],
						'tax_status'		=> $method['tax_status'],
						'cost'				=> $method['cost'],
						'title'				=> $method['title'],
						'method_suburbs'	=> $method['method_suburbs'],
					];
				}
				$result[$suburb] = $methodarray;
			}
		}
		if (count($result)) {
			$resulthtml = '';
			foreach ($result as $suburb => $methods) {
				$resulthtml .= '<div style="margin:5px;padding:5px;border:1px solid #A5ACB2;"><h3 style="margin-bottom:2px;"><strong>'.$suburb.'</strong></h3>';
				foreach ($methods as $method) {
					$resulthtml .= '<div>#'.$method['instanceid'].' – '.$method['usertitle'].' ('.bkf_currency_symbol().number_format($method['cost'], 2, '.', '').')</div>';
				}
				$resulthtml .= '</div>';
			}
		} else {
			$resulthtml = __('No suburbs match your search.', 'bakkbone-florist-companion');
		}
		echo $resulthtml;
		die();
	}

	function search_suburbs_frontend(){
		$string = sanitize_text_field($_REQUEST['query']);
		$suburbs = bkf_get_suburbs();
		$result = [];
		foreach ($suburbs as $suburb => $methods) {
			if (stristr($suburb, $string)) {
				$methodarray = [];
				foreach ($methods as $method) {
					$methodarray[] = [
						'enabled'			=> $method['enabled'],
						'instanceid'		=> $method['instanceid'],
						'settings'			=> $method['settings'],
						'rateid'			=> $method['rateid'],
						'usertitle'			=> $method['usertitle'],
						'tax_status'		=> $method['tax_status'],
						'cost'				=> $method['cost'],
						'title'				=> $method['title'],
						'method_suburbs'	=> $method['method_suburbs'],
					];
				}
				$result[$suburb] = $methodarray;
			}
		}
		if (count($result)) {
			$resulthtml = '<h2 class="bkf-suburb-search-results-header">'.stripslashes($_REQUEST['header']).'</h2>';
			foreach ($result as $suburb => $methods) {
				$resulthtml .= '<div class="bkf-suburb-search-results-item"><h3><strong>'.$suburb.'</strong></h3><ul>';
				foreach ($methods as $method) {
					$resulthtml .= '<li>'.$method['usertitle'].' ('.bkf_currency_symbol().number_format($method['cost'], 2, '.', '').')</li>';
				}
				$resulthtml .= '</ul></div>';
			}
		} else {
			$resulthtml = '<div class="bkf-suburb-search-results-noresults"><p>'.stripslashes($_REQUEST['noresults']).'</p></div>';
		}
		echo $resulthtml;
		die();
	}
	
	function check_delivery_date(){
		$length = get_option('bkf_ddi_setting')['ddi'] * 7;
		$tz = new DateTimeZone(wp_timezone_string());
		$singleday = new DateInterval('P1D');
		$today = new DateTimeImmutable('today', $tz);
		$dates = new DatePeriod($today, $singleday, $length);
		
		$result = [];
		
		$cart = WC()->cart->get_cart();
		$categories = [];
		foreach($cart as $k => $v){
			$cat = wc_get_product_cat_ids($v['product_id']);
			foreach($cat as $c){
				$categories[] = $c;
			}
		}
		
		foreach ($dates as $then) {
			$date = $then->format('j');
			$month = $then->format('n');
			$year = $then->format('Y');
			$method = $_REQUEST['method'];
			
			$result[$date.'.'.$month.'.'.$year] = bkf_check_dd_availability($then, $method);
		}
		
		echo json_encode($result);
		die();
	}
	
}