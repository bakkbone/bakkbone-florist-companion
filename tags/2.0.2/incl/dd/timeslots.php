<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdTsOptions{

    private $bkf_dd_ts_setting = array();

    function __construct(){
		global $bkf_dd_ts_db_version;
		$bkf_dd_ts_db_version = '1.2';
		register_activation_hook( __FILE__, array($this, 'bkf_dd_ts_db_init'));
        $this->bkf_dd_ts_setting = get_option("bkf_dd_ts_setting");
        add_action("admin_menu", array($this,"bkf_admin_menu"));
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
					PRIMARY KEY  (id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				update_option( 'bkf_dd_ts_db_version', $bkf_dd_ts_db_version );
			}
	    }
	}
	
    function bkf_admin_menu(){
        add_submenu_page(
        "bkf_options",//parent slug
        __("Delivery Timeslots","bakkbone-florist-companion"),//page title
        '— ' . __("Timeslots","bakkbone-florist-companion"),//menu title
        "manage_options",//capability
        "bkf_ddts",//menu slug
        array($this, "bkf_dd_ts_page"),//callback
        32
        );
    }

    function bkf_dd_ts_page()
    {
		date_default_timezone_set(wp_timezone_string());
		$addnonce = wp_create_nonce("ts-add");			
		$delnonce = wp_create_nonce("ts-del");			
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 ); // ADD ZONE "0" MANUALLY
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
	            $method_title = $method->get_method_title(); // e.g. "Flat Rate"
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title(); // e.g. whatever you renamed "Flat Rate" into
	            $method_rate_id = $method->get_rate_id(); // e.g. "flat_rate:18"
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
		uasort($ts, function($a,$b){
			return strcmp($a['start'],$b['start']);} );
		?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Timeslots","bakkbone-florist-companion") ?></h1>
			<p><?php _e('Only weekdays enabled on the Delivery Dates page will be displayed below. Any method within a day with no timeslots added will not require timeslots at checkout.','bakkbone-florist-companion') ?></p>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
                <?php
				foreach($days as $day => $on){
				if($on == 1){?><div class="inside bkf-inside">
					<h2 style="margin-top:0;text-align:center;"><?php echo ucwords($day); ?></h2>
					<?php foreach($sm as $smethod){
						echo '<div class="bkf-form" id="'.$day.'-'.$smethod['instanceid'].'"><p style="margin:0;"><strong>'.__('Delivery Method: ', 'bakkbone-florist-companion').'</strong>'.$smethod['usertitle'].'</p>';
						?>
						<form class="bkf-form" id="<?php echo $day.'-addts-'.$smethod['instanceid']; ?>" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_ts_add" />
							<input type="hidden" name="method" value="<?php echo $smethod['rateid']; ?>" />
							<input type="hidden" name="day" value="<?php echo $day; ?>" />
							<input type="hidden" name="nonce" value="<?php echo $addnonce; ?>" />
							<label><?php _e('Start: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="<?php echo $day.'-'.$smethod['instanceid']; ?>-start" name="start" /></label>
							<label><?php _e('End: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="<?php echo $day.'-'.$smethod['instanceid']; ?>-end" name="end" /></label>
							<input type="submit" value="<?php _e('Add Timeslot','bakkbone-florist-companion'); ?>" id="<?php echo $day.'-'.$smethod['instanceid']; ?>-submit" class="button button-primary" />
							<div style="max-width:350px;" id="<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error" class="bkf-error bkf-hidden"><p><?php _e('End time must be greater than start time.','bakkbone-florist-companion'); ?></p></div>
						</form>
						<script>
							jQuery(function($){
								const startElement = document.getElementById("<?php echo $day.'-'.$smethod['instanceid']; ?>-start");
								const endElement = document.getElementById("<?php echo $day.'-'.$smethod['instanceid']; ?>-end");
								
								startElement.addEventListener('change', (event) => {
									var start = document.getElementById("<?php echo $day.'-'.$smethod['instanceid']; ?>-start").value;
									var end = document.getElementById("<?php echo $day.'-'.$smethod['instanceid']; ?>-end").value;
									if( start !== '' && end !== '' && start >= end ) {
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").addClass( 'bkf-invalid' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").removeClass( 'bkf-validated' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").removeClass( 'bkf-invalid' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").removeClass( 'bkf-validated' );                  
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error").addClass( 'bkf-hidden' );
				                  } else {
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").addClass( 'bkf-validated' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error").addClass( 'bkf-hidden' );
									}
								});
								
								endElement.addEventListener('change', (event) => {
									var start = document.getElementById("<?php echo $day.'-'.$smethod['instanceid']; ?>-start").value;
									var end = document.getElementById("<?php echo $day.'-'.$smethod['instanceid']; ?>-end").value;
									if( start !== '' && end !== '' && start >= end ) {
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").addClass( 'bkf-invalid' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").removeClass( 'bkf-validated' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").removeClass( 'bkf-invalid' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").removeClass( 'bkf-validated' );                  
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error").addClass( 'bkf-hidden' );
				                  } else {
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>").addClass( 'bkf-validated' );
										$("#<?php echo $day.'-addts-'.$smethod['instanceid']; ?>-error").addClass( 'bkf-hidden' );
									}
								});
							});
							</script>
						<?php
						foreach($ts as $tslot){
							if($tslot['day'] == $day && $tslot['method'] == $smethod['rateid']){
									echo '<p>'.date("g:i a", strtotime($tslot['start'])).' - '.date("g:i a", strtotime($tslot['end'])).' <em><a href="'.admin_url('admin-ajax.php?action=bkf_ts_del&nonce='.$delnonce.'&id='.$tslot['id']).'">'.__('Delete','bakkbone-florist-companion').'</a></em></p>';
							}else{}
						}
						?></div><?php
					}
			}?>
				</div><?php
		}
		?></div></div><?php
    }
    
	function bkfAddDdTsInit()
	{
		register_setting(
			"bkf_dd_ts_group",// group
			"bkf_dd_timeslots", //setting name
			array('type' => 'array') //sanitize_callback
		);
	}
	
	function bkfDdtsAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_ddts")
		{

		}
	}
	
	function bkfDdtsAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "florist-options_page_bkf_ddts")
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
		
		global $wpdb;
		$wpdb->insert(
		$wpdb->prefix.'bkf_dd_timeslots',
		array(
			'day'	=>	$day,
			'method'=>	$method,
			'start'	=>	$start,
			'end'	=>	$end
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