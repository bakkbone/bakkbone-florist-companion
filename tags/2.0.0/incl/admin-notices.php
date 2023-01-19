<?php

/**
 * @author BAKKBONE Australia
 * @package BkfAdminNotices
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfAdminNotices{

	function __construct()
	{
		if (!in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices", array($this,"bkfWoocommerceNotice"));
		}
	}

	function bkfWoocommerceNotice( $admin_notice){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div id="message-woocommerce" class="error notice is-dismissible">
			<p>'. sprintf(__('<strong>%s</strong> requires WooCommerce to be installed and activated on your site.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
		
	}
	
}