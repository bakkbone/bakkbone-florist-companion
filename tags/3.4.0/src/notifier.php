<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Notifier
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

class BKF_Notifier{

	function __construct(){
		global $bkf_notifier_version;
		$bkf_notifier_version = 1;
		add_action("plugins_loaded", array($this,"bkf_update_notifier_check"));
		add_action('load-edit.php', [$this, 'bkf_notifier_help_tab'] );
		add_action('load-woocommerce_page_wc-orders', [$this, 'bkf_notifier_help_tab'] );
		$features = get_option('bkf_features_setting');
		if($features['order_notifier']){
			if(is_admin()){
				add_action('admin_print_footer_scripts', [$this, 'bkf_notifier_js'] );
			}
			add_filter('views_edit-shop_order', [$this, 'bkf_notifier_toggle']);
			add_filter("views_woocommerce_page_wc-orders", [$this, 'bkf_notifier_toggle']);
		}
	}
	
	function bkf_notifier_js() {
	    $audiourl = BKF_URL . 'assets/audio/';
		$current = get_current_screen();
		$hpos = OrderUtil::custom_orders_table_usage_is_enabled();
		$screen = $hpos ? wc_get_page_screen_id( 'shop-order' ) : 'edit-shop_order';
		
		if ($hpos) {
			$display = $current->id == $screen && (!isset($_GET['action'])) ? true : false;
		} else {
			$display = $current->id == $screen ? true : false;
		}
		if ( $display ) {
		?>
		<script type="text/javascript" id="order_notifier_js">
			
			function bkfDismiss(button){
				notice = document.getElementById(button).parentElement;
				notice.remove();
			}
			
			function bkfToggleNotifierStatus(){
				var toggle = document.querySelector("#bkfNotifierOn");
				if (toggle.checked == true){
					console.info("<?php esc_html_e('Notifier enabled', 'bakkbone-florist-companion'); ?>");
					notifierInterval = setInterval(runNotifierCheck, 30000);
					var toggleUrl = "<?php echo admin_url('admin-ajax.php?action=notifier_status&status=1&user='.get_current_user_id()); ?>";
					jQuery( function($){ jQuery.ajax({url: toggleUrl, type: 'POST'}); });
				} else {
					console.info("<?php esc_html_e('Notifier disabled', 'bakkbone-florist-companion'); ?>");
					if(typeof notifierInterval !== 'undefined'){
						clearInterval(notifierInterval);
						var toggleUrl = "<?php echo admin_url('admin-ajax.php?action=notifier_status&status=0&user='.get_current_user_id()); ?>";
						jQuery( function($){ jQuery.ajax({url: toggleUrl, type: 'POST'}); });
					}
				}
			}
			
			function runNotifierCheck(){
				jQuery( function($){
					currentTime = Math.floor(Date.now() / 1000);
					ts = currentTime - 30;
					currentTimeDisplay = new Date(currentTime * 1000);
					tsDisplay = new Date(ts * 1000);
			
					currentTimeHours = currentTimeDisplay.getHours();
					currentTimeMinutes = "0" + currentTimeDisplay.getMinutes();
					currentTimeSeconds = "0" + currentTimeDisplay.getSeconds();
					tsHours = tsDisplay.getHours();
					tsMinutes = "0" + tsDisplay.getMinutes();
					tsSeconds = "0" + tsDisplay.getSeconds();
			
					var url = "<?php echo admin_url('admin-ajax.php?action=notifier&timestamp='); ?>" + ts;
					jQuery.ajax({url: url, type: 'POST', success: function(result){
						JSON.parse(result).forEach(bkfDisplay)
				
						function bkfDisplay(order){
							if(order.requires_shipping == 1){
								var text = "<strong><?php echo get_option('bkf_ddi_setting')['ddt']; ?>:</strong> " + order.delivery_date + "<br><strong><?php esc_html_e('Customer Name', 'bakkbone-florist-companion'); ?>:</strong> " + order.billing_name + "<br><strong><?php esc_html_e('Delivery Address', 'bakkbone-florist-companion'); ?>:</strong> " + order.shipping_address + "<br><strong><?php esc_html_e('Value', 'bakkbone-florist-companion'); ?>:</strong> " + order.value + '<br><strong><a href="' + order.url + '" target="_blank"><?php esc_html_e('View Order', 'bakkbone-florist-companion'); ?></a></strong>';
							} else {
								var text = + "<br><strong><?php esc_html_e('Customer Name', 'bakkbone-florist-companion'); ?>:</strong> " + order.billing_name + "<strong><?php esc_html_e('Value', 'bakkbone-florist-companion'); ?>:</strong> " + order.value + '<br><strong><a href="' + order.url + '" target="_blank"><?php esc_html_e('View Order', 'bakkbone-florist-companion'); ?></a></strong>';
							}
							const header = document.querySelector("#order-notifier-toggle");
							var string = Object.prototype.toString.call(result);
							let html = '<div id="' + order.id + '-notice" class="notice notice-success is-dismissible"><h3>' + "<?php esc_html_e('New Order #', 'bakkbone-florist-companion'); ?>" + order.id + '</h3><p>' + text + '</p><button type="button" id="' + order.id + '-dismiss" class="bkf-dismiss notice-dismiss" onclick="bkfDismiss(this.id)"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'bakkbone-florist-companion'); ?></span></button></div>';
							header.insertAdjacentHTML("afterend", html);
						}
						if(JSON.parse(result).length !== 0){
							var audio = document.createElement("audio");
							audio.src = '<?php echo $audiourl . get_option('bkf_audio_setting')['notifier_audio']; ?>';
							audio.loop = false;
							audio.play();
						} else {
							console.log("<?php esc_html_e('No orders found between the following times:', 'bakkbone-florist-companion'); ?>" + ' ' + tsHours + ':' + tsMinutes.substr(-2) + ':' + tsSeconds.substr(-2) + ', ' + currentTimeHours + ':' + currentTimeMinutes.substr(-2) + ':' + currentTimeSeconds.substr(-2) );
						}
					}});
					
			});
			}
			
			window.onload = bkfToggleNotifierStatus();
		</script><?php
		}
	}
	
	function bkf_notifier_toggle($views){
		$user = get_current_user_id();
		$status = get_user_meta($user, 'bkf_notifier_status', true);
		if($status == 1){
			$checked = ' checked';
		} else {
			$checked = '';
		}
		echo '<div style="width:100%;padding:5px;display:flex;justify-content:end;align-items:center;" id="order-notifier-toggle"><h3 style="margin:0 5px 0 0">'.esc_html__('Enable Order Notifier:', 'bakkbone-florist-companion').'</h3><label class="bkf-switch"><input type="checkbox" id="bkfNotifierOn" onclick="bkfToggleNotifierStatus()" '.$checked.'/><span class="bkf-slider round"></span></label></div>';
		return $views;
	}
	
	function bkf_update_notifier_check(){
		global $bkf_notifier_version;
		$current_local = get_option( 'bkf_notifier_version' );
		if ( $current_local !==  $bkf_notifier_version ){
			$option = get_option('bkf_features_setting');
			if(!isset($option['order_notifier'])){
				$option['order_notifier'] = false;
				update_option('bkf_features_setting', $option);
			}
			update_option('bkf_notifier_version', $bkf_notifier_version);
		}
	}
	
	function bkf_notifier_help_tab(){
		$id = 'bkf_notifier_help';
		$callback = [$this, 'bkf_notifier_help'];
		$current = get_current_screen();
		$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'edit-shop_order';
		if ( $current->id == $screen ) {
			$current->add_help_tab( array( 
			   'id' => $id,
			   'title' => __('Documentation','bakkbone-florist-companion'),
			   'callback' => $callback,
			   'priority' => 1
			) );
		}
	}
	
	function bkf_notifier_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/day-to-day/orders-list/" target="_blank">https://docs.floristpress.org/day-to-day/orders-list/</a>
		<?php
	}
	
}