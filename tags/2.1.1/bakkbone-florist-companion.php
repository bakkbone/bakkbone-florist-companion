<?php

/**
 * Plugin Name: BAKKBONE Florist Companion
 * Plugin URI: https://www.floristwebsites.au/
 * Description: Provides standardised features for floristry websites.
 * Version: 2.1.1
 * Requires at least: 6.0
 * Requires PHP: 7.3
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

require BKF_PATH . "/incl/lib/action-scheduler/action-scheduler.php";
require BKF_PATH . "/incl/lib/dompdf/autoload.inc.php";
require BKF_PATH . "/incl/custom-posts.php";
require BKF_PATH . "/incl/enqueue-styles.php";
require BKF_PATH . "/incl/plugin-options.php";
require BKF_PATH . "/incl/shortcodes.php";
require BKF_PATH . "/incl/admin-notices.php";
require BKF_PATH . "/incl/core.php";
require BKF_PATH . "/incl/pdf/pdf.php";
require BKF_PATH . "/incl/pdf/pdf-options.php";
require BKF_PATH . "/incl/pdf/actions.php";
require BKF_PATH . "/incl/dd/dd.php";
require BKF_PATH . "/incl/dd/dd-options.php";
require BKF_PATH . "/incl/dd/blocks.php";
require BKF_PATH . "/incl/dd/catblocks.php";
require BKF_PATH . "/incl/dd/calendar.php";
require BKF_PATH . "/incl/dd/timeslots.php";
require BKF_PATH . "/incl/order-status.php";
require BKF_PATH . "/incl/petals/petals-options.php";
require BKF_PATH . "/incl/petals/petals.php";
require BKF_PATH . "/incl/petals/email.php";
require BKF_PATH . "/incl/petals/outbound.php";
require BKF_PATH . "/incl/petals/decision.php";
require BKF_PATH . "/incl/petals/messaging.php";
require BKF_PATH . "/incl/emails/status-email.php";
require BKF_PATH . "/incl/suburbs/suburbs.php";
require BKF_PATH . "/incl/suburbs/suburbs-options.php";

function run_bakkbone_florist_companion()
{
	$customposts = new BkfCustomPosts();  
	$enqueueStyles = new BkfEnqueueStyles();  
	$plugin_options = new BkfPluginOptions();  
	$plugin_options = new BkfShortCodes();  
	$admin_notices = new BkfAdminNotices();  
	$bkfcore = new BkfCore();
	$bkfpdfoptions = new BkfPdfOptions();
	$bkfpdfactions = new BkfPdfActions();
    $bkfdd = new BkfDd();
    $bkfddoptions = new BkfDdOptions();
	$bkfddblocks = new BkfDdBlocks();
	$bkfddcatblocks = new BkfDdCBOptions();
	$bkfddcalendar = new BkfDdCalendar();
	$bkfddts = new BkfDdTsOptions();
	$bkforderstatus = new BkfOrderStatus();
	$bkfpetalsoptions = new BkfPetalsOptions();
	$bkfpetals = new BkfPetals();
	$bkfpetalsout = new BkfPetalsOutbound();
	$bkfpetalsdec = new BkfPetalsDecision();
	$bkfpetalsmsg = new BkfPetalsMsg();
	$bkfwcemail = new Bkf_WC_Email();
	$bkfpetalsemail = new Bkf_WC_Petals_Email();
	$bkfsuburbs = new BkfSuburbs();
	$bkfsuburbsoptions = new BkfSuburbsOptions();
}

register_activation_hook( __FILE__, 'bkf_active' );
register_deactivation_hook( __FILE__, 'bkf_inactive' );

function bkf_active(){
	$options = '';
	$features = '';
	$closed = '';
	$full = '';
	$ddi = '';
	$dd = '';
	$sd = '';
	$advanced = '';
	
	$options = get_option('bkf_options_setting');
	$features = get_option('bkf_features_setting');
	$closed = get_option('bkf_dd_closed');
	$full = get_option('bkf_dd_full');
	$ddi = get_option('bkf_ddi_setting');
	$dd = get_option('bkf_dd_setting');
	$sd = get_option('bkf_sd_setting');
	$dm = get_option('bkf_dm_setting');
	$advanced = get_option('bkf_advanced_setting');
	
	if($options == ''){
		update_option('bkf_options_setting',array(
			'card_length'	=>	'250',
			'cs_heading'	=>	__('How about adding...','bakkbone-florist-companion'),
			'noship'		=>	__('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion'),
		));
	}
	if($features == ''){
		update_option('bkf_features_setting',array(
			'excerpt_pa'	=>	false,
			'suburbs_on'	=>	true,
			'petals_on'		=>	false,
			'disable_order_comments'	=>	true
		));
	}
	if($closed == ''){
		update_option('bkf_dd_closed', array());
		update_option('bkf_dd_full', array());
		update_option('bkf_ddi_setting', array(
			'ddi'	=>	'52',
			'ddt'	=>	__("Delivery Date","bakkbone-florist-companion"),
			'ddts'	=>	false
		));
	}
	if($dd == ''){
		update_option('bkf_dd_setting',array(
			'monday'	=>	false,
			'tuesday'	=>	false,
			'wednesday'	=>	false,
			'thursday'	=>	false,
			'friday'	=>	false,
			'saturday'	=>	false,
			'sunday'	=>	false,
		));
	}
	if($sd == ''){
		update_option('bkf_sd_setting',array());
	}
	if($dm == ''){
		update_option('bkf_dm_setting',array());
	}
	if($advanced == ''){
		update_option('bkf_advanced_setting',array(
			'deactivation_purge'	=>	false
		));
	}
}

function bkf_inactive(){
	$setting = get_option('bkf_advanced_setting')['deactivation_purge'];
	if($setting == true){
		$settings = array( 'bkf_options_setting', 'bkf_features_setting', 'bkf_petals_setting', 'bkf_petals_product_setting', 'bkf_dd_closed', 'bkf_dd_full', 'bkf_pdf_setting', 'bkf_ddi_setting', 'bkf_dd_setting', 'bkf_sd_setting', 'bkf_dd_ts_db_version', 'bkf_suburbs_db_version', 'bkf_advanced_setting');
		foreach($settings as $setting){
			delete_option($setting);
		}
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_dd_timeslots" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bkf_suburbs" );
	}
}

run_bakkbone_florist_companion();