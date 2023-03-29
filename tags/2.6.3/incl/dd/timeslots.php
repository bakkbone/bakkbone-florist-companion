<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdTsOptions{

    function __construct(){
		global $bkf_dd_ts_db_version;
		$bkf_dd_ts_db_version = '1.3';
		register_activation_hook( __FILE__, array($this, 'bkf_dd_ts_db_init'));
        add_action("admin_menu", array($this,"bkf_admin_menu"),7);
        add_action("admin_init",array($this,"bkfAddDdTsInit"));
		add_action("admin_footer",array($this,"bkfDdtsAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfDdtsAdminEnqueueScripts"));
		add_action("plugins_loaded",array($this,"bkf_update_db_check"));
    	add_action('wp_ajax_bkf_ts_add', array($this, 'bkf_ts_add') ); 
    	add_action('wp_ajax_bkf_ts_del', array($this, 'bkf_ts_del') );
    }
	
	function bkf_dd_ts_db_init(){
		global $wpdb;
		global $bkf_dd_ts_db_version;

		$table_name = $wpdb->prefix . 'bkf_dd_timeslots';
	
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day text(9) NOT NULL,
			start tinytext NOT NULL,
			end tinytext NOT NULL,
			fee tinytext,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
		
		$installed_ver = get_option( "bkf_dd_ts_db_version" );

		if ( $installed_ver != $bkf_dd_ts_db_version ) {

			$table_name = $wpdb->prefix . 'bkf_dd_timeslots';

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				day text(9) NOT NULL,
				start tinytext NOT NULL,
				end tinytext NOT NULL,
				fee tinytext,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
		}		
	}
	
	function bkf_update_db_check() {
	    global $bkf_dd_ts_db_version;
	    if ( get_site_option( 'bkf_dd_ts_db_version' ) != $bkf_dd_ts_db_version ) {
			global $wpdb;
			global $bkf_dd_ts_db_version;

			$table_name = $wpdb->prefix . 'bkf_dd_timeslots';
	
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				day text(9) NOT NULL,
				start tinytext NOT NULL,
				end tinytext NOT NULL,
				fee tinytext,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
		
			$installed_ver = get_option( "bkf_dd_ts_db_version" );

			if ( $installed_ver != $bkf_dd_ts_db_version ) {

				$table_name = $wpdb->prefix . 'bkf_dd_timeslots';

				$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					method tinytext NOT NULL,
					day text(9) NOT NULL,
					start tinytext NOT NULL,
					end tinytext NOT NULL,
					fee tinytext,
					PRIMARY KEY  (id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				update_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
			}
	    }
	}
	
    function bkf_admin_menu(){
        $admin_page = add_submenu_page(
        "bkf_dd",
        __("Delivery Timeslots","bakkbone-florist-companion"),
        __("Timeslots","bakkbone-florist-companion"),
        "manage_woocommerce",
        "bkf_ddts",
        array($this, "bkf_dd_ts_page"),
        7
        );
		add_action( 'load-'.$admin_page, array($this, 'bkf_ts_help_tab') );
    }
	
	function bkf_ts_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_ts_help';
		$callback = array($this, 'bkf_ts_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_ts_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/timeslots" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/timeslots</a>
		<?php
	}
	
    function bkf_dd_ts_page()
    {
		$addnonce = wp_create_nonce("ts-add");			
		$delnonce = wp_create_nonce("ts-del");			
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}
		
		$days = get_option('bkf_dd_setting');
		$daysoff = get_option('bkf_dm_setting');
		global $wpdb;
		$ts = array();
		$timeslots = $wpdb->get_results(
			"
				SELECT id, method, day, start, end, fee
				FROM {$wpdb->prefix}bkf_dd_timeslots
			"
		);
		foreach($timeslots as $timeslot){
			$ts[] = array(
				'id'		=>	$timeslot->id,
				'method'	=>	$timeslot->method,
				'day'		=>	$timeslot->day,
				'start'		=>	$timeslot->start,
				'end'		=>	$timeslot->end,
				'fee'		=>	$timeslot->fee
			);
		}
		uasort($ts, function($a,$b){
			return strcmp($a['start'],$b['start']);} );
		?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Timeslots","bakkbone-florist-companion") ?></h1>
			<p><?php _e('Only weekdays enabled on the Delivery Dates page will be displayed below. Any method within a day with no timeslots added will not require timeslots at checkout.','bakkbone-florist-companion') ?><br><em><?php _e('Fees are optional - leave fee blank if not required for a timeslot. If taxable status is enabled on the Fees Options page, fees below must be entered <em>exclusive</em> of tax.','bakkbone-florist-companion') ?></em></p>
			<form class="bkf-form" id="addform" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_ts_add" />
							<input type="hidden" name="nonce" value="<?php echo $addnonce; ?>" />
								<select id="add-day" name="day" class="bkf-form-control" required>
									<option value="" disabled selected><?php _e('Select a day...','bakkbone-florist-companion'); ?></option>
									<?php
									foreach($days as $day => $on){
										if($on == 1){
											echo '<option value="'.$day.'">'.ucwords($day).'</option>';
										}
									}
										?>
									</select>
							<select disabled class="bkf-form-control" name="method" id="add-method" required>
									<option value="" disabled selected><?php _e('Select a delivery method...','bakkbone-florist-companion'); ?></option>
							</select>
							<label><?php _e('Start: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="add-start" name="start" required /></label>
							<label><?php _e('End: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="add-end" name="end" required /></label>
							<label><?php _e('Fee: ', 'bakkbone-florist-companion'); echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?><input type="text" placeholder="***.**" class="bkf-form-control" id="add-fee" name="fee" pattern="\d+\.\d{2,}" /></label>
							<input type="submit" value="<?php _e('Add Timeslot','bakkbone-florist-companion'); ?>" id="add-submit" class="button button-primary" />
							<div style="max-width:350px;" id="add-error" class="bkf-error bkf-hidden"><p><?php _e('End time must be greater than start time.','bakkbone-florist-companion'); ?></p></div>
			</form>
						<script id="addform">
							jQuery(function($){
								const startElement = document.getElementById("add-start");
								const endElement = document.getElementById("add-end");
								const dayElement = document.getElementById("add-day");
								const methodElement = document.getElementById("add-method");
								const deliveryMethods = [
									<?php foreach($days as $day => $on){
										if($on == 1){
											echo '{ day: "'.$day.'", methods: [';
												foreach($sm as $smethod){
													if(array_key_exists($day,$daysoff)){
														if(!in_array($smethod['rateid'],$daysoff[$day])){
															echo '{ name: "'.$smethod['title'].' #'.$smethod['instanceid'].': '.$smethod['usertitle'].'", id: "'.$smethod['rateid'].'" },';
														}
													} else {
														echo '{ name: "'.$smethod['title'].' #'.$smethod['instanceid'].': '.$smethod['usertitle'].'", id: "'.$smethod['rateid'].'" },';
													}
												}
											echo '] },';
										}
									} ?>];
								
								dayElement.addEventListener('change', (event) => {
									var day = dayElement.value;
									if( day !== '' ) {
										methodElement.removeAttribute('disabled');
					  				  jQuery(methodElement).empty($);
									  let defaultOption = new Option('<?php _e('Select a delivery method...', 'bakkbone-florist-companion'); ?>', '');
									  methodElement.add(defaultOption, 0);
									  methodElement.options[0].disabled = true;
										deliveryMethods.forEach(checkAvail);
										function checkAvail(method){
											if(method.day == day){
												method.methods.forEach(doAvail);
												function doAvail(thismethod){
													let newOption = new Option(thismethod.name, thismethod.id)
													methodElement.add(newOption, undefined);
												}
											}
										}
									}
								});
								
								startElement.addEventListener('change', (event) => {
									var start = startElement.value;
									var end = endElement.value;
									if( start !== '' && end !== '' && start >= end ) {
										$("#addform").addClass( 'bkf-invalid' );
										$("#addform").removeClass( 'bkf-validated' );
										$("#add-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										$("#addform").removeClass( 'bkf-invalid' );
										$("#addform").removeClass( 'bkf-validated' );                  
										$("#add-error").addClass( 'bkf-hidden' );
				                  } else {
										$("#addform").addClass( 'bkf-validated' );
										$("#add-error").addClass( 'bkf-hidden' );
									}
								});
								
								endElement.addEventListener('change', (event) => {
									var start = startElement.value;
									var end = endElement.value;
									if( start !== '' && end !== '' && start >= end ) {
										$("#addform").addClass( 'bkf-invalid' );
										$("#addform").removeClass( 'bkf-validated' );
										$("#add-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										$("#addform").removeClass( 'bkf-invalid' );
										$("#addform").removeClass( 'bkf-validated' );                  
										$("#add-error").addClass( 'bkf-hidden' );
				                  } else {
										$("#addform").addClass( 'bkf-validated' );
										$("#add-error").addClass( 'bkf-hidden' );
									}
								});
							});
							</script>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
                <?php
				foreach($days as $day => $on){
				if($on == 1){?><div class="inside bkf-inside">
					<h2 style="margin-top:0;text-align:center;"><?php echo ucwords($day); ?></h2>
					<?php foreach($sm as $smethod){
						echo '<div class="bkf-form" id="'.$day.'-'.$smethod['instanceid'].'"><p style="margin:0;"><strong>'.$smethod['title'].' #'.$smethod['instanceid'].': </strong>'.$smethod['usertitle'].'</p>';
						foreach($ts as $tslot){
							if($tslot['day'] == $day && $tslot['method'] == $smethod['rateid']){
									echo '<p>'.date("g:i a", strtotime($tslot['start'])).' - '.date("g:i a", strtotime($tslot['end'])).' <em><a href="'.admin_url('admin-ajax.php?action=bkf_ts_del&nonce='.$delnonce.'&id='.$tslot['id']).'">'.__('Delete','bakkbone-florist-companion').'</a></em>';
									if($tslot['fee'] !== '' && $tslot['fee'] !== null){
										echo '<br><strong>'.__('Fee: ', 'bakkbone-florist-companion').'</strong>'.get_woocommerce_currency_symbol(get_woocommerce_currency()).$tslot['fee'];
									}
									echo '</p>';
							}
						}
						?></div><?php
					}?>
				</div><?php
			}
		}
		?></div></div></div><?php
    }
    
	function bkfAddDdTsInit()
	{
		register_setting(
			"bkf_dd_ts_group",
			"bkf_dd_timeslots",
			array('type' => 'array')
		);
	}
	
	function bkfDdtsAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_ddts")
		{

		}
	}
	
	function bkfDdtsAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_ddts")
		{
		
		}
	}
	
	function bkf_ts_add(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "ts-add")) {
          exit("No funny business please");
        }
		$day = $_REQUEST['day'];
		$method = $_REQUEST['method'];
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];
		$fee = $_REQUEST['fee'];
		
		global $wpdb;
		$wpdb->insert(
		$wpdb->prefix.'bkf_dd_timeslots',
		array(
			'day'	=>	$day,
			'method'=>	$method,
			'start'	=>	$start,
			'end'	=>	$end,
			'fee'	=>	$fee
		)
	);
			
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {        
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();		
	}

	function bkf_ts_del(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "ts-del")) {
          exit("No funny business please");
        }
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete(
		$wpdb->prefix.'bkf_dd_timeslots',
		array(
			'id'	=>	$id
		)
	);
			
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