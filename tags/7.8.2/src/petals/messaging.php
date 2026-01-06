<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Petals_Messaging
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class BKF_Petals_Messaging{
  
	function __construct() {
	  $bkfoptions = get_option("bkf_features_setting");
	  if($bkfoptions["petals_on"] == 1) {
		  add_action('add_meta_boxes', [$this, 'bkf_pm_metabox_init'] );
	  };
	}
	
	function bkf_pm_metabox_init(){
		$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		if(get_current_screen()->id == $screen){
			$order = new WC_Order($_GET['id']);
			if($order->get_meta("_petals_on",true) !== ''){
				add_meta_box('bkf_pm', __('Petals Messaging', 'bakkbone-florist-companion'), [$this, 'bkf_pm_metabox_callback'], $screen, 'advanced', 'low');
			}
		}
	}
	
	public function bkf_pm_metabox_callback($post){
		?>
		<div id="bkf_pm" name="bkf_pm">
			<p><select required class="bkf-form-control regular-text" form="bkf_pm" id="petals_msg_type" name="msgtype">
				<option value="" disabled selected><?php esc_html_e('Select a message type...', 'bakkbone-florist-companion'); ?></option>
				<option value="M"><?php esc_html_e("Message - non-complaint", "bakkbone-florist-companion"); ?></option>
				<option value="C"><?php esc_html_e("Complaint", "bakkbone-florist-companion"); ?></option>
				<option value="F"><?php esc_html_e("Final message (no response required)", "bakkbone-florist-companion"); ?></option>
				<?php
				// Future compatibility:
				// echo '<option value="D">'.esc_html__("Mark order as delivered", "bakkbone-florist-companion").'</option>';
				?>
			</select></p>
			<p><input type="text" required class="bkf-form-control large-text" form="bkf_pm" name="msg" id="petals_msg_body" placeholder="<?php esc_html_e("Message to Petals","bakkbone-florist-companion"); ?>" /></p>
			<p><button form="bkf_pm" class="button" onclick="ajaxPetalsMessage()"><?php esc_html_e("Send Message","bakkbone-florist-companion"); ?></button></p>
		</div>
		<script>
			function ajaxPetalsMessage( $ ) {
					var postNonce = "<?php echo wp_create_nonce("bkf"); ?>";
					var orderId = "<?php echo $post->ID; ?>";
					var msgType = document.getElementById("petals_msg_type").value;
					var msgBody = document.getElementById("petals_msg_body").value;
					var postUrl = "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=petals_msg&nonce=' + postNonce + '&orderid=' + orderId + '&msgtype=' + msgType + '&msgbody=' + msgBody;
					if(msgType == '' || msgBody == ''){alert('<?php esc_html_e('Please complete both fields before attempting to send a message.', 'bakkbone-florist-companion'); ?>');} else {
					if(confirm('<?php esc_html_e('Send Message?', 'bakkbone-florist-companion'); ?>')){
						alert('<?php esc_html_e('Please wait...', 'bakkbone-florist-companion'); ?>')
						jQuery.post(postUrl);
						setTimeout(ajaxPetalsMessageProceed,3000);
					};};
				}
				function ajaxPetalsMessageProceed( $ ) {
					document.getElementById("petals_msg_type").value = '';
					document.getElementById("petals_msg_body").value = '';
					alert('<?php esc_html_e("Message queued for sending - this page will now attempt to refresh to display the outcome in the Order Notes. An email will also be sent to the site admin if enabled in WooCommerce settings.", "bakkbone-florist-companion"); ?>');
					location.reload();					
				}
		</script>
		<?php
	}
	
}