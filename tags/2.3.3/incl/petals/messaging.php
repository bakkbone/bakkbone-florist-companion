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
		  add_action('wp_ajax_petals_msg', array($this, 'bkf_petals_msg') ); 
      }else{};
    }
	
	
    function bkf_pm_metabox_init(){
	        add_meta_box('bkf_pm', __('Petals Messaging', 'bakkbone-florist-companion'),array($this, 'bkf_pm_metabox_callback'),'shop_order','advanced','low');
    }
    
    public function bkf_pm_metabox_callback($post){
		global $post;
		if(get_post_meta($post->ID,"_petals_on",true) !== ''){
		?>
		<div id="bkf_pm" name="bkf_pm">
			<p><select required class="bkf-form-control regular-text" form="bkf_pm" id="petals_msg_type" name="msgtype">
				<option value="" disabled selected>Select a message type...</option>
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
					if(msgType == '' || msgBody == ''){alert('<?php _e("Please fill in both fields before attempting to send a message.", "bakkbone-florist-companion"); ?>');}else{
					if(confirm('<?php _e("Send Message?", "bakkbone-florist-companion"); ?>')){
						alert('<?php _e("Please wait...", "bakkbone-florist-companion"); ?>')
						jQuery.post(postUrl);
						setTimeout(ajaxPetalsMessageProceed,3000);
					}else{};};
				}
				function ajaxPetalsMessageProceed( $ ) {
					document.getElementById("petals_msg_type").value = '';
					document.getElementById("petals_msg_body").value = '';
					alert('<?php _e("Message queued for sending - this page will now attempt to refresh to display the outcome in the Order Notes. An email will also be sent to the site admin.", "bakkbone-florist-companion"); ?>');
					location.reload();					
				}
		</script>
		<?php
	}else{_e("This metabox only applies for Petals Network orders, and will show a messaging form on relevant orders.","bakkbone-florist-companion");};
    }
	
	function bkf_petals_msg(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "petalsmsg")) {
          exit("No funny business please");
        }
		
		$msgtype = $_REQUEST['msgtype'];
		$msgbody = $_REQUEST['msgbody'];
		$orderid = $_REQUEST['orderid'];
		$order = new WC_Order($orderid);
        $mn         = get_option('bkf_petals_setting')['mn'];
        $password   = get_option('bkf_petals_setting')['ppw'];
        $pw         = base64_decode($password);        
        $petalsid   = get_post_meta($orderid,"_petals_on");
        //$url        = 'https://pin.petals.com.au/wconnect/wc.isa?pbo~&ctype=45'
        $url        = 'https://webhook.site/fcea56eb-ae95-40da-ac28-ea056612eb5a';
		
		$body = '<?xml version="1.0" encoding="UTF-8"?>
<message>
<member>'.$mn.'</member>
<password>'.$pw.'</password>
<petalsid>'.$petalsid.'</petalsid>
<recordtype>03</recordtype>
<type>'.$msgtype.'</type>
<notes>'.$msgbody.'</notes>
</message>';
		
        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'headers'   => array('Content-Type' => 'application/xml'),
            'body'      => $body
            ));
			
	        $rawxml = $response['body'];
	        $xml = simplexml_load_string($rawxml);
	        $symbol = '</strong>: ';
	        $xmlarray = json_decode(json_encode((array)$xml), TRUE);
	        $implosion = implode('<br><strong>', array_map(
	                    function($k, $v) use($symbol) { 
	                        return $k . $symbol . $v;
	                    }, 
	                    array_keys($xmlarray), 
	                    array_values($xmlarray)
	                    )
	                );
	        if($xml->type == '300'){
                $note = __('<strong>Message successfully sent to Petals: </strong><br>', 'bakkbone-florist-companion') . $msgbody . '<br><br><strong>Response from Petals:</strong> <br><strong>' . $implosion;
                $ordernote = $order->add_order_note($note);				
			}else{
                $note = __('<strong>Message FAILED TO SEND to Petals. Your message: </strong><br>', 'bakkbone-florist-companion') . $msgbody . '<br><br>Response from Petals: <br><strong>' . $implosion;
                $ordernote = $order->add_order_note($note);				
			}
            $wc_emails = WC()->mailer()->get_emails();
            $wc_emails['WC_Email_Petals_Note']->trigger( $order->get_id() );
			
		
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
	   }
	   else {        
	      header("Location: ".$_SERVER["HTTP_REFERER"]);
	   }
	   die();
		
	}

}