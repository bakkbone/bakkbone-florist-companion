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
		add_action( 'woocommerce_email_classes', array( $this, 'bkf_register_email' ), 90, 1 );
		define( 'CUSTOM_WC_EMAIL_PATH', plugin_dir_path( __FILE__ ) );
		add_filter('woocommerce_email_actions', array($this, 'bkf_woocommerce_email_actions'), 10, 1);
		add_filter('woocommerce_locate_template', array($this, 'bkf_customer_completed_order_template'), PHP_INT_MAX, 4);
	}

	/**
	 * @param array $emails
	 *
	 * @return array
	 */
	public function bkf_register_email( $emails ) {
		$emails['WC_Email_Customer_Scheduled_Order'] = include __DIR__ . 'emails/class-wc-customer-scheduled-order.php';
		$emails['WC_Email_Customer_Prepared_Order'] = include __DIR__ . 'emails/class-wc-customer-made-order.php';
		$emails['WC_Email_Customer_Out_for_Delivery_Order'] = include __DIR__ . 'emails/class-wc-customer-out-order.php';

		return $emails;
	}

    public function bkf_woocommerce_email_actions($actions)
    {
        $actions[] = 'woocommerce_order_status_scheduled';
        $actions[] = 'woocommerce_order_status_made';
        $actions[] = 'woocommerce_order_status_out';
        $actions[] = 'woocommerce_order_status_relay';
        $actions[] = 'woocommerce_order_status_processing';
        $actions[] = 'woocommerce_order_status_completed';
        $actions[] = 'woocommerce_order_status_scheduled_to_made';
        $actions[] = 'woocommerce_order_status_scheduled_to_out';
        $actions[] = 'woocommerce_order_status_scheduled_to_relay';
        $actions[] = 'woocommerce_order_status_scheduled_to_processing';
        $actions[] = 'woocommerce_order_status_scheduled_to_completed';
        $actions[] = 'woocommerce_order_status_made_to_scheduled';
        $actions[] = 'woocommerce_order_status_made_to_out';
        $actions[] = 'woocommerce_order_status_made_to_relay';
        $actions[] = 'woocommerce_order_status_made_to_processing';
        $actions[] = 'woocommerce_order_status_made_to_completed';
        $actions[] = 'woocommerce_order_status_out_to_scheduled';
        $actions[] = 'woocommerce_order_status_out_to_made';
        $actions[] = 'woocommerce_order_status_out_to_relay';
        $actions[] = 'woocommerce_order_status_out_to_processing';
        $actions[] = 'woocommerce_order_status_out_to_completed';
        $actions[] = 'woocommerce_order_status_relay_to_scheduled';
        $actions[] = 'woocommerce_order_status_relay_to_made';
        $actions[] = 'woocommerce_order_status_relay_to_out';
        $actions[] = 'woocommerce_order_status_relay_to_processing';
        $actions[] = 'woocommerce_order_status_relay_to_completed';
        $actions[] = 'woocommerce_order_status_processing_to_scheduled';
        $actions[] = 'woocommerce_order_status_processing_to_made';
        $actions[] = 'woocommerce_order_status_processing_to_out';
        $actions[] = 'woocommerce_order_status_processing_to_relay';
        $actions[] = 'woocommerce_order_status_processing_to_completed';
        $actions[] = 'woocommerce_order_status_completed_to_scheduled';
        $actions[] = 'woocommerce_order_status_completed_to_made';
        $actions[] = 'woocommerce_order_status_completed_to_out';
        $actions[] = 'woocommerce_order_status_completed_to_relay';
        $actions[] = 'woocommerce_order_status_completed_to_processing';
        $bkfoptions = get_option("bkf_options_setting");
        if($bkfoptions["bkf_petals"] == "1") {
        $actions[] = 'woocommerce_order_status_new';
        $actions[] = 'woocommerce_order_status_accept';
        $actions[] = 'woocommerce_order_status_reject';
        $actions[] = 'woocommerce_order_status_scheduled_to_new';
        $actions[] = 'woocommerce_order_status_scheduled_to_accept';
        $actions[] = 'woocommerce_order_status_scheduled_to_reject';
        $actions[] = 'woocommerce_order_status_made_to_new';
        $actions[] = 'woocommerce_order_status_made_to_accept';
        $actions[] = 'woocommerce_order_status_made_to_reject';
        $actions[] = 'woocommerce_order_status_out_to_new';
        $actions[] = 'woocommerce_order_status_out_to_accept';
        $actions[] = 'woocommerce_order_status_out_to_reject';
        $actions[] = 'woocommerce_order_status_relay_to_new';
        $actions[] = 'woocommerce_order_status_relay_to_accept';
        $actions[] = 'woocommerce_order_status_relay_to_reject';
        $actions[] = 'woocommerce_order_status_processing_to_new';
        $actions[] = 'woocommerce_order_status_processing_to_accept';
        $actions[] = 'woocommerce_order_status_processing_to_reject';
        $actions[] = 'woocommerce_order_status_completed_to_new';
        $actions[] = 'woocommerce_order_status_completed_to_accept';
        $actions[] = 'woocommerce_order_status_completed_to_reject';
        $actions[] = 'woocommerce_order_status_new_to_scheduled';
        $actions[] = 'woocommerce_order_status_new_to_made';
        $actions[] = 'woocommerce_order_status_new_to_out';
        $actions[] = 'woocommerce_order_status_new_to_relay';
        $actions[] = 'woocommerce_order_status_new_to_processing';
        $actions[] = 'woocommerce_order_status_new_to_completed';
        $actions[] = 'woocommerce_order_status_new_to_accept';
        $actions[] = 'woocommerce_order_status_new_to_reject';
        $actions[] = 'woocommerce_order_status_accept_to_scheduled';
        $actions[] = 'woocommerce_order_status_accept_to_made';
        $actions[] = 'woocommerce_order_status_accept_to_out';
        $actions[] = 'woocommerce_order_status_accept_to_relay';
        $actions[] = 'woocommerce_order_status_accept_to_processing';
        $actions[] = 'woocommerce_order_status_accept_to_completed';
        $actions[] = 'woocommerce_order_status_accept_to_new';
        $actions[] = 'woocommerce_order_status_accept_to_reject';
        $actions[] = 'woocommerce_order_status_reject_to_scheduled';
        $actions[] = 'woocommerce_order_status_reject_to_made';
        $actions[] = 'woocommerce_order_status_reject_to_out';
        $actions[] = 'woocommerce_order_status_reject_to_relay';
        $actions[] = 'woocommerce_order_status_reject_to_processing';
        $actions[] = 'woocommerce_order_status_reject_to_completed';
        $actions[] = 'woocommerce_order_status_reject_to_new';
        $actions[] = 'woocommerce_order_status_reject_to_accept';
        }
        return $actions;
    }
  


    public function bkf_customer_completed_order_template($template, $template_name, $template_path)
    {
        if ('customer-completed-order.php' === basename($template)){
       $template = trailingslashit(CUSTOM_WC_EMAIL_PATH) . 'templates/emails/customer-completed-order.php';
        }
        return $template;
    }
    
    
    
}

new Bkf_WC_Email();
