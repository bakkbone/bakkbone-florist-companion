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
                    $accepturl = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=accept&nonce='.$nonce);
                    $rejecturl = admin_url('admin-ajax.php?action=petals_decision&orderid='.$post_id.'&petalsid='.$pid.'&outcome=reject&nonce='.$nonce.'&code=');
                    echo '<div class="bkfdropdown">
  <button class="bkfdropbtn">'.BKF_PETALS_AR.'</button>
  <div class="bkfdropdown-content">
    <a href="'.$accepturl.'">'.BKF_PETALS_ACCEPT.'</a>
    <div class="bkfdropdownsubmenu">'.BKF_PETALS_REJECT.'
      <div class="bkfdropdown-subcontent">
<a href="'.$rejecturl.'293">'.BKF_PETALS_REJECT_293.'</a>
<a href="'.$rejecturl.'294">'.BKF_PETALS_REJECT_294.'</a>
<a href="'.$rejecturl.'270">'.BKF_PETALS_REJECT_270.'</a>
<a href="'.$rejecturl.'280">'.BKF_PETALS_REJECT_280.'</a>
<a href="'.$rejecturl.'281">'.BKF_PETALS_REJECT_281.'</a>
<a href="'.$rejecturl.'282">'.BKF_PETALS_REJECT_282.'</a>
<a href="'.$rejecturl.'272">'.BKF_PETALS_REJECT_272.'</a>
<a href="'.$rejecturl.'283">'.BKF_PETALS_REJECT_283.'</a>
<a href="'.$rejecturl.'273">'.BKF_PETALS_REJECT_273.'</a>
<a href="'.$rejecturl.'274">'.BKF_PETALS_REJECT_274.'</a>
<a href="'.$rejecturl.'284">'.BKF_PETALS_REJECT_284.'</a>
<a href="'.$rejecturl.'285">'.BKF_PETALS_REJECT_285.'</a>
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
	 			<strong><?php echo BKF_PETALS_ON; ?></strong>
				<?php echo ! empty( $petalson ) ? $petalson : '' ?>
			</p>
		</div>
		<div class="edit_address">
			<p<?php if( empty( $petalson ) ) { echo ' class="none_set"'; } ?>>
	 			<strong><?php echo BKF_PETALS_ON; ?></strong>
				<?php echo ! empty( $petalson ) ? $petalson : '' ?>
			</p>
		</div>
	<?php
	}else{}
    }

}