<?php

/**
 * Plugin Name: BAKKBONE Florist Companion
 * Plugin URI: https://docs.bkbn.au/v/bkf/
 * Description: Provides standardized features for floristry websites.
 * Version: 2.6.4
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: BAKKBONE Australia
 * Author URI: https://www.bakkbone.com.au/
 * License: GNU General Public License (GPL) 3.0 or later
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Text Domain: bakkbone-florist-companion
**/

if (!defined("WPINC")){
	die;
}

define("BKF_EXEC",true);

define("BKF_DEBUG",false);

define("BKF_FILE",__FILE__);

define("BKF_PATH",dirname(__FILE__));

define("BKF_URL",plugins_url("/",__FILE__));

require BKF_PATH . "/incl/functions.php";
require BKF_PATH . "/incl/lib/action-scheduler/action-scheduler.php";
require BKF_PATH . "/incl/lib/dompdf/autoload.inc.php";
require BKF_PATH . "/incl/cpt/dsposts.php";
require BKF_PATH . "/incl/enqueue.php";
require BKF_PATH . "/incl/options.php";
require BKF_PATH . "/incl/shortcodes.php";
require BKF_PATH . "/incl/admin-notices.php";
require BKF_PATH . "/incl/core.php";
require BKF_PATH . "/incl/pickup.php";
require BKF_PATH . "/incl/pdf/pdf.php";
require BKF_PATH . "/incl/pdf/pdf-options.php";
require BKF_PATH . "/incl/pdf/actions.php";
require BKF_PATH . "/incl/dd/dd.php";
require BKF_PATH . "/incl/dd/filter.php";
require BKF_PATH . "/incl/dd/sameday.php";
require BKF_PATH . "/incl/dd/fees/fees.php";
require BKF_PATH . "/incl/dd/fees/date-specific.php";
require BKF_PATH . "/incl/dd/fees/fees-options.php";
require BKF_PATH . "/incl/dd/dd-options.php";
require BKF_PATH . "/incl/dd/blocks.php";
require BKF_PATH . "/incl/dd/catblocks.php";
require BKF_PATH . "/incl/dd/calendar.php";
require BKF_PATH . "/incl/dd/timeslots.php";
require BKF_PATH . "/incl/localisation.php";
require BKF_PATH . "/incl/order-status.php";
require BKF_PATH . "/incl/petals/petals-options.php";
require BKF_PATH . "/incl/petals/petals.php";
require BKF_PATH . "/incl/petals/email.php";
require BKF_PATH . "/incl/petals/outbound.php";
require BKF_PATH . "/incl/petals/decision.php";
require BKF_PATH . "/incl/petals/messaging.php";
require BKF_PATH . "/incl/petals/cpt.php";
require BKF_PATH . "/incl/emails/status-email.php";
require BKF_PATH . "/incl/suburbs/suburbs.php";
require BKF_PATH . "/incl/suburbs/suburbs-options.php";
require BKF_PATH . "/incl/notifier.php";
require BKF_PATH . "/incl/svg.php";

define( 'BKF_ACF_PATH', BKF_PATH . '/incl/lib/acf/' );
define( 'BKF_ACF_URL', BKF_URL . '/incl/lib/acf/' );
include_once( BKF_ACF_PATH . 'acf.php' );
add_filter('acf/settings/url', 'bkf_acf_settings_url');
function bkf_acf_settings_url( $url ) {
    return BKF_ACF_URL;
}
if(!in_array('advanced-custom-fields/acf.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
	add_filter('acf/settings/show_admin', '__return_false');
	add_filter('acf/settings/show_updates', '__return_false', 100);
}

function run_bkf()
{
	$dsposts = new BkfDsPosts();  
	$enqueueStyles = new BkfEnqueueStyles();  
	$plugin_options = new BkfPluginOptions();  
	$shortcodes = new BkfShortCodes();  
	$admin_notices = new BkfAdminNotices();  
	$core = new BkfCore();
	$pickup = new BkfPickup();
	$pdfoptions = new BkfPdfOptions();
	$pdfactions = new BkfPdfActions();
    $dd = new BkfDd();
    $ddfilter = new BkfDdFilter();
    $sd = new BkfSameDay();
    $ddfees = new BkfDdFees();
    $ddfeesds = new BkfDdFeesDS();
    $ddfeesoptions = new BkfDdFeesOptions();
    $ddoptions = new BkfDdOptions();
	$ddblocks = new BkfDdBlocks();
	$ddcatblocks = new BkfDdCBOptions();
	$ddcalendar = new BkfDdCalendar();
	$ddts = new BkfDdTsOptions();
	$orderstatus = new BkfOrderStatus();
	$petalsoptions = new BkfPetalsOptions();
	$petals = new BkfPetals();
	$petalsout = new BkfPetalsOutbound();
	$petalsdec = new BkfPetalsDecision();
	$petalsmsg = new BkfPetalsMsg();
	$petalsCPT = new BkfPOPosts();
	$wcemail = new Bkf_WC_Email();
	$petalsemail = new Bkf_WC_Petals_Email();
	$suburbs = new BkfSuburbs();
	$suburbsoptions = new BkfSuburbsOptions();
	$localisation = new BkfLocalisation();
	$notifier = new BkfNotifier();
}

add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );

register_activation_hook( __FILE__, 'bkf_active' );
register_deactivation_hook( __FILE__, 'bkf_inactive' );

function bkf_active(){
	$options = '';
	$features = '';
	$closed = '';
	$full = '';
	$ddi = '';
	$dd = '';
	$ddf = '';
	$dsf = '';
	$sd = '';
	$wf = '';
	$dm = '';
	$advanced = '';
	$localisation = '';
	
	$options = get_option('bkf_options_setting');
	$features = get_option('bkf_features_setting');
	$closed = get_option('bkf_dd_closed');
	$full = get_option('bkf_dd_full');
	$ddi = get_option('bkf_ddi_setting');
	$dd = get_option('bkf_dd_setting');
	$ddf = get_option('bkf_ddf_setting');
	$dsf = get_option('bkf_dd_ds_fees');
	$sd = get_option('bkf_sd_setting');
	$wf = get_option('bkf_wf_setting');
	$dm = get_option('bkf_dm_setting');
	$advanced = get_option('bkf_advanced_setting');
	$localisation = get_option('bkf_localisation_setting');
	
	if($options == ''){
		update_option('bkf_options_setting', array(
			'card_length'	=>	'250',
			'cs_heading'	=>	default_csheading,
			'noship'		=>	default_noship,
		));
	}
	if($features == ''){
		update_option('bkf_features_setting', array(
			'excerpt_pa'	=>	false,
			'suburbs_on'	=>	true,
			'petals_on'		=>	false,
			'disable_order_comments'	=>	true,
			'order_notifier'	=>	false
		));
	}
	if($closed == ''){
		update_option('bkf_dd_closed', array());
	}
	if($full == ''){
		update_option('bkf_dd_full', array());
	}
	if($ddi == ''){
		update_option('bkf_ddi_setting', array(
			'ddi'	=>	'8',
			'ddt'	=>	BKF_DELIVERY4
		));
	}
	if($dd == ''){
		update_option('bkf_dd_setting', array(
			'monday'	=>	false,
			'tuesday'	=>	false,
			'wednesday'	=>	false,
			'thursday'	=>	false,
			'friday'	=>	false,
			'saturday'	=>	false,
			'sunday'	=>	false
		));
	}
	if($ddf == ''){
		update_option('bkf_ddf_setting', array(
			'ddtst'	=>	false,
			'ddwft'	=>	false,
			'dddft'	=>	false
		));
	}
	if($dsf == ''){
		update_option('bkf_dd_ds_fees', array());
	}
	if($sd == ''){
		update_option('bkf_sd_setting', array());
	}
	if($wf == ''){
		update_option('bkf_wf_setting', array(
		));
	}
	if($dm == ''){
		update_option('bkf_dm_setting', array());
	}
	if($advanced == ''){
		update_option('bkf_advanced_setting', array(
			'deactivation_purge'	=>	false
		));
	}
	if($localisation == ''){
		update_option('bkf_localisation_setting', array(
			'billing_label_business' => default_billing_label_business,
			'global_label_state' => default_global_label_state,
			'global_label_postcode' => default_global_label_postcode,
			'global_label_country' => default_global_label_country,
			'global_label_phone' => default_global_label_phone,
			'delivery_label_business' => default_delivery_label_business,
			'delivery_description_business' => default_delivery_description_business,
			'delivery_label_notes' => default_delivery_label_notes,
			'delivery_description_notes' => default_delivery_description_notes,
			'additional_description_cardmessage' => default_additional_description_cardmessage,
			'csheading' => default_csheading,
			'noship' => default_noship
		));
	}
}

function bkf_inactive(){
	$setting = get_option('bkf_advanced_setting')['deactivation_purge'];
	if($setting == true){
		$settings = array( 'bkf_options_setting', 'bkf_features_setting', 'bkf_petals_setting', 'bkf_petals_product_setting', 'bkf_dd_closed', 'bkf_dd_full', 'bkf_pdf_setting', 'bkf_ddi_setting', 'bkf_dd_setting', 'bkf_ddf_setting', 'bkf_dd_ds_fees', 'bkf_sd_setting', 'bkf_wf_setting', 'bkf_dd_ts_db_version', 'bkf_suburbs_db_version', 'bkf_advanced_setting', 'bkf_localisation_setting');
		foreach($settings as $setting){
			delete_option($setting);
		}
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_dd_timeslots" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_dd_catblocks" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_dd_sameday_methods" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_suburbs" );
	}
}

run_bkf();