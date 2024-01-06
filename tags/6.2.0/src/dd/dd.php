<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Core
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class BKF_Delivery_Date_Core {

	function __construct(){
		add_action('woocommerce_review_order_before_payment', [$this,'dd_field_init'], 10 );
		add_action('woocommerce_checkout_update_order_meta', [$this, 'dd_order'], 10, 1);
		add_filter('woocommerce_email_order_meta_fields', [$this, 'dd_to_email'], 10, 3 );
		add_filter('woocommerce_order_details_before_order_table', [$this, 'dd_thankyou'], 10 , 1 );
		add_action('woocommerce_after_checkout_validation', [$this, 'dd_checkout_validation'], 10, 2 );
		add_action('wp_footer', [$this, 'dd_checkout_validation_js']);
		add_filter('manage_edit-shop_order_columns', [$this, 'dd_col_init'], 10, 1 );
		add_filter('manage_edit-shop_order_sortable_columns', [$this, 'dd_col_sort'], 10, 1 );
		add_filter('manage_woocommerce_page_wc-orders_columns', [$this, 'dd_col_init'], 10, 1 );
		add_filter('manage_woocommerce_page_wc-orders_sortable_columns', [$this, 'dd_col_sort'], 10, 1 );
		add_action('pre_get_posts', [$this, 'dd_filter_legacy'] );
		add_action('woocommerce_order_query_args', [$this, 'dd_filter']);
		add_action('manage_shop_order_posts_custom_column', [$this, 'dd_col'], 10, 2 );
		add_action('woocommerce_shop_order_list_table_custom_column', [$this, 'dd_col'], 10, 2 );
		add_filter('woocommerce_checkout_required_field_notice', [$this, 'ship_deliver'],10,3);
		add_action('woocommerce_before_checkout_shipping_form', [$this, 'delivery_details'], 20);
		add_filter('woocommerce_order_data_store_cpt_get_orders_query', [$this, 'handle_custom_query_var'], PHP_INT_MAX, 2 );
	}

	function delivery_details(){
		echo '<h3>'.esc_html__('Delivery details', 'bakkbone-florist-companion').'</h3>';
	}

	function ship_deliver($sprintf, $label, $key){
		$newlabel = __('Delivery Recipient', 'bakkbone-florist-companion');
		$label = str_replace('Shipping', $newlabel, $label);
		$sprintf = sprintf( __( '%s is a required field.', 'bakkbone-florist-companion' ), '<strong>' . esc_html( $label ) . '</strong>' );
		return $sprintf;
	}

	function dd_field_init () {
		if( bkf_cart_has_physical() ) {
			$ddtitle = get_option('bkf_ddi_setting')['ddt'];
			$ddvalue = WC()->session->get("delivery_date" ) !== null && WC()->session->get("delivery_date" ) !== '' ? 'value="'.WC()->session->get("delivery_date").'" ' : '';
			?><div class="bkf_dd_fields"><h3 class="bkf_dd_title"><?php echo $ddtitle; ?></h3>
			<p class="form-row form-row-wide form-group validate-required validate-delivery_date" id="delivery_date_field"><label for="delivery_date">
			<?php
			esc_html_e( "We'll schedule your order for: ", "bakkbone-florist-companion");
			?>
			<abbr class="required" title="required">*</abbr></label>
			<input type="text" name="delivery_date" class="delivery_date input-text form-control" id="delivery_date" placeholder="<?php echo $ddtitle; ?>" <?php echo $ddvalue; ?>required autocomplete="off" />
			</p>
			<p class="form-row form-row-wide form-group validate-required validate-delivery_timeslot" id="delivery_timeslot_field"><label for="delivery_timeslot">
			<?php esc_html_e( "Timeslot: ", "bakkbone-florist-companion"); ?>
			<abbr class="required" title="required">*</abbr></label>
				<select name="delivery_timeslot" id="delivery_timeslot" class="delivery_timeslot form-control">
				</select>
			</p>
			</div>
		<?php
		}
	}

	function dd_order ($order_id){
		$order = new WC_Order($order_id);
		if ( isset( $_POST['delivery_date'] ) &&  '' != $_POST['delivery_date'] ) {
			$order->update_meta_data('_delivery_date',  sanitize_text_field( $_POST['delivery_date'] ) );
			$order->update_meta_data('_delivery_timestamp',  (string)strtotime(sanitize_text_field( $_POST['delivery_date'] )) );
		}
		if ( isset( $_POST ['delivery_timeslot'] ) &&  '' != $_POST ['delivery_timeslot'] ) {
			$delivery_timeslot = $_POST['delivery_timeslot'];
			$text = bkf_get_timeslot_string($delivery_timeslot);
			$order->update_meta_data('_delivery_timeslot_id',  sanitize_text_field( $_POST['delivery_timeslot'] ) );
			$order->update_meta_data('_delivery_timeslot',  $text );
		}
		$order->save();
	}

	function dd_to_email ( $fields, $sent_to_admin, $order ) {
		$delivery_date = $order->get_meta('_delivery_date', true );
		$delivery_timeslot_id = $order->get_meta( '_delivery_timeslot_id', true );
		$tsid = $delivery_timeslot_id !== null && $delivery_timeslot_id !== '' && $delivery_timeslot_id ? 'ts'.$delivery_timeslot_id : false;
		$timeslot = $tsid ? bkf_get_timeslots_associative()['ts'.$delivery_timeslot_id] : null;
		$rawtimeslot = $order->get_meta( '_delivery_timeslot', true );
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$tstitle = __('Timeslot', 'bakkbone-florist-companion');

		if ( '' != $delivery_date ) {
			$fields[ 'delivery_date' ] = array(
				'label' => $ddtitle,
				'value' => $delivery_date,
			);
		}
		if ( null !== $timeslot ) {
			$fields[ 'delivery_timeslot' ] = array(
				'label' => $tstitle,
				'value' => date("g:i a", strtotime($timeslot['start'])).' - '.date("g:i a", strtotime($timeslot['end'])),
			);
		} elseif ( '' != $rawtimeslot ) {
			$fields[ 'delivery_timeslot' ] = array(
				'label' => $tstitle,
				'value' => $rawtimeslot,
			);
		}

		return $fields;
	}

	function dd_thankyou ( $order ) {
		$delivery_date = $order->get_meta( '_delivery_date', true );
		$delivery_timeslot_id = $order->get_meta( '_delivery_timeslot_id', true );
		$tsid = $delivery_timeslot_id !== null && $delivery_timeslot_id !== '' && $delivery_timeslot_id ? 'ts'.$delivery_timeslot_id : false;
		$timeslot = $tsid ? bkf_get_timeslots_associative()['ts'.$delivery_timeslot_id] : null;
		$delivery_timeslot = $order->get_meta( '_delivery_timeslot', true );
		$ddtitle = get_option('bkf_ddi_setting')['ddt'];
		$tstitle = __('Timeslot', 'bakkbone-florist-companion');

		if ( '' !== $delivery_date ) {
			echo '<p><strong>' . $ddtitle . ':</strong><br>' . $delivery_date . '</p>';
		}

		if ( null !== $timeslot ) {
			echo '<p><strong>' . $tstitle . ':</strong><br>' . date("g:i a", strtotime($timeslot['start'])).' - '.date("g:i a", strtotime($timeslot['end'])) . '</p>';
		} elseif ( '' !== $delivery_timeslot ) {
			echo '<p><strong>' . $tstitle . ':</strong><br>' . $delivery_timeslot . '</p>';
		}
	}

	function dd_checkout_validation ( $fields, $errors ){
		if( bkf_cart_has_physical() ) {
			$ddtitle = get_option('bkf_ddi_setting')['ddt'];
			$invalidtext = sprintf(__('Please select a valid <strong>%s</strong> via the datepicker.', 'bakkbone-florist-companion'), $ddtitle);
			$notimeslottext = __('Please select a valid <strong>timeslot</strong>.', 'bakkbone-florist-companion');

			if($_POST['delivery_date'] !== ''){
				$day = strtolower(wp_date('l',strtotime($_POST['delivery_date'])));
				$currentshipping = WC()->session->get( 'chosen_shipping_methods' );
				$validts = bkf_get_timeslots_for_order($currentshipping, $day);
			} else {
				$validts = [];
			}
			
			// Invalid if time slot available but not selected
			if( !empty($validts) && $_POST['delivery_timeslot'] == ''){
				$errors->add( 'validation', $notimeslottext );
			}
			
			// Invalid if not an actual date or date format incomplete
			if($_POST['delivery_date'] !== ''){
				if( ! preg_match( '/[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/', $_POST['delivery_date'] ) ){
					$errors->add( 'validation', $invalidtext );
				} elseif(wp_date("l, j F Y", strtotime($_POST['delivery_date'])) !== $_POST['delivery_date'] ){
					$errors->add( 'validation', $invalidtext );
				} else {
					$tz = new DateTimeZone(wp_timezone_string());
					$dateobject = new DateTimeImmutable($_POST['delivery_date'], $tz);
					$currentshipping = WC()->session->get( 'chosen_shipping_methods' );
					$cart = WC()->cart->get_cart();
					$availability = bkf_check_dd_availability($dateobject, $cart);
					
					if(!$availability['pass']) {
						/* translators: %1$s: Delivery date title. %2$s: Error message. */
						$failtext = sprintf(__('<strong>%1$s<strong> error: %2$s. Please select another %1$s via the datepicker.', 'bakkbone-florist-companion'), $ddtitle, $availability['outcome'][2]);
						$errors->add( 'validation', $failtext );
					} else {
					    if(isset($availability['mr'])){
					        if(in_array($currentshipping, $availability['mr'])) {
					            $error = __('Selected delivery method unavailable on this weekday', 'bakkbone-florist-companion');
        						/* translators: %1$s: Delivery date title. %2$s: Error message. */
        						$failtext = sprintf(__('<strong>%1$s<strong> error: %2$s. Please select another %1$s via the datepicker.', 'bakkbone-florist-companion'), $ddtitle, $error);
        						$errors->add( 'validation', $failtext );
					        }
					    }
					    if(isset($availability['msl'])){
					        foreach ($availability['msl'] as $msl) {
					            if($msl[0] == $currentshipping && $msl[1]) {
					                $error = __('Order cutoff has passed for selected delivery method for this date', 'bakkbone-florist-companion');
            						/* translators: %1$s: Delivery date title. %2$s: Error message. */
            						$failtext = sprintf(__('<strong>%1$s<strong> error: %2$s. Please select another %1$s via the datepicker.', 'bakkbone-florist-companion'), $ddtitle, $error);
            						$errors->add( 'validation', $failtext );
					            }
					        }
					    }
					}
					
				}
			} else {
				$errors->add( 'validation', $invalidtext );
			}
		}
	}

	function dd_checkout_validation_js(){
		if( ! is_checkout() ) {
			return;
		}
	?>
	<script id="bkf-dd-val">
	jQuery(function($){
		jQuery( 'body' ).on( 'blur change', '#delivery_date', function(){
			const wrapper = jQuery(this).closest( '.form-row' );
			if( ! /[a-zA-Z]{6,9}\,\ \d{1,2}\ [a-zA-Z]{3,9}\ \d{4}/.test( jQuery(this).val() ) ) {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else {
				wrapper.addClass( 'woocommerce-validated' );
			}
		});
	});
	jQuery(function($){
		jQuery( 'body' ).on( 'blur change', '#delivery_date, #delivery_timeslot', function(){
			const wrapper = jQuery('#delivery_timeslot').closest( '.form-row' );
			const item = document.getElementById("delivery_timeslot");
			if(item.hasAttribute('required') && item.value == '') {
				wrapper.addClass( 'woocommerce-invalid' );
				wrapper.removeClass( 'woocommerce-validated' );
			} else {
				wrapper.addClass( 'woocommerce-validated' );
			}
		});
	});
	</script>
	<?php
	}

	function dd_col_init( $columns ) {
			$columns['bkf_dd'] = __('Delivery Date', 'bakkbone-florist-companion');
			$columns['billing_address'] = __('Customer', 'bakkbone-florist-companion');
			$columns['shipping_address'] = __('Recipient', 'bakkbone-florist-companion');
			return $columns;
	}

	function dd_col_sort( $a ){
		return wp_parse_args( array( 'bkf_dd' => 'deldate'), $a );
	}

	function dd_filter_legacy( $query ) {
		if ( $query->get( 'orderby') == 'deldate' ){
			$meta_query = array(
				'deldate' => array(
					'key' => '_delivery_timestamp',
				),
			);
			$query->set('meta_query', $meta_query);
			$query->set('meta_key', '_delivery_timestamp');
			$query->set('orderby', 'deldate');
			$query->set('tax_query', []);
		}
	}
	
	function dd_filter($query){
		if(isset($_GET['dd_filter'])){
			if($_GET['dd_filter'] !== '' && $_GET['dd_filter'] !== null){
				$filter_dd = $_GET['dd_filter'];
				$filter_ts = strtotime($filter_dd);
				$query['meta_query'][] = [
					'key'			=> '_delivery_timestamp',
					'value'			=> $filter_ts,
					'comparison'	=> 'LIKE'
				];
			}
		}
		return $query;
	}

	function dd_col( $column, $post_or_order_object ) {
	    if(is_int($post_or_order_object)){
	        $order = new WC_Order($post_or_order_object);
	    } else {
	        $order = ( $post_or_order_object instanceof WC_Order ) ? $post_or_order_object : wc_get_order( $post_or_order_object->ID );
	    }
        if($column == 'bkf_dd'){
			$dt = $order->get_meta( '_delivery_timestamp', true );
			if($dt !== ''){
				echo wp_date("l, j F", $dt);
				$timeslot = $order->get_meta( '_delivery_timeslot', true );
				if($timeslot !== null && $timeslot !== ''){
					echo '<br>'.$timeslot;
				}
			}
        }
	}
	
	function handle_custom_query_var( $query, $query_vars ) {
		if ( ! empty( $query_vars['_delivery_timestamp'] ) ) {
			$query['meta_query'][] = array(
				'key' => '_delivery_timestamp',
				'value' => esc_attr( $query_vars['_delivery_timestamp'] ),
			);
		}
		if ( ! empty( $query_vars['_delivery_date'] ) ) {
			$query['meta_query'][] = array(
				'key' => '_delivery_date',
				'value' => esc_attr( $query_vars['_delivery_date'] ),
			);
		}
		return $query;
	}
	
}