<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Email_Status
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Email_Status{

	public function __construct() {
		add_action('woocommerce_email_classes', [$this, 'bkf_register_email'], 10, 1 );
		define( '__BKF_WC_EMAIL_PATH__', plugin_dir_path( __FILE__ ) );
		add_action('woocommerce_order_status_changed', [$this, 'bkf_woocommerce_order_status_changed'], PHP_INT_MAX, 4 );
	}

	public function bkf_register_email( $emails ) {
		require_once 'emails/class-wc-customer-scheduled-order.php';
		require_once 'emails/class-wc-customer-completed-order.php';
		require_once 'emails/class-wc-customer-processed-order.php';
		require_once 'emails/class-wc-customer-collected-order.php';
		require_once 'emails/class-wc-customer-made-order.php';
		require_once 'emails/class-wc-customer-collect-order.php';
		require_once 'emails/class-wc-customer-out-order.php';
		require_once 'emails/class-wc-draft-phone-order.php';
		
		unset($emails['WC_Email_Customer_Completed_Order']);
		$emails['WC_Email_Customer_Completed_Order'] = new WC_BKF_Email_Customer_Completed_Order();
		$emails['WC_Email_Customer_Processed_Order'] = new WC_Email_Customer_Processed_Order();
		$emails['WC_Email_Customer_Collected_Order'] = new WC_Email_Customer_Collected_Order();
		$emails['WC_Email_Customer_Scheduled_Order'] = new WC_Email_Customer_Scheduled_Order();
		$emails['WC_Email_Customer_Prepared_Order'] = new WC_Email_Customer_Prepared_Order();
		$emails['WC_Email_Customer_Collect_Order'] = new WC_Email_Customer_Collect_Order();
		$emails['WC_Email_Customer_Out_for_Delivery_Order'] = new WC_Email_Customer_Out_for_Delivery_Order();
		$emails['WC_Email_Draft_Phone_Order'] = new WC_Email_Draft_Phone_Order();
		
		return $emails;
	}

	function bkf_woocommerce_order_status_changed( $order_id, $from, $to, $order ) {
	$wc_emails = WC()->mailer()->get_emails();
	$bkfstatuses = [
		'scheduled',
		'made',
		'collect',
		'out',
		'new',
		'accept',
		'reject',
		'relay',
		'phone-draft',
		'invoiced'
	];
		if(in_array($from, $bkfstatuses)) {
			if( $to == 'processing' ) {
				$wc_emails['WC_Email_Customer_Processing_Order']->trigger( $order_id );
			}
		}
		if(in_array($from, $bkfstatuses)) {
			if( $to == 'completed' ) {
				$wc_emails['WC_Email_Customer_Completed_Order']->trigger( $order_id );
			}
			if( $to == 'collected' ) {
				$wc_emails['WC_Email_Customer_Collected_Order']->trigger( $order_id );
			}
			if( $to == 'processed' ) {
				$wc_emails['WC_Email_Customer_Processed_Order']->trigger( $order_id );
			}
		}
		if( $to == 'scheduled' ) {
			$wc_emails['WC_Email_Customer_Scheduled_Order']->trigger( $order_id );
		}
		if( $to == 'made' ) {
			$wc_emails['WC_Email_Customer_Prepared_Order']->trigger( $order_id );
		}
		if( $to == 'collect' ) {
			$wc_emails['WC_Email_Customer_Collect_Order']->trigger( $order_id );
		}
		if( $to == 'out' ) {
			$wc_emails['WC_Email_Customer_Out_for_Delivery_Order']->trigger( $order_id );
		}
		if( $to == 'phone-draft' ) {
			$wc_emails['WC_Email_Draft_Phone_Order']->trigger( $order_id );
		}
		
	}  
	
}