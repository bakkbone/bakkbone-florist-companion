<?php
/**
 * Class Bkf_Delivered_WC_Email
 */
class Bkf_Delivered_WC_Email {

	/**
	 * Delivered_WC_Email constructor.
	 */
	public function __construct() {
		// Filtering the emails and adding our own email.
		add_filter( 'woocommerce_email_classes', array( $this, 'bkf_register_delivered_email' ), 90, 1 );
		// Absolute path to the plugin folder.
		define( 'DELIVERED_WC_EMAIL_PATH', get_stylesheet_directory() );
	}

	/**
	 * @param array $emails
	 *
	 * @return array
	 */
	public function bkf_register_delivered_email( $emails ) {
		require_once 'emails/class-wc-delivered-status-order.php';
		$emails['WC_Delivered_status_Order'] = new WC_Delivered_status_Order();
		return $emails;
	}
}
new Bkf_Delivered_WC_Email();
