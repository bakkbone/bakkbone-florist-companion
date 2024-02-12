<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Options
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class BKF_Delivery_Date_Options{

	function __construct(){
		add_action('admin_head', [$this, 'head_css']);
		add_action('woocommerce_update_options_bkf_dd', [$this, 'save_bkf_dd_settings']);
		add_action('add_meta_boxes', [$this, 'bkf_dd_metabox_init']);
		add_action('save_post', [$this, 'bkf_dd_save_metabox_data']);
		add_action('woocommerce_process_shop_order_meta', [$this, 'bkf_dd_save_metabox_data']);
	}

	function head_css(){
		$sections = [
			'mlt',
			'ddb',
			'cb',
			'ts',
			'ds'
		];
		if (isset($_GET['page']) && $_GET['page'] == 'wc-settings' && isset($_GET['tab']) && $_GET['tab'] == 'bkf_dd' && isset($_GET['section']) && in_array($_GET['section'], $sections)) {
			 ?><style>
			 	.submit {
			 		display: none;
			 	}
			 </style><?php
		}
	}

	function save_bkf_dd_settings(){
		if (isset($_REQUEST['section']) && $_REQUEST['section'] == 'wd') {
			$bkf_sd_setting = $_REQUEST['bkf_sd_setting'];

			$newdd = $_REQUEST['bkf_dd_setting'];
			$bkf_dd_setting = [];
			$weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

			foreach ($weekdays as &$day) {
				$bkf_dd_setting[$day] = isset($newdd[$day]) && $newdd[$day] ? true : false;
			}

			update_option('bkf_dd_setting', $bkf_dd_setting);
			update_option('bkf_sd_setting', $bkf_sd_setting);
		} elseif (isset($_REQUEST['section']) && $_REQUEST['section'] == 'fee') {
			$newdd = $_REQUEST['bkf_ddf_setting'];
			$bkf_ddf_setting = [];
			$bkf_ddf_setting['ddtst'] = isset($newdd['ddtst']) && $newdd['ddtst'] ? true : false;
			$bkf_ddf_setting['ddwft'] = isset($newdd['ddwft']) && $newdd['ddwft'] ? true : false;
			$bkf_ddf_setting['dddft'] = isset($newdd['dddft']) && $newdd['dddft'] ? true : false;
			update_option('bkf_ddf_setting', $bkf_ddf_setting);
		}
	}

	public function bkf_dd_metabox_init(){
		$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		add_meta_box('bkf_dd', __('Delivery Date', 'bakkbone-florist-companion'), [$this, 'bkf_dd_metabox_callback'], $screen, 'side', 'core');
	}

	public function bkf_dd_metabox_callback( $post_or_order_object ){
	    $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		$maxdate = get_option('bkf_ddi_setting')['ddi'];
		$delivery_date = $order->get_meta('_delivery_date', true );
		$delivery_timeslot = $order->get_meta('_delivery_timeslot', true );
		$delivery_timeslot_id = $order->get_meta('_delivery_timeslot_id', true );
		$tsid = $delivery_timeslot_id !== null && $delivery_timeslot_id !== '' && $delivery_timeslot_id ? 'ts'.$delivery_timeslot_id : false;
		$timeslot = $tsid ? bkf_get_timeslots_associative()['ts'.$delivery_timeslot_id] : null;
		$methods = $order->get_shipping_methods();
		$method = '';
		foreach($methods as $v){
			$method = $v->get_method_id().":".$v->get_instance_id();
		}

		if (null !== $delivery_date){
			$dd = ' value="' . $delivery_date . '"';
		} else {
			$dd = '';
		}
		if (null == $delivery_timeslot || '' == $delivery_timeslot){
			$tsp = ' selected';
		} else {
			$tsp = '';
		}

		echo '<input type="hidden" name="bkf_dd_nonce" value="' . wp_create_nonce() . '">';
		?><p style="text-align:center;">
			<?php echo $delivery_date;
			if(null !== $timeslot){
				echo '<br>'.date("g:i a", strtotime($timeslot['start'])).' - '.date("g:i a", strtotime($timeslot['end']));
			} elseif(null !== $delivery_timeslot) {
				echo '<br>'.$delivery_timeslot;
			}
			?>
		</p>
		<input type="text" name="delivery_date" style="width:100%" class="delivery_date input-text form-control" id="delivery_date" placeholder="Delivery Date"<?php echo $dd ?> />
		<select name="delivery_timeslot" style="width:100%;" class="delivery_timeslot form-control" id="delivery_timeslot">
			<option value="" <?php echo $tsp; ?>><?php esc_html_e('Select a timeslot...', 'bakkbone-florist-companion') ?></option>
			<?php
			$day = date("l", strtotime($delivery_date));
			$validts = bkf_get_timeslots_for_order($method,$day);
			foreach($validts as $tslot){
				$id = $tslot['id'];
				$stringts = bkf_get_timeslot_string($id);
				if($stringts == $delivery_timeslot || $id == $delivery_timeslot_id){
					$sel = ' selected';
				} else {
					$sel = '';
				}
				echo '<option value="'.$tslot['id'].'"'.$sel.'>'.$stringts.'</option>';
			} ?>
		</select>
		<p class="description"><em><?php esc_html_e('Timeslot choices will update after order is saved, if delivery date and/or delivery method changed.', 'bakkbone-florist-companion'); ?></em></p>
		<script>
			jQuery(document).ready(function( $ ) {
				jQuery(".delivery_date").datepicker( {
					minDate: 0,
					maxDate: "+<?php echo $maxdate; ?>w",
					dateFormat: "DD, d MM yy",
					hideIfNoPrevNext: true,
					firstDay: 1,
					constrainInput: true,
					beforeShowDay: blockedDates,
					showButtonPanel: true,
					showOtherMonths: true,
					selectOtherMonths: true,
					changeMonth: true,
					changeYear: true,
				} );
  			 var closedDatesList = [<?php
		 		$closeddates = get_option('bkf_dd_closed');
				if( !empty($closeddates)){
				 $i = 0;
				 $len = count($closeddates);
				 foreach($closeddates as $date){
					 $ts = strtotime($date);
					 $jsdate = wp_date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';
			 } else {
					 echo '['.$jsdate.'],';
					 }
					 $i++;
			 };}; ?>];
   			 var fullDatesList = [<?php
		 		$fulldates = get_option('bkf_dd_full');
				if( !empty($fulldates)){
				 $i = 0;
				 $len = count($fulldates);
				 foreach($fulldates as $date){
					 $ts = strtotime($date);
					 $jsdate = wp_date('n,j,Y',$ts);
					 if ($i == $len - 1) {
					 echo '['.$jsdate.']';
				 } else {
					 echo '['.$jsdate.'],';
					 }
					 $i++;
				 };}; ?>];

		 function blockedDates(date) {
			 var w = date.getDay();
			 var m = date.getMonth();
			 var d = date.getDate();
			 var y = date.getFullYear();

			 <?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
			 if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) {
				  return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			  }<?php }; ?>
 			 <?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
			  if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) {
				   return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			   }<?php }; ?>
  			 <?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
			   if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) {
					return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				}<?php }; ?>
   			 <?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) {
					 return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				 }<?php }; ?>
				 <?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
				 if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) {
					  return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				  }<?php }; ?>
	 			 <?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
				  if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) {
					   return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				   }<?php }; ?>
	  			 <?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
				   if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) {
						return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
					}<?php }; ?>

		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [true,"closed","Closed"];
		   }
		 }
		 for (i = 0; i < fullDatesList.length; i++) {
		   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
		   {
			 return [true,"booked","Fully Booked"];
		   }
		 }
		 return [true];
	 }
		 } );
		</script>
		<?php
	}

	function bkf_dd_save_metabox_data( $post_id ) {
		if(isset($_POST['delivery_date'])){
			if ( ! isset( $_POST[ 'bkf_dd_nonce' ] ) ){
						return $post_id;
					} else {
						$nonce = $_POST[ 'bkf_dd_nonce' ];

						if ( ! wp_verify_nonce( $nonce ) )
							return $post_id;

						if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
							return $post_id;

						if ( ! current_user_can( 'manage_woocommerce', $post_id ) )
							return $post_id;

						$order = new WC_Order($post_id);
						$order->update_meta_data( '_delivery_date', wc_sanitize_textarea( $_POST[ 'delivery_date' ] ) );
						$order->update_meta_data( '_delivery_timestamp', (string)strtotime(wc_sanitize_textarea( $_POST[ 'delivery_date' ] )));
						$order->save();
					}
		} else {
			return $post_id;
		}

		if(isset($_POST['delivery_timeslot'])){
			if($_POST['delivery_timeslot'] !== ''){
				$order = new WC_Order($post_id);
				$tsid = $_POST['delivery_timeslot'];
				$thists = bkf_get_timeslots_associative()['ts'.$tsid];
				$text = date("g:i a", strtotime($thists['start'])).' - '.date("g:i a", strtotime($thists['end']));
				$order->update_meta_data( '_delivery_timeslot_id',  sanitize_text_field( $_POST['delivery_timeslot'] ) );
				$order->update_meta_data( '_delivery_timeslot',  $text );
				$order->save();
			} else {
				$order = new WC_Order($post_id);
				$order->delete_meta_data( '_delivery_timeslot_id');
				$order->delete_meta_data( '_delivery_timeslot');
				$order->save();
			}
		}
	}

}