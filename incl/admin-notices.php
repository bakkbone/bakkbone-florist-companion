<?php

/**
 * @author BAKKBONE Australia
 * @package BkfAdminNotices
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silent is golden");

/**
 * BkfAdminNotices
**/
class BkfAdminNotices{


	/**
	 * BkfAdminNotices:__construct()
	**/
	function __construct()
	{
		if (!in_array("booster", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices",array($this,"bkfBoosterNotice"));
		}
		if (!in_array("gravityforms/gravityforms.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices",array($this,"bkfGravityformsNotice"));
		}
		if (!in_array("woo-address-book/woocommerce-address-book.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices",array($this,"bkfWooAddressBookNotice"));
		}
		if (!in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices",array($this,"bkfWoocommerceNotice"));
		}
	}
	
	
	/**
	 * BkfAdminNotices:bkfBoosterNotice( $admin_notice)
	**/
	function bkfBoosterNotice( $admin_notice){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div id="message-booster" class="updated notice is-dismissible">
			<p>'. sprintf(__('<strong>%s</strong> recommends Booster for WooCommerce be installed and activated.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
		
	}
	
	
	/**
	 * BkfAdminNotices:bkfGravityformsNotice( $admin_notice)
	**/
	function bkfGravityformsNotice( $admin_notice){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div id="message-gravityforms" class="updated notice is-dismissible">
			<p>'. sprintf(__('<strong>%s</strong> recommends Gravity Forms be installed and activated on your site.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
		
	}
	
	
	/**
	 * BkfAdminNotices:bkfWooAddressBookNotice( $admin_notice)
	**/
	function bkfWooAddressBookNotice( $admin_notice){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div id="message-woo-address-book" class="error notice is-dismissible">
			<p>'. sprintf(__('<strong>%s</strong> requires WooCommerce Address Book to be installed and activated.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
		
	}
	
	
	/**
	 * BkfAdminNotices:bkfWoocommerceNotice( $admin_notice)
	**/
	function bkfWoocommerceNotice( $admin_notice){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div id="message-woocommerce" class="error notice is-dismissible">
			<p>'. sprintf(__('<strong>%s</strong> requires WooCommerce to be installed and activated on your site.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
		
	}
	
	
}
