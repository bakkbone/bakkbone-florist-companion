<?php
/**
 * Class Bkf_WC_Email
 */
class Bkf_WC_Email {

	/**
	 * Scheduled_WC_Email constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'bkf_register_emails' ), 90, 1 );
	}

	/**
	 * @param array $emails
	 *
	 * @return array
	 */
	public function bkf_register_emails( $emails ) {
		require_once 'emails/class-wc-scheduled.php';
		$emails['WC_Scheduled_status_Order'] = new WC_Scheduled_status_Order();
		return $emails;
	}
}
new Bkf_WC_Email();
