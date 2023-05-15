<?php

/**
 * @author BAKKBONE Australia
 * @package BkfNotifier
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfNotifier{

	function __construct()
	{
		global $bkf_notifier_version;
		$bkf_notifier_version = 1;
		add_action("plugins_loaded", array($this,"bkf_update_notifier_check"));
		$features = get_option('bkf_features_setting');
		if($features['order_notifier']){
			if(is_admin()){
				add_action('load-edit.php', array($this, 'bkf_notifier_help_tab') );
				add_action('admin_footer', array($this, 'bkf_notifier_js') );
			}
			add_filter('views_edit-shop_order', array($this, 'bkf_notifier_toggle'));
		}
	}
	
	function bkf_notifier_js() {
		$files = array(
			'01-reveal.wav' => BKF_URL . 'assets/audio/' . '01-reveal.wav',
			'02-bells.wav' => BKF_URL . 'assets/audio/' . '02-bells.wav',
			'03-alert.wav' => BKF_URL . 'assets/audio/' . '03-alert.wav',
			'04-phone.mp3' => BKF_URL . 'assets/audio/' . '04-phone.mp3',
			'05-chime.mp3' => BKF_URL . 'assets/audio/' . '05-chime.mp3',
			'06-message.mp3' => BKF_URL . 'assets/audio/' . '06-message.mp3',
			'07-cute.mp3' => BKF_URL . 'assets/audio/' . '07-cute.mp3',
			'08-roll.mp3' => BKF_URL . 'assets/audio/' . '08-roll.mp3',
			'09-nit.mp3' => BKF_URL . 'assets/audio/' . '09-nit.mp3'
		);
		global $pagenow;
	    if ( $pagenow == 'edit.php' && $_GET['post_type'] == 'shop_order' ) {
		?>
		<script type="text/javascript" id="order_notifier_js">
			
			window.onload = toggleNotifierStatus();
			
			function bkfDismiss(button){
				notice = document.getElementById(button).parentElement;
				notice.remove();
			}
			
			function toggleNotifierStatus(){
				var toggle = document.querySelector("#bkfNotifierOn");
				if (toggle.checked == true){
					console.info("<?php _e('Notifier enabled', 'bakkbone-florist-companion'); ?>");
					notifierInterval = setInterval(runNotifierCheck, 30000);
					var toggleUrl = "<?php echo admin_url('admin-ajax.php?action=notifier_status&status=1&user='.get_current_user_id()); ?>";
					jQuery( function($){ jQuery.ajax({url: toggleUrl, type: 'POST'}); });
				} else {
					console.info("<?php _e('Notifier disabled', 'bakkbone-florist-companion'); ?>");
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
								var text = "<strong><?php echo get_option('bkf_ddi_setting')['ddt']; ?>:</strong> " + order.delivery_date + "<br><strong><?php _e('Customer Name', 'bakkbone-florist-companion'); ?>:</strong> " + order.billing_name + "<br><strong><?php _e('Delivery Address', 'bakkbone-florist-companion'); ?>:</strong> " + order.shipping_address + "<br><strong><?php _e('Value', 'bakkbone-florist-companion'); ?>:</strong> " + order.value + '<br><strong><a href="' + order.url + '" target="_blank"><?php _e('View Order', 'bakkbone-florist-companion'); ?></a></strong>';
							} else {
								var text = + "<br><strong><?php _e('Customer Name', 'bakkbone-florist-companion'); ?>:</strong> " + order.billing_name + "<strong><?php _e('Value', 'bakkbone-florist-companion'); ?>:</strong> " + order.value + '<br><strong><a href="' + order.url + '" target="_blank"><?php _e('View Order', 'bakkbone-florist-companion'); ?></a></strong>';
							}
							const header = document.querySelector("#order-notifier-toggle");
							var string = Object.prototype.toString.call(result);
							let html = '<div id="' + order.id + '-notice" class="notice notice-success is-dismissible"><h3>' + "<?php _e('New Order #', 'bakkbone-florist-companion'); ?>" + order.id + '</h3><p>' + text + '</p><button type="button" id="' + order.id + '-dismiss" class="bkf-dismiss notice-dismiss" onclick="bkfDismiss(this.id)"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'bakkbone-florist-companion'); ?></span></button></div>';
							header.insertAdjacentHTML("afterend", html);
						}
						if(JSON.parse(result).length !== 0){
							var audio = document.createElement("audio");
							audio.src = '<?php echo BKF_URL . 'assets/audio/' . get_option('bkf_audio_setting')['notifier_audio']; ?>';
							audio.loop = false;
							audio.play();
						} else {
							console.log("<?php _e('No orders found between the following times:', 'bakkbone-florist-companion'); ?>" + ' ' + tsHours + ':' + tsMinutes.substr(-2) + ':' + tsSeconds.substr(-2) + ', ' + currentTimeHours + ':' + currentTimeMinutes.substr(-2) + ':' + currentTimeSeconds.substr(-2) );
						}
					}});
					
			});
			}
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
	    echo '<div style="width:100%;padding:5px;display:flex;justify-content:end;align-items:center;" id="order-notifier-toggle"><h3 style="margin:0 5px 0 0">'.__('Enable Order Notifier:', 'bakkbone-florist-companion').'</h3><label class="bkf-switch"><input type="checkbox" id="bkfNotifierOn" onclick="toggleNotifierStatus()" '.$checked.'/><span class="bkf-slider round"></span></label></div>';
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
		$screen = get_current_screen();
		$id = 'bkf_notifier_help';
		$callback = array($this, 'bkf_notifier_help');
		if($screen->post_type == 'shop_order'){
			$screen->add_help_tab( array( 
			   'id' => $id,
			   'title' => BKF_HELP_TITLE,
			   'callback' => $callback,
			   'priority' => 1
			) );
		}
	}
	
	function bkf_notifier_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/day-to-day/orders-list/" target="_blank">https://plugins.bkbn.au/docs/bkf/day-to-day/orders-list/</a>
		<?php
	}
	
}