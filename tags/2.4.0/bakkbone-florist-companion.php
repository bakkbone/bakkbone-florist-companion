<?php

/**
 * Plugin Name: BAKKBONE Florist Companion
 * Plugin URI: https://docs.bkbn.au/v/bkf/
 * Description: Provides standardised features for floristry websites.
 * Version: 2.4.0
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

define("BKF_HELP_TITLE",__('Documentation','bakkbone-florist-companion'));
define("BKF_HELP_SUBTITLE",__('View documentation for this page at: ','bakkbone-florist-companion'));
define("BKF_DELIVERY1",__('Delivery', 'bakkbone-florist-companion'));
define("BKF_DELIVERY2",__('delivery', 'bakkbone-florist-companion'));
define("BKF_DELIVERY3",__('Delivery details', 'bakkbone-florist-companion'));

require BKF_PATH . "/incl/lib/action-scheduler/action-scheduler.php";
require BKF_PATH . "/incl/lib/dompdf/autoload.inc.php";
require BKF_PATH . "/incl/custom-posts.php";
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
	$shortcodes = new BkfShortCodes();  
	$admin_notices = new BkfAdminNotices();  
	$bkfcore = new BkfCore();
	$bkfpickup = new BkfPickup();
	$bkfpdfoptions = new BkfPdfOptions();
	$bkfpdfactions = new BkfPdfActions();
    $bkfdd = new BkfDd();
    $bkfddfilter = new BkfDdFilter();
    $bkfsd = new BkfSameDay();
    $bkfddfees = new BkfDdFees();
    $bkfddfeesds = new BkfDdFeesDS();
    $bkfddfeesoptions = new BkfDdFeesOptions();
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
	}
	if($full == ''){
		update_option('bkf_dd_full', array());
	}
	if($ddi == ''){
		update_option('bkf_ddi_setting', array(
			'ddi'	=>	'52',
			'ddt'	=>	__("Delivery Date","bakkbone-florist-companion")
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
		update_option('bkf_dd_ds_fees',array());
	}
	if($sd == ''){
		update_option('bkf_sd_setting',array());
	}
	if($wf == ''){
		update_option('bkf_wf_setting',array(
		));
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
		$settings = array( 'bkf_options_setting', 'bkf_features_setting', 'bkf_petals_setting', 'bkf_petals_product_setting', 'bkf_dd_closed', 'bkf_dd_full', 'bkf_pdf_setting', 'bkf_ddi_setting', 'bkf_dd_setting', 'bkf_ddf_setting', 'bkf_dd_ds_fees', 'bkf_sd_setting', 'bkf_wf_setting', 'bkf_dd_ts_db_version', 'bkf_suburbs_db_version', 'bkf_advanced_setting');
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

run_bakkbone_florist_companion();