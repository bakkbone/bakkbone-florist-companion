<?php
/**
 * Class WC_BKF_Email_Customer_Completed_Order file.
 *
 * @package WooCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

	/**
	 * Customer Completed Order Email.
	 *
	 * Order completed emails are sent to the customer when the order is marked completed.
	 *
	 * @class	   WC_BKF_Email_Customer_Completed_Order
	 * @version	 2.0.0
	 * @package	 WooCommerce\Classes\Emails
	 * @extends	 WC_Email
	 */
	class WC_BKF_Email_Customer_Completed_Order extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id			 = 'customer_completed_order';
			$this->customer_email = true;
			$this->title		  = __( 'Order Delivered', 'woocommerce' );
			$this->description	= __( 'An email sent to the customer when an order is delivered.', 'woocommerce' );
			$this->template_html  = 'emails/customer-completed-order.php';
			$this->template_plain = 'emails/plain/customer-completed-order.php';
			$this->template_base  = __BKF_WC_EMAIL_PATH__ . 'templates/';
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int			$order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id, $order = false ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object						 = $order;
				$this->recipient					  = $this->object->get_billing_email();
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Your {site_title} order has been delivered', 'woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'It\'s been delivered!', 'woocommerce' );
		}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'		 => $this->object,
			'email_heading' => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin' => false,
			'plain_text'	=> false,
			'email'			=> $this
		), '', $this->template_base );
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'		 => $this->object,
			'email_heading' => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin' => false,
			'plain_text'	=> true,
			'email'			=> $this
		), '', $this->template_base );
	}

}