<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email' ) ) {
	return;
}

/**
 * Class WC_Email_Customer_Out_for_Delivery_Order
 */
class WC_Email_Customer_Out_for_Delivery_Order extends WC_Email {

	/**
	 * Create an instance of the class.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		$this->id          = 'customer_out_order';
		$this->title       = __( 'Order Out for Delivery', 'woocommerce' );
		$this->description = __( 'An email sent to the customer when an order is out for delivery.', 'woocommerce' );
		$this->customer_email = true;
		$this->heading     = __( 'Order Out for Delivery', 'woocommerce' );
		$this->subject     = sprintf( _x( '[%s] Order Out for Delivery', 'default email subject for out for delivery emails', 'woocommerce' ), '{blogname}' );

		$this->template_html  = 'emails/customer-out-order.php';
		$this->template_plain = 'emails/plain/customer-out-order.php';
		$this->template_base  = CUSTOM_WC_EMAIL_PATH . 'templates/';

		add_action( 'woocommerce_order_status_out', array( $this, 'trigger' ) );

		parent::__construct();
	}

	/**
	 * Trigger Function that will send this email to the customer.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order_id ) {
		$this->object = wc_get_order( $order_id );

		if ( version_compare( '3.0.0', WC()->version, '>' ) ) {
			$order_email = $this->object->billing_email;
		} else {
			$order_email = $this->object->get_billing_email();
		}

		$this->recipient = $order_email;


		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin' => false,
			'plain_text'    => false,
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
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this
		), '', $this->template_base );
	}

}

return new WC_Email_Customer_Out_for_Delivery_Order();