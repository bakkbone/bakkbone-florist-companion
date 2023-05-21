<?php

/**
 * @author BAKKBONE Australia
 * @package BkfSuburbsOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfSuburbsOptions{

    private $bkf_suburbs_setting = array();

    function __construct(){
		global $bkf_suburbs_db_version;
		global $bkf_suburbs_setting_version;
		$bkf_suburbs_db_version = '1.2';
		$bkf_suburbs_setting_version = '1';
		register_activation_hook( __FILE__, array($this, 'bkf_suburbs_db_init'));
        $this->bkf_suburbs_setting = get_option("bkf_suburbs_setting");
		add_action("plugins_loaded",array($this,"bkf_update_suburbs_db_check"));
        add_action("admin_menu", array($this,"bkf_suburbs_admin_menu"),40);	
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
	    global $bkf_suburbs_setting_version;
	    if ( get_option( 'bkf_suburbs_db_version' ) != $bkf_suburbs_db_version ) {
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
	    if ( get_option( 'bkf_suburbs_setting_version' ) != $bkf_suburbs_setting_version ) {
			$option = get_option('bkf_features_setting');
			unset($option['suburbs_on']);
			update_option('bkf_features_setting', $option);
			update_option('bkf_suburbs_settings_version', $bkf_suburbs_setting_version);
		}
	}

    function bkf_suburbs_admin_menu(){
        $admin_page = add_submenu_page(
        "bkf_options",
        __("Delivery Suburbs","bakkbone-florist-companion"),
        __("Delivery Suburbs","bakkbone-florist-companion"),
        "manage_woocommerce",
        "bkf_suburbs",
        array($this, "bkf_suburbs_page"),
        40
        );
		add_action( 'load-'.$admin_page, array($this, 'bkf_ds_help_tab') );
    }
	
	function bkf_ds_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_ds_help';
		$callback = array($this, 'bkf_ds_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_ds_help(){
		?>
		<h2><?php _e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/florist-options/delivery-suburbs/" target="_blank">https://plugins.bkbn.au/docs/bkf/florist-options/delivery-suburbs/</a>
		<?php
	}
	
    function bkf_suburbs_page()
    {
		$addnonce = wp_create_nonce("suburbs-add");
		$delnonce = wp_create_nonce("suburbs-del");
		
		$sm = bkf_get_shipping_rates();

		$sub = bkf_get_all_suburbs();
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
			<p><?php _e('If a delivery method below has no suburbs attached, then the delivery method will be available for all suburbs within its Zone. "Local pickup" delivery methods will not be displayed here as they do not request a delivery suburb at checkout.', 'bakkbone-florist-companion') ?></p>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
                <?php foreach($sm as $smethod){if(!$smethod['pickup']){?><div class="inside bkf-inside">
					<h2 style="margin:0;text-align:center;color:black;"><?php echo $smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle']; ?></h2>
					<p style="margin-top:0;text-align:center;color:black;"><strong><?php _e('Zone Name', 'bakkbone-florist-companion'); ?>: </strong><?php echo $smethod['zone']; ?><br><strong><?php _e('Area', 'bakkbone-florist-companion'); ?>: </strong><?php echo $smethod['zonelocation']; ?></p>
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
					}}?>
				</div>
		</div><?php
    }
	
}