<?php

/**
 * @author BAKKBONE Australia
 * @package BkfAdminNotices
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfAdminNotices{

	function __construct(){
		if (!in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices", array($this, "installwoo"));
		}
		add_action('wp_dashboard_setup', array($this, 'dashwidgets'));
	}

	function installwoo($admin_notice){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div id="message-woocommerce" class="error notice is-dismissible">
			<p>'. sprintf(__('<strong>%s</strong> requires WooCommerce to be installed and activated on your site.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
		
	}
	
	function dashwidgets() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget('bkf_today', __("Today's Deliveries", "bakkbone-florist-companion"), array($this, 'dashtoday'));
		wp_add_dashboard_widget('bkf_recent', __("Recent Orders", "bakkbone-florist-companion"), array($this, 'dashrecent'));
		wp_add_dashboard_widget('bkf_news', BKF_PLUGIN_NAME, array($this, 'dashnews'));
	}
 
	function dashtoday() {
		$todayserver = strtotime('today midnight');
		$todaylocal = strtotime('today midnight '.wp_timezone_string());
		$orders = get_posts(array(
			'post_type' => 'shop_order',
			'post_status' => array('new','accept','processing','completed','scheduled','prepared','collect','out','relayed'),
			'numberposts' => '-1',
			'orderby' => 'meta_value_num',
			'meta_key' => '_delivery_timestamp',
			'meta_query' => array(
				'relation' => 'OR',
				array('key' => '_delivery_timestamp', 'compare' => '=', 'value' => $todayserver ),
				array('key' => '_delivery_timestamp', 'compare' => '=', 'value' => $todaylocal ),
				array('key' => '_delivery_date', 'compare' => '=', 'value' => wp_date("l, j F Y") ),
			)
		));
		if(count($orders) == 0){
			echo '<p>'.__('No deliveries today...', 'bakkbone-florist-companion').'</p>';
		} else {
			echo '<ul>';
			foreach($orders as $order){
				$wcorder = new WC_Order($order->ID);
				$on = $order->ID;
				$recipient = $wcorder->get_formatted_shipping_full_name();
				$suburb = $wcorder->get_shipping_city();
				$url = $wcorder->get_edit_order_url();
				$phone = get_post_meta($order->ID, '_shipping_phone', true);
			
				echo '<li><p><a href="'.$url.'"><strong>#'.$on." - ". $suburb.'</strong></a><br>'.$recipient.' - <a href="tel:'.$phone.'">'.$phone.'</a></p></li>';			
			}
			echo '</ul>';
		}
	}
	
	function dashrecent(){
		$recentorders = wc_get_orders(array(
		    'limit' => 5,
		    'orderby' => 'date',
		    'order' => 'DESC',
			'type' => 'shop_order',
		));
		if(count($recentorders) == 0){
			echo '<p>'.__('No orders yet...', 'bakkbone-florist-companion').'</p>';
		} else {
			echo '<ul>';
			foreach($recentorders as $wcorder){
				$on = $wcorder->get_id();
				$value = $wcorder->get_formatted_order_total();
				$url = $wcorder->get_edit_order_url();
				$suburb = $wcorder->get_shipping_city();
				$phone = get_post_meta($on, '_shipping_phone', true);
				$ts = get_post_meta($on, '_delivery_timestamp', true);
				$dd = wp_date("l, j F Y", $ts);
				$shipping = 0;
				$shipping = $wcorder->needs_shipping_address();
				$wsnonce = wp_create_nonce("bkf_worksheet_pdf");
				$wsurl = admin_url( 'admin-ajax.php?action=bkfdw&order_id=' . $on . '&nonce=' . $wsnonce );
				
				if($shipping){
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on." - ". $value.'</strong></a></h3><p>'.$suburb.' - '.$dd.'</p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
				} else {
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on." - ". $value.'</strong></a></h3></li>';
				}
			
				echo $text;			
			}
			echo '</ul>';
		}
	}
	
	function dashnews(){
		$url = 'https://news.bkbn.au/bkf.xml';
		$feed = bkf_get_rss_feed($url);
		$guid = array_column($feed, 'guid');
		array_multisort($guid, SORT_DESC, SORT_STRING, $feed);
		array_slice($feed, 0, 3, true);
		echo '<div style="text-align:center"><img src="'.BKF_URL.'/assets/img/B22_landscape_250.png" style="max-width:200px;"/><h3>'.__('News and updates from Team BAKKBONE', 'bakkbone-florist-companion').'</h3></div>';
		echo '<ul>';
		foreach($feed as $item){
			$ts = strtotime($item->pubDate);
			$date = wp_date("D j<\s\up>S</\s\up> M 'y, H:i", $ts);
			echo '<li><h3>'.$item->title.'</h3><p>'.$item->description.'</p><p class="date"><em>'.$date.'</em></p></li>';
		}
		echo '</ul>';
	}
	
}