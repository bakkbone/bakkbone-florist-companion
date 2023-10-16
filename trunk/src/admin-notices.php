<?php

/**
 * @author BAKKBONE Australia
 * @package BKF_Admin_Notices
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BKF_Admin_Notices{

	function __construct(){
		add_action('plugins_loaded', array($this, 'check_plugin_compatibility'));
		$features = get_option('bkf_features_setting');
		if($features['excerpt_pa'] == 1 && in_array("breakdance/plugin.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices", array($this, "breakdance_clash"));
		}
		add_action('admin_notices', array($this, 'notices'));
		if (!in_array("woocommerce/woocommerce.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_action("admin_notices", array($this, "installwoo"));
		}
		add_action('wp_dashboard_setup', array($this, 'dashwidgets'));
		if(BKF_VERSION == "3.2.0"){
			add_action("admin_notices", array($this, "upgrade_suburbs"));
		}
	}
	
	function upgrade_suburbs(){
		global $pagenow;
		$suburbs = count(bkf_get_all_suburbs());
		if($pagenow == 'admin.php' && $_GET['page'] == 'bkf_suburbs'){
			echo '<div class="notice notice-bkf"><h1 style="text-transform:uppercase;font-weight:bold;">'.__('This feature is deprecated', 'bakkbone-florist-companion').'</h1><p>'.sprintf(__('The Delivery Suburbs functionality provided by FloristPress is changing. %1sClick here%2s to understand what you need to do.','bakkbone-florist-companion'),'<a href="https://floristpress.canny.io/changelog/the-delivery-suburbs-feature-is-changing" target="_blank">','</a>').'</p><p>'.__('This page, and all data entered on it, will be permanently deleted when you update to v4 (once it is available).', 'bakkbone-florist-companion').'</p></div>';
		} elseif($suburbs) {
			echo '<div class="notice notice-bkf"><h1 style="text-transform:uppercase;font-weight:bold;">'.__('Action Required', 'bakkbone-florist-companion').'</h1><p>'.sprintf(__('The Delivery Suburbs functionality provided by FloristPress is changing. %1sClick here%2s to understand what you need to do.','bakkbone-florist-companion'),'<a href="https://floristpress.canny.io/changelog/the-delivery-suburbs-feature-is-changing" target="_blank">','</a>').'</p><p>'.__('This message will automatically disappear either when you delete all suburbs listed under the previous method, or when you update to v4 (once it is available).', 'bakkbone-florist-companion').'</p></div>';
		}
	}
	
	function check_plugin_compatibility(){
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$all_plugins = get_plugins();
		$plugin_data = get_plugin_data(BKF_FILE);
		$bkftitle = $plugin_data['Name'];
		
		$untested = [];
		$incompatible = [];
		
		foreach($all_plugins as $plugin){
			$tested = $plugin['BKF tested up to'] !== '' && $plugin['BKF tested up to'] !== null ? $plugin['BKF tested up to'] : BKF_VERSION;
			$requires = $plugin['BKF requires at least'] !== '' && $plugin['BKF requires at least'] !== null ? $plugin['BKF requires at least'] : "0.0.0";
			if(bkf_compare_semantic_version(BKF_VERSION, $tested)){
				$untested[] = $plugin;
			}
			if(bkf_compare_semantic_version($requires, BKF_VERSION)){
				$incompatible[] = $plugin;
			}
		}
		
		foreach($untested as $offender){
			/* translators: %1s: conflicting plugin name. %2s: this plugin name. */
			echo '<div class="notice-error notice"><p>'. sprintf(__('<strong>%1s</strong> has not been tested with your current version of %2s – please ask the developer to update their plugin.','bakkbone-florist-companion'), $offender['Title'], $bkftitle).'</p></div>';
		}
		foreach($incompatible as $offender){
			/* translators: %1s: conflicting plugin name. %2s: Required version of this plugin. %3s: this plugin name. */
			echo '<div class="notice-error notice"><p>'. sprintf(__('<strong>%1s</strong> has requires at least version <strong>%2s</strong> of %3s – please update %3s.','bakkbone-florist-companion'), $offender['Title'], $offender['BKF requires at least'], $bkftitle, $bkftitle).'</p></div>';
		}
	}
	
	function breakdance_clash(){
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$all_plugins = get_plugins();
		$bdtitle = $all_plugins['breakdance/plugin.php']['Title'];
		$plugin_data = get_plugin_data(BKF_FILE);
		/* translators: %1s: conflicting plugin name. %2s: this plugin name. %3s: opening link tag. %4s: closing link tag. */
		echo '<div class="notice-error notice"><p>'. sprintf(__('<strong>%1s</strong> already includes the capability to show Short Descriptions in product archives – please disable the %2s Short Description feature %3shere%4s.','bakkbone-florist-companion'), $bdtitle, $plugin_data["Name"], '<a href="'.esc_url( add_query_arg( array( 'page' => 'bkf_options' ), get_admin_url() . 'admin.php' ) ).'">', '</a>').'</p>
		</div>';
	}
	
	function notices(){
		$uid = get_current_user_id();
		$submeta = get_user_meta($uid, 'bkf_sub_notice_dismissed', true);
		
		if(!$submeta){
			/* translators: %1s: subscription link. %2s: dashboard link. %3s: Canny roadmap link. */
			echo '<div class="notice notice-bkf is-dismissible"><p>'.sprintf(__('Want to get notified when updates/improvements to FloristPress are available? <a href="%1s" target="_blank">Click here</a> to subscribe. You can also keep up to date by reading the news feed on your <a href="%2s">dashboard</a>, and by checking <a href="%3s" target="_blank">Canny</a>.','bakkbone-florist-companion'), 'https://plugins.bkbn.au/bkf-subscribe/', admin_url('index.php'), 'https://floristpress.canny.io/').'</p><a class="notice-dismiss" style="text-decoration:none;" href="'.admin_url('admin-ajax.php?action=bkf_sub_notice_dismissed&uid='.$uid).'"></a></div>';
		}
	}

	function installwoo(){
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<div class="notice-error notice">
			<p>'. sprintf(__('<strong>%s</strong> requires WooCommerce to be installed and activated on your site.','bakkbone-florist-companion'), $plugin_data["Name"]).'</p>
		</div>';
	}
	
	function dashwidgets() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget('bkf_today', __("Today's Deliveries", "bakkbone-florist-companion"), array($this, 'dashtoday'));
		wp_add_dashboard_widget('bkf_recent', __("Recent Orders", "bakkbone-florist-companion"), array($this, 'dashrecent'));
		wp_add_dashboard_widget('bkf_news', __("FloristPress", 'bakkbone-florist-companion'), array($this, 'dashnews'));
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
			echo '<p>'.esc_html__('No deliveries today...', 'bakkbone-florist-companion').'</p>';
		} else {
			echo '<ul>';
			foreach($orders as $order){
				$wcorder = new WC_Order($order->ID);
				$on = $order->ID;
				$recipient = $wcorder->get_formatted_shipping_full_name();
				$customer = $wcorder->get_formatted_billing_full_name();
				$suburb = $wcorder->get_shipping_city();
				$url = $wcorder->get_edit_order_url();
				$phone = get_post_meta($order->ID, '_shipping_phone', true);
				$shipping = 0;
				$shipping = $wcorder->needs_shipping_address();
				$pickup = bkf_order_has_physical($order->ID) && !$shipping ? 1 : 0;
				$wsnonce = wp_create_nonce("bkf");
				$wsurl = admin_url( 'admin-ajax.php?action=bkfdw&order_id=' . $on . '&nonce=' . $wsnonce );
								
				if($shipping){
					echo '<li><h3><a href="'.$url.'"><strong>#'.$on." - ". $suburb.'</strong></a></h3><p>'.$recipient.' - <a href="tel:'.$phone.'">'.$phone.'</a></p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';			
				} elseif($pickup) {
					echo '<li><h3><a href="'.$url.'"><strong>#'.$on.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$customer.'</h3><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';			
				} else {
					echo '<li><h3><a href="'.$url.'"><strong>#'.$on.', '.$customer.'</h3></li>';			
				}
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
			echo '<p>'.esc_html__('No orders yet...', 'bakkbone-florist-companion').'</p>';
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
				if(get_post_meta($on, '_delivery_timeslot', false)){
					$ts = implode(get_post_meta($on, '_delivery_timeslot', false));
					$dd .= '<br>'.$ts;
				}
				$shipping = 0;
				$shipping = $wcorder->needs_shipping_address();
				$pickup = bkf_order_has_physical($on) && !$shipping ? 1 : 0;
				$wsnonce = wp_create_nonce("bkf");
				$wsurl = admin_url( 'admin-ajax.php?action=bkfdw&order_id=' . $on . '&nonce=' . $wsnonce );
				
				if($shipping){
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on." - ".$suburb.', '.$value.'</strong></a></h3><p>'.$dd.'</p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
				} elseif ($pickup) {
					$text = '<li><h3><a href="'.$url.'" target="_blank"><strong>#'.$on.' - '.esc_html__('Pickup', 'bakkbone-florist-companion').', '.$value.'</strong></a></h3><p>'.$dd.'</p><p><strong><a href="'.$wsurl.'">'.get_option('bkf_pdf_setting')['ws_title'].'</a></strong></p></li>';
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
		$feed = array_slice($feed, 0, 3, true);
		echo '<div style="text-align:center"><img src="'.BKF_URL.'assets/img/B22_landscape_250.png" style="max-width:150px;"/><h3>'.esc_html__('News and updates from the FloristPress team', 'bakkbone-florist-companion').'</h3></div><ul>';
		foreach($feed as $item){
			$ts = strtotime($item->pubDate);
			$date = wp_date("D j<\s\up>S</\s\up> M 'y, H:i", $ts);
			echo '<li><h3>'.$item->title.'</h3><p>'.$item->description.'</p><p class="date"><em>'.$date.'</em></p></li>';
		}
		echo '</ul><p>'.sprintf(__('You can also keep up to date and request features/integrations on <a href="%s" target="_blank">Canny</a>.'), 'https://floristpress.canny.io/').'</p>';
	}
	
}