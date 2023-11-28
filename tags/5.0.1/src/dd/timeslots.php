<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Timeslots
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

class BKF_Delivery_Date_Timeslots{

	function __construct(){
		global $bkf_dd_ts_db_version;
		$bkf_dd_ts_db_version = '1.3';
		register_activation_hook( __BKF_FILE__, [$this, 'bkf_dd_ts_db_init']);
		add_action("admin_menu", [$this, "bkf_admin_menu"], 7);
		add_action("admin_init", [$this, "bkfAddDdTsInit"]);
		add_action("plugins_loaded", [$this, "bkf_update_db_check"]);
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
		[$this, 'bkf_dd_ts_page'],
		7
		);
		add_action( 'load-'.$admin_page, [$this, 'bkf_ts_help_tab'] );
	}
	
	function bkf_ts_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_ts_help';
		$callback = [$this, 'bkf_ts_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_ts_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/timeslots/" target="_blank">https://docs.floristpress.org/dd/timeslots/</a>
		<?php
	}
	
	function bkf_dd_ts_page(){
		$nonce = wp_create_nonce("bkf");			
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		$daysoff = get_option('bkf_dm_setting') !== '' ? get_option('bkf_dm_setting') : [];
		$ts = bkf_get_timeslots();
		
		$tslist = [];
		
		foreach($days as $day => $on){
			if($on){
				$tslist[$day] = [];
				foreach($ts as $tslot){
					if($day == $tslot['day']){
						$tslist[$day][] = $tslot;
					}
				}
			}
		}
		
		$daymethods = [];
		foreach($tslist as $day => $slots){
			$methods = [];
			foreach($slots as $thisslot){
				$methods[] = $thisslot['method'];
			}
			$daymethods[$day] = $methods;
		}
		
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Delivery Timeslots","bakkbone-florist-companion") ?></h1>
			<p><?php esc_html_e('Only weekdays enabled on the Delivery Dates page will be displayed below. Any method within a day with no timeslots added will not require timeslots at checkout.','bakkbone-florist-companion') ?><br><em><?php esc_html_e('Fees are optional - leave fee blank if not required for a timeslot.','bakkbone-florist-companion'); ?></em></p>
			<form class="bkf-form" id="addform" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_ts_add" />
							<?php wp_nonce_field('bkf', 'nonce'); ?>
								<select id="add-day" name="day" class="bkf-form-control" required>
									<option value="" disabled selected><?php esc_html_e('Select a day...','bakkbone-florist-companion'); ?></option>
									<?php
									foreach($days as $day => $on){
										if($on == 1){
											echo '<option value="'.$day.'">'.ucwords($day).'</option>';
										}
									}
										?>
									</select>
							<select disabled class="bkf-form-control" name="method" id="add-method" required>
									<option value="" disabled selected><?php esc_html_e('Select a delivery method...','bakkbone-florist-companion'); ?></option>
							</select>
							<label><?php esc_html_e('Start: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="add-start" name="start" required /></label>
							<label><?php esc_html_e('End: ', 'bakkbone-florist-companion'); ?><input type="time" class="bkf-form-control" id="add-end" name="end" required /></label>
							<label><?php esc_html_e('Fee: ', 'bakkbone-florist-companion'); ?><div class="bkf-input-icon"><input type="text" placeholder="***.**" class="bkf-form-control" id="add-fee" name="fee" pattern="\d+\.\d{2,}" /><i><?php bkf_currency_symbol(true); ?></i></div></label>
							<input type="submit" value="<?php esc_html_e('Add Timeslot','bakkbone-florist-companion'); ?>" id="add-submit" class="button button-primary" />
							<div style="max-width:350px;" id="add-error" class="bkf-error bkf-hidden"><p><?php esc_html_e('End time must be greater than start time.','bakkbone-florist-companion'); ?></p></div>
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
									  let defaultOption = new Option('<?php esc_html_e('Select a delivery method...', 'bakkbone-florist-companion'); ?>', '');
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
										jQuery("#addform").addClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );
										jQuery("#add-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										jQuery("#addform").removeClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );				  
										jQuery("#add-error").addClass( 'bkf-hidden' );
								  } else {
										jQuery("#addform").addClass( 'bkf-validated' );
										jQuery("#add-error").addClass( 'bkf-hidden' );
									}
								});
								
								endElement.addEventListener('change', (event) => {
									var start = startElement.value;
									var end = endElement.value;
									if( start !== '' && end !== '' && start >= end ) {
										jQuery("#addform").addClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );
										jQuery("#add-error").removeClass( 'bkf-hidden' );
									} else if(start == '' || end == '') {
										jQuery("#addform").removeClass( 'bkf-invalid' );
										jQuery("#addform").removeClass( 'bkf-validated' );				  
										jQuery("#add-error").addClass( 'bkf-hidden' );
								  } else {
										jQuery("#addform").addClass( 'bkf-validated' );
										jQuery("#add-error").addClass( 'bkf-hidden' );
									}
								});
							});
							</script>
			<div style="columns: 500px 3;width:100%;">
				<?php
				foreach($days as $day => $on){
					if($on == 1){?><div class="inside bkf-inside" style="break-inside:avoid;margin-top:0;margin-left:0;margin-right:0;">
						<h2 style="margin-top:0;text-align:center;"><?php echo ucwords($day); ?></h2>
						<?php foreach($sm as $smethod){
							if(in_array($smethod['rateid'], $daymethods[$day])){
								echo '<div class="bkf-form" id="'.$day.'-'.$smethod['instanceid'].'"><p style="margin:0;"><strong>'.$smethod['title'].' #'.$smethod['instanceid'].': </strong>'.$smethod['usertitle'].'</p>';
								foreach($ts as $tslot){
									if($tslot['day'] == $day && $tslot['method'] == $smethod['rateid']){
											echo '<p>'.date("g:i a", strtotime($tslot['start'])).' - '.date("g:i a", strtotime($tslot['end']));
											if($tslot['fee'] !== '' && $tslot['fee'] !== null){
												echo ', '.bkf_currency_symbol().$tslot['fee'];
											}
											echo ' <em><a href="'.admin_url('admin-ajax.php?action=bkf_ts_del&nonce='.$nonce.'&id='.$tslot['id']).'">'.esc_html__('Delete','bakkbone-florist-companion').'</a></em></p>';
										}
									}
									?></div><?php
								}
							}?>
						</div><?php
					}
		}
		?></div></div></div><?php
	}
	
	function bkfAddDdTsInit(){
		register_setting(
			"bkf_dd_ts_group",
			"bkf_dd_timeslots",
			array('type' => 'array')
		);
	}
	
}