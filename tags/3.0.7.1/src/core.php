<?php

/**
 * @author BAKKBONE Australia
 * @package BkfCore
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfCore{
	
	private $del1 = '';
	private $del2 = '';
	
	function __construct() {
		add_action('wp_head', array($this, 'bkf_sig_head'));
		add_action('wp_footer', array($this, 'bkf_sig_shutdown'), 999);
		add_filter('admin_footer_text', array($this, 'bkf_admin_footer'), 10, 1);
		$bkffeatures = get_option("bkf_features_setting");
		if($bkffeatures["disable_order_comments"] == "1"){
			add_filter('woocommerce_checkout_fields', array($this, 'bkf_remove_order_comments'));
		}
		if($bkffeatures["excerpt_pa"] == "1") {
			add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'bkf_add_excerpt_pa') );
		};
		if($bkffeatures["confirm_email"] == "1") {
			add_filter( 'woocommerce_checkout_fields' , [$this, 'add_email_verification_field_checkout'], 1, 1 );
			add_action( 'woocommerce_checkout_process', [$this, 'matching_email_addresses'] );
		};
		add_filter( 'plugin_action_links_bakkbone-florist-companion/bakkbone-florist-companion.php', array($this, 'bkf_settings_link') );
		add_filter( "woocommerce_shipping_package_name" , array($this, "bkf_shipping_to_delivery"), 10, 3);
		add_filter( "gettext" , array($this, "bkf_translate_reply"), PHP_INT_MAX, 1 );
		add_filter( "ngettext" , array($this, "bkf_translate_reply"), PHP_INT_MAX, 1 );
		add_filter( "woocommerce_billing_fields" , array($this, "bkf_override_billing_fields") );
		add_filter( "woocommerce_shipping_fields" , array($this, "bkf_override_shipping_fields"));
		add_filter( "woocommerce_admin_billing_fields" , array($this, "bkf_override_billing_fields_admin") );
		add_filter( "woocommerce_admin_shipping_fields" , array($this, "bkf_override_shipping_fields_admin"));
		add_filter( "woocommerce_email_order_meta_fields", array($this, "bkf_notes_email"), 10, 3);
		add_filter( 'woocommerce_order_details_after_order_table', array($this, 'bkf_ot_thankyou'), PHP_INT_MAX , 1 );
		add_filter( 'woocommerce_order_details_after_customer_details', array($this, 'bkf_cd_thankyou'), PHP_INT_MAX , 1 );
		add_action( "woocommerce_checkout_update_order_meta" , array($this, "bkf_checkout_field_update_order_meta") );
		add_action( 'add_meta_boxes', array($this, 'bkf_cm_metabox_init') );
		add_action( 'save_post', array($this, 'bkf_cm_save_metabox_data') );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'bkf_editable_order_meta_shipping') );
		add_action( 'woocommerce_process_shop_order_meta', array($this, 'bkf_save_general_details') );
		add_filter( "woocommerce_checkout_fields" , array($this, "bkf_override_checkout_fields") );
		add_action( 'woocommerce_after_checkout_billing_form', array($this, 'card_message_js'));
		add_filter( "woocommerce_product_cross_sells_products_heading", array($this, "bkf_add_cs_heading"), 10, 1 );
		add_filter( 'woocommerce_cart_no_shipping_available_html', array($this, 'noship_message') );
		add_filter( 'woocommerce_no_shipping_available_html', array($this, 'noship_message') );
		add_action( 'woocommerce_new_order', array($this, 'bkf_link_guest_order', 10, 1 ) );
		add_filter( 'admin_bar_menu', array($this, 'bkf_replace_wordpress_howdy'), PHP_INT_MAX, 1 );
		
		$this->del1 = __('Delivery', 'bakkbone-florist-companion');
		$this->del2 = __('delivery', 'bakkbone-florist-companion');
	}
	
	function bkf_replace_wordpress_howdy( $wp_admin_bar ) {
		$user_id	  = get_current_user_id();
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
		
		$time = wp_date("H");
		
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
	
	function bkf_sig_head() {
		if ( ! is_user_logged_in() && ! defined( 'ADD_BKF_HTML_SIGNATURE' ) ) {
			define( 'ADD_BKF_HTML_SIGNATURE', true );
		}
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data(BKF_FILE);
		echo '<meta name="generator" content="'.$plugin_data["Name"].'">';
	}
	
	function bkf_sig_shutdown() {
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data(BKF_FILE);
		if ( ! is_user_logged_in() && defined( 'ADD_BKF_HTML_SIGNATURE' ) ) {
			echo "\n<!-- ".sprintf(__('Created with %s', 'bakkbone-florist-companion'), $plugin_data["Name"])." -->\n";
		}
	}
	
	function bkf_admin_footer($text){
		$plugin_data = get_plugin_data(BKF_FILE);
		$text = '<em>'.sprintf(__('Thank you for creating with <a href="%1s" target="_blank">%2s</a> and <a href="%3s" target="_blank">%4s</a>.', 'bakkbone-florist-companion'), $plugin_data["PluginURI"], $plugin_data["Name"], 'https://wordpress.org/', __('WordPress', 'bakkbone-florist-companion')).'</em>';
		return $text;
	}

	function bkf_remove_order_comments( $checkout_fields ) {
		unset( $checkout_fields[ 'order' ][ 'order_comments' ] );
		return $checkout_fields;
	}

	function bkf_settings_link( $links ) {
		$url = esc_url( add_query_arg( array( 'page' => 'bkf_options' ), get_admin_url() . 'admin.php' ) );
		$settings_link = "<a href='$url'>" . esc_html__( 'Settings','bakkbone-florist-companion' ) . '</a>';
		$docs_link = "<a href='https://plugins.bkbn.au/docs/bkf/'>" . esc_html__('Documentation','bakkbone-florist-companion') . '</a>';
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
		if (in_array("woo-address-book/woocommerce-address-book.php", apply_filters("active_plugins", get_option("active_plugins")))){
		$fields['shipping_address_nickname']['priority'] = 1;
		$fields['shipping_address_nickname']['label'] = __('Address nickname', 'bakkbone-florist-companion');
		$fields['shipping_address_nickname']['description'] = __('Send to someone regularly? An address nickname will help you find this address easily next time. Example: Jessica\'s Work', 'bakkbone-florist-companion');
		}
		$fields['shipping_company']['label'] = get_option('bkf_localisation_setting')['delivery_label_business'];
		$fields['shipping_company']['description'] = get_option('bkf_localisation_setting')['delivery_description_business'];
		$fields['shipping_city']['label'] = get_option('bkf_localisation_setting')['global_label_suburb'];
		$fields['shipping_state']['label'] = get_option('bkf_localisation_setting')['global_label_state'];
		$fields['shipping_postcode']['label'] = get_option('bkf_localisation_setting')['global_label_postcode'];
		$fields['shipping_country']['label'] = get_option('bkf_localisation_setting')['global_label_country'];
		$fields['shipping_phone'] = array(
		'label'	 => get_option('bkf_localisation_setting')['global_label_telephone'],
		'placeholder'   => get_option('bkf_localisation_setting')['global_label_telephone'],
		'required'  => true,
		'class'	 => array("form-row-wide"),
		'clear'	 => true,
		'type'	  => 'tel',
		'validate'  => array( 'phone' ),
		 );
		$fields['shipping_notes'] = array(
		'label'	 => get_option('bkf_localisation_setting')['delivery_label_notes'],
		'required'  => false,
		'class'	 => array('form-row-wide'),
		'clear'	 => true,
		'type'		=> 'textarea',
		'description' => get_option('bkf_localisation_setting')['delivery_description_notes']
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
			'value' => get_post_meta( $order->get_id(), '_shipping_notes', true ),
		);
		$fields['_card_message'] = array(
			'label' => __('Card Message', 'bakkbone-florist-companion'),
			'value' => get_post_meta( $order->get_id(), '_card_message', true ),
		);
		return $fields;
	}
	
	function bkf_ot_thankyou ( $order ) {
		$order_id = $order->get_id();
		$cm = get_post_meta( $order_id, '_card_message', true );

		if ( '' !== $cm ) {
			echo '<p><strong>' . esc_html__('Card Message', 'bakkbone-florist-companion') . ':</strong><br>' . $cm . '</p>';
		}
		
	}
	
	function bkf_cd_thankyou ( $order ) {
		$order_id = $order->get_id();
		$sn = get_post_meta( $order_id, '_shipping_notes', true );
		
		if ( '' !== $sn ) {
			echo '<p><strong>' . esc_html__('Delivery Notes', 'bakkbone-florist-companion') . ':</strong><br>' . $sn . '</p>';
		}
		
	}
	
	function bkf_checkout_field_update_order_meta( $order_id ) {
		if ( ! empty( $_POST['shipping_notes'] ) ) {
			update_post_meta( $order_id, '_shipping_notes', sanitize_text_field( $_POST['shipping_notes'] ) );
		}
		if ( ! empty( $_POST['card_message'] ) ) {
			update_post_meta( $order_id, '_card_message', sanitize_text_field( $_POST['card_message'] ) );
		}
	}
	
	function bkf_cm_metabox_init(){
		add_meta_box('bkf_cm', __('Card Message', 'bakkbone-florist-companion'),array($this, 'bkf_cm_metabox_callback'),'shop_order','side','core');
	}
	
	public function bkf_cm_metabox_callback( $post ){
		$cardmessage = get_post_meta( get_the_id(), '_card_message', true );
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

						if ( ! current_user_can( 'edit_shop_order', $post_id ) && ! current_user_can( 'edit_shop_orders', $post_id ) )
							return $post_id;

						update_post_meta( $post_id, '_card_message', wc_sanitize_textarea( $_POST[ 'card_message' ] ) );
					}
		} else {
			return $post_id;
		}
	}

	function bkf_editable_order_meta_shipping( $order ){
		$shippingnotes = $order->get_meta( '_shipping_notes' );
		?>
			<div class="address">
				<p<?php if( empty( $shippingnotes ) ) { echo ' class="none_set"'; } ?>>
		 			<strong><?php esc_html_e('Delivery Notes', 'bakkbone-florist-companion'); ?>:</strong>
					<?php echo ! empty( $shippingnotes ) ? $shippingnotes : '' ?>
				</p>
			</div>
			<div class="edit_address">
				<?php
					woocommerce_wp_textarea_input( array(
						'id' => '_shipping_notes',
						'label' => __('Delivery Notes', 'bakkbone-florist-companion'),
						'wrapper_class' => 'form-field-wide',
						'class' => 'input-text',
						'style' => 'width:100%',
						'value' => $shippingnotes,
						'description' => get_option('bkf_localisation_setting')['delivery_description_notes']
					) );
				?>
			</div>
		<?php
	}
	
	function bkf_save_general_details( $order_id ){
		update_post_meta( $order_id, '_shipping_notes', wc_sanitize_textarea( $_POST[ '_shipping_notes' ] ) );
		update_post_meta( $order_id, '_card_message', wc_sanitize_textarea( $_POST[ 'card_message' ] ) );
	}
	
	function bkf_override_checkout_fields( $fields ) {
		$bkfoptions = get_option("bkf_options_setting");
		$cardlength = $bkfoptions["card_length"];

		if( bkf_cart_has_physical() ) {
			 $fields['order']['card_message'] = array(
			'label'	 => __('Card Message', 'bakkbone-florist-companion'),
			'required'  => true,
			'class'	 => array('form-row-wide'),
			'clear'	 => true,
			'type'		=> 'textarea',
			'input_class'=> array('card_message'),
			'description' => sprintf( get_option('bkf_localisation_setting')['additional_description_cardmessage'], $cardlength ),
			'maxlength' => $cardlength
				 );
		}
		 return $fields;
	}
	
	function card_message_js(){
		$bkfoptions = get_option("bkf_options_setting");
		$cardlength = $bkfoptions["card_length"];
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
	
	function bkf_add_excerpt_pa() {
		the_excerpt();
	}
	
	function bkf_add_cs_heading( $string ) {
		$bkflocalisation = get_option("bkf_localisation_setting");
		$string = $bkflocalisation["csheading"];
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
				update_post_meta($order_id, '_customer_user', $userdata->ID );
			}
		}
	}
	
	function add_email_verification_field_checkout( $fields ) {
	    $fields['billing']['billing_em_ver'] = array(
	       'label' => 'Confirm email address',
	       'required' => true,
	       'clear' => true,
	       'priority' => 120,
	    );
	    return $fields;
	}
	
	function matching_email_addresses() { 
	    $email1 = $_POST['billing_email'];
	    $email2 = $_POST['billing_em_ver'];
	    if ( $email2 !== $email1 ) {
	        wc_add_notice( 'Your email addresses do not match', 'error' );
	    }
	}
	
}