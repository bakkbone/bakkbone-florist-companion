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
//		add_filter( 'wc_get_template', array( $this, 'bkf_replace_template' ), 90, 1 );
		define( 'CUSTOM_WC_EMAIL_PATH', plugin_dir_path( __FILE__ ) );
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

/**    public function bkf_replace_template( $located, $template_name, $args, $template_path, $default_path ) {
    if( $template_name == 'emails/customer-completed-order.php'){
        $located = CUSTOM_WC_EMAIL_PATH . '/templates/' . $template_name ;
    }
    return $located;
    }**/
  
}

new Bkf_WC_Email();