<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPetalsMsg
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfPetalsMsg{
  
    function __construct() {
      $bkfoptions = get_option("bkf_features_setting");
      if($bkfoptions["petals_on"] == 1) {
		  add_action('add_meta_boxes', array($this, 'bkf_pm_metabox_init') );
      };
    }
	
    function bkf_pm_metabox_init(){
		global $post;
		if(get_post_meta($post->ID,"_petals_on",true) !== ''){
	        add_meta_box('bkf_pm', __('Petals Messaging', 'bakkbone-florist-companion'),array($this, 'bkf_pm_metabox_callback'),'shop_order','advanced','low');
		}
    }
    
    public function bkf_pm_metabox_callback($post){
		?>
		<div id="bkf_pm" name="bkf_pm">
			<p><select required class="bkf-form-control regular-text" form="bkf_pm" id="petals_msg_type" name="msgtype">
				<option value="" disabled selected><?php _e('Select a message type...', 'bakkbone-florist-companion'); ?></option>
				<option value="M"><?php _e("Message - non-complaint", "bakkbone-florist-companion"); ?></option>
				<option value="C"><?php _e("Complaint", "bakkbone-florist-companion"); ?></option>
				<option value="F"><?php _e("Final message (no response required)", "bakkbone-florist-companion"); ?></option>
				<option value="D"><?php _e("Mark order as delivered", "bakkbone-florist-companion"); ?></option>
			</select></p>
			<p><input type="text" required class="bkf-form-control large-text" form="bkf_pm" name="msg" id="petals_msg_body" placeholder="<?php _e("Message to Petals","bakkbone-florist-companion"); ?>" /></p>
			<p><button form="bkf_pm" class="button" onclick="ajaxPetalsMessage()"><?php _e("Send Message","bakkbone-florist-companion"); ?></button></p>
		</div>
		<script>
			function ajaxPetalsMessage( $ ) {
					var postNonce = "<?php echo wp_create_nonce("petalsmsg"); ?>";
					var orderId = "<?php echo $post->ID; ?>";
					var msgType = document.getElementById("petals_msg_type").value;
					var msgBody = document.getElementById("petals_msg_body").value;
					var postUrl = "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=petals_msg&nonce=' + postNonce + '&orderid=' + orderId + '&msgtype=' + msgType + '&msgbody=' + msgBody;
					if(msgType == '' || msgBody == ''){alert('<?php echo BKF_PETALS_MESSAGE_VALIDATION; ?>');}else{
					if(confirm('<?php echo BKF_PETALS_MESSAGE_SEND_PROMPT; ?>')){
						alert('<?php echo BKF_PETALS_MESSAGE_WAIT; ?>')
						jQuery.post(postUrl);
						setTimeout(ajaxPetalsMessageProceed,3000);
					};};
				}
				function ajaxPetalsMessageProceed( $ ) {
					document.getElementById("petals_msg_type").value = '';
					document.getElementById("petals_msg_body").value = '';
					alert('<?php _e("Message queued for sending - this page will now attempt to refresh to display the outcome in the Order Notes. An email will also be sent to the site admin.", "bakkbone-florist-companion"); ?>');
					location.reload();					
				}
		</script>
		<?php
    }
	
}