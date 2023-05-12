<?php

/**
 * @author BAKKBONE Australia
 * @package BkfSameDay
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfSameDay{
	
    function __construct(){
		global $bkf_dd_sd_ms_db_version;
		$bkf_dd_sd_ms_db_version = '1.0';
		register_activation_hook( __FILE__, array($this, 'bkf_dd_sd_ms_db_init'));
		add_action("plugins_loaded",array($this,"bkf_update_sd_db_check"));
        add_action("admin_menu", array($this,"bkf_admin_menu"),3.2);
    	add_action('wp_ajax_bkf_sd_add', array($this, 'bkf_sd_add') );
    	add_action('wp_ajax_bkf_sd_del', array($this, 'bkf_sd_del') );
	}
	
	function bkf_dd_cbs_db_init(){
		global $wpdb;
		global $bkf_dd_sd_ms_db_version;

		$table_name = $wpdb->prefix . 'bkf_dd_sameday_methods';
	
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day tinytext NOT NULL,
			cutoff text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'bkf_dd_sd_ms_db_version', $bkf_dd_sd_ms_db_version );
		
		$installed_ver = get_option( "bkf_dd_sd_ms_db_version" );

		if ( $installed_ver != $bkf_dd_sd_ms_db_version ) {

			$table_name = $wpdb->prefix . 'bkf_dd_sameday_methods';

			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day tinytext NOT NULL,
			cutoff text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'bkf_dd_sd_ms_db_version', $bkf_dd_sd_ms_db_version );
		}		
	}
	
	function bkf_update_sd_db_check() {
	    global $bkf_dd_sd_ms_db_version;
	    if ( get_site_option( 'bkf_dd_sd_ms_db_version' ) != $bkf_dd_sd_ms_db_version ) {
			global $wpdb;
			global $bkf_dd_sd_ms_db_version;

			$table_name = $wpdb->prefix . 'bkf_dd_sameday_methods';
	
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day tinytext NOT NULL,
			cutoff text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'bkf_dd_sd_ms_db_version', $bkf_dd_sd_ms_db_version );
		
			$installed_ver = get_option( "bkf_dd_sd_ms_db_version" );

			if ( $installed_ver != $bkf_dd_sd_ms_db_version ) {

				$table_name = $wpdb->prefix . 'bkf_dd_sameday_methods';

				$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				day tinytext NOT NULL,
				cutoff text NOT NULL,
					PRIMARY KEY  (id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				update_option( 'bkf_dd_sd_ms_db_version', $bkf_dd_sd_ms_db_version );
			}
	    }
	}
	
	function bkf_admin_menu(){
        $sdmpage = add_submenu_page(
        "bkf_dd",
        __("Same Day Delivery Cutoffs","bakkbone-florist-companion"),
        '— ' . __("Method-Specific","bakkbone-florist-companion"),
        "manage_woocommerce",
        "bkf_dd_sd_ms",
        array($this, "bkf_sdms_settings_page"),
        3.2
        );
		add_action( 'load-'.$sdmpage, array($this, 'bkf_ms_help_tab') );
	}
	
	function bkf_ms_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_ms_help';
		$callback = array($this, 'bkf_ms_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_ms_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/same-day/method-specific" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/same-day/method-specific</a>
		<?php
	}
	
	function bkf_sdms_settings_page(){
		$addnonce = wp_create_nonce("sd-add");
		$delnonce = wp_create_nonce("sd-del");
		$wd = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
		global $wpdb;
		$co = array();
		$cutoffs = $wpdb->get_results(
			"
				SELECT id, method, day, cutoff
				FROM {$wpdb->prefix}bkf_dd_sameday_methods
			"
		);
		$sm = array();
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			$zone_name = $zone->get_zone_name();
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
					'rateid'		=>	$method_rate_id,
					'zone'			=>	$zone_name
				);
			}
		}
		foreach($cutoffs as $cutoff){
			$co[$cutoff->method.'-'.$cutoff->day] = array(
				'id'		=>	$cutoff->id,
				'method'	=>	$cutoff->method,
				'day'		=>	$cutoff->day,
				'cutoff'	=>	$cutoff->cutoff
			);
		}
		$wds = get_option('bkf_dd_setting');
		$dms = get_option('bkf_dm_setting');
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Method-Specific Cutoffs","bakkbone-florist-companion") ?></h1>
			<p><?php _e('Here you can add cutoffs per weekday for specific delivery methods. If no cutoff is specified below, your global default same-day cutoff will apply.','bakkbone-florist-companion'); ?></p>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
                <?php foreach($sm as $smethod){?><div class="inside bkf-inside">
					<h2 style="margin:0;text-align:center;color:black;"><?php echo $smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle']; ?></h2>
					<p style="margin-top:0;text-align:center;color:black;"><strong><?php _e('Zone: ', 'bakkbone-florist-companion'); ?></strong><?php echo $smethod['zone']; ?></p>
					<?php foreach($wd as $day){
						if($wds[$day] == false){
							echo '<p>'.ucwords($day).' '.__('disabled','bakkbone-florist-companion').'</p>';
						} else {
							if(!empty($dms[$day]) && in_array($smethod['rateid'],$dms[$day])){
									echo '<p>'.ucwords($day).' '.__('disabled for this method','bakkbone-florist-companion').'</p>';
							} else {
								if(!empty($co[$smethod['rateid'].'-'.$day])){
									$thisco = $co[$smethod['rateid'].'-'.$day];
									echo '<p><strong>'.ucwords($thisco['day']).':</strong> '.date("g:i a", strtotime($thisco['cutoff'])).' <em><a href="'.admin_url('admin-ajax.php?action=bkf_sd_del&nonce='.$delnonce.'&id='.$thisco['id']).'">'.__('Delete','bakkbone-florist-companion').'</a></em></p>';
								} else {
								echo '<form class="bkf-form" id="addsd-'.$smethod['rateid'].'-'.$day.'" action="'.admin_url('admin-ajax.php').'">
									<p style="margin:0"><strong>'.ucwords($day).'</strong></p><input type="hidden" name="action" value="bkf_sd_add" />
									<input type="hidden" name="method" value="'.$smethod['rateid'].'" />
									<input type="hidden" name="nonce" value="'.$addnonce.'" />
									<input type="hidden" name="day" value="'.$day.'" />
									<input type="time" class="bkf-form-control" required id="'.$smethod['rateid'].'-'.$day.'-cutoff" name="cutoff" step="300" />
									<input type="submit" value="'.__('Set Cutoff','bakkbone-florist-companion').'" id="'.$smethod['rateid'].'-'.$day.'-submit" class="button button-primary" />
								</form>';
							}
							}
						}
				}
					echo '</div>';
				}
				echo '</div></div></div>';
			}
	
	function bkf_sd_add(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "sd-add")) {
          exit("No funny business please");
        }
		$method = $_REQUEST['method'];
		$day	= $_REQUEST['day'];
		$cutoff = $_REQUEST['cutoff'];
		
		global $wpdb;
		$wpdb->insert(
		$wpdb->prefix.'bkf_dd_sameday_methods',
		array(
			'method'=>	$method,
			'day'	=>	$day,
			'cutoff'=>	$cutoff
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

	function bkf_sd_del(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "sd-del")) {
          exit("No funny business please");
        }
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete(
		$wpdb->prefix.'bkf_dd_sameday_methods',
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