<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPetals
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

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
                    $url = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=accept&nonce='.$nonce);
                    $accepturl = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=accept&nonce='.$nonce);
                    $rejecturl = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=reject&nonce='.$nonce.'&code=');
                    echo '<div class="bkfdropdown">
  <button class="bkfdropbtn">Accept/Reject</button>
  <div class="bkfdropdown-content">
    <a href="'.$accepturl.'">Accept</a>
    <div class="bkfdropdownsubmenu">Reject
      <div class="bkfdropdown-subcontent">
<a href="'.$rejecturl.'293">Cannot deliver flowers</a>
<a href="'.$rejecturl.'294">Don\'t have the required flowers</a>
<a href="'.$rejecturl.'270">We cannot deliver to this location ever</a>
<a href="'.$rejecturl.'280">Cannot deliver to this location today</a>
<a href="'.$rejecturl.'281">Do not have these flowers but could do a florist choice</a>
<a href="'.$rejecturl.'282">Do not have any flowers to meet delivery date</a>
<a href="'.$rejecturl.'272">Need more information to deliver this order</a>
<a href="'.$rejecturl.'283">Do not have this container but could do with a substitution of container</a>
<a href="'.$rejecturl.'273">Do not do this product ever</a>
<a href="'.$rejecturl.'274">There is a problem with this address</a>
<a href="'.$rejecturl.'284">This area is restricted, can go on next run but not this delivery date</a>
<a href="'.$rejecturl.'285">This area is restricted and can\'t be delivered until next week</a>
      </div>
    </div>
  </div>
</div>'
;
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
	 			<strong>Petals Order Number:</strong>
				<?php echo ! empty( $petalson ) ? $petalson : '' ?>
			</p>
		</div>
		<div class="edit_address">
			<p<?php if( empty( $petalson ) ) { echo ' class="none_set"'; } ?>>
	 			<strong>Petals Order Number:</strong>
				<?php echo ! empty( $petalson ) ? $petalson : '' ?>
			</p>
		</div>
	<?php
	}else{}
    }

}