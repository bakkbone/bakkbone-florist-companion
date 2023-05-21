<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPho
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfPho{

    function __construct(){
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_bar_menu', array($this, 'admin_bar'), PHP_INT_MAX);
		add_action('admin_notices', array($this, 'admin_notice'));
	}
	
	function admin_notice(){
		global $pagenow;
		if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'new-phone_order'){
			?>
		    <div class="notice notice-bkf">
		        <p><?php _e( 'This form does not yet support fees configured in BKF settings (other than timeslot fees), this is planned for the next major release. Apologies for any inconvenience caused.', 'bakkbone-florist-companion' ); ?></p>
		    </div>
			<?php
		}
	}
	
	function admin_bar($bar){
		$phoargs = array(
			'id'	=> 'phone_order',
			'title'	=> __('Phone Order', 'bakkbone-florist-companion'),
			'parent'=> 'new-shop_order',
			'href'	=> add_query_arg('page', 'new-phone_order', admin_url('admin.php'))
		);
		$webargs = array(
			'id'	=> 'web_order',
			'title'	=> __('Web Order', 'bakkbone-florist-companion'),
			'parent'=> 'new-shop_order',
			'href'	=> add_query_arg('post_type', 'shop_order', admin_url('post-new.php'))
		);
		$bar->add_node($webargs);
		$bar->add_node($phoargs);
	}
	
	function admin_menu(){
		$admin_page = add_menu_page(
		__('New Phone Order', 'bakkbone-florist-companion'),
		__('New Phone Order', 'bakkbone-florist-companion'),
		'manage_woocommerce',
		'new-phone_order',
		array($this, 'phone_order_content'),
		'dashicons-plus',
		3
		);
		
		add_action( 'load-'.$admin_page, array($this, 'phone_order_help_tab') );
	}
	
	function phone_order_content(){
		$users = bkf_get_customers();
		
		$products = bkf_get_all_products();
		
		$wcc = new WC_Countries();
		$countries = $wcc->get_shipping_countries();
		$states = $wcc->get_shipping_country_states();
		$base = $wcc->get_base_country();
		$basestate = $wcc->get_base_state();
		?>
		<div class="bkf-loading-bg loading" style="display:none;"><img class="bkf-loading-spin" src="<?php echo BKF_URL . '/assets/img/spinner.svg'; ?>" /></div>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php _e("Phone Order","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form autocomplete="off" class="bkf-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
						<input type="hidden" name="action" value="bkf_phone_order" />
						<input type="hidden" name="virtualorder" id="virtualorder" />
						<fieldset>
							<legend><?php _e('Customer', 'bakkbone-florist-companion'); ?></legend>
							<table class="form-table" id="customer_form">
								<tbody>
									<tr>
										<th scope="row"><label for="customer_type"><?php _e('Customer Type', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><label class="bkf-radio-container"><input checked class="bkf-form-control" type="radio" name="customer_type" id="customer_type-existing" value="existing" /><span class="bkf-radio-checkmark"></span><?php _e('Existing Customer', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="customer_type" id="customer_type-new" value="new" /><span class="bkf-radio-checkmark"></span><?php _e('New Customer', 'bakkbone-florist-companion'); ?></label></td>
									</tr>
									<tr class="get_customer_form">
										<th scope="row"><label for="customer_id"><?php _e('Customer', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><select class="select2 bkf-form-control" name="customer_id" id="customer_id" style="width:100%;" required>
											<option value="" disabled selected><?php _e('Select a customer...', 'bakkbone-florist-companion'); ?></option>
											<?php
											foreach($users as $user){
												if($user['business']){
													$company = ' ('.$user['company'].')';
												} else {
													$company = '';
												}
												echo '<option value="'.$user['id'].'">#'.$user['id'].': '.$user['name'].$company.' - '.$user['email'].' - '.$user['phone'].'</option>';
											}
											?>
										</select></td>
									</tr>
									<tr class="create_customer_form" style="display:none;">
										<th scope="row"><label for="billing_first"><?php _e('Name', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input class="bkf-form-control regular-text" type="text" autocomplete="off" name="billing[first]" id="billing_first" placeholder="<?php _e('First Name', 'bakkbone-florist-companion'); ?>" /><input class="bkf-form-control regular-text" type="text" autocomplete="off" name="billing[last]" id="billing_last" placeholder="<?php _e('Last Name', 'bakkbone-florist-companion'); ?>" /></td>
									</tr>
									<tr class="create_customer_form" style="display:none;">
										<th scope="row"><label for="billing_company"><?php echo get_option('bkf_localisation_setting')['billing_label_business']; ?></label></th>
										<td><input class="bkf-form-control regular-text" type="text" autocomplete="off" name="billing[company]" id="billing_company" placeholder="<?php echo get_option('bkf_localisation_setting')['billing_label_business']; ?>" /></td>
									</tr>
									<tr class="create_customer_form" style="display:none;">
										<th scope="row"><label for="billing_email"><?php _e('Email', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input class="bkf-form-control regular-text" type="email" autocomplete="off" name="billing[email]" id="billing_email" placeholder="<?php _e('Email', 'bakkbone-florist-companion'); ?>" /></td>
									</tr>
									<tr class="create_customer_form" style="display:none;">
										<th scope="row"><label for="billing_phone"><?php echo get_option('bkf_localisation_setting')['global_label_telephone']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input class="bkf-form-control regular-text" type="tel" autocomplete="off" name="billing[phone]" id="billing_phone" placeholder="<?php echo get_option('bkf_localisation_setting')['global_label_telephone']; ?>" /></td>
									</tr>
									<tr class="create_customer_form">
										<th scope="row"><label for="create_customer"><?php _e('Create account for customer', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><label class="bkf-radio-container"><input checked class="bkf-form-control" type="radio" name="create_customer" id="create_customer-yes" value="1" checked /><span class="bkf-radio-checkmark"></span><?php _e('Yes', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="create_customer" id="create_customer-no" value="0" /><span class="bkf-radio-checkmark"></span><?php _e('No', 'bakkbone-florist-companion'); ?></label></td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<fieldset>
							<legend><?php _e('Product(s)', 'bakkbone-florist-companion'); ?></legend>
							<table class="form-table" id="products_form">
								<tbody>
									<tr id="p1">
										<th scope="row"><span class="dashicons dashicons-plus-alt add_button" style="color:black;cursor:pointer;"></span> <label><?php _e('Product', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><select class="select2 bkf-form-control product" name="product[p1][product]" required>
											<option value="" disabled selected><?php _e('Select a product...', 'bakkbone-florist-companion'); ?></option>
											<?php
											foreach($products as $product){
												if(! $product['has_child']){
													echo '<option value="'.$product['id'].'">'.$product['name'].' ('.$product['cat'].') — '.$product['price'].'</option>';
												}
											}
											?>
										</select>
										<div class="bkf-input-icon"><input required autocomplete="off" class="bkf-form-control price med-text" type="number" step="0.01" name="product[p1][value]" min="0.01" placeholder="<?php _e('Price', 'bakkbone-florist-companion'); ?>" /><i><?php echo bkf_currency_symbol(); ?></i></div><br><textarea style="margin-top:5px;" class="bkf-form-control regular-text" name="product[p1][notes]" placeholder="<?php _e('Notes (Optional)', 'bakkbone-florist-companion'); ?>" /></textarea><input type="hidden" name="product[p1][virtual]" class="virtual" /></td>
									</tr>
								</tbody>
							</table>
							<table class="form-table" id="products_summary">
								<tbody>
									<tr>
										<th><?php _e('Subtotal'); ?></th>
										<td><input id="subtotal_field" name="subtotal" type="hidden" value="0.00" /><p class="subtotal" id="subtotal_display"><?php echo bkf_currency_symbol(); ?>0.00</p></td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<?php if(bkf_shop_has_pickup()){ ?>
						<fieldset id="delivery_details" style="display:none">
							<legend><?php _e('Delivery/Collection Details', 'bakkbone-florist-companion'); ?></legend>
							<table class="form-table" id="method_form">
								<tbody>
									<tr>
										<th scope="row"><label for="method"><?php _e('Order Type', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="ordertype" id="method-delivery" value="delivery" /><span class="bkf-radio-checkmark"></span><?php _e('Delivery', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="ordertype" id="method-collection" value="collection" /><span class="bkf-radio-checkmark"></span><?php _e('Collection', 'bakkbone-florist-companion'); ?></label></td>
									</tr>
								</tbody>
							</table>
						<?php } else { ?>
						<fieldset id="delivery_details" style="display:none">
							<legend><?php _e('Delivery Details', 'bakkbone-florist-companion'); ?></legend>
							<input type="radio" style="display:none;" checked name="ordertype" value="delivery" />
							<?php } ?>
							<table class="form-table" id="delivery_form" style="display:none;">
								<tbody>
									<tr>
										<th scope="row"><label for="delivery_first"><?php _e('Recipient', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input autocomplete="off" type="text" name="delivery[first]" id="delivery_first" class="bkf-form-control regular-text" placeholder="<?php _e('First', 'bakkbone-florist-companion'); ?>" /><input autocomplete="off" type="text" name="delivery[last]" id="delivery_last" class="bkf-form-control regular-text" placeholder="<?php _e('Last', 'bakkbone-florist-companion'); ?>" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_company"><?php echo get_option('bkf_localisation_setting')['delivery_label_business']; ?></label></th>
										<td><input autocomplete="off" type="text" name="delivery[company]" id="delivery_company" class="bkf-form-control regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_address_1"><?php _e('Address Line 1', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input autocomplete="off" type="text" name="delivery[address_1]" id="delivery_address_1" class="bkf-form-control regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_address_2"><?php _e('Address Line 2', 'bakkbone-florist-companion'); ?></label></th>
										<td><input autocomplete="off" type="text" name="delivery[address_2]" id="delivery_address_2" class="bkf-form-control regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_city"><?php echo get_option('bkf_localisation_setting')['global_label_suburb']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input autocomplete="off" type="text" name="delivery[city]" id="delivery_city" class="bkf-form-control regular-text" /></td>
									</tr>
									<tr id="delivery_state_row">
										<th scope="row"><label for="delivery_state"><?php echo get_option('bkf_localisation_setting')['global_label_state']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><select name="delivery[state]" id="delivery_state" class="bkf-form-control regular-text delivery_state select2" /><option value="" disabled selected><?php echo sprintf(__('Select a %s...', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_state']); ?></option><?php foreach($states as $code => $array){
											if($code == $base){
												foreach($array as $statecode => $statename){
													$selected = $statecode == $basestate ? ' selected' : '';
													echo '<option value="'.$statecode.'"'.$selected.'>'.$statename.'</option>';
												}
											}
										} ?></select></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_postcode"><?php echo get_option('bkf_localisation_setting')['global_label_postcode']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input autocomplete="off" type="text" name="delivery[postcode]" id="delivery_postcode" class="bkf-form-control regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_country"><?php echo get_option('bkf_localisation_setting')['global_label_country']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><select name="delivery[country]" id="delivery_country" class="bkf-form-control regular-text delivery_country select2" /><option value="" disabled><?php echo sprintf(__('Select a %s...', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_country']); ?></option><?php foreach($countries as $code => $name){
											$selected = $code == $base ? ' selected' : '';
											echo '<option value="'.$code.'"'.$selected.'>'.$name.'</option>';
										} ?>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_phone"><?php echo get_option('bkf_localisation_setting')['global_label_telephone']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><input autocomplete="off" type="tel" name="delivery[phone]" id="delivery_phone" class="bkf-form-control regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="delivery_notes"><?php _e('Delivery Notes', 'bakkbone-florist-companion'); ?></label></th>
										<td><textarea autocomplete="off" name="delivery[notes]" id="delivery_notes" class="bkf-form-control regular-text"></textarea></td>
									</tr>
								</tbody>
							</table>
							<table class="form-table" id="delivery_method_form">
								<tbody>
									<tr>
										<th scope="row"><label><?php if(bkf_shop_has_pickup()){ _e('Delivery/Collection Method', 'bakkbone-florist-companion'); } else { _e('Delivery Method', 'bakkbone-florist-companion'); } ?> <abbr class="required" title="required">*</abbr></label></th>
										<td id="shipping_methods"><p><?php _e('No methods available until an order type is selected and/or a valid address entered.', 'bakkbone-florist-companion'); ?></p></td>
									</tr>
									<tr>
										<th scope="row"><label for="shipping_cost"><?php _e('Cost', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td id="shipping_cost_row"><div class="bkf-input-icon"><input type="number" step="0.01" name="shipping_cost" placeholder="<?php _e('Cost', 'bakkbone-florist-companion'); ?>" value="0.00" min="0.00" class="bkf-form-control med-text" id="shipping_cost" readonly /><i><?php echo bkf_currency_symbol(); ?></i></div></td>
									</tr>
								</tbody>
							</table>
							<table class="form-table" id="delivery_date_form">
								<tbody>
									<tr>
										<th scope="row"><?php echo get_option('bkf_ddi_setting')['ddt']; ?> <abbr class="required" title="required">*</abbr></label></th>
										<td>
											<?php
			$closeddates = get_option('bkf_dd_closed');
			$fulldates = get_option('bkf_dd_full');
			$maxdate = get_option('bkf_ddi_setting')['ddi'];
			$ddtitle = get_option('bkf_ddi_setting')['ddt'];

			$cats = array();

			$today = strtolower(wp_date("l"));

			$cb = bkf_get_catblocks();
			$catblocklist = array();
			foreach($cb as $thisblock){
				if(in_array($thisblock['category'],$cats)){
					$catblocklist[] = $thisblock['date'];
				}
			}
			
			$co = bkf_get_cutoffs();

			$sd_custom = array();
			foreach($co as $cos){
				if($cos['day'] == $today && $cos['cutoff'] <= wp_date("G:i")){
					$sd_custom[] = $cos['method'];
				}
			}

			if(get_option('bkf_dd_setting')[$today] == 1 && strtotime(get_option('bkf_sd_setting')[$today]) <= strtotime(wp_date("G:i"))){
				$sdcpassed = true;
			} else {
				$sdcpassed = false;
			}
			
			$bkfoptions = get_option("bkf_options_setting");
			$bkfcardlength = $bkfoptions["card_length"];

	        ?>
	    	<input type="text" readonly name="delivery_date" class="delivery_date bkf-form-control avg-text" id="delivery_date" placeholder="<?php echo $ddtitle; ?>" required autocomplete="off" />
 			 </td>
		 </tr>
			<tr class="delivery_timeslot_row bkf-hidden" id="delivery_timeslot_row">
				<th scope="row"><?php _e('Timeslot', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
				<td><select name="delivery_timeslot" id="delivery_timeslot" class="delivery_timeslot bkf-form-control select2"></select><div class="tsfee bkf-input-icon bkf-hidden"><input class="bkf-form-control med-text" type="number" name="timeslot_fee" id="timeslot_fee" step="0.01"><i><?php echo bkf_currency_symbol(); ?></i></div></td>
			</tr>
			<tr>
				<th scope="row"><label for="card_message"><?php _e('Card Message', 'bakkbone-florist-companion'); ?></label></th>
				<td><textarea style="font-family:monospace" rows="4" autocomplete="off" maxlength="<?php echo $bkfcardlength; ?>" name="card_message" id="card_message" class="bkf-form-control regular-text"></textarea></td>
			</tr>
		</tbody>
	</table>
			</fieldset>
			<fieldset>
				<legend><?php _e('Totals and Payment', 'bakkbone-florist-companion'); ?></legend>
							<table class="form-table" id="totals">
								<tbody>
									<tr>
										<th><?php _e('Total', 'bakkbone-florist-companion'); ?></th>
										<td><input id="total_field" name="total" type="hidden" value="0.00" /><p class="total" id="total_display"><?php echo bkf_currency_symbol(); ?>0.00</p></td>
									</tr>
									<tr>
										<th scope="row"><label for="payment"><?php _e('Payment', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="payment" required id="payment-paid" value="paid" /><span class="bkf-radio-checkmark"></span><?php _e('Mark as Paid', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="payment" required id="payment-invoice" value="invoice" checked /><span class="bkf-radio-checkmark"></span><?php _e('Send Invoice', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="payment" required id="payment-draft" value="draft" /><span class="bkf-radio-checkmark"></span><?php _e('Save as Draft', 'bakkbone-florist-companion'); ?></label></td>
									</tr>
								</tbody>
							</table>
						</fieldset>
			<fieldset>
							<table class="form-table" id="destination_options">
								<tbody>
									<tr>
										<th scope="row"><label for="destination"><?php _e('After Saving', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="destination" required id="destination-list" value="list" /><span class="bkf-radio-checkmark"></span><?php _e('Orders List', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="destination" required id="destination-edit" value="edit" checked /><span class="bkf-radio-checkmark"></span><?php _e('Manage Order', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="destination" required id="destination-new" value="new" /><span class="bkf-radio-checkmark"></span><?php _e('Another Order', 'bakkbone-florist-companion'); ?></label></td>
									</tr>
									<tr>
										<th scope="row"><label for="pdf"><?php _e('Download Worksheet?', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th>
										<td><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="pdf" required id="pdf-yes" value="1" checked /><span class="bkf-radio-checkmark"></span><?php _e('Yes', 'bakkbone-florist-companion'); ?></label><label class="bkf-radio-container"><input class="bkf-form-control" type="radio" name="pdf" required id="pdf-no" value="0" /><span class="bkf-radio-checkmark"></span><?php _e('No', 'bakkbone-florist-companion'); ?></label></td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<?php						
						do_action('bkf_phone_order_before_submit');
						submit_button(__('Create Order', 'bakkbone-florist-companion'));
						?>
					</form>
				</div>
			</div>
		</div>
   	    	<script id="bkf_ddfield">
   	    	    jQuery(document).ready(function( $ ) {
   	    	        jQuery(".delivery_date").datepicker( {
   	    	        	minDate: 0,
   	    	        	maxDate: "+<?php echo $maxdate; ?>w",
   	    	        	dateFormat: "DD, d MM yy",
   	    	        	hideIfNoPrevNext: true,
   	    	        	firstDay: 1,
   	    	        	constrainInput: true,
   						beforeShowDay: blockedDates,
   	    	        } );
   		   		 function blockedDates(date) {
   	  			 var closedDatesList = [<?php
   			 		$closeddates = get_option('bkf_dd_closed');
   					if( !empty($closeddates)){
   					 $i = 0;
   					 $len = count($closeddates);
   					 foreach($closeddates as $date){
   						 $ts = strtotime($date);
   						 $jsdate = wp_date('n,j,Y',$ts);
   						 if ($i == $len - 1) {
   						 echo '['.$jsdate.']';
   				 }else{
   						 echo '['.$jsdate.'],';
   						 }
   						 $i++;
   				 };}; ?>];
   	   			 var fullDatesList = [<?php
   			 		$fulldates = get_option('bkf_dd_full');
   					if( !empty($fulldates)){
   					 $i = 0;
   					 $len = count($fulldates);
   					 foreach($fulldates as $date){
   						 $ts = strtotime($date);
   						 $jsdate = wp_date('n,j,Y',$ts);
   						 if ($i == $len - 1) {
   						 echo '['.$jsdate.']';
   					 }else{
   						 echo '['.$jsdate.'],';
   						 }
   						 $i++;
   				 };}; ?>];
   	   			 var catBlockDatesList = [<?php
   					if( !empty($catblocklist)){
   					 $i = 0;
   					 $len = count($catblocklist);
   					 foreach($catblocklist as $date){
   						 $ts = strtotime($date);
   						 $jsdate = wp_date('n,j,Y',$ts);
   						 if ($i == $len - 1) {
   						 echo '['.$jsdate.']';
   					 }else{
   						 echo '['.$jsdate.'],';
   						 }
   						 $i++;
   				 };}; ?>];
   				 var dmSetting = [<?php
   					 $dmsetting = get_option('bkf_dm_setting');
   					 $i = 0;
   					 $len = count($dmsetting);
   					 foreach($dmsetting as $setting => $methods){
   						 if($i == $len - 1){
   							 echo '{ day: "'.$setting.'", methods: [';
   							 foreach($methods as $thismethod){
   								 echo $thismethod.',';
   							 }
   							 echo '] }';
   						 } else {
   							 echo '{ day: "'.$setting.'", methods: [';
   							 foreach($methods as $thismethod){
   								 echo '"'.$thismethod.'",';
   							 }
   							 echo '] },';
   						 }
   					 }
   				 ?>];

   				 var w = date.getDay();
   				 var m = date.getMonth();
   				 var d = date.getDate();
   				 var y = date.getFullYear();
   		         var ele = document.getElementsByName('shipping_method');
   		         for(i = 0; i < ele.length; i++) {
   		             if(ele[i].checked)
   		   			var currentShippingMethod = ele[i].value;
   		         }

   	 			<?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['monday'])){
   	 					echo 'var monday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['monday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 1 && monday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   	 			<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['tuesday'])){
   	 					echo 'var tuesday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['tuesday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 2 && tuesday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   	 			<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['wednesday'])){
   	 					echo 'var wednesday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['wednesday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 3 && wednesday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   	 			<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['thursday'])){
   	 					echo 'var thursday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['thursday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 4 && thursday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   	 			<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['friday'])){
   	 					echo 'var friday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['friday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 5 && friday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   	 			<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['saturday'])){
   	 					echo 'var saturday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['saturday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 6 && saturday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   	 			<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
   	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) { return [true, "closed", 'Closed']; }<?php } else {
   	 				if(isset(get_option('bkf_dm_setting')['sunday'])){
   	 					echo 'var sunday_items = [';
   	 					$setting = get_option('bkf_dm_setting')['sunday'];
   	 					foreach($setting as $s){
   	 						echo '"'.$s.'",';
   	 					}
   	 					echo '];';?>

   	 					if(w == 0 && sunday_items.includes(currentShippingMethod)){
   	 						return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   	 					}
   	 					<?php
   	 				}
   	 			}; ?>
   			 for (i = 0; i < closedDatesList.length; i++) {
   			   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
   			   {
   			   	 return [true, "closed","<?php _e('Closed', 'bakkbone-florist-companion'); ?>"];
   			   }
   			 }
   		     for (i = 0; i < fullDatesList.length; i++) {
   		       if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
   		       {
   		         return [true, "booked","<?php _e('Fully Booked', 'bakkbone-florist-companion'); ?>"];
   		       }
   		     }
   		     for (i = 0; i < catBlockDatesList.length; i++) {
   		       if ((m == catBlockDatesList[i][0] - 1) && (d == catBlockDatesList[i][1]) && (y == catBlockDatesList[i][2]))
   		       {
   		         return [true, "unavailable","<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>"];
   		       }
   		     }
   			 <?php if($sdcpassed == true){
   				 ?>
   				 if(d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
   					 return [true, "sdc","<?php _e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
   					 }<?php
   			 }?>
   			 var sdc = [<?php foreach($sd_custom as $this_sdc){ echo '"'.$this_sdc.'",';} ?>]
   			 if (sdc.includes(currentShippingMethod) && d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
   				 return [true, "sdc","<?php _e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
   			 }

   			 return [true];
   		 }
   		     } );
   			 </script>
   			 <script id="bkf_ddfield_refresh">
   			jQuery(document.body).on( 'change', 'input.shipping_method', function($) {
   	 	        jQuery(".delivery_date").datepicker( "option", {beforeShowDay: blockedDates2} );
   		   	 function blockedDates2(date) {
   			 var closedDatesList = [<?php
   		 		$closeddates = get_option('bkf_dd_closed');
   				if( !empty($closeddates)){
   				 $i = 0;
   				 $len = count($closeddates);
   				 foreach($closeddates as $date){
   					 $ts = strtotime($date);
   					 $jsdate = wp_date('n,j,Y',$ts);
   					 if ($i == $len - 1) {
   					 echo '['.$jsdate.']';
   			 }else{
   					 echo '['.$jsdate.'],';
   					 }
   					 $i++;
   			 };}; ?>];
   				 var fullDatesList = [<?php
   		 		$fulldates = get_option('bkf_dd_full');
   				if( !empty($fulldates)){
   				 $i = 0;
   				 $len = count($fulldates);
   				 foreach($fulldates as $date){
   					 $ts = strtotime($date);
   					 $jsdate = wp_date('n,j,Y',$ts);
   					 if ($i == $len - 1) {
   					 echo '['.$jsdate.']';
   				 }else{
   					 echo '['.$jsdate.'],';
   					 }
   					 $i++;
   			 };}; ?>];
   			 var catBlockDatesList = [<?php
   				if( !empty($catblocklist)){
   				 $i = 0;
   				 $len = count($catblocklist);
   				 foreach($catblocklist as $date){
   					 $ts = strtotime($date);
   					 $jsdate = wp_date('n,j,Y',$ts);
   					 if ($i == $len - 1) {
   					 echo '['.$jsdate.']';
   				 }else{
   					 echo '['.$jsdate.'],';
   					 }
   					 $i++;
   			 };}; ?>];
   			 var dmSetting = [<?php
   				 $dmsetting = get_option('bkf_dm_setting');
   				 $i = 0;
   				 $len = count($dmsetting);
   				 foreach($dmsetting as $setting => $methods){
   					 if($i == $len - 1){
   						 echo '{ day: "'.$setting.'", methods: [';
   						 foreach($methods as $thismethod){
   							 echo $thismethod.',';
   						 }
   						 echo '] }';
   					 } else {
   						 echo '{ day: "'.$setting.'", methods: [';
   						 foreach($methods as $thismethod){
   							 echo '"'.$thismethod.'",';
   						 }
   						 echo '] },';
   					 }
   				 }
   			 ?>];

   			 var w = date.getDay();
   			 var m = date.getMonth();
   			 var d = date.getDate();
   			 var y = date.getFullYear();
   	         var ele = document.getElementsByName('shipping_method');
   	         for(i = 0; i < ele.length; i++) {
   	             if(ele[i].checked)
   	   			var currentShippingMethod = ele[i].value;
   	         }

   				<?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['monday'])){
   						echo 'var monday_items = [';
   						$setting = get_option('bkf_dm_setting')['monday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 1 && monday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   				<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['tuesday'])){
   						echo 'var tuesday_items = [';
   						$setting = get_option('bkf_dm_setting')['tuesday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 2 && tuesday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   				<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['wednesday'])){
   						echo 'var wednesday_items = [';
   						$setting = get_option('bkf_dm_setting')['wednesday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 3 && wednesday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   				<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['thursday'])){
   						echo 'var thursday_items = [';
   						$setting = get_option('bkf_dm_setting')['thursday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 4 && thursday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   				<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['friday'])){
   						echo 'var friday_items = [';
   						$setting = get_option('bkf_dm_setting')['friday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 5 && friday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   				<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['saturday'])){
   						echo 'var saturday_items = [';
   						$setting = get_option('bkf_dm_setting')['saturday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 6 && saturday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   				<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
   					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) { return [true, "closed", 'Closed']; }<?php } else {
   					if(isset(get_option('bkf_dm_setting')['sunday'])){
   						echo 'var sunday_items = [';
   						$setting = get_option('bkf_dm_setting')['sunday'];
   						foreach($setting as $s){
   							echo '"'.$s.'",';
   						}
   						echo '];';?>

   						if(w == 0 && sunday_items.includes(currentShippingMethod)){
   							return [true, "unavailable", '<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>'];
   						}
   						<?php
   					}
   				}; ?>
   		 for (i = 0; i < closedDatesList.length; i++) {
   		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
   		   {
   		   	 return [true, "closed","<?php _e('Closed', 'bakkbone-florist-companion'); ?>"];
   		   }
   		 }
   	     for (i = 0; i < fullDatesList.length; i++) {
   	       if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
   	       {
   	         return [true, "booked","<?php _e('Fully Booked', 'bakkbone-florist-companion'); ?>"];
   	       }
   	     }
   	     for (i = 0; i < catBlockDatesList.length; i++) {
   	       if ((m == catBlockDatesList[i][0] - 1) && (d == catBlockDatesList[i][1]) && (y == catBlockDatesList[i][2]))
   	       {
   	         return [true, "unavailable","<?php _e('Unavailable', 'bakkbone-florist-companion'); ?>"];
   	       }
   	     }
   		 <?php if($sdcpassed == true){
   			 ?>
   			 if(d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
   				 return [true, "sdc","<?php _e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
   				 }<?php
   		 }?>
   		 var sdc = [<?php foreach($sd_custom as $this_sdc){ echo '"'.$this_sdc.'",';} ?>]
   		 if (sdc.includes(currentShippingMethod) && d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
   			 return [true, "sdc","<?php _e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
   		 }

   		 return [true];
   	 }
   		 } );
       	</script>
		<?php
		$ts = bkf_get_timeslots();
		$tsselect = __('Select a timeslot...', 'bakkbone-florist-companion');
		?>
		<script id="timeslot_script">
		jQuery(document.body).on( 'change', 'input[name="shipping_method"]:checked, #delivery_date, input[name="ordertype"]:checked', function($) {
			loading = jQuery('.loading');
			loading.fadeIn();
setTimeout(() =>			{
				var all_options = [<?php foreach($ts as $slot) {
				  echo '{text: "'.date("g:i a", strtotime($slot['start'])).' - '.date("g:i a", strtotime($slot['end'])).'", slot: "'.$slot['id'].'", method: "'.$slot['method'].'", day: "'.$slot['day'].'"},';
			  } ?>];
			      const select = document.querySelector('#delivery_timeslot');
				  jQuery(select).empty($);
				  let defaultOption = new Option('<?php echo $tsselect; ?>', '', true, true);
				  document.querySelector('#delivery_timeslot').add(defaultOption, 0);
				  document.querySelector('#delivery_timeslot').options[0].disabled = true;
				  all_options.forEach(newOption);
				  if(all_options.length == 0) {
				      const wrapper = document.querySelector('#delivery_timeslot_row');
				      jQuery(wrapper).addClass('bkf-hidden');
				      document.querySelector('#delivery_timeslot').removeAttribute('required');
				  }

				  function newOption(value)
				    {
	  				    const wrapper = document.querySelector('#delivery_timeslot_row');
			            var ele = document.getElementsByName('shipping_method');
			            if(ele.length == 1){
			                var currentShippingMethod = ele[0].value
			            } else {
				            for(i = 0; i < ele.length; i++) {
				                if(ele[i].checked)
									var currentShippingMethod = ele[i].value;
				            }
			            }

						var days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
						var aDate = document.querySelector('#delivery_date').value;
						var theDate = Date.parse(aDate);
						var theDateObject = new Date(theDate);
						var delDay = days[theDateObject.getDay() - 1];
						if( value.day == delDay && value.method == currentShippingMethod ){
							const select = document.querySelector('#delivery_timeslot');
							let newOption = new Option(value.text, value.slot);
							select.add(newOption, undefined);
						}

						if(document.querySelector('#delivery_timeslot').options.length > 1) {
							const wrapper = document.querySelector('#delivery_timeslot_row');
							jQuery(wrapper).removeClass('bkf-hidden');
							document.querySelector('#delivery_timeslot').setAttribute('required', '');
						} else {
							const wrapper = document.querySelector('#delivery_timeslot_row');
							jQuery(wrapper).addClass('bkf-hidden');
							document.querySelector('#delivery_timeslot').removeAttribute('required');
						}
				    }
				}
, 1000);
loading.fadeOut();

		});
		jQuery(document.body).on( 'change', '#delivery_timeslot', function($) {
			loading = jQuery('.loading');
			loading.fadeIn();
			tsFeeWrapper = jQuery('.tsfee');
			tsFee = jQuery('#timeslot_fee');
			currentTs = jQuery('#delivery_timeslot').val();
			tsUrl = '<?php echo add_query_arg('action', 'bkf_get_fee_for_timeslot', admin_url('admin-ajax.php')); ?>' + '&ts=' + currentTs;
			jQuery.ajax({url: tsUrl, type: 'GET', success: function(result){
				if(result !== '0.00'){
					tsFee.val(result);
					tsFeeWrapper.removeClass('bkf-hidden');
					document.querySelector('#timeslot_fee').setAttribute('required', '');
				} else {
					tsFee.val('');
					tsFeeWrapper.addClass('bkf-hidden');
					document.querySelector('#timeslot_fee').removeAttribute('required');
				}
			    var arr = document.querySelectorAll('.price');
			    var tot=0;
			    for(var i=0;i<arr.length;i++){
			        if(parseFloat(arr[i].value))
			            tot += parseFloat(arr[i].value);
			    }
			
				field = document.querySelector('#subtotal_field');
				display = document.querySelector('#subtotal_display');
			
				field.value = tot.toFixed(2);
				display.innerText = "<?php echo bkf_currency_symbol(); ?>" + tot.toFixed(2);
				
				shippingCost = Number(jQuery('#shipping_cost').val());
				timeslotFee = Number(jQuery('#timeslot_fee').val());
				total = shippingCost + tot + timeslotFee;
				totalDisplayAmt = total.toFixed(2);
				
				totalField = document.querySelector('#total_field');
				totalDisplay = document.querySelector('#total_display');
				
				totalField.value = totalDisplayAmt;
				totalDisplay.innerText = "<?php echo bkf_currency_symbol(); ?>" + totalDisplayAmt;
				loading.fadeOut();
			}});
			
		});
		</script>
		<script type="text/javascript" id="method_script">
			jQuery(document.body).on( 'change', 'input[name="ordertype"]', function($) {
				currentType = jQuery('input[name="ordertype"]:checked').val();
				requiredDeliveryFields = [ jQuery('input[name="delivery[first]"]'), jQuery('input[name="delivery[last]"]'), jQuery('input[name="delivery[address_1]"]'), jQuery('input[name="delivery[phone]"]'), jQuery('input[name="delivery[city]"]'), jQuery('input[name="delivery[postcode]"]'), jQuery('input[name="delivery[state]"]'), jQuery('input[name="delivery[country]"]') ];
				const deliveryForm = jQuery('#delivery_form');
				if(currentType == 'collection'){
					requiredDeliveryFields.forEach(function(field){
						field.removeAttr('required');
					});
					deliveryForm.fadeOut();
				} else if(currentType == 'delivery'){
					requiredDeliveryFields.forEach(function(field){
						field.attr('required',true);
					});
					deliveryForm.fadeIn();
				}
			});
		</script>
		<script type="text/javascript" id="state_field_script">
			jQuery(document.body).on( 'change', 'select.delivery_country', function($) {
				var all_options = [<?php foreach($states as $state_country => $state) {
					echo '{country: "'.$state_country.'", states: [';
						foreach($state as $code => $name){
							echo '{code: "'.$code.'", name: "'.$name.'"},';
						}
					echo ']},';
			  } ?>];
			      const select = document.querySelector('#delivery_state');
				  jQuery(select).empty($);
				  let defaultOption = new Option("<?php echo sprintf(__('Select a %s...', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_state']); ?>", '', true, true);
				  document.querySelector('#delivery_state').add(defaultOption, 0);
				  document.querySelector('#delivery_state').options[0].disabled = true;
				  all_options.forEach(newOption);
				  if(all_options.length == 0) {
				      const wrapper = document.querySelector('#delivery_state_row');
				      jQuery(wrapper).addClass('bkf-hidden');
				      document.querySelector('#delivery_state').removeAttribute('required');
				  }

				  function newOption(value)
				    {
						currentCountry = document.getElementById('delivery_country').value;
						if(value.country == currentCountry) {
							value.states.forEach(function(state){
								const select = document.querySelector('#delivery_state');
								let newOption = new Option(state.name, state.code);
								select.add(newOption, undefined);
							});
						}


						if(document.querySelector('#delivery_state').options.length > 1) {
							const wrapper = document.querySelector('#delivery_state_row');
							jQuery(wrapper).removeClass('bkf-hidden');
							document.querySelector('#delivery_state').setAttribute('required', '');
						    jQuery('.select2').select2();
						} else {
							const wrapper = document.querySelector('#delivery_state_row');
							jQuery(wrapper).addClass('bkf-hidden');
							document.querySelector('#delivery_state').removeAttribute('required');
						}
				    }
			});
		</script>
		<script type="text/javascript" id="product_field_script">
			jQuery(document).ready( function($) {
			    var wrapper = jQuery('#products_form');
			    var addButton = jQuery('.add_button');
			    var x = 1;
    
			    jQuery(addButton).click(function(){
		            x++;
				    fieldHTML = '<tr id="p' + x + '" style="display:none;"><th scope="row"><span class="dashicons dashicons-dismiss remove_button" style="color:black;cursor:pointer;"></span> <label><?php _e('Product', 'bakkbone-florist-companion'); ?> <abbr class="required" title="required">*</abbr></label></th><td><select class="select2 bkf-form-control product" name="product[p' + x + '][product]" required><option value="" disabled selected><?php _e('Select a product...', 'bakkbone-florist-companion'); ?></option><?php foreach($products as $product){ if(! $product['has_child']){ echo '<option value="'.$product['id'].'">'.$product['name'].' ('.$product['cat'].') — '.$product['price'].'</option>'; } } ?> </select> <div class="bkf-input-icon"><input required class="bkf-form-control price med-text" type="number" step="0.01" name="product[p' + x + '][value]" min="0.01" placeholder="<?php _e('Price', 'bakkbone-florist-companion'); ?>" /><i><?php echo bkf_currency_symbol(); ?></i></div><br><textarea style="margin-top:5px;" class="bkf-form-control regular-text" name="product[p' + x + '][notes]" placeholder="<?php _e('Notes (Optional)', 'bakkbone-florist-companion'); ?>" /></textarea><input type="hidden" name="product[p' + x + '][virtual]" class="virtual" /></td></tr>';
		            jQuery(wrapper).append(fieldHTML);
					field = jQuery('#p' + x);
					field.fadeIn('slow');
				    jQuery('.select2').select2();
					
					jQuery('select.product').change(function(){
						id = jQuery(this).val();
						url = '<?php echo add_query_arg('action', 'product_value', admin_url('admin-ajax.php')); ?>' + '&id=' + id;
						priceDiv = jQuery(this).siblings('.bkf-input-icon');
						price = priceDiv.children('.price');
						virtual = jQuery(this).siblings('.virtual');
						jQuery('.loading').fadeIn();
						jQuery.ajax({url: url, type: 'POST', success: function(result){
							values = JSON.parse(result);
							newValue = values[0];
							newVirtual = values[1];							
							price.val(newValue);
							virtual.val(newVirtual);
							virtualList = jQuery( ".virtual" ).map(function() {
								return this.value;
							}).get();
							if (virtualList.includes('0')){
								deliveryFields = jQuery('#delivery_details');
								deliveryFields.show();
								jQuery('#virtualorder').val('false');
								jQuery('input[name="ordertype"]').prop('required', true);
							} else {
								deliveryFields = jQuery('#delivery_details');
								deliveryFields.hide();
								jQuery('#virtualorder').val('true');
								jQuery('input[name="ordertype"]').prop('required', false);
								requiredDeliveryFields = [ jQuery('input[name="delivery[first]"]'), jQuery('input[name="delivery[last]"]'), jQuery('input[name="delivery[address_1]"]'), jQuery('input[name="delivery[phone]"]'), jQuery('input[name="delivery[city]"]'), jQuery('input[name="delivery[postcode]"]'), jQuery('input[name="delivery[state]"]'), jQuery('input[name="delivery[country]"]') ];
								requiredDeliveryFields.forEach(function(field){
									field.prop('required', false);
								});
							}
							jQuery('.loading').fadeOut();
						    var arr = document.querySelectorAll('.price');
						    var tot=0;
						    for(var i=0;i<arr.length;i++){
						        if(parseFloat(arr[i].value))
						            tot += parseFloat(arr[i].value);
						    }
						
							field = document.querySelector('#subtotal_field');
							display = document.querySelector('#subtotal_display');
						
							field.value = tot.toFixed(2);
							display.innerText = "<?php echo bkf_currency_symbol(); ?>" + tot.toFixed(2);

							shippingCost = Number(jQuery('#shipping_cost').val());
							timeslotFee = Number(jQuery('#timeslot_fee').val());
							total = shippingCost + tot + timeslotFee;
							totalDisplayAmt = total.toFixed(2);
						
							totalField = document.querySelector('#total_field');
							totalDisplay = document.querySelector('#total_display');
						
							totalField.value = totalDisplayAmt;
							totalDisplay.innerText = "<?php echo bkf_currency_symbol(); ?>" + totalDisplayAmt;
						}});
					});
					
					jQuery('.price,#shipping_cost,#delivery_date,#timeslot_fee').change(function(){
					    var arr = document.querySelectorAll('.price');
					    var tot=0;
					    for(var i=0;i<arr.length;i++){
					        if(parseFloat(arr[i].value))
					            tot += parseFloat(arr[i].value);
					    }
					
						field = document.querySelector('#subtotal_field');
						display = document.querySelector('#subtotal_display');
					
						field.value = tot.toFixed(2);
						display.innerText = "<?php echo bkf_currency_symbol(); ?>" + tot.toFixed(2);
						
						shippingCost = Number(jQuery('#shipping_cost').val());
						timeslotFee = Number(jQuery('#timeslot_fee').val());
						total = shippingCost + tot + timeslotFee;
						totalDisplayAmt = total.toFixed(2);
						
						totalField = document.querySelector('#total_field');
						totalDisplay = document.querySelector('#total_display');
						
						totalField.value = totalDisplayAmt;
						totalDisplay.innerText = "<?php echo bkf_currency_symbol(); ?>" + totalDisplayAmt;
					});
					
			    });
				
				jQuery('select.product').change(function(){
					id = jQuery(this).val();
					url = '<?php echo add_query_arg('action', 'product_value', admin_url('admin-ajax.php')); ?>' + '&id=' + id;
					priceDiv = jQuery(this).siblings('.bkf-input-icon');
					price = priceDiv.children('.price');
					virtual = jQuery(this).siblings('.virtual');
					jQuery('.loading').fadeIn();
					jQuery.ajax({url: url, type: 'POST', success: function(result){
						values = JSON.parse(result);
						newValue = values[0];
						newVirtual = values[1];
						price.val(newValue);
						virtual.val(newVirtual);
						virtualList = jQuery( ".virtual" ).map(function() {
							return this.value;
						}).get();
						if (virtualList.includes('0')){
							deliveryFields = jQuery('#delivery_details');
							deliveryFields.show();
							jQuery('#virtualorder').val('false');
							jQuery('input[name="ordertype"]').prop('required', true);
						} else {
							deliveryFields = jQuery('#delivery_details');
							deliveryFields.hide();
							jQuery('#virtualorder').val('true');
							jQuery('input[name="ordertype"]').prop('required', false);
							requiredDeliveryFields = [ jQuery('input[name="delivery[first]"]'), jQuery('input[name="delivery[last]"]'), jQuery('input[name="delivery[address_1]"]'), jQuery('input[name="delivery[phone]"]'), jQuery('input[name="delivery[city]"]'), jQuery('input[name="delivery[postcode]"]'), jQuery('input[name="delivery[state]"]'), jQuery('input[name="delivery[country]"]') ];
							requiredDeliveryFields.forEach(function(field){
								field.prop('required', false);
							});
						}
						jQuery('.loading').fadeOut();
					    var arr = document.querySelectorAll('.price');
					    var tot=0;
					    for(var i=0;i<arr.length;i++){
					        if(parseFloat(arr[i].value))
					            tot += parseFloat(arr[i].value);
					    }
					
						field = document.querySelector('#subtotal_field');
						display = document.querySelector('#subtotal_display');
					
						field.value = tot.toFixed(2);
						display.innerText = "<?php echo bkf_currency_symbol(); ?>" + tot.toFixed(2);
						
						shippingCost = Number(jQuery('#shipping_cost').val());
						timeslotFee = Number(jQuery('#timeslot_fee').val());
						total = shippingCost + tot + timeslotFee;
						totalDisplayAmt = total.toFixed(2);
						
						totalField = document.querySelector('#total_field');
						totalDisplay = document.querySelector('#total_display');
						
						totalField.value = totalDisplayAmt;
						totalDisplay.innerText = "<?php echo bkf_currency_symbol(); ?>" + totalDisplayAmt;
					}});
					
					jQuery('.price,#shipping_cost,#delivery_date,#timeslot_fee').change(function(){
					    var arr = document.querySelectorAll('.price');
					    var tot=0;
					    for(var i=0;i<arr.length;i++){
					        if(parseFloat(arr[i].value))
					            tot += parseFloat(arr[i].value);
					    }
					
						field = document.querySelector('#subtotal_field');
						display = document.querySelector('#subtotal_display');
					
						field.value = tot.toFixed(2);
						display.innerText = "<?php echo bkf_currency_symbol(); ?>" + tot.toFixed(2);
						
						shippingCost = Number(jQuery('#shipping_cost').val());
						timeslotFee = Number(jQuery('#timeslot_fee').val());
						total = shippingCost + tot + timeslotFee;
						totalDisplayAmt = total.toFixed(2);
						
						totalField = document.querySelector('#total_field');
						totalDisplay = document.querySelector('#total_display');
						
						totalField.value = totalDisplayAmt;
						totalDisplay.innerText = "<?php echo bkf_currency_symbol(); ?>" + totalDisplayAmt;
					});
								
				});
			});
			jQuery(document).ready( function($){
			    var wrapper = jQuery('#products_form');
			    var removeButton = jQuery('.remove_button');
			    jQuery(wrapper).on('click', '.remove_button', function(e){
			        jQuery(this).parents('tr').remove();
			    });
			});
		</script>
		<script type="text/javascript" id="customer_form_script">
			jQuery(document).ready(function( $ ) {
			    jQuery('.select2').select2();
		        jQuery('input[name="customer_type"]').change(function(){
					customerType = jQuery("input[name='customer_type']:checked").val();
					var newRequiredFields = [jQuery("#billing_first"), jQuery("#billing_last"), jQuery("#billing_email"), jQuery("#billing_phone")];
					var existingRequiredFields = [jQuery("#customer_id")];
					if(customerType == 'new'){
						jQuery('.create_customer_form').delay('400').fadeIn();
						newRequiredFields.forEach(function(field){
							field.attr('required',true);
						});
					} else {
						jQuery('.create_customer_form').fadeOut('400');
						newRequiredFields.forEach(function(field){
							field.removeAttr('required');
						});
					}
					if(customerType == 'existing'){
						jQuery('.get_customer_form').delay('400').fadeIn();
						existingRequiredFields.forEach(function(field){
							field.attr('required',true);
						});
					} else {
						jQuery('.get_customer_form').fadeOut('400');
						existingRequiredFields.forEach(function(field){
							field.removeAttr('required');
						});
					}
				});
			});
		</script>
		<script type="text/javascript" id="check_suburb">
			jQuery(document.body).on( 'change', '#delivery_city, #delivery_state, #delivery_country, #delivery_postcode, #delivery_date, input[name="ordertype"]:checked', function($) {
				cityField = jQuery('#delivery_city');
				stateField = jQuery('#delivery_state');
				countryField = jQuery('#delivery_country');
				postcodeField = jQuery('#delivery_postcode');
				ddField = jQuery('#delivery_date');
				methodsCell = jQuery('#shipping_methods');
				loading = jQuery('.loading');

				city = cityField.val();
				state = stateField.val();
				country = countryField.val();
				postcode = postcodeField.val();
				dd = ddField.val();
				
				currentType = jQuery('input[name="ordertype"]:checked').val();
				
				if(currentType == 'delivery' && cityField.val() !== '' && stateField.val() !== '' && countryField.val() !== '' && postcodeField.val() !== ''){
					methodsCell.empty();
					suburbUrl = "<?php echo admin_url('admin-ajax.php?action=bkf_delivery_rates'); ?>" + '&city=' + city + '&state=' + state + '&postcode=' + postcode + '&country=' + country;
					loading.fadeIn();
					jQuery.ajax({url: suburbUrl, type: 'GET', success: function(result){
						var first = true;
						availableRates = JSON.parse(result);
						availableRates.forEach(function(rate){
									if(first){
										tempCost = Number(rate.cost);
										currentCost = tempCost.toFixed(2);
										shippingCost = jQuery('#shipping_cost');
										shippingCost.val(currentCost);
										shippingCost.prop('readonly', false);
										HTML = '<label class="bkf-radio-container shipping_method"><input class="bkf-form-control" type="radio" name="shipping_method" required checked value="' + rate.rateid + '" /><span class="bkf-radio-checkmark"></span>' + rate.usertitle + ' (#' + rate.instanceid + ') - <?php echo bkf_currency_symbol(); ?>' + currentCost + '</label>';
										first = false;
									} else {
										tempCost = Number(rate.cost);
										thisCost = tempCost.toFixed(2);
										HTML = '<label class="bkf-radio-container shipping_method"><input class="bkf-form-control" type="radio" name="shipping_method" required value="' + rate.rateid + '" /><span class="bkf-radio-checkmark"></span>' + rate.usertitle + ' (#' + rate.instanceid + ') - <?php echo bkf_currency_symbol(); ?>' + thisCost + '</label>';
									}
									methodsCell.append(HTML);
						});
						loading.fadeOut();
					}});
				} else if(currentType == 'delivery'){
					methodsCell.empty();
					HTML = '<p><?php _e('No methods available until an order type is selected and/or a valid address entered.', 'bakkbone-florist-companion'); ?></p>';
					methodsCell.append(HTML);
					shippingCost = jQuery('#shipping_cost');
					shippingCost.prop('readonly', true);
				} else if(currentType == 'collection') {
					methodsCell.empty();
					pickupUrl = "<?php echo admin_url('admin-ajax.php?action=bkf_pickup_rates'); ?>";
					loading.fadeIn();
					jQuery.ajax({url: pickupUrl, type: 'GET', success: function(result){
						var first = true;
						availableRates = JSON.parse(result);
						availableRates.forEach(function(rate){
							if(first){
								tempCost = Number(rate.cost);
								currentCost = tempCost.toFixed(2);
								shippingCost = jQuery('#shipping_cost');
								shippingCost.val(currentCost);
								shippingCost.prop('readonly', false);
								HTML = '<label class="bkf-radio-container shipping_method"><input class="bkf-form-control" type="radio" name="shipping_method" required checked value="' + rate.rateid + '" /><span class="bkf-radio-checkmark"></span>' + rate.usertitle + ' (#' + rate.instanceid + ') - <?php echo bkf_currency_symbol(); ?>' + currentCost + '</label>';
								first = false;
							} else {
								tempCost = Number(rate.cost);
								thisCost = tempCost.toFixed(2);
								HTML = '<label class="bkf-radio-container shipping_method"><input class="bkf-form-control" type="radio" name="shipping_method" required value="' + rate.rateid + '" /><span class="bkf-radio-checkmark"></span>' + rate.usertitle + ' (#' + rate.instanceid + ') - <?php echo bkf_currency_symbol(); ?>' + thisCost + '</label>';
							}
							methodsCell.append(HTML);
						});
						loading.fadeOut();
					}});
				} else {
					methodsCell.empty();
					HTML = '<p><?php _e('No methods available until an order type is selected and/or a valid address entered.', 'bakkbone-florist-companion'); ?></p>';
					methodsCell.append(HTML);
				}
			});
		</script>
		<script type="text/javascript" id="shipping_cost_script">			
			jQuery(document.body).on( 'change', 'input[name="shipping_method"]', function($) {
				loading = jQuery('.loading');
				loading.fadeIn();
				currentMethod = jQuery('input[name="shipping_method"]:checked').val();
				methodCostUrl = "<?php echo admin_url('admin-ajax.php?action=bkf_method_cost'); ?>" + '&method=' + currentMethod;
				jQuery.ajax({
					url: methodCostUrl, type: 'GET', success: function(result){
						tempCost = Number(result);
						thisCost = tempCost.toFixed(2);
						shippingCost = jQuery('#shipping_cost');
						shippingCost.val(thisCost);
						shippingCost.prop('readonly', false);
						loading.fadeOut();
					}
				});
			});
		</script>
		<?php
	}
	
	function phone_order_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_phone_order_help';
		$callback = array($this, 'bkf_phone_order_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );
	}
	
	function bkf_phone_order_help(){
		?>
		<h2><?php _e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/day-to-day/phone-orders/" target="_blank">https://plugins.bkbn.au/docs/bkf/day-to-day/phone-orders/</a>
		<?php
	}
	
}