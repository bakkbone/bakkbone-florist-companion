<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Same_Day
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Delivery_Date_Method_Specific_Lead_Times{
	
	function __construct(){
		global $bkf_dd_sd_ms_db_version;
		$bkf_dd_sd_ms_db_version = 2;
		register_activation_hook( __BKF_FILE__, [$this, 'bkf_dd_sd_ms_db_init']);
		add_action("plugins_loaded", [$this,"bkf_update_sd_db_check"]);
		add_action("admin_menu", [$this,"bkf_admin_menu"], 2.2);
	}
	
	function bkf_dd_sd_ms_db_init(){
		global $wpdb;
		global $bkf_dd_sd_ms_db_version;

		$table_name = $wpdb->prefix . 'bkf_dd_sameday_methods';
	
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			method tinytext NOT NULL,
			day tinytext NOT NULL,
			cutoff text NOT NULL,
			`leadtime` smallint NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'bkf_dd_sd_ms_db_version', $bkf_dd_sd_ms_db_version );	
	}
	
	function bkf_update_sd_db_check() {
		if ( get_option( 'bkf_dd_sd_ms_db_version', 0 ) < 1 ) {
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

			update_option( 'bkf_dd_sd_ms_db_version', 1 );
		}
		
		if ( get_option( 'bkf_dd_sd_ms_db_version' ) < 2 ) {
			global $wpdb;
			global $bkf_dd_sd_ms_db_version;
			$table_name = $wpdb->prefix . 'bkf_dd_sameday_methods';
			
			$sql = "ALTER TABLE $table_name
			ADD COLUMN `leadtime` SMALLINT NOT NULL;
			";
						
			$wpdb->query( $sql );
			
			update_option( 'bkf_dd_sd_ms_db_version', 2 );
		}
	}
	
	function bkf_admin_menu(){
		$sdmpage = add_submenu_page(
		"bkf_dd",
		__("Lead Times","bakkbone-florist-companion"),
		'— ' . esc_html__("Method Lead Times","bakkbone-florist-companion"),
		"manage_woocommerce",
		"bkf_dd_sd_ms",
		[$this, 'bkf_sdms_settings_page'],
		2.2
		);
		add_action( 'load-'.$sdmpage, [$this, 'bkf_ms_help_tab'] );
	}
	
	function bkf_ms_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_ms_help';
		$callback = [$this, 'bkf_ms_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_ms_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/method-specific/" target="_blank">https://docs.floristpress.org/dd/method-specific/</a>
		<?php
	}
	
	function bkf_sdms_settings_page(){
		$nonce = wp_create_nonce("bkf");
		$wd = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
		global $wpdb;
		$co = bkf_get_method_specific_leadtimes();
		$sm = [];
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
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
		$wds = get_option('bkf_dd_setting');
		$dms = get_option('bkf_dm_setting');
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Method-Specific Lead Times","bakkbone-florist-companion") ?></h1>
			<p><?php esc_html_e('Here you can add lead times per weekday for specific delivery methods. If no lead time is specified below for a specific day/method, your global defaults will apply.','bakkbone-florist-companion'); ?></p>
			<div style="display:grid;grid-template-columns:auto auto;width:100%;">
				<?php foreach($sm as $smethod){?><div class="inside bkf-inside">
					<h2 style="margin:0;text-align:center;color:black;"><?php echo $smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle']; ?></h2>
					<p style="margin-top:0;text-align:center;color:black;"><strong><?php esc_html_e('Zone: ', 'bakkbone-florist-companion'); ?></strong><?php echo $smethod['zone']; ?></p>
					<div style="columns: 100px 3;">
					<?php foreach($wd as $day){
						if($wds[$day] == false){
							echo '<p style="break-inside:avoid;margin-top:0;">'.sprintf(__('%s disabled globally','bakkbone-florist-companion'), ucwords($day)).'</p>';
						} else {
							if(!empty($dms[$day]) && in_array($smethod['rateid'],$dms[$day])){
									echo '<p style="break-inside:avoid;margin-top:0;">'.sprintf(__('%s disabled for this method','bakkbone-florist-companion'), ucwords($day)).'</p>';
							} else {
								if(!empty($co[$smethod['rateid'].'-'.$day])){
									$thisco = $co[$smethod['rateid'].'-'.$day];
									echo '<p style="break-inside:avoid;margin-top:0;"><strong>'.ucwords($thisco['day']).':</strong> '.sprintf(__('%1s, %2s day(s) ahead', 'bakkbone-florist-companion'), date("g:i a", strtotime($thisco['cutoff'])), $thisco['leadtime']).' <em><a href="'.admin_url('admin-ajax.php?action=bkf_sd_del&nonce='.$nonce.'&id='.$thisco['id']).'">'.esc_html__('Reset','bakkbone-florist-companion').'</a></em></p>';
								} else {
									$maxlead = get_option('bkf_ddi_setting')['ddi'] * 7 - 1;
									/* translators: %1s: cutoff time. %2s: lead days. */
								echo '<form style="break-inside:avoid;margin-top:0;" class="bkf-form" id="addsd-'.$smethod['rateid'].'-'.$day.'" action="'.admin_url('admin-ajax.php').'"><p style="margin:0"><strong>'.ucwords($day).'</strong><br>('.sprintf(__('%1s, %2s day(s) ahead', 'bakkbone-florist-companion'), date("g:i a", strtotime(get_option('bkf_sd_setting')[$day])), get_option('bkf_sd_setting')[$day.'lead']).')</p><input type="hidden" name="action" value="bkf_sd_add" />
									<input type="hidden" name="method" value="'.$smethod['rateid'].'" />'.wp_nonce_field('bkf', 'nonce', true, false).'
									<input type="hidden" name="day" value="'.$day.'" />
									<input type="time" class="bkf-form-control" required id="'.$smethod['rateid'].'-'.$day.'-cutoff" name="cutoff" step="300" />
									<input type="number" class="bkf-form-control" required id="'.$smethod['rateid'].'-'.$day.'-leadtime" name="leadtime" step="1" min="0" max="'.$maxlead.'" />
									<input type="submit" value="'.esc_html__('Set Cutoff','bakkbone-florist-companion').'" id="'.$smethod['rateid'].'-'.$day.'-submit" class="button button-primary" />
								</form>';
							}
							}
						}
				}
					echo '</div></div>';
				}
				echo '</div></div></div>';
			}
		
}