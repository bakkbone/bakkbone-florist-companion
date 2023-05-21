<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPetals
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfPetals{
  
    function __construct() {
      $bkfoptions = get_option("bkf_features_setting");
      if($bkfoptions["petals_on"] == 1) {
          add_action('manage_shop_order_posts_custom_column', array($this, 'bkf_petals_actions'), 12, 2 );
          add_filter('woocommerce_admin_order_data_after_order_details', array($this, 'bkf_order_meta_petals'));
      }else{};
    }

	function bkf_petals_actions( $column, $post_id ) {
		$bkfoptions = get_option("bkf_petals_setting");
			if ( $column == 'wc_actions' ) {
				$order = wc_get_order( $post_id );
				if ( $order->has_status( 'new' ) ) {
				    $nonce = wp_create_nonce("bkf_petals_decision_nonce");
				    $pid = get_post_meta($post_id,'_petals_on',1);
                    $accepturl = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=accept&nonce='.$nonce);
                    $rejecturl = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=reject&nonce='.$nonce.'&code=');
                    echo '<div class="bkfdropdown">
  <button class="bkfdropbtn">'.__('Accept/Reject', 'bakkbone-florist-companion').'</button>
  <div class="bkfdropdown-content">
    <a href="'.$accepturl.'">'.__('Accept', 'bakkbone-florist-companion').'</a>
    <div class="bkfdropdownsubmenu">'.__('Reject', 'bakkbone-florist-companion').'
      <div class="bkfdropdown-subcontent">
<a href="'.$rejecturl.'293">'.__('Cannot deliver flowers', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'294">'.__('Don\'t have the required flowers', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'270">'.__('We cannot deliver to this location ever', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'280">'.__('Cannot deliver to this location today', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'281">'.__('Do not have these flowers but could do a florist choice', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'282">'.__('Do not have any flowers to meet delivery date', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'272">'.__('Need more information to deliver this order', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'283">'.__('Do not have this container but could do with a substitution of container', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'273">'.__('Do not do this product ever', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'274">'.__('There is a problem with this address', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'284">'.__('This area is restricted, can go on next run but not this delivery date', 'bakkbone-florist-companion').'</a>
<a href="'.$rejecturl.'285">'.__('This area is restricted and can\'t be delivered until next week', 'bakkbone-florist-companion').'</a>
      </div>
    </div>
  </div>
</div>';
			}
		}
	}
	
	function bkf_order_meta_petals( $order ){
	$petalson = $order->get_meta( '_petals_on' );
	if(! empty($petalson)){
	?>
    	<div class="clear"></div>
		<div class="address">
			<p<?php if( empty( $petalson ) ) { echo ' class="none_set"'; } ?>>
	 			<strong><?php _e('Petals Order Number:', 'bakkbone-florist-companion'); ?></strong>
				<?php echo ! empty( $petalson ) ? $petalson : '' ?>
			</p>
		</div>
		<div class="edit_address">
			<p<?php if( empty( $petalson ) ) { echo ' class="none_set"'; } ?>>
	 			<strong><?php _e('Petals Order Number:', 'bakkbone-florist-companion'); ?></strong>
				<?php echo ! empty( $petalson ) ? $petalson : '' ?>
			</p>
		</div>
	<?php
	}else{}
    }

}