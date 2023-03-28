<?php
/**
 * @author BAKKBONE Australia
 * @package PetalsSingle
 * @license GNU General Public License (GPL) 3.0
 * Template Name: Petals Order
 * Template Post Type: bkf_petals_order
**/

$thispost = get_post();
$post_id = $thispost->ID;
$countries = BKF_ISO_COUNTRIES;
$sendid = get_post_meta($post_id, 'sendid', true);
$description = get_post_meta($post_id, 'description', true);
$productid = get_post_meta($post_id, 'productid', true);
$makeup = get_post_meta($post_id, 'makeup', true);
$upsell = get_post_meta($post_id, 'upsell', true);
$upsellamt = get_post_meta($post_id, 'upsellamt', true);
$tvalue = get_post_meta($post_id, 'tvalue', true);
$deldate = get_post_meta($post_id, 'deldate', true);
$deltime = get_post_meta($post_id, 'deltime', true);
$recipient = get_post_meta($post_id, 'recipient', true);
$surname = get_post_meta($post_id, 'surname', true);
$addresstype = get_post_meta($post_id, 'addresstype', true);
$address = get_post_meta($post_id, 'address', true);
$town = get_post_meta($post_id, 'town', true);
$state = get_post_meta($post_id, 'state', true);
$postalcode = get_post_meta($post_id, 'postalcode', true);
$crtycode = get_post_meta($post_id, 'crtycode', true);
$crtyname = get_post_meta($post_id, 'crtyname', true);
$phone = get_post_meta($post_id, 'phone', true);
$message = get_post_meta($post_id, 'message', true);
$occasion = get_post_meta($post_id, 'occasion', true);
$comments = get_post_meta($post_id, 'comments', true);
$supplier = get_post_meta($post_id, 'supplier', true);
$contact_name = get_post_meta($post_id, 'contact_name', true);
$contact_email = get_post_meta($post_id, 'contact_email', true);
$contact_phone = get_post_meta($post_id, 'contact_phone', true);

if( wp_is_block_theme() ) {
	?><!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div class="wp-site-blocks"><header><?php
	block_template_part('header');
	echo '</header>';
} else {
	get_header();
}

while ( have_posts() ) :
	if(current_user_can( 'read_petals_order')){
		echo '<main><h2>'.sprintf(BKF_ORDER_VIEW_TITLE, $sendid).'</h2><table class="bkf_po_table">';
		if($description !== '' && $description !== null){ echo '<tr><th>'.BKF_PO_FIELD_DESCRIPTION.'</th><td>'.$description.'</td></tr>'; }
		if($productid !== '' && $productid !== null){ echo '<tr><th>'.BKF_PO_FIELD_PRODUCTID.'</th><td>'.$productid.'</td></tr>'; }
		if($makeup !== '' && $makeup !== null){ echo '<tr><th>'.BKF_PO_FIELD_MAKEUP.'</th><td>'.$makeup.'</td></tr>'; }
		if($upsell !== '' && $upsell !== null){ echo '<tr><th>'.BKF_PO_FIELD_UPSELL.'</th><td>'.$upsell.'</td></tr>'; }
		if($upsellamt !== '' && $upsellamt !== null){ echo '<tr><th>'.BKF_PO_FIELD_UPSELLAMT.'</th><td>'.get_woocommerce_currency_symbol(get_woocommerce_currency()).$upsellamt.'</td></tr>'; }
		if($tvalue !== '' && $tvalue !== null){ echo '<tr><th>'.BKF_PO_FIELD_TVALUE.'</th><td>'.get_woocommerce_currency_symbol(get_woocommerce_currency()).$tvalue.'</td></tr>'; }
		if($deldate !== '' && $deldate !== null){ echo '<tr><th>'.BKF_PO_FIELD_DELDATE.'</th><td>'.$deldate.'</td></tr>'; }
		if($deltime !== '' && $deltime !== null){ echo '<tr><th>'.BKF_PO_FIELD_DELTIME.'</th><td>'.$deltime.'</td></tr>'; }
		if($recipient !== '' && $recipient !== null){ echo '<tr><th>'.BKF_PO_FIELD_RECIPIENT.'</th><td>'.$recipient.'</td></tr>'; }
		if($surname !== '' && $surname !== null){ echo '<tr><th>'.BKF_PO_FIELD_SURNAME.'</th><td>'.$surname.'</td></tr>'; }
		if($addresstype !== '' && $addresstype !== null){ echo '<tr><th>'.BKF_PO_FIELD_ADDRESSTYPE.'</th><td>'.$addresstype.'</td></tr>'; }
		if($address !== '' && $address !== null){ echo '<tr><th>'.BKF_PO_FIELD_ADDRESS.'</th><td>'.$address.'</td></tr>'; }
		if($town !== '' && $town !== null){ echo '<tr><th>'.BKF_PO_FIELD_TOWN.'</th><td>'.$town.'</td></tr>'; }
		if($state !== '' && $state !== null){ echo '<tr><th>'.BKF_PO_FIELD_STATE.'</th><td>'.$state.'</td></tr>'; }
		if($postalcode !== '' && $postalcode !== null){ echo '<tr><th>'.BKF_PO_FIELD_POSTALCODE.'</th><td>'.$postalcode.'</td></tr>'; }
		if($crtyname !== '' && $crtyname !== null){ echo '<tr><th>'.BKF_PO_FIELD_CRTYNAME.'</th><td>'.$crtyname.'</td></tr>'; }
		if($phone !== '' && $phone !== null){ echo '<tr><th>'.BKF_PO_FIELD_PHONE.'</th><td>'.$phone.'</td></tr>'; }
		if($message !== '' && $message !== null){ echo '<tr><th>'.BKF_PO_FIELD_MESSAGE.'</th><td>'.$message.'</td></tr>'; }
		if($occasion !== '' && $occasion !== null){ echo '<tr><th>'.BKF_PO_FIELD_OCCASION.'</th><td>'.$occasion.'</td></tr>'; }
		if($comments !== '' && $comments !== null){ echo '<tr><th>'.BKF_PO_FIELD_COMMENTS.'</th><td>'.$comments.'</td></tr>'; }
		if($supplier !== '' && $supplier !== null){ echo '<tr><th>'.BKF_PO_FIELD_SUPPLIER.'</th><td>'.$supplier.'</td></tr>'; }
		if($contact_name !== '' && $contact_name !== null){ echo '<tr><th>'.BKF_PO_FIELD_CONTACT_NAME.'</th><td>'.$contact_name.'</td></tr>'; }
		if($contact_email !== '' && $contact_email !== null){ echo '<tr><th>'.BKF_PO_FIELD_CONTACT_EMAIL.'</th><td>'.$contact_email.'</td></tr>'; }
		if($contact_phone !== '' && $contact_phone !== null){ echo '<tr><th>'.BKF_PO_FIELD_CONTACT_PHONE.'</th><td>'.$contact_phone.'</td></tr>'; }
		echo '</table>';
		$comments = get_comments(array('post_id' => $post_id));
		$commentscount = get_comments(array('post_id' => $post_id, 'count' => true));
		echo '<h3>'.__('Messages to/from Petals Network:', 'bakkbone-florist-companion').'</h3>';
		?>
		<div id="bkf_pm" name="bkf_pm">
			<p><div class="bkf-select" style="width:50%;"><select required form="bkf_pm" id="petals_msg_type" name="msgtype">
				<option value="" disabled selected>Select a message type...</option>
				<option value="M"><?php _e("Message - non-complaint", "bakkbone-florist-companion"); ?></option>
				<option value="C"><?php _e("Complaint", "bakkbone-florist-companion"); ?></option>
				<option value="F"><?php _e("Final message (no response required)", "bakkbone-florist-companion"); ?></option>
				<option value="D"><?php _e("Mark order as delivered", "bakkbone-florist-companion"); ?></option>
			</select></div></p>
			<p><input type="text" required class="bkf-form-control big" form="bkf_pm" name="msg" id="petals_msg_body" placeholder="<?php _e("Message to Petals","bakkbone-florist-companion"); ?>" /></p>
			<p><button form="bkf_pm" class="button wp-element-button" onclick="ajaxPetalsMessage()"><?php _e("Send Message","bakkbone-florist-companion"); ?></button></p>
		</div>
		<script>
			function ajaxPetalsMessage( $ ) {
					var postNonce = "<?php echo wp_create_nonce("petalsmsg"); ?>";
					var orderId = "<?php echo $post->ID; ?>";
					var msgType = document.getElementById("petals_msg_type").value;
					var msgBody = document.getElementById("petals_msg_body").value;
					var postUrl = "<?php echo admin_url('admin-ajax.php'); ?>" + '?action=petals_msg_frontend&nonce=' + postNonce + '&orderid=' + orderId + '&msgtype=' + msgType + '&msgbody=' + msgBody;
					if(msgType == '' || msgBody == ''){alert('<?php echo BKF_PETALS_MESSAGE_VALIDATION; ?>');}else{
					if(confirm('<?php echo BKF_PETALS_MESSAGE_SEND_PROMPT; ?>')){
						alert('<?php echo BKF_PETALS_MESSAGE_WAIT; ?>')
						jQuery.post(postUrl);
						setTimeout(ajaxPetalsMessageProceed,3000);
					}else{};};
				}
				function ajaxPetalsMessageProceed( $ ) {
					document.getElementById("petals_msg_type").value = '';
					document.getElementById("petals_msg_body").value = '';
					alert('<?php _e("Message queued for sending - this page will now attempt to refresh to display the outcome in the messages. An email will also be sent to the site admin.", "bakkbone-florist-companion"); ?>');
					location.reload();					
				}
		</script><?php
		if($commentscount !== 0){
			echo '<div id="petals_order_notes">';
			wp_list_comments(array('type'=>'petals_order_note','style'=>'div'),$comments);
			echo '</div>';
		}
	} else {
		echo '<main><h2>'.__('Not Authorized', 'bakkbone-florist-companion').'</h2><p>'.__('Sorry, you must be logged in as an authorized user to view this content.','bakkbone-florist-companion').'</p></main>';
	}
	the_post();
	get_template_part( 'template-parts/content/content-single' );
	echo '</main>';
endwhile;

if( wp_is_block_theme() ) {
    ?></div><footer>
	<?php wp_footer();
    block_template_part('footer');
	echo '</footer>';
} else {
	get_footer();
}