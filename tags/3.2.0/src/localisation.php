<?php

/**
 * @author BAKKBONE Australia
 * @package BKF\Localisation
 * @license GNU General Public License (GPL) 3.0
**/

namespace BKF;

defined("BKF_EXEC") or die("Ah, sweet silence.");

class Localisation{
	
	private $bkf_localisation_setting = [];
		
	function __construct() {
		global $bkf_localisation_version;
		$bkf_localisation_version = 2.3;
		add_action("plugins_loaded",array($this,"bkf_update_localisation_check"));
		$this->bkf_localisation_setting = get_option("bkf_localisation_setting");
		add_action("admin_menu", array($this,"bkf_localisation_menu"),60);
		add_action("admin_init",array($this,"bkfAddLocalisationPageInit"));
		if (in_array("gravityforms/gravityforms.php", apply_filters("active_plugins", get_option("active_plugins")))){
			add_filter( "gform_phone_formats" , array($this, "bkf_au_phone_format") );
			add_filter( "gform_address_types" , array($this, "bkf_au_address_format"), 10, 2 );
		}
	}
	
	function bkf_update_localisation_check() {
		global $bkf_localisation_version;
		$current_local = get_option( 'bkf_localisation_version' );
		if ( $current_local != $bkf_localisation_version ) {
			if($current_local == '' || $current_local == null || $current_local < 2.2){
				$bkfoptions = get_option("bkf_options_setting");
				$csheading = array_key_exists('cs_heading', $bkfoptions) ? $bkfoptions['cs_heading'] : '';
				$noship = array_key_exists('noship', $bkfoptions) ? $bkfoptions['noship'] : '';
				$oldlocal = get_option("bkf_localisation_setting");
				$newlocal = [];
				if($oldlocal['billing_label_business'] !== null && $oldlocal['billing_label_business'] !== ''){
					$newlocal['billing_label_business'] = $oldlocal['billing_label_business'];
				} else {
					$newlocal['billing_label_business'] = __('Business Name', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_state'] !== null && $oldlocal['global_label_state'] !== ''){
					$newlocal['global_label_state'] = $oldlocal['global_label_state'];
				} else {
					$newlocal['global_label_state'] = __('State/Territory', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_suburb'] !== null && $oldlocal['global_label_suburb'] !== ''){
					$newlocal['global_label_suburb'] = $oldlocal['global_label_suburb'];
				} else {
					$newlocal['global_label_suburb'] = __('Suburb', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_postcode'] !== null && $oldlocal['global_label_postcode'] !== ''){
					$newlocal['global_label_postcode'] = $oldlocal['global_label_postcode'];
				} else {
					$newlocal['global_label_postcode'] = __('Postcode', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_country'] !== null && $oldlocal['global_label_country'] !== ''){
					$newlocal['global_label_country'] = $oldlocal['global_label_country'];
				} else {
					$newlocal['global_label_country'] = __('Country', 'bakkbone-florist-companion');
				}
				if($oldlocal['global_label_telephone'] !== null && $oldlocal['global_label_telephone'] !== ''){
					$newlocal['global_label_telephone'] = $oldlocal['global_label_telephone'];
				} else {
					$newlocal['global_label_telephone'] = __('Phone', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_label_business'] !== null && $oldlocal['delivery_label_business'] !== ''){
					$newlocal['delivery_label_business'] = $oldlocal['delivery_label_business'];
				} else {
					$newlocal['delivery_label_business'] = __('Business/Hospital/Hotel Name', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_description_business'] !== null && $oldlocal['delivery_description_business'] !== ''){
					$newlocal['delivery_description_business'] = $oldlocal['delivery_description_business'];
				} else {
					$newlocal['delivery_description_business'] = __('For hospitals/hotels/etc., please include ward/room information if known', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_label_notes'] !== null && $oldlocal['delivery_label_notes'] !== ''){
					$newlocal['delivery_label_notes'] = $oldlocal['delivery_label_notes'];
				} else {
					$newlocal['delivery_label_notes'] = __('Anything we need to know about the address?', 'bakkbone-florist-companion');
				}
				if($oldlocal['delivery_description_notes'] !== null && $oldlocal['delivery_description_notes'] !== ''){
					$newlocal['delivery_description_notes'] = $oldlocal['delivery_description_notes'];
				} else {
					$newlocal['delivery_description_notes'] = __('eg. gate code, fence, dog, etc.', 'bakkbone-florist-companion');
				}
				if($oldlocal['additional_description_cardmessage'] !== null && $oldlocal['additional_description_cardmessage'] !== ''){
					$newlocal['additional_description_cardmessage'] = $oldlocal['additional_description_cardmessage'];
				} else {
					$newlocal['additional_description_cardmessage'] = __("We'll include this with your gift. Maximum %s characters.", 'bakkbone-florist-companion');
				}
				if($oldlocal['csheading'] !== null && $oldlocal['csheading'] !== ''){
					$newlocal['csheading'] = $oldlocal['csheading'];
				} elseif($csheading !== null && $csheading !== '') {
					$newlocal['csheading'] = $csheading;
				} else {
					$newlocal['csheading'] = __('How about adding...','bakkbone-florist-companion');
				}
				if($oldlocal['noship'] !== null && $oldlocal['noship'] !== ''){
					$newlocal['noship'] = $oldlocal['noship'];
				} elseif($noship !== null && $noship !== '') {
					$newlocal['noship'] = $noship;
				} else {
					$newlocal['noship'] = __('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion');
				}
				update_option('bkf_localisation_setting', $newlocal);
				unset($bkfoptions['cs_heading'],$bkfoptions['noship']);
				update_option('bkf_options_setting',$bkfoptions);
				update_option('bkf_localisation_version', $bkf_localisation_version);
			}
		}
	}
	
	function bkf_localisation_menu(){
		$localisation = __("Localization","bakkbone-florist-companion");
		$admin_page = add_submenu_page(
			"bkf_options",
			$localisation,
			$localisation,
			"manage_woocommerce",
			"bkf_localisation",
			array($this, "bkf_localisation_settings_page"),
			60
		);
		add_action( 'load-'.$admin_page, array($this, 'bkf_localisation_help_tab') );
	}
	
	function bkf_localisation_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_localisation_help';
		$callback = array($this, 'bkf_localisation_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_localisation_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/florist-options/localisation/" target="_blank">https://plugins.bkbn.au/docs/bkf/florist-options/localisation/</a>
		<?php
	}
	
	function bkf_localisation_settings_page(){
		$this->bkf_localisation_setting = get_option("bkf_localisation_setting");
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Localization Settings","bakkbone-florist-companion"); ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php settings_fields("bkf_localisation_options_group"); ?>
						<?php do_settings_sections("bkf-localisation"); ?>
						<?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'localisation_submit') ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	function bkfAddLocalisationPageInit(){
		
		register_setting(
			"bkf_localisation_options_group",
			"bkf_localisation_setting",
			array($this,"bkfAddLocalisationOptionsSanitize")
		);

		add_settings_section(
			"bkf_localisation_global_section",
			__("Global Fields","bakkbone-florist-companion"),
			null,
			"bkf-localisation"
		);
		
		add_settings_section(
			"bkf_localisation_billing_section",
			__("Billing Fields","bakkbone-florist-companion"),
			null,
			"bkf-localisation"
		);
		
		add_settings_section(
			"bkf_localisation_delivery_section",
			__("Delivery Fields","bakkbone-florist-companion"),
			null,
			"bkf-localisation"
		);
		
		add_settings_section(
			"bkf_localisation_additional_section",
			__("Additional Fields","bakkbone-florist-companion"),
			null,
			"bkf-localisation"
		);
		
		add_settings_section(
			"bkf_localisation_flow_section",
			__("Customer Experience Flow","bakkbone-florist-companion"),
			null,
			"bkf-localisation"
		);
		
		add_settings_field(
			"global_label_state",
			__("Label: State/Territory","bakkbone-florist-companion"),
			array($this,"bkfGlsCallback"),
			"bkf-localisation",
			"bkf_localisation_global_section"
		);
		
		add_settings_field(
			"global_label_suburb",
			__("Label: Suburb","bakkbone-florist-companion"),
			array($this,"bkfGlsubCallback"),
			"bkf-localisation",
			"bkf_localisation_global_section"
		);
		
		add_settings_field(
			"global_label_postcode",
			__("Label: Postcode","bakkbone-florist-companion"),
			array($this,"bkfGlpCallback"),
			"bkf-localisation",
			"bkf_localisation_global_section"
		);
		
		add_settings_field(
			"global_label_country",
			__("Label: Country","bakkbone-florist-companion"),
			array($this,"bkfGlcCallback"),
			"bkf-localisation",
			"bkf_localisation_global_section"
		);
		
		add_settings_field(
			"global_label_telephone",
			__("Label: Telephone","bakkbone-florist-companion"),
			array($this,"bkfGltCallback"),
			"bkf-localisation",
			"bkf_localisation_global_section"
		);
		
		add_settings_field(
			"billing_label_business",
			__("Label: Business Name","bakkbone-florist-companion"),
			array($this,"bkfBlbCallback"),
			"bkf-localisation",
			"bkf_localisation_billing_section"
		);
		
		add_settings_field(
			"delivery_label_business",
			__("Label: Business Name","bakkbone-florist-companion"),
			array($this,"bkfDlbCallback"),
			"bkf-localisation",
			"bkf_localisation_delivery_section"
		);
		
		add_settings_field(
			"delivery_description_business",
			__("Description: Business Name","bakkbone-florist-companion"),
			array($this,"bkfDdbCallback"),
			"bkf-localisation",
			"bkf_localisation_delivery_section"
		);
		
		add_settings_field(
			"delivery_label_notes",
			__("Label: Delivery Notes","bakkbone-florist-companion"),
			array($this,"bkfDlnCallback"),
			"bkf-localisation",
			"bkf_localisation_delivery_section"
		);
		
		add_settings_field(
			"delivery_description_notes",
			__("Description: Delivery Notes","bakkbone-florist-companion"),
			array($this,"bkfDdnCallback"),
			"bkf-localisation",
			"bkf_localisation_delivery_section"
		);
		
		add_settings_field(
			"additional_description_cardmessage",
			__("Description: Card Message","bakkbone-florist-companion"),
			array($this,"bkfAdcCallback"),
			"bkf-localisation",
			"bkf_localisation_additional_section"
		);
		
		add_settings_field(
			"csheading",
			__("Heading: Cart Cross-Sells","bakkbone-florist-companion"),
			array($this,"bkfCsHeadingCallback"),
			"bkf-localisation",
			"bkf_localisation_flow_section"
		);
		
		add_settings_field(
			"noship",
			__("Error: No-Ship","bakkbone-florist-companion"),
			array($this,"bkfNoshipCallback"),
			"bkf-localisation",
			"bkf_localisation_flow_section"
		);

	}
	
	function bkfAddLocalisationOptionsSanitize($input){
		$new_input = [];
		
		if(isset($input["global_label_state"])){
			$new_input["global_label_state"] = sanitize_text_field($input["global_label_state"]);
		}
		
		if(isset($input["global_label_suburb"])){
			$new_input["global_label_suburb"] = sanitize_text_field($input["global_label_suburb"]);
		}
		
		if(isset($input["global_label_postcode"])){
			$new_input["global_label_postcode"] = sanitize_text_field($input["global_label_postcode"]);
		}
		
		if(isset($input["global_label_country"])){
			$new_input["global_label_country"] = sanitize_text_field($input["global_label_country"]);
		}
		
		if(isset($input["global_label_telephone"])){
			$new_input["global_label_telephone"] = sanitize_text_field($input["global_label_telephone"]);
		}
		
		if(isset($input["billing_label_business"])){
			$new_input["billing_label_business"] = sanitize_text_field($input["billing_label_business"]);
		}
		
		if(isset($input["delivery_label_business"])){
			$new_input["delivery_label_business"] = sanitize_text_field($input["delivery_label_business"]);
		}
		
		if(isset($input["delivery_description_business"])){
			$new_input["delivery_description_business"] = sanitize_text_field($input["delivery_description_business"]);
		}
		
		if(isset($input["delivery_label_notes"])){
			$new_input["delivery_label_notes"] = sanitize_text_field($input["delivery_label_notes"]);
		}
		
		if(isset($input["delivery_description_notes"])){
			$new_input["delivery_description_notes"] = sanitize_text_field($input["delivery_description_notes"]);
		}
		
		if(isset($input["additional_description_cardmessage"])){
			$new_input["additional_description_cardmessage"] = sanitize_text_field($input["additional_description_cardmessage"]);
		}
		
		if(isset($input["csheading"])){
			$new_input["csheading"] = sanitize_text_field($input["csheading"]);
		}
		
		if(isset($input["noship"])){
			$new_input["noship"] = sanitize_text_field($input["noship"]);
		}
		
		return $new_input;
	}

	function bkfGlsCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_state"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_state"]);
		} else {
			$value = __('State/Territory', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-state" name="bkf_localisation_setting[global_label_state]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGlsubCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_suburb"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_suburb"]);
		} else {
			$value = __('Suburb', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-suburb" name="bkf_localisation_setting[global_label_suburb]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGlpCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_postcode"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_postcode"]);
		} else {
			$value = __('Postcode', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-postcode" name="bkf_localisation_setting[global_label_postcode]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGlcCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_country"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_country"]);
		} else {
			$value = __('Country', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-country" name="bkf_localisation_setting[global_label_country]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGltCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_telephone"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_telephone"]);
		} else {
			$value = __('Phone', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-telephone" name="bkf_localisation_setting[global_label_telephone]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfBlbCallback(){
		
		if(isset($this->bkf_localisation_setting["billing_label_business"])){
			$value = esc_attr($this->bkf_localisation_setting["billing_label_business"]);
		} else {
			$value = __('Business Name', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="billing-label-business" name="bkf_localisation_setting[billing_label_business]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDlbCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_label_business"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_label_business"]);
		} else {
			$value = __('Business/Hospital/Hotel Name', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-label-business" name="bkf_localisation_setting[delivery_label_business]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDdbCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_description_business"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_description_business"]);
		} else {
			$value = __('For hospitals/hotels/etc., please include ward/room information if known', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-description-business" name="bkf_localisation_setting[delivery_description_business]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDlnCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_label_notes"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_label_notes"]);
		} else {
			$value = __('Anything we need to know about the address?', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-label-notes" name="bkf_localisation_setting[delivery_label_notes]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDdnCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_description_notes"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_description_notes"]);
		} else {
			$value = __('eg. gate code, fence, dog, etc.', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-description-notes" name="bkf_localisation_setting[delivery_description_notes]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfAdcCallback(){
		
		if(isset($this->bkf_localisation_setting["additional_description_cardmessage"])){
			$value = esc_attr($this->bkf_localisation_setting["additional_description_cardmessage"]);
		} else {
			$value = __("We'll include this with your gift. Maximum %s characters.", 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="additional-description-cardmessage" name="bkf_localisation_setting[additional_description_cardmessage]" value="<?php echo $value; ?>" required />
		<p class="description"><?php echo sprintf(__('Insert <strong>%%s</strong> where your maximum characters will appear.<br><em>eg. Entering "Maximum %%s characters" will display "Maximum %d characters" at checkout.</em>',"bakkbone-florist-companion"), get_option('bkf_options_setting')['card_length']); ?></p>
		<?php
	}
	
	function bkfCsHeadingCallback(){
	
		if(isset($this->bkf_localisation_setting["csheading"])){
			$value = esc_attr($this->bkf_localisation_setting["csheading"]);
		} else {
			$value = __('How about adding...','bakkbone-florist-companion');
		}
		?>
		<input class="bkf-form-control large-text" id="csheading" type="text" name="bkf_localisation_setting[csheading]" placeholder="<?php esc_html_e('How about adding...','bakkbone-florist-companion'); ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Replaces the heading of the Cross-Sells section of the Cart page","bakkbone-florist-companion"); ?></p>
		<?php
	}

	function bkfNoshipCallback(){
		
		if(isset($this->bkf_localisation_setting["noship"])){
			$value = esc_attr($this->bkf_localisation_setting["noship"]);
		} else {
			$value = __('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion');
		}
		?>
		<input class="bkf-form-control large-text" id="noship" type="text" name="bkf_localisation_setting[noship]" placeholder="<?php esc_html_e('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion'); ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Displays at checkout if the delivery address' suburb is not serviced.","bakkbone-florist-companion"); ?></p>
		<?php
	}
	
	function bkf_au_phone_format( $phone_formats ) {
		$phone_formats['au'] = array(
			'label'	   => __('Australia Mobile', 'bakkbone-florist-companion'),
			'mask'		=> '9999 999 999',
			'regex'	   => '/^\d{4} \d{3} \d{3}$/',
			'instruction' => '#### ### ###',
		);
		$phone_formats['aul'] = array(
			'label'	   => __('Australia Landline', 'bakkbone-florist-companion'),
			'mask'		=> '99 9999 9999',
			'regex'	   => '/^\d{2} \d{4} \d{4}$/',
			'instruction' => '## #### ####',
		);
		return $phone_formats;
	}
	
	function bkf_au_address_format( $formats, $form_id ) {
		$formats['au'] = array(
			'label'	   => esc_html__( 'Australia', 'bakkbone-florist-companion' ),
			'zip_label'   => gf_apply_filters( array( 'gform_address_zip', $form_id ), esc_html( get_option('bkf_localisation_setting')['global_label_postcode'] ), $form_id ),
			'state_label' => gf_apply_filters( array( 'gform_address_state', $form_id ), esc_html( get_option('bkf_localisation_setting')['global_label_state'] ), $form_id ),
			'country'	 => 'Australia',
			'states'	  => array(
				'',
				'ACT'	=> __('Australian Capital Territory', 'bakkbone-florist-companion'),
				'NSW'	=> __('New South Wales', 'bakkbone-florist-companion'),
				'NT'	=> __('Northern Territory', 'bakkbone-florist-companion'),
				'QLD'	=> __('Queensland', 'bakkbone-florist-companion'),
				'SA'	=> __('South Australia', 'bakkbone-florist-companion'),
				'TAS'	=> __('Tasmania', 'bakkbone-florist-companion'),
				'VIC'	=> __('Victoria', 'bakkbone-florist-companion'),
				'WA'	=> __('Western Australia', 'bakkbone-florist-companion'),
			)
		);
		
		return $formats;
	}
	
}