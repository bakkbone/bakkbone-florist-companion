<?php
/**
 * Class WC_Email_Petals_Outcome file
 *
 * @package WooCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Email_Petals_Outcome' ) ) :

	/**
	 * Petals Outcome Email.
	 *
	 * An email sent to the admin when specified Petals actions are completed, disclosing the outcome of the action.
	 *
	 * @class       WC_Email_Petals_Outcome
	 * @version     2.0.0
	 * @package     WooCommerce\Classes\Emails
	 * @extends     WC_Email
	 */
	class WC_Email_Petals_Outcome extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'petals_message';
			$this->title          = __( 'Petals - Action Outcome', 'bakkbone-florist-companion' );
			$this->description    = __( 'Action Outcome emails are sent to chosen recipient(s) when you perform certain actions in your dashboard, notifying you of the outcome of the action.', 'bakkbone-florist-companion' );
			$this->template_html  = 'emails/petals-outcome.php';
			$this->template_plain = 'emails/plain/petals-outcome.php';
			$this->template_base  = BKF_WC_PETALS_EMAIL_PATH . '/templates/';
			$this->placeholders   = array();

			// Call parent constructor.
			parent::__construct();

			// Other settings.
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( '[{site_title}]: Petals Action Outcome', 'bakkbone-florist-companion' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Petals Action Outcome', 'bakkbone-florist-companion' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id ) {
			$this->setup_locale();
			
			$this->object                         = get_post($order_id);
			$this->placeholders['{delivery_date}']   = get_post_meta($this->object, 'deldate', true);
			$this->placeholders['{order_number}'] = get_post_meta($this->object, '_petals_on', true);
			
			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
		), '', $this->template_base );
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => true,
					'email'              => $this,
		), '', $this->template_base );
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thank you', 'bakkbone-florist-companion' );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'bakkbone-florist-companion' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'bakkbone-florist-companion' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'bakkbone-florist-companion' ),
					'default' => 'yes',
				),
				'recipient'          => array(
					'title'       => __( 'Recipient(s)', 'bakkbone-florist-companion' ),
					'type'        => 'text',
					/* translators: %s: WP admin email */
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'bakkbone-florist-companion' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'            => array(
					'title'       => __( 'Subject', 'bakkbone-florist-companion' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'            => array(
					'title'       => __( 'Email heading', 'bakkbone-florist-companion' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => __( 'Additional content', 'bakkbone-florist-companion' ),
					'description' => __( 'Text to appear below the main email content.', 'bakkbone-florist-companion' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'bakkbone-florist-companion' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type'         => array(
					'title'       => __( 'Email type', 'bakkbone-florist-companion' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'bakkbone-florist-companion' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;