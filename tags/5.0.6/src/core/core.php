<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Core
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

class BKF_Core{
	
	private $del1 = '';
	private $del2 = '';
	
	function __construct() {
		add_action('wp_head', [$this, 'sig_head']);
		add_action('wp_footer', [$this, 'sig_shutdown'], 999);
		add_filter('admin_footer_text', [$this, 'admin_footer'], 10, 1);
		$bkffeatures = get_option('bkf_features_setting');
		if($bkffeatures['disable_order_comments']){
			add_filter('woocommerce_checkout_fields', [$this, 'remove_order_comments']);
		}
		if($bkffeatures['excerpt_pa']) {
			add_action( 'woocommerce_after_shop_loop_item_title', [$this, 'add_excerpt_pa'] );
		};
		if($bkffeatures['confirm_email']) {
			add_filter( 'woocommerce_checkout_fields', [$this, 'add_email_verification_field_checkout'], 1, 1 );
			add_action( 'woocommerce_checkout_process', [$this, 'matching_email_addresses'] );
		};
		if($bkffeatures['autoprocess']) {
			add_action('woocommerce_thankyou', [$this, 'auto_process']);
		}
		add_action( 'woocommerce_checkout_process', [$this, 'validation'] );
		add_filter( 'plugin_action_links_bakkbone-florist-companion/bakkbone-florist-companion.php', [$this, 'bkf_settings_link'] );
		add_filter( 'woocommerce_shipping_package_name' , [$this, 'bkf_shipping_to_delivery'], 10, 3);
		add_filter( 'gettext' , [$this, 'bkf_translate_reply'], PHP_INT_MAX, 1 );
		add_filter( 'ngettext' , [$this, 'bkf_translate_reply'], PHP_INT_MAX, 1 );
		add_filter( 'woocommerce_billing_fields' , [$this, 'bkf_override_billing_fields'] );
		add_filter( 'woocommerce_shipping_fields' , [$this, 'bkf_override_shipping_fields']);
		add_filter( 'woocommerce_admin_billing_fields' , [$this, 'bkf_override_billing_fields_admin'] );
		add_filter( 'woocommerce_admin_shipping_fields' , [$this, 'bkf_override_shipping_fields_admin']);
		add_filter( 'woocommerce_email_order_meta_fields', [$this, 'bkf_notes_email'], 10, 3);
		add_filter( 'woocommerce_order_details_after_order_table', [$this, 'bkf_ot_thankyou'], PHP_INT_MAX , 1 );
		add_filter( 'woocommerce_order_details_after_customer_details', [$this, 'bkf_cd_thankyou'], PHP_INT_MAX , 1 );
		add_action( 'woocommerce_checkout_update_order_meta' , [$this, 'bkf_checkout_field_update_order_meta'] );
		add_action( 'add_meta_boxes', [$this, 'bkf_cm_metabox_init'] );
		add_action( 'save_post', [$this, 'bkf_cm_save_metabox_data'] );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', [$this, 'bkf_editable_order_meta_shipping'] );
		add_action( 'woocommerce_process_shop_order_meta', [$this, 'bkf_save_general_details'], 10, 2);
		add_filter( 'woocommerce_checkout_fields' , [$this, 'add_card_message_field'] );
		add_action( 'woocommerce_after_checkout_billing_form', [$this, 'card_message_js']);
		add_filter( 'woocommerce_product_cross_sells_products_heading', [$this, 'bkf_add_cs_heading'], 10, 1 );
		add_filter( 'woocommerce_cart_no_shipping_available_html', [$this, 'noship_message'] );
		add_filter( 'woocommerce_no_shipping_available_html', [$this, 'noship_message'] );
		add_action( 'woocommerce_new_order', [$this, 'bkf_link_guest_order'], 10, 1 );
		add_filter( 'admin_bar_menu', [$this, 'bkf_replace_wordpress_howdy'], PHP_INT_MAX, 1 );
		add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
		add_action( 'wp_footer', [$this, 'session_script']);
		
		$this->del1 = __('Delivery', 'bakkbone-florist-companion');
		$this->del2 = __('delivery', 'bakkbone-florist-companion');
	}
	
	function bkf_replace_wordpress_howdy( $wp_admin_bar ) {
		$user_id = get_current_user_id();
		$current_user = wp_get_current_user();

		if ( ! $user_id ) {
			return;
		}

		if ( current_user_can( 'read' ) ) {
			$profile_url = get_edit_profile_url( $user_id );
		} elseif ( is_multisite() ) {
			$profile_url = get_dashboard_url( $user_id, 'profile.php' );
		} else {
			$profile_url = false;
		}
		
		$time = wp_date('H');
		
		if($time < "12") {
			/* translators: %s: Current user's display name. */
			$greeting = __('Good morning, %s', 'bakkbone-florist-companion');
		} elseif($time >= "12" && $time < "18") {
			/* translators: %s: Current user's display name. */
			$greeting = __('Good afternoon, %s', 'bakkbone-florist-companion');
		} elseif($time >= "18") {
			/* translators: %s: Current user's display name. */
			$greeting = __('Good evening, %s', 'bakkbone-florist-companion');
		} else {
			$greeting = __('Hi, %s', 'bakkbone-florist-companion');
		}
		$avatar = get_avatar( $user_id, 26 );
		$howdy = sprintf( $greeting, '<span class="display-name">' . $current_user->display_name . '</span>' );
		$class = empty( $avatar ) ? '' : 'with-avatar';

		$wp_admin_bar->add_node(
			array(
				'id'	 => 'my-account',
				'parent' => 'top-secondary',
				'title'  => $howdy . $avatar,
				'href'   => $profile_url,
				'meta'   => array(
					'class' => $class,
				),
			)
		);
	}
	
	function sig_head() {
		if ( ! defined( '__ADD_BKF_HTML_SIGNATURE__' ) ) {
			define( '__ADD_BKF_HTML_SIGNATURE__', true );
		}
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data(__BKF_FILE__);
		echo '<meta name="generator" content="'.$plugin_data["Name"].'">';
	}
	
	function sig_shutdown() {
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data(__BKF_FILE__);
		if ( defined( '__ADD_BKF_HTML_SIGNATURE__' ) ) {
			echo "\n<!-- ".sprintf(__('Created with %s', 'bakkbone-florist-companion'), $plugin_data["Name"])." -->\n";
		}
	}
	
	function admin_footer($text){
		$plugin_data = get_plugin_data(__BKF_FILE__);
		$text = '<em>'.sprintf(__('Thank you for creating with <a href="%1s" target="_blank">%2s</a> and <a href="%3s" target="_blank">%4s</a>.', 'bakkbone-florist-companion'), $plugin_data["PluginURI"], $plugin_data["Name"], 'https://wordpress.org/', __('WordPress', 'bakkbone-florist-companion')).'</em>';
		return $text;
	}

	function remove_order_comments( $checkout_fields ) {
		unset( $checkout_fields[ 'order' ][ 'order_comments' ] );
		return $checkout_fields;
	}

	function bkf_settings_link( $links ) {
		$url = esc_url( add_query_arg( array( 'page' => 'bkf_options' ), get_admin_url() . 'admin.php' ) );
		$settings_link = "<a href='$url'>" . esc_html__( 'Settings','bakkbone-florist-companion' ) . '</a>';
		$docs_link = "<a href='https://docs.floristpress.org/'>" . esc_html__('Documentation','bakkbone-florist-companion') . '</a>';
		$bkf_support_link = "<a href='https://wordpress.org/support/plugin/bakkbone-florist-companion/'>" . esc_html__( 'Support','bakkbone-florist-companion' ) . '</a>';
		array_push( $links, $settings_link, $docs_link, $bkf_support_link );
		return $links;
	}
	
	function bkf_shipping_to_delivery($package_name, $i, $package){
		return sprintf( _nx( 'Delivery', 'Delivery %d', ( $i + 1 ), 'shipping packages', 'shipping-i18n' ), ( $i + 1 ) );
	}

	function bkf_translate_reply($translated) {
		$translated = str_replace('Shipping', $this->del1, $translated);
		$translated = str_replace('shipping', $this->del2, $translated);
		return $translated;
	}
	
	function bkf_override_billing_fields( $fields ) {
		$fields['billing_company']['label'] = get_option('bkf_localisation_setting')['billing_label_business'];
		$fields['billing_city']['label'] = get_option('bkf_localisation_setting')['global_label_suburb'];
		$fields['billing_state']['label'] = get_option('bkf_localisation_setting')['global_label_state'];
		$fields['billing_postcode']['label'] = get_option('bkf_localisation_setting')['global_label_postcode'];
		$fields['billing_country']['label'] = get_option('bkf_localisation_setting')['global_label_country'];
		return $fields;
	}
	
	function bkf_override_billing_fields_admin( $fields ) {
		$fields['company']['label'] = get_option('bkf_localisation_setting')['billing_label_business'];
		$fields['city']['label'] = get_option('bkf_localisation_setting')['global_label_suburb'];
		$fields['state']['label'] = get_option('bkf_localisation_setting')['global_label_state'];
		$fields['postcode']['label'] = get_option('bkf_localisation_setting')['global_label_postcode'];
		$fields['country']['label'] = get_option('bkf_localisation_setting')['global_label_country'];
		return $fields;
	}
	
	function bkf_override_shipping_fields( $fields ) {
		$fields['shipping_company']['label'] = get_option('bkf_localisation_setting')['delivery_label_business'];
		$fields['shipping_company']['description'] = get_option('bkf_localisation_setting')['delivery_description_business'];
		$fields['shipping_city']['label'] = get_option('bkf_localisation_setting')['global_label_suburb'];
		$fields['shipping_state']['label'] = get_option('bkf_localisation_setting')['global_label_state'];
		$fields['shipping_postcode']['label'] = get_option('bkf_localisation_setting')['global_label_postcode'];
		$fields['shipping_country']['label'] = get_option('bkf_localisation_setting')['global_label_country'];
		$fields['shipping_phone'] = array(
			'label'			=> get_option('bkf_localisation_setting')['global_label_telephone'],
			'placeholder'	=> get_option('bkf_localisation_setting')['global_label_telephone'],
			'required'		=> true,
			'class'			=> array("form-row-wide"),
			'type'			=> 'tel',
			'validate'		=> array( 'phone' ),
			'autocomplete'	=> 'tel',
			'priority'		=> 100,
		 );
		$fields['shipping_notes'] = array(
			'label'			=> get_option('bkf_localisation_setting')['delivery_label_notes'],
			'required'		=> false,
			'class'			=> array('form-row-wide'),
			'clear'			=> true,
			'type'			=> 'textarea',
			'description'	=> get_option('bkf_localisation_setting')['delivery_description_notes'],
			'priority'		=> 110,
			'default'		=> stripslashes(stripslashes(WC()->session->get('shipping_notes')))
		 );
		 return $fields;
	}
	
	function bkf_override_shipping_fields_admin( $fields ) {
		$fields['company']['label'] = get_option('bkf_localisation_setting')['delivery_label_business'];
		$fields['city']['label'] = get_option('bkf_localisation_setting')['global_label_suburb'];
		$fields['state']['label'] = get_option('bkf_localisation_setting')['global_label_state'];
		$fields['postcode']['label'] = get_option('bkf_localisation_setting')['global_label_postcode'];
		$fields['country']['label'] = get_option('bkf_localisation_setting')['global_label_country'];
		return $fields;
	}
	
	function bkf_notes_email( $fields, $sent_to_admin, $order ) {
		$fields['_shipping_notes'] = array(
			'label' => __('Delivery Notes', 'bakkbone-florist-companion'),
			'value' => stripslashes($order->get_meta( '_shipping_notes', true )),
		);
		$fields['_card_message'] = array(
			'label' => __('Card Message', 'bakkbone-florist-companion'),
			'value' => nl2br(stripslashes($order->get_meta( '_card_message', true ))),
		);
		return $fields;
	}
	
	function bkf_ot_thankyou ( $order ) {
		$cm = nl2br(stripslashes($order->get_meta( '_card_message', true )));

		if ( '' !== $cm ) {
			echo '<p><strong>' . esc_html__('Card Message', 'bakkbone-florist-companion') . ':</strong><br>' . $cm . '</p>';
		}
		
	}
	
	function bkf_cd_thankyou ( $order ) {
		$sn = stripslashes($order->get_meta( '_shipping_notes', true ));
		
		if ( '' !== $sn ) {
			echo '<p><strong>' . esc_html__('Delivery Notes', 'bakkbone-florist-companion') . ':</strong><br>' . $sn . '</p>';
		}
		
	}
	
	function bkf_checkout_field_update_order_meta( $order_id ) {
		$order = new WC_Order($order_id);
		if ( ! empty( $_POST['shipping_notes'] ) ) {
			$order->update_meta_data( '_shipping_notes', wc_sanitize_textarea( $_POST['shipping_notes'] ) );
		}
		if ( ! empty( $_POST['card_message'] ) ) {
			$order->update_meta_data( '_card_message', wc_sanitize_textarea( $_POST['card_message'] ) );
		}
		$order->save();
	}
	
	function bkf_cm_metabox_init(){
		$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		add_meta_box('bkf_cm', __('Card Message', 'bakkbone-florist-companion'), [$this, 'bkf_cm_metabox_callback'], $screen, 'side', 'core');
	}
	
	public function bkf_cm_metabox_callback( $post_or_order_object ){
		$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		
		$cardmessage = stripslashes($order->get_meta( '_card_message', true ));
		echo '<input type="hidden" name="bkf_cm_nonce" value="' . wp_create_nonce() . '">';
		?><textarea style="font-family:monospace;width:100%;" name="card_message" rows="6" class="card_message input-text form-control" id="card_message" maxlength="<?php echo get_option('bkf_options_setting')['card_length'] ?>" placeholder="<?php esc_html_e('Card Message', 'bakkbone-florist-companion'); ?>"><?php echo esc_html( $cardmessage ) ?></textarea>
		<?php  
	}
	
	function bkf_cm_save_metabox_data( $post_id ){
		if ( isset( $_POST['card_message'] ) ){
			if (!isset( $_POST[ 'bkf_cm_nonce' ])){
						return $post_id;
					} else {
						$nonce = $_POST[ 'bkf_cm_nonce' ];

						if ( ! wp_verify_nonce( $nonce ) )
							return $post_id;

						if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
							return $post_id;

						if ( ! current_user_can( 'manage_woocommerce' ) )
							return $post_id;
						
						$order = new WC_Order($post_id);
						$order->update_meta_data( '_card_message', wc_sanitize_textarea( $_POST[ 'card_message' ] ) );
						$order->save();
					}
		} else {
			return $post_id;
		}
	}

	function bkf_editable_order_meta_shipping( $order ){
		$shippingnotes = stripslashes($order->get_meta( '_shipping_notes' ));
		?>
			<div class="address">
				<p <?php if( empty( $shippingnotes ) ) { echo 'class="none_set"'; } ?>>
		 			<strong><?php esc_html_e('Delivery Notes', 'bakkbone-florist-companion'); ?>:</strong>
					<?php echo ! empty( $shippingnotes ) ? $shippingnotes : '' ?>
				</p>
			</div>
			<div class="edit_address">
				<?php
					woocommerce_wp_textarea_input( array(
						'id'			=> '_shipping_notes',
						'label'			=> __('Delivery Notes', 'bakkbone-florist-companion'),
						'wrapper_class'	=> 'form-field-wide',
						'class'			=> 'input-text',
						'style'			=> 'width:100%',
						'value'			=> $shippingnotes,
						'description'	=> get_option('bkf_localisation_setting')['delivery_description_notes']
					) );
				?>
			</div>
		<?php
	}
	
	function bkf_save_general_details( $order_id, $post_or_order_object ){
		$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		
		$order->update_meta_data( '_shipping_notes', wc_sanitize_textarea( $_POST[ '_shipping_notes' ] ) );
		$order->update_meta_data( '_card_message', wc_sanitize_textarea( $_POST[ 'card_message' ] ) );
		$order->save();
	}
	
	function add_card_message_field( $fields ) {
		$bkfoptions = get_option("bkf_options_setting");
		$cardlength = $bkfoptions["card_length"];

		if( bkf_cart_has_physical() ) {
			$fields['order']['card_message'] = array(
				'label'			=> __('Card Message', 'bakkbone-florist-companion'),
				'required'		=> true,
				'class'			=> array('form-row-wide'),
				'clear'			=> true,
				'type'			=> 'textarea',
				'input_class'	=> array('card_message'),
				'description'	=> sprintf( get_option('bkf_localisation_setting')['additional_description_cardmessage'], $cardlength ),
				'maxlength'		=> $cardlength,
				'default'		=> stripslashes(stripslashes(WC()->session->get('card_message')))
			);
		}
		return $fields;
	}
	
	function card_message_js(){
		$cardlength = get_option("bkf_options_setting")["card_length"];
		?>
		<script type="text/javascript" id="card_message_js">
			jQuery(document.body).on( 'keyup', 'textarea.card_message', function($) {
				var max = <?php echo $cardlength; ?>;
				var input = jQuery('textarea.card_message');
				var value = input.val();
				var current = value.length;
				var remaining = max - current;
				var displayString = current + ' / ' + max;
				var displayHTML = '<div class="card_message_count" id="card_message_count">' + displayString + '</div>';
				if (jQuery('#card_message_count').length) {
					jQuery('#card_message_count').text(displayString);
				} else {
					jQuery('#card_message').before(displayHTML);
				}
			});
		</script>
		<?php
	}
	
	function add_excerpt_pa() {
		the_excerpt();
	}
	
	function bkf_add_cs_heading( $string ) {
		$string = get_option("bkf_localisation_setting")["csheading"];
		return $string;
	}
	
	function noship_message() {
		$bkflocalisation = get_option("bkf_localisation_setting");
		print '<span class="woocommerce-no-shipping-available-html e-checkout-message">' . esc_html( $bkflocalisation["noship"] ) . '</span>';
	}
		
	function bkf_link_guest_order( $order_id ) {
		$order = new WC_Order($order_id);
		$user = $order->get_user();
		if( !$user ){
			$userdata = get_user_by( 'email', $order->get_billing_email() );
			if(isset( $userdata->ID )){
				$order->update_meta_data('_customer_user', $userdata->ID );
				$order->save();
			}
		}
	}
	
	function add_email_verification_field_checkout( $fields ) {
		if(!is_user_logged_in()){
			$fields['billing']['billing_email']['class'] = 'form-row-first';
			$fields['billing']['billing_email']['priority'] = 110;
			$fields['billing']['billing_em_ver'] = array(
				'label'		=> esc_html__('Confirm email address', 'bakkbone-florist-companion'),
				'required'	=> true,
				'priority'	=> 111,
				'class'		=> 'form-row-last'
			);
		}
		return $fields;
	}
	
	function matching_email_addresses() { 
		if(!is_user_logged_in()){
			$email1 = $_POST['billing_email'];
			$email2 = $_POST['billing_em_ver'];
			if ( $email2 !== $email1 && $email2 !== '' && $email1 !== '' ) {
				wc_add_notice( wp_kses_post(__('<strong>Billing Email addresses</strong> do not match', 'bakkbone-florist-companion')), 'error' );
			}
		}
	}
	
	function validation() {
		if(isset($_POST['card_message'])){
			if (preg_match('/\p{So}/u', $_POST['card_message'])) {
				wc_add_notice( wp_kses_post(__('<strong>Card Message</strong> may not contain non-text characters such as emoji.', 'bakkbone-florist-companion')), 'error' );
			}
		}
		if(isset($_POST['billing_country'])){
		    if($_POST['billing_phone'] !== ''){
    			$billing_country = $_POST['billing_country'];
    			try {
    				$billing_phone = PhoneNumber::parse($_POST['billing_phone'], $billing_country);
    			} catch (PhoneNumberParseException $e) {
    				wc_add_notice( '<strong>'.esc_html(sprintf(__('Billing %s error:', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_telephone'])) . '</strong> ' . wp_kses_post($e->getMessage()), 'error' );
    			}
    			if(! $billing_phone->isValidNumber()) {
    				wc_add_notice( wp_kses_post(sprintf(__('<strong>Billing %s</strong> does not appear to be a valid phone number for the location.', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_telephone'])), 'error' );
    			}
		    }
		}
		
		if(isset($_POST['shipping_country'])){
		    if($_POST['shipping_phone'] !== ''){
    			$shipping_country = $_POST['shipping_country'];
    			try {
    				$shipping_phone = PhoneNumber::parse($_POST['shipping_phone'], $shipping_country);
    			} catch (PhoneNumberParseException $e) {
    				wc_add_notice( '<strong>'.esc_html(sprintf(__('Delivery Recipient %s error:', 'bakkbone-florist-companion'),get_option('bkf_localisation_setting')['global_label_telephone'])) . '</strong> ' . wp_kses_post($e->getMessage()), 'error' );
    			}
    			if(! $shipping_phone->isValidNumber()) {
    				wc_add_notice( wp_kses_post(sprintf(__('<strong>Delivery Recipient %s</strong> does not appear to be a valid phone number for the location.', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_telephone'])), 'error' );
    			}
		    }
		}
	}
	
	function auto_process( $order_id ) {  
		if ( ! $order_id ) {return;}            
		$order = wc_get_order( $order_id );
		if( 'processing'== $order->get_status() ) {
			$order->update_status( 'wc-processed' );
		}
	}
	
	function session_script() {
		if ( is_checkout() && ! is_wc_endpoint_url() ) :
		?>
		<script type="text/javascript" id="sn_cm_ajax">
		jQuery( function($){
			jQuery('form.checkout').on('change', '#shipping_notes, #card_message', function(){
				var sn = jQuery('#shipping_notes').val();
				var cm = jQuery('#card_message').val();
				jQuery.ajax({
					type: 'POST',
					url: wc_checkout_params.ajax_url,
					data: {
						'action': 'bkf_checkout_get_ajax_data',
						'shipping_notes': sn,
						'card_message': cm,
					},
					success: function (result) {
						jQuery('body').trigger('update_checkout');
					},
					error: function(error){
						console.log(error);
					}
				});
			});
		});
		</script>
		<?php
		endif;
	}
	
}