<?php
/**
 * @author BAKKBONE Australia
 * @package Bkf_WC_Email
 * @license GNU General Public License (GPL) 3.0
**/

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Bkf_WC_Email
 */
class Bkf_WC_Email {

	/**
	 * Bkf_WC_Email constructor.
	 */
	public function __construct() {
		add_action('woocommerce_email_classes', array( $this, 'bkf_register_email' ), 90, 1 );
		define( 'CUSTOM_WC_EMAIL_PATH', plugin_dir_path( __FILE__ ) );
		add_filter('woocommerce_email_actions', array($this, 'bkf_woocommerce_email_actions'), 10, 1);
		add_filter('woocommerce_locate_template', array($this, 'bkf_customer_completed_order_template'), PHP_INT_MAX, 3);
		add_action('woocommerce_order_status_changed', array($this, 'bkf_woocommerce_order_status_changed'), PHP_INT_MAX, 4 );
	}

	/**
	 * @param array $emails
	 *
	 * @return array
	 */
	public function bkf_register_email( $emails ) {
		require_once 'emails/class-wc-customer-scheduled-order.php';
		require_once 'emails/class-wc-customer-made-order.php';
		require_once 'emails/class-wc-customer-out-order.php';

		$emails['WC_Email_Customer_Scheduled_Order'] = new WC_Email_Customer_Scheduled_Order();
		$emails['WC_Email_Customer_Prepared_Order'] = new WC_Email_Customer_Prepared_Order();
		$emails['WC_Email_Customer_Out_for_Delivery_Order'] = new WC_Email_Customer_Out_for_Delivery_Order();

		return $emails;
	}

    public function bkf_customer_completed_order_template($template, $template_name, $template_path)
    {
        if ('customer-completed-order.php' === basename($template)){
       $template = trailingslashit(CUSTOM_WC_EMAIL_PATH) . 'templates/emails/customer-completed-order.php';
        }
        return $template;
    }
    
    
    function bkf_woocommerce_order_status_changed( $order_id, $from, $to, $order ) {

    $wc_emails = WC()->mailer()->get_emails();

    if( $to == 'processing' ) {
        $wc_emails['WC_Email_Customer_Processing_Order']->trigger( $order_id );
    }
    if( $to == 'completed' ) {
        $wc_emails['WC_Email_Customer_Completed_Order']->trigger( $order_id );
    }
    if( $to == 'scheduled' ) {
        $wc_emails['WC_Email_Customer_Scheduled_Order']->trigger( $order_id );
    }
    if( $to == 'made' ) {
        $wc_emails['WC_Email_Customer_Prepared_Order']->trigger( $order_id );
    }
    if( $to == 'out' ) {
        $wc_emails['WC_Email_Customer_Out_for_Delivery_Order']->trigger( $order_id );
    }
    }
    
    
}

new Bkf_WC_Email();
