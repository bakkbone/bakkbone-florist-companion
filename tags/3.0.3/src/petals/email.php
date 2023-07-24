<?php
/**
 * @author BAKKBONE Australia
 * @package Bkf_WC_Petals_Email
 * @license GNU General Public License (GPL) 3.0
**/

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Bkf_WC_Petals_Email {

	public function __construct() {
		add_action('woocommerce_email_classes', array( $this, 'bkf_register_petals_email' ), 90, 1 );
		define('BKF_WC_PETALS_EMAIL_PATH', plugin_dir_path( __FILE__ ) );
		add_action('woocommerce_email_order_meta', array( $this, 'bkf_add_order_notes_to_email'), 10, 3 );
		add_action('woocommerce_order_status_changed', array($this, 'bkf_woocommerce_petals_status_changed'), PHP_INT_MAX, 4 );
	}

	public function bkf_register_petals_email( $emails ) {
		require_once 'emails/class-wc-petals-note.php';
		require_once 'emails/class-wc-petals-new.php';
		require_once 'emails/class-wc-petals-inbound.php';
		require_once 'emails/class-wc-petals-message.php';
		require_once 'emails/class-wc-petals-outcome.php';

		$emails['WC_Email_Petals_New'] = new WC_Email_Petals_New();
		$emails['WC_Email_Petals_Inbound'] = new WC_Email_Petals_Inbound();
		$emails['WC_Email_Petals_Message'] = new WC_Email_Petals_Message();
		$emails['WC_Email_Petals_Note'] = new WC_Email_Petals_Note();
		$emails['WC_Email_Petals_Outcome'] = new WC_Email_Petals_Outcome();

		return $emails;
	}
	
	function bkf_add_order_notes_to_email( $order, $sent_to_admin = true, $plain_text = false ) {
	if ( ! $sent_to_admin ) {
		return;
	}

	$notes = array();

	$args = array(
		'post_id' => $order->get_id(),
		'approve' => 'approve',
		'type' => ''
	);

	remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

	$notes = get_comments( $args );

	add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

	echo '<h4>Order Notes</h4>';
	echo '<ul class="order_notes">';
	if ( $notes ) {
		foreach( $notes as $note ) {
			$note_classes = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? array( 'customer-note', 'note' ) : array( 'note' );
			?>
			<li rel="<?php echo absint($note->comment_ID); ?>" class="<?php echo implode(' ', $note_classes); ?>">
				<div class="note_content">
					<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
					<em><?php echo $note->comment_date ?></em>
				</div>
			</li>
		<?php
		}
	} else {
		echo '<li class="no-order-comment">'.esc_html__('There are no order notes compatible to display here.','bakkbone-florist-companion').'</li>';
	}
	echo '</ul>';
	}
	
	function bkf_woocommerce_petals_status_changed( $order_id, $from, $to, $order ) {
	$wc_emails = WC()->mailer()->get_emails();
		if( $to == 'new' ) {
			$wc_emails['WC_Email_Petals_New']->trigger( $order_id );
		}
	}  
	
}