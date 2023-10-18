<?php

/**
 * @author BAKKBONE Australia
 * @package BKF_Suburbs_Core_v1
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Suburbs_Core_v1{

	private $bkf_suburbs_setting = [];

	function __construct(){
		global $bkf_suburbs_db_version;
		global $bkf_suburbs_setting_version;
		$bkf_suburbs_db_version = '1.2';
		$bkf_suburbs_setting_version = '1';
		register_activation_hook( __FILE__, array($this, 'bkf_suburbs_db_init'));
		$this->bkf_suburbs_setting = get_option("bkf_suburbs_setting");
		add_action("plugins_loaded",array($this,"bkf_update_suburbs_db_check"));
		add_action("admin_menu", array($this,"bkf_suburbs_admin_menu"),40);	
		add_filter('woocommerce_package_rates', array( $this, 'bkf_del_suburb_methods' ), PHP_INT_MAX, 2);
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
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/florist-options/delivery-suburbs-classic/" target="_blank">https://plugins.bkbn.au/docs/bkf/florist-options/delivery-suburbs-classic/</a>
		<?php
	}
	
	function bkf_suburbs_page(){
		$nonce = wp_create_nonce("bkf");
		
		$sm = bkf_get_shipping_rates();
		$sub = bkf_get_all_suburbs();
		
		$subnames = array_column($sub, 'suburb');
		array_multisort($subnames, SORT_ASC, $sub);
		
		$smid = [];
		foreach($sm as $sms => $array){
			if($array['type'] == 'local_pickup' || $array['type'] == 'floristpress'){
				unset($sm[$sms]);
			} else {
				$smid[$array['rateid']] = [];
				foreach($sub as $subby){
					if($subby['method'] == $array['rateid']){
						$smid[$array['rateid']][] = $subby['id'];
					}
				}	
			}
		}

		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Delivery Suburbs","bakkbone-florist-companion") ?></h1>
			<p><?php esc_html_e('If a delivery method below has no suburbs attached, then the delivery method will be available for all suburbs within its Zone. "Local pickup" delivery methods will not be displayed here as they do not request a delivery suburb at checkout. "FloristPress Suburbs List" delivery methods will not be displayed here as they have their own suburbs lists provided by FloristPress.', 'bakkbone-florist-companion') ?></p>
			<div style="columns: 500px 3;width:100%;">
				<?php foreach($sm as $smethod){
				    $cost = $smethod['cost'];
				    if(!is_float($cost)){
				        if(str_contains($cost, '[')){
				            $cost = 999999;
				        } else {
		include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';
				            $sum = preg_replace( '/\s+/', '', $smethod['cost'] );
				            $sum = str_replace( wc_get_price_decimal_separator(), '.', $sum );
				            $cost = WC_Eval_Math::evaluate($sum);
				            }
				    }
				    $taxrates = bkf_shipping_tax_rates();
				    if(!empty($taxrates)){
				        foreach($taxrates as $rate){
				            $rateval = $rate['rate'];
				            $ratepc = $rateval / 100;
				            $ratecalc = $ratepc + 1;
				            $cost = $cost * $ratecalc;
				        }
				    }
				    $smethod['cost'] = $cost;
				?><div class="inside bkf-inside" style="break-inside:avoid;margin-top:0;">
					<h4 style="margin:0;text-align:center;color:black;"><?php echo $smethod['title'].' #'.$smethod['instanceid'].'</h4><h2 style="margin:0;text-align:center;color:black;">'.$smethod['usertitle'].' ('.bkf_currency_symbol().number_format($smethod['cost'],2,'.','').')'; ?></h2>
					<p style="margin-top:0;text-align:center;color:black;"><strong><?php esc_html_e('Zone', 'bakkbone-florist-companion'); ?>: </strong><?php echo $smethod['zone']; ?><br><strong><?php esc_html_e('Area', 'bakkbone-florist-companion'); ?>: </strong><?php echo $smethod['zonelocation']; ?></p>
						<form class="bkf-form" id="<?php echo 'addsuburb-'.$smethod['instanceid']; ?>" action="<?php echo admin_url('admin-ajax.php') ?>">
							<input type="hidden" name="action" value="bkf_suburb_add" />
							<input type="hidden" name="method" value="<?php echo $smethod['rateid']; ?>" />
							<?php wp_nonce_field('bkf', 'nonce'); ?>
							<label><?php esc_html_e('Suburb:', 'bakkbone-florist-companion'); ?> <input type="text" class="bkf-form-control regular-text" required id="<?php echo $smethod['instanceid']; ?>-suburb" name="suburb" /></label>
							<input type="submit" value="<?php esc_html_e('Add Suburb','bakkbone-florist-companion'); ?>" id="<?php echo $smethod['rateid']; ?>-submit" class="button button-primary" />
						</form>
						<?php
						if(empty($smid[$smethod['rateid']])){
							echo '<div class="bkf-error"><p style="font-weight:600;">'.sprintf(esc_html__('This delivery method has no suburbs attached, and will be available for any suburb in its delivery Zone\'s area. If you do not want this rate to be available for all suburbs in the area listed above, please disable or delete the delivery method %1shere%2s.', 'bakkbone-florist-companion'), '<a href="'.esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=' ) ).$smethod['zoneid'].'" target="_blank">', '</a>').'</p></div>';
						} else {
							echo '<ul class="bkf-list" style="columns: 200px auto;">';
						foreach($sub as $suburbitem){
							if($suburbitem['method'] == $smethod['rateid']){
									echo '<li>'.stripslashes($suburbitem['suburb']).' <em><a href="'.admin_url('admin-ajax.php?action=bkf_suburb_del&nonce='.$nonce.'&id='.$suburbitem['id']).'">'.esc_html__('Delete','bakkbone-florist-companion').'</a></em></li>';
							}
						}
						echo '</ul>';
					}
						?></div><?php
					}?>
				</div>
		</div></div><?php
	}
	
	function bkf_del_suburb_methods($rates, $package) {
		$sm = bkf_get_shipping_rates();	
		$customer_sub = strtoupper( isset( $_REQUEST['s_city'] ) ? $_REQUEST['s_city'] : ( isset ( $_REQUEST['calc_shipping_city'] ) ? $_REQUEST['calc_shipping_city'] : ( ! empty( $user_city = WC()->customer->get_shipping_city() ) ? $user_city : WC()->countries->get_base_city() ) ) );
		
		foreach($sm as $smethod){
			if($smethod['type'] !== 'floristpress' && $smethod['type'] !== 'local_pickup' && $smethod['hassuburbs']){
				$suburbs = array_map('strtoupper', $smethod['suburbs']);
				
				if(!in_array($customer_sub, $suburbs)){
					unset($rates[$smethod['rateid']]);
				}
			}
		}
		return $rates;
		
	}
	
}