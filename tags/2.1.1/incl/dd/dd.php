<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDd
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDd{

    function __construct(){
        add_action('woocommerce_after_checkout_billing_form', array($this,'bkf_dd_field_init') , 10, 1 );
        add_action('wp_enqueue_scripts', array($this, 'bkf_enqueue_datepicker') );
        add_action('woocommerce_checkout_update_order_meta', array($this, 'bkf_dd_order'), 10, 1);
        add_filter('woocommerce_email_order_meta_fields', array($this, 'bkf_dd_to_email'), 10, 3 );
        add_filter('woocommerce_order_details_before_order_table', array($this, 'bkf_dd_thankyou'), 10 , 1 );
        add_action('woocommerce_after_checkout_validation', array($this, 'bkf_dd_checkout_validation'), 10, 2 );
        add_action('wp_footer', array($this, 'bkf_dd_checkout_validation_js'));
        add_filter('manage_edit-shop_order_columns', array($this, 'bkf_dd_col_init'), 10, 1 ); 
        add_filter('manage_edit-shop_order_sortable_columns', array($this, 'bkf_dd_col_sort'), 10, 1 );
        add_action('pre_get_posts', array($this, 'bkf_dd_filter') );
        add_action('manage_shop_order_posts_custom_column', array($this, 'bkf_dd_col'), 10, 2 );
        add_action('woocommerce_process_shop_order_meta', array($this, 'bkf_dd_stamp'), PHP_INT_MAX, 2 );
    }
    
    function bkf_dd_field_init () {
		$closeddates = get_option('bkf_dd_closed');
		$fulldates = get_option('bkf_dd_full');
		$maxdate = get_option('bkf_ddi_setting')['ddi'];
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$fulltext = __('Fully Booked', 'bakkbone-florist-companion');
		$closedtext = __('Closed', 'bakkbone-florist-companion');
		$uatext = __('Unavailable', 'bakkbone-florist-companion');
		$sdctext = __('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion');

		$cart = WC()->cart->get_cart();
		$cats = array();
		foreach($cart as $k => $v){
			$cat = wc_get_product_cat_ids($v['product_id']);
			foreach($cat as $c){
				$cats[] = $c;
			}
		}
				
		$cb = array();
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
		
		$catblocklist = array();
		foreach($cb as $thisblock){
			if(in_array($thisblock['category'],$cats)){
				$catblocklist[] = $thisblock['date'];
			}
		}
		
		$today = strtolower(date("l"));
		if(get_option('bkf_dd_setting')[$today] == 1 && get_option('bkf_sd_setting')[$today] <= date("G:i")){
			$sdcpassed = true;
		} else {
			$sdcpassed = false;
		}
		
        ?><h3><?php echo $ddtitle; ?></h3>
        <p class="form-row form-row-wide form-group validate-required validate-delivery_date" id="delivery_date_field">
        <?php
    	_e( "We'll schedule your order for: ", "bakkbone-florist-companion");
    	?>
    	<abbr class="required" title="required">*</abbr><br>
    	<input type="text" name="delivery_date" class="delivery_date input-text form-control" id="delivery_date" placeholder="<?php echo $ddtitle; ?>" required autocomplete="off" />
    	<script id="bkf_ddfield">
    	    jQuery(document).ready(function( $ ) {
				$("#delivery_date").attr( 'readOnly' , 'true' );
    	        $(".delivery_date").datepicker( {
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
					 $jsdate = date('n,j,Y',$ts);
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
					 $jsdate = date('n,j,Y',$ts);
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
					 $jsdate = date('n,j,Y',$ts);
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
	         var ele = document.getElementsByName('shipping_method[0]');
	         for(i = 0; i < ele.length; i++) {
	             if(ele[i].checked)
	   			var currentShippingMethod = ele[i].value;
	         }
		 		 
 			<?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
 				if (w == 1) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['monday'])){
 					echo 'var monday_items = [';
 					$setting = get_option('bkf_dm_setting')['monday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 1 && monday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
 			<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
 				if (w == 2) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['tuesday'])){
 					echo 'var tuesday_items = [';
 					$setting = get_option('bkf_dm_setting')['tuesday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 2 && tuesday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
 			<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
 				if (w == 3) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['wednesday'])){
 					echo 'var wednesday_items = [';
 					$setting = get_option('bkf_dm_setting')['wednesday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 3 && wednesday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
 			<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
 				if (w == 4) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['thursday'])){
 					echo 'var thursday_items = [';
 					$setting = get_option('bkf_dm_setting')['thursday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 4 && thursday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
 			<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
 				if (w == 5) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['friday'])){
 					echo 'var friday_items = [';
 					$setting = get_option('bkf_dm_setting')['friday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 5 && friday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
 			<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
 				if (w == 6) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['saturday'])){
 					echo 'var saturday_items = [';
 					$setting = get_option('bkf_dm_setting')['saturday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 6 && saturday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
 			<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
 				if (w == 0) { return [false, "closed", 'Closed']; }<?php } else {
 				if(isset(get_option('bkf_dm_setting')['sunday'])){
 					echo 'var sunday_items = [';
 					$setting = get_option('bkf_dm_setting')['sunday'];
 					foreach($setting as $s){
 						echo '"'.$s.'",';
 					}
 					echo '];';?>
					
 					if(w == 0 && sunday_items.includes(currentShippingMethod)){
 						return [false, "unavailable", '<?php echo $uatext; ?>'];
 					}
 					<?php
 				}
 			}; ?>
		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [false,"closed","<?php echo $closedtext; ?>"];
		   }
		 }
	     for (i = 0; i < fullDatesList.length; i++) {
	       if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
	       {
	         return [false,"booked","<?php echo $fulltext; ?>"];
	       }
	     }
	     for (i = 0; i < catBlockDatesList.length; i++) {
	       if ((m == catBlockDatesList[i][0] - 1) && (d == catBlockDatesList[i][1]) && (y == catBlockDatesList[i][2]))
	       {
	         return [false,"unavailable","<?php echo $uatext; ?>"];
	       }
	     }
		 <?php if($sdcpassed == true){
			 ?>
			 if(d == "<?php echo date("j"); ?>" && y == "<?php echo date("Y"); ?>" && m == <?php echo date("n"); ?> - 1){
				 return [false,"sdc","<?php echo $sdctext; ?>"];
				 }<?php
		 }?>

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
				 $jsdate = date('n,j,Y',$ts);
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
				 $jsdate = date('n,j,Y',$ts);
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
				 $jsdate = date('n,j,Y',$ts);
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
         var ele = document.getElementsByName('shipping_method[0]');
         for(i = 0; i < ele.length; i++) {
             if(ele[i].checked)
   			var currentShippingMethod = ele[i].value;
         }
		 		 
			<?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
				if (w == 1) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['monday'])){
					echo 'var monday_items = [';
					$setting = get_option('bkf_dm_setting')['monday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 1 && monday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
			<?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
				if (w == 2) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['tuesday'])){
					echo 'var tuesday_items = [';
					$setting = get_option('bkf_dm_setting')['tuesday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 2 && tuesday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
			<?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
				if (w == 3) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['wednesday'])){
					echo 'var wednesday_items = [';
					$setting = get_option('bkf_dm_setting')['wednesday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 3 && wednesday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
			<?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
				if (w == 4) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['thursday'])){
					echo 'var thursday_items = [';
					$setting = get_option('bkf_dm_setting')['thursday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 4 && thursday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
			<?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
				if (w == 5) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['friday'])){
					echo 'var friday_items = [';
					$setting = get_option('bkf_dm_setting')['friday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 5 && friday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
			<?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
				if (w == 6) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['saturday'])){
					echo 'var saturday_items = [';
					$setting = get_option('bkf_dm_setting')['saturday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 6 && saturday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
			<?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
				if (w == 0) { return [false, "closed", 'Closed']; }<?php } else {
				if(isset(get_option('bkf_dm_setting')['sunday'])){
					echo 'var sunday_items = [';
					$setting = get_option('bkf_dm_setting')['sunday'];
					foreach($setting as $s){
						echo '"'.$s.'",';
					}
					echo '];';?>
					
					if(w == 0 && sunday_items.includes(currentShippingMethod)){
						return [false, "unavailable", '<?php echo $uatext; ?>'];
					}
					<?php
				}
			}; ?>
	 for (i = 0; i < closedDatesList.length; i++) {
	   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
	   {
	   	 return [false,"closed","<?php echo $closedtext; ?>"];
	   }
	 }
     for (i = 0; i < fullDatesList.length; i++) {
       if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
       {
         return [false,"booked","<?php echo $fulltext; ?>"];
       }
     }
     for (i = 0; i < catBlockDatesList.length; i++) {
       if ((m == catBlockDatesList[i][0] - 1) && (d == catBlockDatesList[i][1]) && (y == catBlockDatesList[i][2]))
       {
         return [false,"unavailable","<?php echo $uatext; ?>"];
       }
     }
	 <?php if($sdcpassed == true){
		 ?>
		 if(d == "<?php echo date("j"); ?>" && y == "<?php echo date("Y"); ?>" && m == <?php echo date("n"); ?> - 1){
			 return [false,"sdc","<?php echo $sdctext; ?>"];
			 }<?php
	 }?>
	 	 		 
	 return [true];
 }
     } );
    	</script>
		</p>
		<?php
		$ts = array();
		global $wpdb;
		$timeslots = $wpdb->get_results(
			"
				SELECT id, method, day, start, end
				FROM {$wpdb->prefix}bkf_dd_timeslots
			"
		);
		foreach($timeslots as $timeslot){
			$ts[] = array(
				'id'		=>	$timeslot->id,
				'method'	=>	$timeslot->method,
				'day'		=>	$timeslot->day,
				'start'		=>	$timeslot->start,
				'end'		=>	$timeslot->end
			);
		}
		uasort($ts, function($a,$b){
			return strcmp($a['start'],$b['start']);} );
		?>
        <p class="form-row form-row-wide form-group validate-required validate-delivery_timeslot" id="delivery_timeslot_field">
        <?php
    	_e( "Time Slot: ", "bakkbone-florist-companion");
    	?>
    	<abbr class="required" title="required">*</abbr><br>
			<select name="delivery_timeslot" id="delivery_timeslot" class="delivery_timeslot form-control input-text" style="-webkit-appearance:auto;">
			</select>
		</p>
		<script id="bkf_timeslot">
			jQuery(document.body).on( 'change', 'input.shipping_method, input.delivery_date', function($) {
				
				var all_options = [<?php foreach($ts as $slot) {
				  echo '{text: "'.date("g:i a", strtotime($slot['start'])).' - '.date("g:i a", strtotime($slot['end'])).'", slot: "'.$slot['id'].'", method: "'.$slot['method'].'", day: "'.$slot['day'].'"},';
			  } ?>];					  	
			      const select = document.querySelector('#delivery_timeslot'); 
				  jQuery(select).empty($);
				  let defaultOption = new Option('Select a time slot...', '', true, true);
				  document.querySelector('#delivery_timeslot').add(defaultOption, 0);
				  document.querySelector('#delivery_timeslot').options[0].disabled = true;
				  all_options.forEach(newOption);

				  function newOption(value)
				    {
	  				    const wrapper = document.querySelector('#delivery_timeslot_field');
			            var ele = document.getElementsByName('shipping_method[0]');
			            for(i = 0; i < ele.length; i++) {
			                if(ele[i].checked)
								var currentShippingMethod = ele[i].value;
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

						
						if(select.options.length > 1) {
							jQuery(wrapper).removeClass('bkf-hidden');
							select.setAttribute('required', '');
						} else {
							jQuery(wrapper).addClass('bkf-hidden');
							select.removeAttribute('required');
						}
				    }
			});
			jQuery(document).ready( function($) {
				
				var all_options = [<?php foreach($ts as $slot) {
				  echo '{text: "'.date("g:i a", strtotime($slot['start'])).' - '.date("g:i a", strtotime($slot['end'])).'", slot: "'.$slot['id'].'", method: "'.$slot['method'].'", day: "'.$slot['day'].'"},';
			  } ?>];					  	
			      const select = document.querySelector('#delivery_timeslot'); 
				  jQuery(select).empty($);
				  let defaultOption = new Option('Select a time slot...', '', true, true);
				  document.querySelector('#delivery_timeslot').add(defaultOption, 0);
				  document.querySelector('#delivery_timeslot').options[0].disabled = true;
				  all_options.forEach(newOption);

				  function newOption(value)
				    {
	  				    const wrapper = document.querySelector('#delivery_timeslot_field');
			            var ele = document.getElementsByName('shipping_method[0]');
			            for(i = 0; i < ele.length; i++) {
			                if(ele[i].checked)
								var currentShippingMethod = ele[i].value;
			            }
						var days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
						var aDate = document.querySelector('#delivery_date').value;
						var theDate = Date.parse(aDate);
						var theDateObject = new Date(theDate);
						var delDay = days[theDateObject.getDay() - 1];
						if( value.day == delDay && value.method == currentShippingMethod ){
							const select = document.querySelector('#delivery_timeslot'); 
							let newOption = new Option(value.text, value.text);
							select.add(newOption, undefined);
						}

						
						if(select.options.length > 1) {
							jQuery(wrapper).removeClass('bkf-hidden');
							select.setAttribute('required', '');
						} else {
							jQuery(wrapper).addClass('bkf-hidden');
							select.removeAttribute('required');
						}
				    }
			});
			</script>

    	<?php  
    }

    function bkf_enqueue_datepicker() {
        if ( is_checkout() ) {
         wp_enqueue_script( 'jquery-ui-datepicker' );
         wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
         wp_enqueue_style( 'jquery-ui' );  
        }  
    }
    
    function bkf_dd_order ( $order_id ) {
    	if ( isset( $_POST ['delivery_date'] ) &&  '' != $_POST ['delivery_date'] ) {
    		update_post_meta( $order_id, '_delivery_date',  sanitize_text_field( $_POST ['delivery_date'] ) );
			update_post_meta( $order_id, '_delivery_timestamp',  (string)strtotime(sanitize_text_field( $_POST ['delivery_date'] )) );
    	}
    	if ( isset( $_POST ['delivery_timeslot'] ) &&  '' != $_POST ['delivery_timeslot'] ) {
			global $wpdb;
			$ts = array();
			$timeslots = $wpdb->get_results(
				"
					SELECT id, method, day, start, end
					FROM {$wpdb->prefix}bkf_dd_timeslots
				"
			);
			foreach($timeslots as $timeslot){
				$ts[] = array(
					'id'		=>	$timeslot->id,
					'method'	=>	$timeslot->method,
					'day'		=>	$timeslot->day,
					'start'		=>	$timeslot->start,
					'end'		=>	$timeslot->end
				);
			}
			$tscol = array_column($ts, 'id');
			$thistsid = array_search($delivery_timeslot, $tscol);
			$thists = $ts[$thistsid];
			$text = date("g:i a", strtotime($thists['start'])).' - '.date("g:i a", strtotime($thists['end']));
    		update_post_meta( $order_id, '_delivery_timeslot_id',  sanitize_text_field( $_POST ['delivery_timeslot'] ) );
    		update_post_meta( $order_id, '_delivery_timeslot',  $text );
    	}
    }
    
    function bkf_dd_to_email ( $fields, $sent_to_admin, $order ) {
		$order_id = $order->get_id();
        $delivery_date = get_post_meta( $order_id, '_delivery_date', true );
        $rawtimeslot = get_post_meta( $order_id, '_delivery_timeslot', true );
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$tstitle = __('Time Slot', 'bakkbone-florist-companion');
			
        if ( '' != $delivery_date ) {
    	$fields[ 'delivery_date' ] = array(
    	    'label' => $ddtitle,
    	    'value' => $delivery_date,
    	);
        }
        if ( '' != $rawtimeslot ) {
    	$fields[ 'delivery_timeslot' ] = array(
    	    'label' => $tstitle,
    	    'value' => implode($rawtimeslot),
    	);
        }		
		
        return $fields;
    }
    
    function bkf_dd_thankyou ( $order ) {
		$order_id = $order->get_id();
        $delivery_date = get_post_meta( $order_id, '_delivery_date', true );
        $delivery_timeslot = get_post_meta( $order_id, '_delivery_timeslot', true );
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$tstitle = __('Time Slot', 'bakkbone-florist-companion');
        
        if ( '' !== $delivery_date ) {
        	echo '<p><strong>' . $ddtitle . ':</strong><br>' . $delivery_date . '</p>';
    	}

        if ( '' !== $delivery_timeslot ) {
        	echo '<p><strong>' . $tstitle . ':</strong><br>' . implode($delivery_timeslot) . '</p>';
    	}
    }

    function bkf_dd_checkout_validation( $fields, $errors ){
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
        $invalidtext = sprintf(__('Please select a valid <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
		$closedtext = sprintf(__('We are <strong>closed</strong> on the date selected. Please select another <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
		$fulltext = sprintf(__('We are <strong>fully booked</strong> on the date selected. Please select another <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
		$disabledtext = sprintf(__('The date selected is <strong>not available</strong>. Please select another <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
		$sdctext = sprintf(__('The same-day delivery cutoff has passed. Please select a valid <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
		$notimeslottext = sprintf(__('Please select a valid %s <strong>time slot</strong>.', 'bakkbone-florist-companion'), $ddtitle);

		if( !empty($validts) && $_POST['delivery_timeslot'] == ''){
			$errors->add( 'validation', $notimeslottext );
		}
        if( ! preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) ){
            $errors->add( 'validation', $invalidtext );
        }
		
        // Groundwork for store-specific validation
        $ddweekdays = get_option("bkf_dd_setting");
        $sdweekdays = get_option("bkf_sd_setting");
		$closed = get_option("bkf_dd_closed");
		$full = get_option("bkf_dd_full");
        date_default_timezone_set(wp_timezone_string());
		
		// Invalid if not an actual date
		if(date("l, j F Y", strtotime($_POST['delivery_date'])) !== $_POST['delivery_date'] ){
            $errors->add( 'validation', $invalidtext );
		}
		
		// Invalid if closed
		if(in_array($_POST['delivery_date'], $closed)){
            $errors->add( 'validation', $closedtext );			
		}
        
		// Invalid if full
		if(in_array($_POST['delivery_date'], $full)){
            $errors->add( 'validation', $fulltext );			
		}
		
        // Invalid if weekday not enabled
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Monday/', $_POST['delivery_date']) && $ddweekdays['monday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Tuesday/', $_POST['delivery_date']) && $ddweekdays['tuesday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Wednesday/', $_POST['delivery_date']) && $ddweekdays['wednesday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Thursday/', $_POST['delivery_date']) && $ddweekdays['thursday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Friday/', $_POST['delivery_date']) && $ddweekdays['friday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Saturday/', $_POST['delivery_date']) && $ddweekdays['saturday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Sunday/', $_POST['delivery_date']) && $ddweekdays['sunday'] !== true){
            $errors->add( 'validation', $disabledtext );
        }
        
        // Invalid if SDC passed
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Monday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['monday'] == true && date("H:i") >= $sdweekdays['monday']){
            $errors->add( 'validation', $sdctext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Tuesday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['tuesday'] == true && date("H:i") >= $sdweekdays['tuesday']){
            $errors->add( 'validation', $sdctext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Wednesday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['wednesday'] == true && date("H:i") >= $sdweekdays['wednesday']){
            $errors->add( 'validation', $sdctext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Thursday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['thursday'] == true && date("H:i") >= $sdweekdays['thursday']){
            $errors->add( 'validation', $sdctext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Friday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['friday'] == true && date("H:i") >= $sdweekdays['friday']){
            $errors->add( 'validation', $sdctext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Saturday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['saturday'] == true && date("H:i") >= $sdweekdays['saturday']){
            $errors->add( 'validation', $sdctext );
        }
        if( preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) && preg_match ('/Sunday/', $_POST['delivery_date']) && date('l, j F Y') == $_POST['delivery_date'] && $ddweekdays['sunday'] == true && date("H:i") >= $sdweekdays['sunday']){
            $errors->add( 'validation', $sdctext );
        }
    }
    
    function bkf_dd_checkout_validation_js(){
        	if( ! is_checkout() ) {
		return;
	}
	$dates = array_merge(get_option("bkf_dd_closed"),get_option("bkf_dd_full"));
	$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$closeddays = array();
	foreach($days as $day){
		if(get_option("bkf_dd_setting")[strtolower($day)] == "1") {
		}else{
			$closeddays[] = $day;
		
		};
	}
	date_default_timezone_set(wp_timezone_string());
	if( ! in_array(date("l"),$closeddays)){
		$sdc = get_option("bkf_sd_setting")[strtolower(date("l"))];
		if( date("H:i") >= $sdc ){
			$sdcpassed = "1";
		}else{
			$sdcpassed = "0";
		}
	}else{
		$sdcpassed = "0";
	}
	?>
	<script id="bkf-dd-val">
	jQuery(function($){
		$( 'body' ).on( 'blur change', '#delivery_date', function(){
			const wrapper = $(this).closest( '.form-row' );
			const dates = [<?php foreach($dates as $ts) { echo '"' . $ts . '", '; }; ?>];
			const days = [<?php foreach($closeddays as $day) { echo '"' . $day . '", '; }; ?>];
			const day = $(this).val().split(",")[0];
			const sdcPassed = <?php echo $sdcpassed ?>;
			const today = "<?php echo date('l, j F Y'); ?>";
			
			const calcdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
			const calcmonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
			const calcdate = new Date($(this).val());
			const calctimestampInMs = calcdate.getTime();
			const calctimestampInSeconds = Math.floor(calcdate.getTime() / 1000);
			const calcdaycalc = Math.floor(calcdate.getDay() - 1);
			const calcday = calcdays[calcdaycalc];
			const calcdayNo = calcdate.getDate();
			const calcmonth = calcmonths[calcdate.getMonth()];
			const calcyear = calcdate.getFullYear();
			var calcFormatted = calcday + ', ' + calcdayNo + ' ' + calcmonth + ' ' + calcyear;
			
			if( ! /[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/.test( $(this).val() ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else if( $(this).val() !== calcFormatted ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );				
			} else if( dates.includes( $(this).val() ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );				
			} else if( days.includes( day ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );						
			} else if( sdcPassed == "1" && $(this).val() == today ) {
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
		$( 'body' ).on( 'blur change', '#delivery_date', function(){
			const wrapper = $('#delivery_timeslot').closest( '.form-row' );
			const item = document.getElementById("delivery_timeslot");
			if(item.hasAttribute('required') == true && item.value == '') {
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

	function bkf_dd_col_init( $columns ) {
			$ddtitle = get_option('bkf_ddi_setting')['ddt'];
			$columns['bkf_dd'] = __("Delivery Date", "bakkbone-florist-companion");
			return $columns;
	}
	
	function bkf_dd_col_sort( $a ){
	return wp_parse_args( array( 'bkf_dd' => 'deldate'), $a );
    }
    
    function bkf_dd_filter( $query ) {
		if ( ! is_admin() ) return;
		if( empty( $_GET['orderby'] ) || empty( $_GET['order'] ) ) return;
		if( $_GET['orderby'] == 'deldate' ) {
			$query->set('meta_key', '_delivery_timestamp' );
			$query->set('orderby', 'meta_value');
			$query->set('order', $_GET['order'] );
		}
		return $query;
    }

	function bkf_dd_col( $column, $post_id ) {

	    switch ( $column )
	    {
	        case 'bkf_dd' :
			$dt = implode(get_post_meta( $post_id, '_delivery_timestamp' ));
			echo date("l, j F", $dt);
		   $timeslotvalue = get_post_meta( $post_id, '_delivery_timeslot' );
		   if($timeslotvalue !== null){
			   echo '<br>'.implode($timeslotvalue);
		   }
	    }        
	}
	
	function bkf_dd_stamp( $order_id, $order ) {
		$ddstring = $_POST['delivery_date'];
		$ddunix = strtotime($ddstring);
		update_post_meta($order_id,'_delivery_timestamp', (string)$ddunix);
	}

}