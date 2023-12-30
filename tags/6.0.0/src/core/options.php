<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Options
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Options{

	function __construct(){
		global $bkf_options_version;
		$bkf_options_version = 5;
		add_action("plugins_loaded", [$this, "update_options_check"]);
		add_action('woocommerce_update_options_bkf', [$this, 'save_bkf_settings']);
	}
	
	function update_options_check() {
		global $bkf_options_version;
		$current_version = get_option('bkf_options_version');
		if($current_version != $bkf_options_version) {
			if($current_version == '' || $current_version == null || $current_version < 1){
				$bkfoptions = get_option("bkf_options_setting");
				$oldfeatures = get_option("bkf_features_setting");
				$newfeatures = $oldfeatures;
				if($oldfeatures['billing_label_business'] !== null){
					$newfeatures = [
						'excerpt_pa'				=> false,
						'petals_on'					=> false,
						'disable_order_comments'	=> true,
						'order_notifier'			=> false,
						'confirm_email'				=> false];
				} elseif ($oldfeatures['confirm_email'] !== null && $oldfeatures['confirm_email'] !== ''){
					$newfeatures['confirm_email'] = $oldfeatures['confirm_email'];
				} else {
					$newfeatures['confirm_email'] = false;
				}
				update_option('bkf_features_setting', $newfeatures);
				update_option('bkf_options_version', 1);
			}
			if($current_version !== '' && $current_version !== null && $current_version < 2){
				delete_option('bkf_suburbs_db_version');
				delete_option('bkf_suburbs_settings_version');
				global $wpdb;
				$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_suburbs" );
				update_option('bkf_options_version', 2);
			}
			if($current_version !== '' && $current_version !== null && $current_version < 3){
				$features = get_option("bkf_features_setting");
				$features['autoprocess'] = false;
				update_option('bkf_features_setting', $features);
				update_option('bkf_options_version', 3);
			}
			if($current_version !== '' && $current_version !== null && $current_version < 4){
				$features = get_option("bkf_features_setting");
				$features['settingsbar'] = false;
				update_option('bkf_features_setting', $features);
				update_option('bkf_options_version', 4);
			}
			if($current_version !== '' && $current_version !== null && $current_version < 5){
				$sd = get_option('bkf_sd_setting');
				foreach ($sd as $key => $value) {
					$sd[$key.'lead'] = '0';
				}
				update_option('bkf_sd_setting', $sd);
				update_option('bkf_options_version', 5);		
			}
		}
	}
	
	function save_bkf_settings(){
		if ($_REQUEST['section'] == '' || !isset($_REQUEST['section'])) {
			$features = get_option('bkf_features_setting');
			$advanced = get_option('bkf_advanced_setting');
			
			foreach ($features as $key => $value) {
				$features[$key] = $value == 'yes' ? true : false;
			}
			foreach ($advanced as $key => $value) {
				$advanced[$key] = $value == 'yes' ? true : false;
			}
			
			update_option('bkf_features_setting', $features);
			update_option('bkf_advanced_setting', $advanced);
		}
		if (isset($_REQUEST['section']) && $_REQUEST['section'] == 'petals') {
			$petals = get_option('bkf_petals_setting');
			$petals['ppw'] = base64_encode($petals['ppw']);
			update_option('bkf_petals_setting', $petals);
		}
	}
	
}