<?php

/**
 * @author BAKKBONE Australia
 * @package BkfSuburbsOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfSuburbsOptions{

    private $bkf_suburbs_setting = array();

    function __construct(){
		global $bkf_suburbs_db_version;
		$bkf_suburbs_db_version = '1.2';
		register_activation_hook( __FILE__, array($this, 'bkf_suburbs_db_init'));
        $bkffeatures = get_option("bkf_features_setting");
		if($bkffeatures["suburbs_on"] == "1") {
	        $this->bkf_suburbs_setting = get_option("bkf_suburbs_setting");
			add_action("plugins_loaded",array($this,"bkf_update_suburbs_db_check"));
	        add_action("admin_menu", array($this,"bkf_suburbs_admin_menu"));
	        add_action("admin_init",array($this,"bkfSuburbsInit"));
			add_action("admin_footer",array($this,"bkfSuburbsAdminFooter"));
			add_action("admin_enqueue_scripts",array($this,"bkfSuburbsAdminEnqueueScripts"));			
        	add_action('wp_ajax_bkf_suburb_add', array($this, 'bkf_suburb_add') ); 
        	add_action('wp_ajax_bkf_suburb_del', array($this, 'bkf_suburb_del') ); 
		}
    }
	
	function bkf_suburbs_db_init(){
		global $wpdb;
		global $bkf_suburbs_db_version;

		$table_name = $wpdb->prefix . 'bkf_suburbs';
	
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			suburb tinytext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'bkf_suburbs_db_version', $bkf_suburbs_db_version );
		
		$installed_ver = get_option( "bkf_suburbs_db_version" );

		if ( $installed_ver != $bkf_suburbs_db_version ) {

			$table_name = $wpdb->prefix . 'bkf_suburbs';

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				suburb tinytext NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'bkf_suburbs_db_version', $bkf_suburbs_db_version );
		}		
	}
	
	function bkf_update_suburbs_db_check() {
	    global $bkf_suburbs_db_version;
	    if ( get_site_option( 'bkf_suburbs_db_version' ) != $bkf_suburbs_db_version ) {
			global $wpdb;
			global $bkf_suburbs_db_version;

			$table_name = $wpdb->prefix . 'bkf_suburbs';
	
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				method tinytext NOT NULL,
				suburb tinytext NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'bkf_suburbs_db_version', $bkf_suburbs_db_version );
		
			$installed_ver = get_option( "bkf_suburbs_db_version" );

			if ( $installed_ver != $bkf_suburbs_db_version ) {

				$table_name = $wpdb->prefix . 'bkf_suburbs';

				$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					method tinytext NOT NULL,
					suburb tinytext NOT NULL,
					PRIMARY KEY  (id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				update_option( 'bkf_suburbs_db_version', $bkf_suburbs_db_version );
			}
	    }
	}

    function bkf_suburbs_admin_menu(){
        add_submenu_page(
        "bkf_options",//parent slug
        __("Delivery Suburbs","bakkbone-florist-companion"),//page title
        __("Delivery Suburbs","bakkbone-florist-companion"),//menu title
        "manage_options",//capability
        "bkf_suburbs",//menu slug
        array($this, "bkf_suburbs_page"),//callback
        40
        );
    }

    function bkf_suburbs_page()
    {
		$addnonce = wp_create_nonce("suburbs-add");			
		$delnonce = wp_create_nonce("suburbs-del");			
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
			$zone_name = $zone->get_zone_name();
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
					'rateid'		=>	$method_rate_id,
					'zone'			=>	$zone_name
				);
			}
		}
		
		global $wpdb;
		$sub = array();
		$subs = $wpdb->get_results(
			"
				SELECT id, method, suburb
				FROM {$wpdb->prefix}bkf_suburbs
			"
		);
		foreach($subs as $suburb){
			$sub[] = array(
				'id'		=>	$suburb->id,
				'method'	=>	$suburb->method,
				'suburb'	=>	$suburb->suburb
			);
		}
		$subnames = array_column($sub, 'suburb');
		array_multisort($subnames, SORT_ASC, $sub);
		$smid = array();
		foreach($sm as $sms){
			$smid[$sms['rateid']] = array();
			foreach($sub as $subby){
				if($subby['method'] == $sms['rateid']){
					$smid[$sms['rateid']][] = $subby['id'];
				}
			}
		}
		?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Suburbs","bakkbone-florist-companion") ?></h1>
			<p><?php _e('If a delivery method below has no suburbs attached, then the delivery method will be available for all suburbs within its Zone.') ?></p>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
                <?php foreach($sm as $smethod){?><div class="inside bkf-inside">
					<h2 style="margin:0;text-align:center;color:black;"><?php echo $smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle']; ?></h2>
					<p style="margin-top:0;text-align:center;color:black;"><strong><?php _e('Zone: ', 'bakkbone-florist-companion'); ?></strong><?php echo $smethod['zone']; ?></p>
						<form class="bkf-form" id="<?php echo 'addsuburb-'.$smethod['instanceid']; ?>" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_suburb_add" />
							<input type="hidden" name="method" value="<?php echo $smethod['rateid']; ?>" />
							<input type="hidden" name="nonce" value="<?php echo $addnonce; ?>" />
							<label><?php _e('Suburb: ', 'bakkbone-florist-companion'); ?><input type="text" class="bkf-form-control regular-text" required id="<?php echo $smethod['instanceid']; ?>-suburb" name="suburb" /></label>
							<input type="submit" value="<?php _e('Add Suburb','bakkbone-florist-companion'); ?>" id="<?php echo $smethod['rateid']; ?>-submit" class="button button-primary" />
						</form>
						<?php
						if(empty($smid[$smethod['rateid']])){
							echo '<div class="bkf-info"><p>'.__('This delivery method has no suburbs attached, and will be available for any suburb in its delivery Zone.', 'bakkbone-florist-companion').'</p></div>';
						} else{
							echo '<ul class="bkf-list">';
						foreach($sub as $suburbitem){
							if($suburbitem['method'] == $smethod['rateid']){
									echo '<li>'.$suburbitem['suburb'].' <em><a href="'.admin_url('admin-ajax.php?action=bkf_suburb_del&nonce='.$delnonce.'&id='.$suburbitem['id']).'">'.__('Delete','bakkbone-florist-companion').'</a></em></li>';
							}
						}
						echo '</ul>';
					}
						?></div><?php
					}?>
				</div>
		</div><?php
    }
    
	function bkfSuburbsInit()
	{
	}
	
	function bkfSuburbsAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_suburbs")
		{

		}
	}
	
	function bkfSuburbsAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "florist-options_page_bkf_suburbs")
		{
		
		}
	}
	
	function bkf_suburb_add(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "suburbs-add")) {
          exit("No funny business please");
        }
		$method = $_REQUEST['method'];
		$suburb = $_REQUEST['suburb'];
		
		global $wpdb;
		$wpdb->insert(
		$wpdb->prefix.'bkf_suburbs',
		array(
			'method'=>	$method,
			'suburb'=>	$suburb
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

	function bkf_suburb_del(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "suburbs-del")) {
          exit("No funny business please");
        }
		$id = $_REQUEST['id'];
		
		global $wpdb;
		$wpdb->delete(
		$wpdb->prefix.'bkf_suburbs',
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