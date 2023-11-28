<?php
/**
 * Plugin Name:			FloristPress
 * Plugin URI:			https://docs.floristpress.org/
 * Description:			Provides standardized features for floristry websites – built by florists, for florists.
 * Version:				5.0.1
 * Requires at least:	6.0
 * Requires PHP:		7.4
 * Author:				BAKKBONE Australia
 * Author URI:			https://www.floristpress.org/
 * License:				GNU General Public License (GPL) 3.0 or later
 * License URI:			https://www.gnu.org/licenses/gpl.html
 * Tested up to:		6.4
 * WC tested up to:		8.3.1
 * Text Domain:			bakkbone-florist-companion
 * Domain Path:			/lang/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if (!defined("WPINC")) {
	die;
}

if ( ! function_exists('get_plugin_data') ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
	
define("__BKF_EXEC__", true);
define("__BKF_FILE__", __FILE__);
define("__BKF_VERSION__", get_plugin_data(__BKF_FILE__)['Version']);
define("__BKF_PATH__", dirname(__BKF_FILE__));
define("__BKF_PATH_SRC__", __BKF_PATH__.'/src');
define("__BKF_PATH_CORE__", __BKF_PATH_SRC__.'/core');
define("__BKF_PATH_LIB__", __BKF_PATH__.'/lib');
define("__BKF_URL__", plugins_url("/",__BKF_FILE__));

function bkf_is_woocommerce_active(){
	return in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")));
}
function bkf_is_breakdance_active(){
	return in_array("breakdance/plugin.php", apply_filters("active_plugins", get_option("active_plugins")));
}
function bkf_is_acf_active(){
	return in_array("advanced-custom-fields/acf.php", apply_filters("active_plugins", get_option("active_plugins")));
}
function bkf_is_gravityforms_active(){
	return in_array("gravityforms/gravityforms.php", apply_filters("active_plugins", get_option("active_plugins")));
}

function bkf_enable_bkf_plugin_headers($headers){
	$headers['BKFTested'] = 'BKF tested up to';
	$headers['BKFMinimum'] = 'BKF requires at least';
	return $headers;
}
add_filter('extra_plugin_headers', 'bkf_enable_bkf_plugin_headers');

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __BKF_FILE__, true );
	}
} );

require __BKF_PATH_LIB__ . "/dompdf/autoload.inc.php";
require __BKF_PATH_LIB__ . "/phonenumber/autoload.inc.php";

require __BKF_PATH_CORE__ . "/notifier.php";
require __BKF_PATH_CORE__ . "/order-status.php";
require __BKF_PATH_CORE__ . "/functions.php";
require __BKF_PATH_CORE__ . "/enqueue.php";
require __BKF_PATH_CORE__ . "/options.php";
require __BKF_PATH_CORE__ . "/shortcodes.php";
require __BKF_PATH_CORE__ . "/admin-notices.php";
require __BKF_PATH_CORE__ . "/core.php";
require __BKF_PATH_CORE__ . "/localisation.php";
require __BKF_PATH_CORE__ . "/svg.php";
require __BKF_PATH_CORE__ . "/pickup.php";
require __BKF_PATH_CORE__ . "/ajax.php";
require __BKF_PATH_CORE__ . "/tools.php";

require __BKF_PATH_SRC__ . "/cpt/delivery-suburb.php";
require __BKF_PATH_SRC__ . "/pdf/pdf.php";
require __BKF_PATH_SRC__ . "/pdf/pdf-options.php";
require __BKF_PATH_SRC__ . "/pdf/actions.php";
require __BKF_PATH_SRC__ . "/dd/dd.php";
require __BKF_PATH_SRC__ . "/dd/filter.php";
require __BKF_PATH_SRC__ . "/dd/method-specific.php";
require __BKF_PATH_SRC__ . "/dd/fees/fees.php";
require __BKF_PATH_SRC__ . "/dd/fees/date-specific.php";
require __BKF_PATH_SRC__ . "/dd/fees/fees-options.php";
require __BKF_PATH_SRC__ . "/dd/dd-options.php";
require __BKF_PATH_SRC__ . "/dd/blocks.php";
require __BKF_PATH_SRC__ . "/dd/catblocks.php";
require __BKF_PATH_SRC__ . "/dd/calendar.php";
require __BKF_PATH_SRC__ . "/dd/timeslots.php";
require __BKF_PATH_SRC__ . "/petals/petals-options.php";
require __BKF_PATH_SRC__ . "/petals/petals.php";
require __BKF_PATH_SRC__ . "/petals/email.php";
require __BKF_PATH_SRC__ . "/petals/outbound.php";
require __BKF_PATH_SRC__ . "/petals/messaging.php";
require __BKF_PATH_SRC__ . "/petals/cpt.php";
require __BKF_PATH_SRC__ . "/emails/status-email.php";
require __BKF_PATH_SRC__ . "/emails/override.php";
require __BKF_PATH_SRC__ . "/suburbs/method.php";
require __BKF_PATH_SRC__ . "/pos/phone.php";

define('__BKF_ACF_PATH__', __BKF_PATH__ . '/lib/acf/');
define('__BKF_ACF_URL__', __BKF_URL__ . 'lib/acf/');
include_once(__BKF_ACF_PATH__ . 'acf.php');
add_filter('acf/settings/url', 'BKF_acf_settings_url');
function BKF_acf_settings_url($url){
    return __BKF_ACF_URL__;
}
if(!bkf_is_acf_active()){ 
	add_filter('acf/settings/show_admin', '__return_false');
	add_filter('acf/settings/show_updates', '__return_false', 100);
}

function run_bkf(){
	if (bkf_is_woocommerce_active()) {
		new BKF_CPT_Delivery_Suburb();
		new BKF_Enqueue();
		new BKF_Options();
		new BKF_Shortcodes();
		new BKF_Core();
		new BKF_Local_Pickup();
		new BKF_PDF_Options();
		new BKF_PDF_Actions();
        new BKF_Delivery_Date_Core();
        new BKF_Delivery_Date_Filter();
        new BKF_Delivery_Date_Method_Specific_Lead_Times();
        new BKF_Delivery_Date_Fees_Core();
        new BKF_Delivery_Date_Fees_Date_Specific();
        new BKF_Delivery_Date_Fees_Options();
        new BKF_Delivery_Date_Options();
		new BKF_Delivery_Date_Block();
		new BKF_Delivery_Date_Category_Block();
		new BKF_Delivery_Date_Calendar();
		new BKF_Delivery_Date_Timeslots();
		new BKF_Order_Status();
		new BKF_Petals_Core();
		new BKF_Petals_Options();
		new BKF_Petals_Outbound();
		new BKF_Petals_Messaging();
		new BKF_Petals_CPT();
		new BKF_Petals_Email();
        new BKF_Email_Override();
        new BKF_Email_Status();
		new BKF_Localisation();
		new BKF_Notifier();
		new BKF_Phone_Order();
		new BKF_Ajax();
		new BKF_Tools();
	}
	new BKF_Admin_Notices();
}

register_activation_hook(__BKF_FILE__, 'bkf_active');
register_deactivation_hook(__BKF_FILE__, 'bkf_inactive');

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
	$pdf = '';
	
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
	$pdf = get_option('bkf_pdf_setting');
	
	if($options == ''){
		update_option('bkf_options_setting', array(
			'card_length'	=>	'250',
			'cs_heading'	=>	__('How about adding...', 'bakkbone-florist-companion'),
			'noship'		=>	__('You have selected a suburb or region we do not deliver to.', 'bakkbone-florist-companion'),
		));
	}
	if($features == ''){
		update_option('bkf_features_setting', array(
			'excerpt_pa'				=> false,
			'petals_on'					=> false,
			'disable_order_comments'	=> true,
			'order_notifier'			=> false,
			'confirm_email'				=> false,
			'autoprocess'				=> false,
			'settingsbar'				=> false
		));
	}
	if($closed == ''){
		update_option('bkf_dd_closed', []);
	}
	if($full == ''){
		update_option('bkf_dd_full', []);
	}
	if($ddi == ''){
		update_option('bkf_ddi_setting', array(
			'ddi'	=>	'8',
			'ddt'	=>	__('Delivery Date', 'bakkbone-florist-companion')
		));
	}
	if($dd == ''){
		update_option('bkf_dd_setting', array(
			'monday'		=> false,
			'tuesday'		=> false,
			'wednesday'		=> false,
			'thursday'		=> false,
			'friday'		=> false,
			'saturday'		=> false,
			'sunday'		=> false,
			'mondaylead'	=> 0,
			'tuesdaylead'	=> 0,
			'wednesdaylead'	=> 0,
			'thursdaylead'	=> 0,
			'fridaylead'	=> 0,
			'saturdaylead'	=> 0,
			'sundaylead'	=> 0
		));
	}
	if($ddf == ''){
		update_option('bkf_ddf_setting', array(
			'ddtst'	=> false,
			'ddwft'	=> false,
			'dddft'	=> false
		));
	}
	if($dsf == ''){
		update_option('bkf_dd_ds_fees', []);
	}
	if($sd == ''){
		update_option('bkf_sd_setting', []);
	}
	if($wf == ''){
		update_option('bkf_wf_setting', []);
	}
	if($dm == ''){
		update_option('bkf_dm_setting', []);
	}
	if($advanced == ''){
		update_option('bkf_advanced_setting', array(
			'deactivation_purge'	=> false
		));
	}
	if($localisation == ''){
		update_option('bkf_localisation_setting', array(
			'billing_label_business'				=> __('Business Name', 'bakkbone-florist-companion'),
			'global_label_state'					=> __('State/Territory', 'bakkbone-florist-companion'),
			'global_label_suburb'					=> __('Suburb', 'bakkbone-florist-companion'),
			'global_label_postcode'					=> __('Postcode', 'bakkbone-florist-companion'),
			'global_label_country'					=> __('Country', 'bakkbone-florist-companion'),
			'global_label_phone'					=> __('Phone', 'bakkbone-florist-companion'),
			'delivery_label_business'				=> __('Business/Hospital/Hotel Name', 'bakkbone-florist-companion'),
			'delivery_description_business'			=> __('For hospitals/hotels/etc., please include ward/room information if known', 'bakkbone-florist-companion'),
			'delivery_label_notes'					=> __('Anything we need to know about the address?', 'bakkbone-florist-companion'),
			'delivery_description_notes'			=> __('eg. gate code, fence, dog, etc.', 'bakkbone-florist-companion'),
			'additional_description_cardmessage'	=> __("We'll include this with your gift. Maximum %s characters.", 'bakkbone-florist-companion'),
			'csheading'								=> __('How about adding...','bakkbone-florist-companion'),
			'noship'								=> __('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion')
		));
	}
	if($pdf == ''){
		update_option('bkf_pdf_setting', array(
			'page_size'		=> 'a4',
			'inv_title'		=> __('Tax Invoice', 'bakkbone-florist-companion'),
			'inv_text'		=> '',
			'ws_title'		=> __('Worksheet', 'bakkbone-florist-companion'),
			'inv_sn'		=> get_bloginfo('name'),
			'inv_a1'		=> get_option('woocommerce_store_address'),
			'inv_a2'		=> get_option('woocommerce_store_address_2'),
			'inv_sub'		=> get_option('woocommerce_store_city'),
			'inv_state'		=> '',
			'inv_pc'		=> get_option('woocommerce_store_postcode'),
			'inv_phone'		=> '',
			'inv_eml'		=> get_bloginfo('admin_email'),
			'inv_web'		=> get_bloginfo('url'),
			'inv_tax_label'	=> '',
			'inv_tax_value'	=> '',
		));
	}
}

function bkf_inactive(){
	$setting = get_option('bkf_advanced_setting')['deactivation_purge'];
	if($setting == true){
		$settings = [
			'bkf_options_setting',
			'bkf_features_setting',
			'bkf_audio_setting',
			'bkf_petals_setting',
			'bkf_petals_product_setting',
			'bkf_dd_closed',
			'bkf_dd_full',
			'bkf_pdf_setting',
			'bkf_ddi_setting',
			'bkf_dd_setting',
			'bkf_dm_setting',
			'bkf_ddf_setting',
			'bkf_dd_ds_fees',
			'bkf_sd_setting',
			'bkf_wf_setting',
			'bkf_dd_ts_db_version',
			'bkf_dd_cb_db_version',
			'bkf_suburbs_db_version',
			'bkf_advanced_setting',
			'bkf_dd_sd_ms_db_version',
			'bkf_localisation_setting',
			'bkf_localisation_version',
			'bkf_suburbs_settings_version',
			'bkf_options_version',
			'bkf_notifier_version',
		];
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