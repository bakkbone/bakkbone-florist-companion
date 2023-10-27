<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Core
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class BKF_Delivery_Date_Core {

	function __construct(){
		add_action('woocommerce_review_order_before_payment', [$this,'dd_field_init'], 10 );
		add_action('woocommerce_checkout_update_order_meta', [$this, 'dd_order'], 10, 1);
		add_filter('woocommerce_email_order_meta_fields', [$this, 'dd_to_email'], 10, 3 );
		add_filter('woocommerce_order_details_before_order_table', [$this, 'dd_thankyou'], 10 , 1 );
		add_action('woocommerce_after_checkout_validation', [$this, 'dd_checkout_validation'], 10, 2 );
		add_action('wp_footer', [$this, 'dd_checkout_validation_js']);
		add_filter('manage_edit-shop_order_columns', [$this, 'dd_col_init'], 10, 1 );
		add_filter('manage_edit-shop_order_sortable_columns', [$this, 'dd_col_sort'], 10, 1 );
		add_filter('manage_woocommerce_page_wc-orders_columns', [$this, 'dd_col_init'], 10, 1 );
		add_filter('manage_woocommerce_page_wc-orders_sortable_columns', [$this, 'dd_col_sort'], 10, 1 );
		add_action('pre_get_posts', [$this, 'dd_filter_legacy'] );
		add_action('woocommerce_order_query_args', [$this, 'dd_filter']);
		add_action('manage_shop_order_posts_custom_column', [$this, 'dd_col'], 10, 2 );
		add_action('woocommerce_shop_order_list_table_custom_column', [$this, 'dd_col'], 10, 2 );
		add_filter('woocommerce_checkout_required_field_notice', [$this, 'ship_deliver'],10,3);
		add_action('woocommerce_before_checkout_shipping_form', [$this, 'delivery_details'], 20);
		add_filter('woocommerce_order_data_store_cpt_get_orders_query', [$this, 'handle_custom_query_var'], PHP_INT_MAX, 2 );
	}

	function delivery_details(){
		echo '<h3>'.esc_html__('Delivery details', 'bakkbone-florist-companion').'</h3>';
	}

	function ship_deliver($sprintf, $label, $key){
		$newlabel = __('Delivery Recipient', 'bakkbone-florist-companion');
		$label = str_replace('Shipping',$newlabel,$label);
		$sprintf = sprintf( __( '%s is a required field.', 'bakkbone-florist-companion' ), '<strong>' . esc_html( $label ) . '</strong>' );
		return $sprintf;
	}

	function dd_field_init () {
		if( bkf_cart_has_physical() ) {
			$closeddates = get_option('bkf_dd_closed');
			$fulldates = get_option('bkf_dd_full');
			$maxdate = get_option('bkf_ddi_setting')['ddi'];
			$ddtitle = get_option('bkf_ddi_setting')['ddt'];

			$cart = WC()->cart->get_cart();
			$cats = [];
			foreach($cart as $k => $v){
				$cat = wc_get_product_cat_ids($v['product_id']);
				foreach($cat as $c){
					$cats[] = $c;
				}
			}

			$today = strtolower(wp_date("l"));

			$cb = [];
			global $wpdb;
			$catblocks = $wpdb->get_results(
				"
					SELECT id, category, date
					FROM {$wpdb->prefix}bkf_dd_catblocks
				"
			);
			foreach($catblocks as $catblock){
				$cb[] = array(
					'id'		=>	$catblock->id,
					'category'	=>	$catblock->category,
					'date'		=>	$catblock->date
				);
			}
			$catblocklist = [];
			foreach($cb as $thisblock){
				if(in_array($thisblock['category'],$cats)){
					$catblocklist[] = $thisblock['date'];
				}
			}
			$co = bkf_get_cutoffs();
			$sd_custom = [];
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

			?><div class="bkf_dd_fields"><h3 class="bkf_dd_title"><?php echo $ddtitle; ?></h3>
			<p class="form-row form-row-wide form-group validate-required validate-delivery_date" id="delivery_date_field"><label for="delivery_date">
			<?php
			esc_html_e( "We'll schedule your order for: ", "bakkbone-florist-companion");
			?>
			<abbr class="required" title="required">*</abbr></label>
			<input type="text" name="delivery_date" class="delivery_date input-text form-control" id="delivery_date" placeholder="<?php echo $ddtitle; ?>" required autocomplete="off" />
			<script id="bkf_ddfield">
				jQuery(document).ready(function( $ ) {
					jQuery("#delivery_date").attr( 'readOnly' , 'true' );
					jQuery(".delivery_date").datepicker( {
						minDate: 0,
						maxDate: "+<?php echo $maxdate; ?>w",
						dateFormat: "DD, d MM yy",
						hideIfNoPrevNext: true,
						firstDay: 1,
						constrainInput: true,
						beforeShowDay: blockedDates
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
				 } else {
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
					 } else {
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
					 } else {
						 echo '['.$jsdate.'],';
						 }
						 $i++;
				 };}; ?>];
				 var dmSetting = [<?php
					 $dmsetting = get_option('bkf_dm_setting') !== '' ? get_option('bkf_dm_setting') : [] ;
					 $i = 0;
					 $len = count($dmsetting);
					 foreach($dmsetting as $setting => $methods){
						 if($i == $len - 1){
							 echo '{ day: "'.$setting.'", methods: [';
							 foreach($methods as $thismethod){
								 echo '"'.$thismethod.'",';
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
				 var ele = document.getElementsByName('shipping_method[0]');
				 for(i = 0; i < ele.length; i++) {
					 if(ele[i].checked)
		   			var currentShippingMethod = ele[i].value;
				 }

	 			<?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['monday'])){
	 					echo 'var monday_items = [';
	 					$setting = get_option('bkf_dm_setting')['monday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 1 && monday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
	 			<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['tuesday'])){
	 					echo 'var tuesday_items = [';
	 					$setting = get_option('bkf_dm_setting')['tuesday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 2 && tuesday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
	 			<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['wednesday'])){
	 					echo 'var wednesday_items = [';
	 					$setting = get_option('bkf_dm_setting')['wednesday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 3 && wednesday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
	 			<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['thursday'])){
	 					echo 'var thursday_items = [';
	 					$setting = get_option('bkf_dm_setting')['thursday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 4 && thursday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
	 			<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['friday'])){
	 					echo 'var friday_items = [';
	 					$setting = get_option('bkf_dm_setting')['friday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 5 && friday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
	 			<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['saturday'])){
	 					echo 'var saturday_items = [';
	 					$setting = get_option('bkf_dm_setting')['saturday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 6 && saturday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
	 			<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
	 				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
	 				if(isset(get_option('bkf_dm_setting')['sunday'])){
	 					echo 'var sunday_items = [';
	 					$setting = get_option('bkf_dm_setting')['sunday'];
	 					foreach($setting as $s){
	 						echo '"'.$s.'",';
	 					}
	 					echo '];';?>

	 					if(w == 0 && sunday_items.includes(currentShippingMethod)){
	 						return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
	 					}
	 					<?php
	 				}
	 			}; ?>
			 for (i = 0; i < closedDatesList.length; i++) {
			   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
			   {
			   	 return [false,"closed","<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			   }
			 }
			 for (i = 0; i < fullDatesList.length; i++) {
			   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
			   {
				 return [false,"booked","<?php esc_html_e('Fully Booked', 'bakkbone-florist-companion'); ?>"];
			   }
			 }
			 for (i = 0; i < catBlockDatesList.length; i++) {
			   if ((m == catBlockDatesList[i][0] - 1) && (d == catBlockDatesList[i][1]) && (y == catBlockDatesList[i][2]))
			   {
				 return [false,"unavailable","<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>"];
			   }
			 }
			 <?php if($sdcpassed == true){
				 ?>
				 if(d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
					 return [false,"sdc","<?php esc_html_e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
					 }<?php
			 }?>
			 var sdc = [<?php foreach($sd_custom as $this_sdc){ echo '"'.$this_sdc.'",';} ?>]
			 if (sdc.includes(currentShippingMethod) && d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
				 return [false,"sdc","<?php esc_html_e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
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
			 } else {
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
				 } else {
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
				 } else {
					 echo '['.$jsdate.'],';
					 }
					 $i++;
			 };}; ?>];
			 var dmSetting = [<?php
				 $dmsetting = get_option('bkf_dm_setting') !== '' ? get_option('bkf_dm_setting') : [];
				 $i = 0;
				 $len = count($dmsetting);
				 foreach($dmsetting as $setting => $methods){
					 if($i == $len - 1){
						 echo '{ day: "'.$setting.'", methods: [';
						 foreach($methods as $thismethod){
							 echo '"'.$thismethod.'",';
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
			 var ele = document.getElementsByName('shipping_method[0]');
			 for(i = 0; i < ele.length; i++) {
				 if(ele[i].checked)
	   			var currentShippingMethod = ele[i].value;
			 }

				<?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['monday'])){
						echo 'var monday_items = [';
						$setting = get_option('bkf_dm_setting')['monday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 1 && monday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
				<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['tuesday'])){
						echo 'var tuesday_items = [';
						$setting = get_option('bkf_dm_setting')['tuesday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 2 && tuesday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
				<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['wednesday'])){
						echo 'var wednesday_items = [';
						$setting = get_option('bkf_dm_setting')['wednesday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 3 && wednesday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
				<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['thursday'])){
						echo 'var thursday_items = [';
						$setting = get_option('bkf_dm_setting')['thursday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 4 && thursday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
				<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['friday'])){
						echo 'var friday_items = [';
						$setting = get_option('bkf_dm_setting')['friday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 5 && friday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
				<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['saturday'])){
						echo 'var saturday_items = [';
						$setting = get_option('bkf_dm_setting')['saturday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 6 && saturday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
				<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
					if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) { return [false, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"]; }<?php } else {
					if(isset(get_option('bkf_dm_setting')['sunday'])){
						echo 'var sunday_items = [';
						$setting = get_option('bkf_dm_setting')['sunday'];
						foreach($setting as $s){
							echo '"'.$s.'",';
						}
						echo '];';?>

						if(w == 0 && sunday_items.includes(currentShippingMethod)){
							return [false, "unavailable", '<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>'];
						}
						<?php
					}
				}; ?>
		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [false,"closed","<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
		   }
		 }
		 for (i = 0; i < fullDatesList.length; i++) {
		   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
		   {
			 return [false,"booked","<?php esc_html_e('Fully Booked', 'bakkbone-florist-companion'); ?>"];
		   }
		 }
		 for (i = 0; i < catBlockDatesList.length; i++) {
		   if ((m == catBlockDatesList[i][0] - 1) && (d == catBlockDatesList[i][1]) && (y == catBlockDatesList[i][2]))
		   {
			 return [false,"unavailable","<?php esc_html_e('Unavailable', 'bakkbone-florist-companion'); ?>"];
		   }
		 }
		 <?php if($sdcpassed == true){
			 ?>
			 if(d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
				 return [false,"sdc","<?php esc_html_e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
				 }<?php
		 }?>
		 var sdc = [<?php foreach($sd_custom as $this_sdc){ echo '"'.$this_sdc.'",';} ?>]
		 if (sdc.includes(currentShippingMethod) && d == "<?php echo wp_date("j"); ?>" && y == "<?php echo wp_date("Y"); ?>" && m == <?php echo wp_date("n"); ?> - 1){
			 return [false,"sdc","<?php esc_html_e('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'); ?>"];
		 }

		 return [true];
	 }
		 } );
		</script>
		</p>
		<?php
		$ts = bkf_get_timeslots();
		$tsselect = __('Select a timeslot...', 'bakkbone-florist-companion');
		?>
		<p class="form-row form-row-wide form-group validate-required validate-delivery_timeslot" id="delivery_timeslot_field"><label for="delivery_timeslot">
		<?php esc_html_e( "Timeslot: ", "bakkbone-florist-companion"); ?>
		<abbr class="required" title="required">*</abbr></label>
			<select name="delivery_timeslot" id="delivery_timeslot" class="delivery_timeslot form-control">
			</select>
		</p>
		</div>
		<script id="bkf_timeslot">
			jQuery(document.body).on( 'change', 'input.shipping_method, input.delivery_date', function($) {
				var all_options = [<?php foreach($ts as $slot) {
					echo '{text: "'.date("g:i a", strtotime($slot['start'])).' - '.date("g:i a", strtotime($slot['end'])).'", slot: "'.$slot['id'].'", method: "'.$slot['method'].'", day: "'.$slot['day'].'"},';} ?>];
					const select = document.querySelector('#delivery_timeslot');
					jQuery(select).empty($);
					let defaultOption = new Option('<?php echo $tsselect; ?>', '', true, true);
					document.querySelector('#delivery_timeslot').add(defaultOption, 0);
					document.querySelector('#delivery_timeslot').options[0].disabled = true;
					all_options.forEach(newOption);
					if(all_options.length == 0) {
						const wrapper = document.querySelector('#delivery_timeslot_field');
						jQuery(wrapper).addClass('bkf-hidden');
						document.querySelector('#delivery_timeslot').removeAttribute('required');
				  }

				  function newOption(value)
					{
	  					const wrapper = document.querySelector('#delivery_timeslot_field');
						var ele = document.getElementsByName('shipping_method[0]');
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
							const wrapper = document.querySelector('#delivery_timeslot_field');
							jQuery(wrapper).removeClass('bkf-hidden');
							document.querySelector('#delivery_timeslot').setAttribute('required', '');
						} else {
							jQuery(wrapper).addClass('bkf-hidden');
							document.querySelector('#delivery_timeslot').removeAttribute('required');
						}
					}
			});
			</script>
			<script id="bkf_timeslot_load">
			jQuery(document).ready( function($) {
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
					  const wrapper = document.querySelector('#delivery_timeslot_field');
					  jQuery(wrapper).addClass('bkf-hidden');
					  document.querySelector('#delivery_timeslot').removeAttribute('required');
				  }

				  function newOption(value)
					{
	  					const wrapper = document.querySelector('#delivery_timeslot_field');
						var ele = document.getElementsByName('shipping_method[0]');
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
							const wrapper = document.querySelector('#delivery_timeslot_field');
							jQuery(wrapper).removeClass('bkf-hidden');
							document.querySelector('#delivery_timeslot').setAttribute('required', '');
						} else {
							jQuery(wrapper).addClass('bkf-hidden');
							document.querySelector('#delivery_timeslot').removeAttribute('required');
						}
					}
			});
			</script>
		<?php
		}
	}

	function dd_order($order_id){
		$order = new WC_Order($order_id);
		if ( isset( $_POST['delivery_date'] ) &&  '' != $_POST['delivery_date'] ) {
			$order->update_meta_data('_delivery_date',  sanitize_text_field( $_POST['delivery_date'] ) );
			$order->update_meta_data('_delivery_timestamp',  (string)strtotime(sanitize_text_field( $_POST['delivery_date'] )) );
		}
		if ( isset( $_POST ['delivery_timeslot'] ) &&  '' != $_POST ['delivery_timeslot'] ) {
			$delivery_timeslot = $_POST['delivery_timeslot'];
			$text = bkf_get_timeslot_string($delivery_timeslot);
			$order->update_meta_data('_delivery_timeslot_id',  sanitize_text_field( $_POST['delivery_timeslot'] ) );
			$order->update_meta_data('_delivery_timeslot',  $text );
		}
		$order->save();
	}

	function dd_to_email ( $fields, $sent_to_admin, $order ) {
		$delivery_date = $order->get_meta('_delivery_date', true );
		$delivery_timeslot_id = $order->get_meta( '_delivery_timeslot_id', true );
		$tsid = $delivery_timeslot_id !== null && $delivery_timeslot_id !== '' && $delivery_timeslot_id ? 'ts'.$delivery_timeslot_id : false;
		$timeslot = $tsid ? bkf_get_timeslots_associative()['ts'.$delivery_timeslot_id] : null;
		$rawtimeslot = $order->get_meta( '_delivery_timeslot', true );
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$tstitle = __('Timeslot', 'bakkbone-florist-companion');

		if ( '' != $delivery_date ) {
			$fields[ 'delivery_date' ] = array(
				'label' => $ddtitle,
				'value' => $delivery_date,
			);
		}
		if ( null !== $timeslot ) {
			$fields[ 'delivery_timeslot' ] = array(
				'label' => $tstitle,
				'value' => date("g:i a", strtotime($timeslot['start'])).' - '.date("g:i a", strtotime($timeslot['end'])),
			);
		} elseif ( '' != $rawtimeslot ) {
			$fields[ 'delivery_timeslot' ] = array(
				'label' => $tstitle,
				'value' => $rawtimeslot,
			);
		}

		return $fields;
	}

	function dd_thankyou ( $order ) {
		$delivery_date = $order->get_meta( '_delivery_date', true );
		$delivery_timeslot_id = $order->get_meta( '_delivery_timeslot_id', true );
		$tsid = $delivery_timeslot_id !== null && $delivery_timeslot_id !== '' && $delivery_timeslot_id ? 'ts'.$delivery_timeslot_id : false;
		$timeslot = $tsid ? bkf_get_timeslots_associative()['ts'.$delivery_timeslot_id] : null;
		$delivery_timeslot = $order->get_meta( '_delivery_timeslot', true );
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$tstitle = __('Timeslot', 'bakkbone-florist-companion');

		if ( '' !== $delivery_date ) {
			echo '<p><strong>' . $ddtitle . ':</strong><br>' . $delivery_date . '</p>';
		}

		if ( null !== $timeslot ) {
			echo '<p><strong>' . $tstitle . ':</strong><br>' . date("g:i a", strtotime($timeslot['start'])).' - '.date("g:i a", strtotime($timeslot['end'])) . '</p>';
		} elseif ( '' !== $delivery_timeslot ) {
			echo '<p><strong>' . $tstitle . ':</strong><br>' . $delivery_timeslot . '</p>';
		}
	}

	function dd_checkout_validation( $fields, $errors ){
		if( bkf_cart_has_physical() ) {
			$ddtitle = get_option('bkf_ddi_setting')['ddt'];
			$invalidtext = sprintf(__('Please select a valid <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
			$notimeslottext = __('Please select a valid <strong>timeslot</strong>.', 'bakkbone-florist-companion');

			if($_POST['delivery_date'] !== ''){
				$day = strtolower(wp_date('l',strtotime($_POST['delivery_date'])));
				$currentshipping = WC()->session->get( 'chosen_shipping_methods' );
				$validts = bkf_get_timeslots_for_order($currentshipping,$day);
			} else {
				$validts = [];
			}

			// Invalid if time slot available but not selected
			if( !empty($validts) && $_POST['delivery_timeslot'] == ''){
				$errors->add( 'validation', $notimeslottext );
			}

			// Invalid if not an actual date or date format incomplete
			if($_POST['delivery_date'] !== ''){
				if( ! preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) ){
					$errors->add( 'validation', $invalidtext );
				} elseif(wp_date("l, j F Y", strtotime($_POST['delivery_date'])) !== $_POST['delivery_date'] ){
					$errors->add( 'validation', $invalidtext );
				}
			} else {
				$errors->add( 'validation', $invalidtext );
			}
		}
	}

	function dd_checkout_validation_js(){
		if( ! is_checkout() ) {
			return;
		}
		$dates = array_merge(get_option("bkf_dd_closed"),get_option("bkf_dd_full"));
		$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		$closeddays = [];
	foreach($days as $day){
		if(get_option("bkf_dd_setting")[strtolower($day)] !== true) {
			$closeddays[] = $day;
		};
	}
	if( ! in_array(wp_date("l"),$closeddays)){
		$sdc = get_option("bkf_sd_setting")[strtolower(wp_date("l"))];
		if( wp_date("H:i") >= $sdc ){
			$sdcpassed = "1";
		} else {
			$sdcpassed = "0";
		}
	} else {
		$sdcpassed = "0";
	}
	?>
	<script id="bkf-dd-val">
	jQuery(function($){
		jQuery( 'body' ).on( 'blur change', '#delivery_date', function(){
			const wrapper = jQuery(this).closest( '.form-row' );
			const dates = [<?php foreach($dates as $ts) { echo '"' . $ts . '", '; }; ?>];
			const days = [<?php foreach($closeddays as $day) { echo '"' . $day . '", '; }; ?>];
			const day = jQuery(this).val().split(",")[0];
			const sdcPassed = <?php echo $sdcpassed ?>;
			const today = "<?php echo wp_date('l, j F Y'); ?>";

			const calcdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
			const calcmonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
			const calcdate = new Date(jQuery(this).val());
			const calctimestampInMs = calcdate.getTime();
			const calctimestampInSeconds = Math.floor(calcdate.getTime() / 1000);
			const calcdaycalc = Math.floor(calcdate.getDay() - 1);
			const calcday = calcdays[calcdaycalc];
			const calcdayNo = calcdate.getDate();
			const calcmonth = calcmonths[calcdate.getMonth()];
			const calcyear = calcdate.getFullYear();
			var calcFormatted = calcday + ', ' + calcdayNo + ' ' + calcmonth + ' ' + calcyear;

			if( ! /[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/.test( jQuery(this).val() ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else if( jQuery(this).val() !== calcFormatted ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else if( dates.includes( jQuery(this).val() ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else if( days.includes( day ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else if( sdcPassed == "1" && jQuery(this).val() == today ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else {
				wrapper.addClass( 'woocommerce-validated' );
			}
		});
	});
	</script>
	<script id="bkf-dd-ts-val">
	jQuery(function($){
		jQuery( 'body' ).on( 'blur change', '#delivery_date', function(){
			const wrapper = jQuery('#delivery_timeslot').closest( '.form-row' );
			const item = document.getElementById("delivery_timeslot");
			if(item.hasAttribute('required') && item.value == '') {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else {
				wrapper.addClass( 'woocommerce-validated' );
			}
		});
	});
	</script>
	<?php
	}

	function dd_col_init( $columns ) {
			$columns['bkf_dd'] = __('Delivery Date', 'bakkbone-florist-companion');
			$columns['billing_address'] = __('Customer', 'bakkbone-florist-companion');
			$columns['shipping_address'] = __('Recipient', 'bakkbone-florist-companion');
			return $columns;
	}

	function dd_col_sort( $a ){
		return wp_parse_args( array( 'bkf_dd' => 'deldate'), $a );
	}

	function dd_filter_legacy( $query ) {
		if ( $query->get( 'orderby') == 'deldate' ){
			$meta_query = array(
				'deldate' => array(
					'key' => '_delivery_timestamp',
				),
			);
			$query->set('meta_query', $meta_query);
			$query->set('meta_key', '_delivery_timestamp');
			$query->set('orderby', 'deldate');
			$query->set('tax_query', []);
		}
	}
	
	function dd_filter($query){
		if(isset($_GET['dd_filter'])){
			if($_GET['dd_filter'] !== '' && $_GET['dd_filter'] !== null){
				$filter_dd = $_GET['dd_filter'];
				$filter_ts = strtotime($filter_dd);
				$query['meta_query'][] = [
					'key'			=> '_delivery_timestamp',
					'value'			=> $filter_ts,
					'comparison'	=> 'LIKE'
				];
			}
		}
		return $query;
	}

	function dd_col( $column, $post_or_order_object ) {
	    $order = ( $post_or_order_object instanceof WC_Order ) ? $post_or_order_object : wc_get_order( $post_or_order_object->ID );
		switch ( $column )
		{
			case 'bkf_dd' :
			$dt = $order->get_meta( '_delivery_timestamp', true );
			if($dt !== ''){
				echo wp_date("l, j F", $dt);
				$timeslotid = $order->get_meta( '_delivery_timeslot_id', true );
				if($timeslotid !== null && $timeslotid !== ''){
					$tsid = 'ts'.$timeslotid;
					$ts = bkf_get_timeslots_associative()[$tsid];
					if($ts !== null){
						echo '<br>'.date("g:i a", strtotime($ts['start'])).' - '.date("g:i a", strtotime($ts['end']));
					} else {
						echo '<br>'.$order->get_meta( '_delivery_timeslot', true );
					}
				}
			}
		}
	}
	
	function handle_custom_query_var( $query, $query_vars ) {
		if ( ! empty( $query_vars['_delivery_timestamp'] ) ) {
			$query['meta_query'][] = array(
				'key' => '_delivery_timestamp',
				'value' => esc_attr( $query_vars['_delivery_timestamp'] ),
			);
		}
		if ( ! empty( $query_vars['_delivery_date'] ) ) {
			$query['meta_query'][] = array(
				'key' => '_delivery_date',
				'value' => esc_attr( $query_vars['_delivery_date'] ),
			);
		}
		return $query;
	}
	
}