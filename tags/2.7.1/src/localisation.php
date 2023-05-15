<?php

/**
 * @author BAKKBONE Australia
 * @package BkfLocalisation
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");

class BkfLocalisation{
    
	private $bkf_localisation_setting = array();
		
	function __construct() {
		global $bkf_localisation_version;
		$bkf_localisation_version = 2.2;
		add_action("plugins_loaded",array($this,"bkf_update_localisation_check"));
        $this->bkf_localisation_setting = get_option("bkf_localisation_setting");
        add_action("admin_menu", array($this,"bkf_localisation_menu"),60);
        add_action("admin_init",array($this,"bkfAddLocalisationPageInit"));
        if (in_array("gravityforms/gravityforms.php", apply_filters("active_plugins", get_option("active_plugins")))){
            add_filter( "gform_phone_formats" , array($this, "bkf_au_phone_format") );
			add_filter( "gform_address_types" , array($this, "bkf_au_address_format") );
		}
	}
	
	function bkf_update_localisation_check() {
	    global $bkf_localisation_version;
		$current_local = get_option( 'bkf_localisation_version' );
	    if ( $current_local != $bkf_localisation_version ) {
			if($current_local == '' || $current_local == null || $current_local < 2.1){
				$bkfoptions = get_option("bkf_options_setting");
				$csheading = $bkfoptions['cs_heading'];
				$noship = $bkfoptions['noship'];
				$oldlocal = get_option("bkf_localisation_setting");
				$newlocal = array();
				if($oldlocal['billing_label_business'] !== null && $oldlocal['billing_label_business'] !== ''){
					$newlocal['billing_label_business'] = $oldlocal['billing_label_business'];
				} else {
					$newlocal['billing_label_business'] = default_billing_label_business;
				}
				if($oldlocal['global_label_state'] !== null && $oldlocal['global_label_state'] !== ''){
					$newlocal['global_label_state'] = $oldlocal['global_label_state'];
				} else {
					$newlocal['global_label_state'] = default_global_label_state;
				}
				if($oldlocal['global_label_suburb'] !== null && $oldlocal['global_label_suburb'] !== ''){
					$newlocal['global_label_suburb'] = $oldlocal['global_label_suburb'];
				} else {
					$newlocal['global_label_suburb'] = default_global_label_suburb;
				}
				if($oldlocal['global_label_postcode'] !== null && $oldlocal['global_label_postcode'] !== ''){
					$newlocal['global_label_postcode'] = $oldlocal['global_label_postcode'];
				} else {
					$newlocal['global_label_postcode'] = default_global_label_postcode;
				}
				if($oldlocal['global_label_country'] !== null && $oldlocal['global_label_country'] !== ''){
					$newlocal['global_label_country'] = $oldlocal['global_label_country'];
				} else {
					$newlocal['global_label_country'] = default_global_label_country;
				}
				if($oldlocal['global_label_telephone'] !== null && $oldlocal['global_label_telephone'] !== ''){
					$newlocal['global_label_telephone'] = $oldlocal['global_label_telephone'];
				} else {
					$newlocal['global_label_telephone'] = default_global_label_telephone;
				}
				if($oldlocal['delivery_label_business'] !== null && $oldlocal['delivery_label_business'] !== ''){
					$newlocal['delivery_label_business'] = $oldlocal['delivery_label_business'];
				} else {
					$newlocal['delivery_label_business'] = default_delivery_label_business;
				}
				if($oldlocal['delivery_description_business'] !== null && $oldlocal['delivery_description_business'] !== ''){
					$newlocal['delivery_description_business'] = $oldlocal['delivery_description_business'];
				} else {
					$newlocal['delivery_description_business'] = default_delivery_description_business;
				}
				if($oldlocal['delivery_label_notes'] !== null && $oldlocal['delivery_label_notes'] !== ''){
					$newlocal['delivery_label_notes'] = $oldlocal['delivery_label_notes'];
				} else {
					$newlocal['delivery_label_notes'] = default_delivery_label_notes;
				}
				if($oldlocal['delivery_description_notes'] !== null && $oldlocal['delivery_description_notes'] !== ''){
					$newlocal['delivery_description_notes'] = $oldlocal['delivery_description_notes'];
				} else {
					$newlocal['delivery_description_notes'] = default_delivery_description_notes;
				}
				if($oldlocal['additional_description_cardmessage'] !== null && $oldlocal['additional_description_cardmessage'] !== ''){
					$newlocal['additional_description_cardmessage'] = $oldlocal['additional_description_cardmessage'];
				} else {
					$newlocal['additional_description_cardmessage'] = default_additional_description_cardmessage;
				}
				if($oldlocal['csheading'] !== null && $oldlocal['csheading'] !== ''){
					$newlocal['csheading'] = $oldlocal['csheading'];
				} elseif($csheading !== null && $csheading !== '') {
					$newlocal['csheading'] = $csheading;
				} else {
					$newlocal['csheading'] = default_csheading;
				}
				if($oldlocal['noship'] !== null && $oldlocal['noship'] !== ''){
					$newlocal['noship'] = $oldlocal['noship'];
				} elseif($noship !== null && $noship !== '') {
					$newlocal['noship'] = $noship;
				} else {
					$newlocal['noship'] = default_noship;
				}
				update_option('bkf_localisation_setting', $newlocal);
				$bkfoptions = get_option("bkf_options_setting");
				unset($bkfoptions['cs_heading'],$bkfoptions['noship']);
				update_option('bkf_options_setting',$bkfoptions);
				update_option('bkf_localisation_version', $bkf_localisation_version);
			} elseif($current_local < $bkf_localisation_version){
				$local = get_option("bkf_localisation_setting");
				if($local['global_label_suburb'] == null || $local['global_label_suburb'] !== ''){
					$local['global_label_suburb'] = default_global_label_suburb;
				}
				update_option('bkf_localisation_setting', $local);
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
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_localisation_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://plugins.bkbn.au/docs/bkf/florist-options/localisation/" target="_blank">https://plugins.bkbn.au/docs/bkf/florist-options/localisation/</a>
		<?php
	}
    
    function bkf_localisation_settings_page()
    {
        $this->bkf_localisation_setting = get_option("bkf_localisation_setting");

        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Localisation Settings","bakkbone-florist-companion"); ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_localisation_options_group"); ?>
                        <?php do_settings_sections("bkf-localisation"); ?>
                        <?php submit_button(SAVEALLCHANGESTEXT, 'primary large', 'submit', true, array('id' => 'localisation_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
	function bkfAddLocalisationPageInit()
	{
		
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
	
	function bkfAddLocalisationOptionsSanitize($input)
	{
		$new_input = array();
		
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
		
		return $new_input;
	}

	function bkfGlsCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_state"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_state"]);
		}else{
			$value = default_global_label_state;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-state" name="bkf_localisation_setting[global_label_state]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGlsubCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_suburb"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_suburb"]);
		}else{
			$value = default_global_label_suburb;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-suburb" name="bkf_localisation_setting[global_label_suburb]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGlpCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_postcode"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_postcode"]);
		}else{
			$value = default_global_label_postcode;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-postcode" name="bkf_localisation_setting[global_label_postcode]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGlcCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_country"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_country"]);
		}else{
			$value = default_global_label_country;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-country" name="bkf_localisation_setting[global_label_country]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfGltCallback(){
		
		if(isset($this->bkf_localisation_setting["global_label_telephone"])){
			$value = esc_attr($this->bkf_localisation_setting["global_label_telephone"]);
		}else{
			$value = default_global_label_telephone;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="global-label-telephone" name="bkf_localisation_setting[global_label_telephone]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfBlbCallback(){
		
		if(isset($this->bkf_localisation_setting["billing_label_business"])){
			$value = esc_attr($this->bkf_localisation_setting["billing_label_business"]);
		}else{
			$value = default_billing_label_business;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="billing-label-business" name="bkf_localisation_setting[billing_label_business]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDlbCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_label_business"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_label_business"]);
		}else{
			$value = default_delivery_label_business;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-label-business" name="bkf_localisation_setting[delivery_label_business]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDdbCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_description_business"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_description_business"]);
		}else{
			$value = default_delivery_description_business;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-description-business" name="bkf_localisation_setting[delivery_description_business]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDlnCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_label_notes"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_label_notes"]);
		}else{
			$value = default_delivery_label_notes;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-label-notes" name="bkf_localisation_setting[delivery_label_notes]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfDdnCallback(){
		
		if(isset($this->bkf_localisation_setting["delivery_description_notes"])){
			$value = esc_attr($this->bkf_localisation_setting["delivery_description_notes"]);
		}else{
			$value = default_delivery_description_notes;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="delivery-description-notes" name="bkf_localisation_setting[delivery_description_notes]" value="<?php echo $value; ?>" required />
		<?php
	}
	
	function bkfAdcCallback(){
		
		if(isset($this->bkf_localisation_setting["additional_description_cardmessage"])){
			$value = esc_attr($this->bkf_localisation_setting["additional_description_cardmessage"]);
		}else{
			$value = default_additional_description_cardmessage;
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="additional-description-cardmessage" name="bkf_localisation_setting[additional_description_cardmessage]" value="<?php echo $value; ?>" required />
		<p class="description"><?php echo sprintf(__('Insert <strong>%%s</strong> where your maximum characters will appear.<br><em>eg. Entering "Maximum %%s characters" will display "Maximum %d characters" at checkout.</em>',"bakkbone-florist-companion"), get_option('bkf_options_setting')['card_length']); ?></p>
		<?php
	}
	
	function bkfCsHeadingCallback(){
	
		if(isset($this->bkf_localisation_setting["csheading"])){
			$value = esc_attr($this->bkf_localisation_setting["csheading"]);
		}else{
			$value = default_csheading;
		}
		?>
		<input class="bkf-form-control large-text" id="csheading" type="text" name="bkf_localisation_setting[csheading]" placeholder="<?php echo default_csheading; ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Replaces the heading of the Cross-Sells section of the Cart page","bakkbone-florist-companion"); ?></p>
		<?php
	}

	function bkfNoshipCallback(){
		
		if(isset($this->bkf_localisation_setting["noship"])){
			$value = esc_attr($this->bkf_localisation_setting["noship"]);
		}else{
			$value = default_noship;
		}
		?>
		<input class="bkf-form-control large-text" id="noship" type="text" name="bkf_localisation_setting[noship]" placeholder="<?php echo default_noship; ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Displays at checkout if the delivery address' suburb is not serviced.","bakkbone-florist-companion"); ?></p>
		<?php
	}
	
	function bkf_au_phone_format( $phone_formats ) {
	    $phone_formats['au'] = array(
	        'label'       => __('Australia Mobile', 'bakkbone-florist-companion'),
	        'mask'        => '9999 999 999',
	        'regex'       => '/^\d{4} \d{3} \d{3}$/',
	        'instruction' => '#### ### ###',
	    );
	    $phone_formats['aul'] = array(
	        'label'       => __('Australia Landline', 'bakkbone-florist-companion'),
	        'mask'        => '99 9999 9999',
	        'regex'       => '/^\d{2} \d{4} \d{4}$/',
	        'instruction' => '## #### ####',
	    );
	    return $phone_formats;
	}
	
	function bkf_au_address_format( $form_id ) {
		$formats['au'] = array(
			'label'       => esc_html__( 'Australia', 'bakkbone-florist-companion' ),
			'zip_label'   => gf_apply_filters( array( 'gform_address_zip', $form_id ), esc_html( get_option('bkf_localisation_setting')['global_label_postcode'] ), $form_id ),
			'state_label' => gf_apply_filters( array( 'gform_address_state', $form_id ), esc_html( get_option('bkf_localisation_setting')['global_label_state'] ), $form_id ),
			'country'     => 'Australia',
			'states'      => array(
				'',
				__('Australian Capital Territory', 'bakkbone-florist-companion'),
				__('New South Wales', 'bakkbone-florist-companion'),
				__('Northern Territory', 'bakkbone-florist-companion'),
				__('Queensland', 'bakkbone-florist-companion'),
				__('South Australia', 'bakkbone-florist-companion'),
				__('Tasmania', 'bakkbone-florist-companion'),
				__('Victoria', 'bakkbone-florist-companion'),
				__('Western Australia', 'bakkbone-florist-companion'),
				)
		);
		
		return $formats;
	}
	
}

define("BKF_PLUGIN_NAME",__("BAKKBONE Florist Companion", 'bakkbone-florist-companion'));
define("BKF_HELP_TITLE",__('Documentation','bakkbone-florist-companion'));
define("BKF_HELP_SUBTITLE",__('View documentation for this page at: ','bakkbone-florist-companion'));
define("BKF_DELIVERY1",__('Delivery', 'bakkbone-florist-companion'));
define("BKF_DELIVERY2",__('delivery', 'bakkbone-florist-companion'));
define("BKF_DELIVERY3",__('Delivery details', 'bakkbone-florist-companion'));
define("BKF_DELIVERY4",__('Delivery Date', 'bakkbone-florist-companion'));
define("DOWNLOADTEXT",__('Download', 'bakkbone-florist-companion'));
define("DELIVERYNOTESTEXT",__('Delivery Notes', 'bakkbone-florist-companion'));
define("CARDMESSAGETEXT",__('Card Message', 'bakkbone-florist-companion'));
define("SAVEALLCHANGESTEXT",__('Save All Changes', 'bakkbone-florist-companion'));
define("MONTEXT",__('Monday', 'bakkbone-florist-companion'));
define("TUETEXT",__('Tuesday', 'bakkbone-florist-companion'));
define("WEDTEXT",__('Wednesday', 'bakkbone-florist-companion'));
define("THUTEXT",__('Thursday', 'bakkbone-florist-companion'));
define("FRITEXT",__('Friday', 'bakkbone-florist-companion'));
define("SATTEXT",__('Saturday', 'bakkbone-florist-companion'));
define("SUNTEXT",__('Sunday', 'bakkbone-florist-companion'));
define("default_billing_label_business",__('Business Name', 'bakkbone-florist-companion'));
define("default_global_label_state",__('State/Territory', 'bakkbone-florist-companion'));
define("default_global_label_suburb",__('Suburb', 'bakkbone-florist-companion'));
define("default_global_label_postcode",__('Postcode', 'bakkbone-florist-companion'));
define("default_global_label_country",__('Country', 'bakkbone-florist-companion'));
define("default_global_label_telephone",__('Phone', 'bakkbone-florist-companion'));
define("default_delivery_label_business",__('Business/Hospital/Hotel Name', 'bakkbone-florist-companion'));
define("default_delivery_description_business",__('For hospitals/hotels/etc., please include ward/room information if known', 'bakkbone-florist-companion'));
define("default_delivery_label_notes",__('Anything we need to know about the address?', 'bakkbone-florist-companion'));
define("default_delivery_description_notes",__('eg. gate code, fence, dog, etc.', 'bakkbone-florist-companion'));
define("default_additional_description_cardmessage",__("We'll include this with your gift. Maximum %s characters.", 'bakkbone-florist-companion'));
define("default_csheading",__('How about adding...','bakkbone-florist-companion'));
define("default_noship",__('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion'));
define('BKF_PO_FIELD_SENDID', __('Order Number', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_RECIPIENT', __('Recipient\'s Name', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_SURNAME', __('Recipient\'s Surname', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_ADDRESS', __('Street Address', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_TOWN', __('Town/City', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_STATE', __('State/Province', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_POSTALCODE', __('Postal Code', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CRTYNAME', __('Country', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CRTYCODE', __('Country Code', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_PHONE', __('Recipient\'s Phone', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_DESCRIPTION', __('Description', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_MESSAGE', __('Card Message', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_COMMENTS', __('Comments', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_MAKEUP', __('Makeup', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_DELDATE', __('Delivery Date', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_DELTIME', __('Delivery Time', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_TVALUE', __('Total Order Value', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_SUPPLIER', __('Designated Executing Florist', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_PRODUCTID', __('Petals Product ID', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CONTACT_NAME', __('Customer Name', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CONTACT_EMAIL', __('Customer Email', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CONTACT_PHONE', __('Customer Phone', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_ADDRESSTYPE', __('Address Type', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_OCCASION', __('Occasion', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_UPSELL', __('Add-Ons', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_UPSELLAMT', __('Add-Ons Value', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_SENDID_INFO', __('An order reference of up to 6 digits. Orders with duplicate or missing numbers, or numbers longer than 6 digits, will be rejected by Petals.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_RECIPIENT_INFO', __('Name of person receiving flowers', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_SURNAME_INFO', __('Recipient\'s surname - first 8 characters', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_PHONE_INFO', __('It is strongly recommended that you seek and provide contact numbers for recipients to reduce delivery problems. Be advised that some partners in some countries will not deliver without a telephone number.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_DESCRIPTION_INFO', __('Order description in words. This field should contain sufficient info to supply the order. For example; 6 red roses bouquet, paper wrapped plus bottle of sparkling wine. Additional information on make-up can be supplied in the make-up field. Special delivery and and other special instructions can be added to the comments field. Not all partners are able to pass on some or all of the make-up and comments fields due to legacy systems so please keep the material in these fields to the minimum.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_COMMENTS_INFO', __('Any comments, delivery notes, or special instructions', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_MAKEUP_INFO', __('Provide optional information on how to make up the order. Note that this is only a guide. Petals Partners do not guarantee specific make-ups unless directly agreed between the Partners.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_DELTIME_INFO', __('Optional: text notes - e.g. am / pm, funeral time', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_TVALUE_INFO', __('Total value of the order in the currency Petals has you registered to send and receive orders in. Format is $$$.¢¢, eg. 123.00 - must include the cents.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_SUPPLIER_INFO', __('Member number of the supplying florist. Normally this will be provided by Petals when it chooses a florist to supply the order. However, if you have a preference, you can insert a valid Petals member number in this field.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_PRODUCTID_INFO', __('The Petals product ID. Please refer to the pricing guide for product IDs and descriptions.', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CONTACT_NAME_INFO', __('Purchaser or other contact person\'s name for the sell side of this order. (Optional, but needed if Petals might need to contact the purchaser)', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CONTACT_EMAIL_INFO', __('Purchaser or other contact person\'s email for the sell side of this order. (Optional, but needed if Petals might need to contact the purchaser)', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_CONTACT_PHONE_INFO', __('Purchaser or other contact person\'s business hours phone for the sell side of this order. (Optional, but needed if Petals might need to contact the purchaser)', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_ADDRESSTYPE_INFO', __('Should be the "location" part of the address, ie. Home/Business/Hospital/Church etc', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_OCCASION_INFO', __('Should be the occasion they select with the card message', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_UPSELL_INFO', __('This is where you would include any add-ons such as chocolates, balloons etc', 'bakkbone-florist-companion'));
define('BKF_PO_FIELD_UPSELLAMT_INFO', __('This is where you would include the total value of all add ons', 'bakkbone-florist-companion'));
define('BKF_PO_ORDER_ITEMS', __('Order Items', 'bakkbone-florist-companion'));
define('BKF_PO_ORDER_DELIVERY', __('Delivery', 'bakkbone-florist-companion'));
define('BKF_PO_ORDER_RECIPIENT', __('Recipient', 'bakkbone-florist-companion'));
define('BKF_PO_ORDER_INFORMATION', __('Information', 'bakkbone-florist-companion'));
define('BKF_PO_ORDER_CUSTOMER', __('Your Customer', 'bakkbone-florist-companion'));
define('BKF_ISO_COUNTRIES', array(
	'AF' => __('Afghanistan', 'bakkbone-florist-companion'),
	'AX' => __('Åland Islands', 'bakkbone-florist-companion'),
	'AL' => __('Albania', 'bakkbone-florist-companion'),
	'DZ' => __('Algeria', 'bakkbone-florist-companion'),
	'AS' => __('American Samoa', 'bakkbone-florist-companion'),
	'AD' => __('Andorra', 'bakkbone-florist-companion'),
	'AO' => __('Angola', 'bakkbone-florist-companion'),
	'AI' => __('Anguilla', 'bakkbone-florist-companion'),
	'AQ' => __('Antarctica', 'bakkbone-florist-companion'),
	'AG' => __('Antigua and Barbuda', 'bakkbone-florist-companion'),
	'AR' => __('Argentina', 'bakkbone-florist-companion'),
	'AM' => __('Armenia', 'bakkbone-florist-companion'),
	'AW' => __('Aruba', 'bakkbone-florist-companion'),
	'AU' => __('Australia', 'bakkbone-florist-companion'),
	'AT' => __('Austria', 'bakkbone-florist-companion'),
	'AZ' => __('Azerbaijan', 'bakkbone-florist-companion'),
	'BS' => __('Bahamas', 'bakkbone-florist-companion'),
	'BH' => __('Bahrain', 'bakkbone-florist-companion'),
	'BD' => __('Bangladesh', 'bakkbone-florist-companion'),
	'BB' => __('Barbados', 'bakkbone-florist-companion'),
	'BY' => __('Belarus', 'bakkbone-florist-companion'),
	'BE' => __('Belgium', 'bakkbone-florist-companion'),
	'BZ' => __('Belize', 'bakkbone-florist-companion'),
	'BJ' => __('Benin', 'bakkbone-florist-companion'),
	'BM' => __('Bermuda', 'bakkbone-florist-companion'),
	'BT' => __('Bhutan', 'bakkbone-florist-companion'),
	'BO' => __('Bolivia (Plurinational State of)', 'bakkbone-florist-companion'),
	'BQ' => __('Bonaire, Sint Eustatius and Saba', 'bakkbone-florist-companion'),
	'BA' => __('Bosnia and Herzegovina', 'bakkbone-florist-companion'),
	'BW' => __('Botswana', 'bakkbone-florist-companion'),
	'BV' => __('Bouvet Island', 'bakkbone-florist-companion'),
	'BR' => __('Brazil', 'bakkbone-florist-companion'),
	'IO' => __('British Indian Ocean Territory', 'bakkbone-florist-companion'),
	'BN' => __('Brunei Darussalam', 'bakkbone-florist-companion'),
	'BG' => __('Bulgaria', 'bakkbone-florist-companion'),
	'BF' => __('Burkina Faso', 'bakkbone-florist-companion'),
	'BI' => __('Burundi', 'bakkbone-florist-companion'),
	'CV' => __('Cabo Verde', 'bakkbone-florist-companion'),
	'KH' => __('Cambodia', 'bakkbone-florist-companion'),
	'CM' => __('Cameroon', 'bakkbone-florist-companion'),
	'CA' => __('Canada', 'bakkbone-florist-companion'),
	'KY' => __('Cayman Islands', 'bakkbone-florist-companion'),
	'CF' => __('Central African Republic', 'bakkbone-florist-companion'),
	'TD' => __('Chad', 'bakkbone-florist-companion'),
	'CL' => __('Chile', 'bakkbone-florist-companion'),
	'CN' => __('China', 'bakkbone-florist-companion'),
	'CX' => __('Christmas Island', 'bakkbone-florist-companion'),
	'CC' => __('Cocos (Keeling) Islands', 'bakkbone-florist-companion'),
	'CO' => __('Colombia', 'bakkbone-florist-companion'),
	'KM' => __('Comoros', 'bakkbone-florist-companion'),
	'CG' => __('Congo', 'bakkbone-florist-companion'),
	'CD' => __('Congo, Democratic Republic of the', 'bakkbone-florist-companion'),
	'CK' => __('Cook Islands', 'bakkbone-florist-companion'),
	'CR' => __('Costa Rica', 'bakkbone-florist-companion'),
	'CI' => __('Côte d\'Ivoire', 'bakkbone-florist-companion'),
	'HR' => __('Croatia', 'bakkbone-florist-companion'),
	'CU' => __('Cuba', 'bakkbone-florist-companion'),
	'CW' => __('Curaçao', 'bakkbone-florist-companion'),
	'CY' => __('Cyprus', 'bakkbone-florist-companion'),
	'CZ' => __('Czechia', 'bakkbone-florist-companion'),
	'DK' => __('Denmark', 'bakkbone-florist-companion'),
	'DJ' => __('Djibouti', 'bakkbone-florist-companion'),
	'DM' => __('Dominica', 'bakkbone-florist-companion'),
	'DO' => __('Dominican Republic', 'bakkbone-florist-companion'),
	'EC' => __('Ecuador', 'bakkbone-florist-companion'),
	'EG' => __('Egypt', 'bakkbone-florist-companion'),
	'SV' => __('El Salvador', 'bakkbone-florist-companion'),
	'GQ' => __('Equatorial Guinea', 'bakkbone-florist-companion'),
	'ER' => __('Eritrea', 'bakkbone-florist-companion'),
	'EE' => __('Estonia', 'bakkbone-florist-companion'),
	'SZ' => __('Eswatini', 'bakkbone-florist-companion'),
	'ET' => __('Ethiopia', 'bakkbone-florist-companion'),
	'FK' => __('Falkland Islands (Malvinas)', 'bakkbone-florist-companion'),
	'FO' => __('Faroe Islands', 'bakkbone-florist-companion'),
	'FJ' => __('Fiji', 'bakkbone-florist-companion'),
	'FI' => __('Finland', 'bakkbone-florist-companion'),
	'FR' => __('France', 'bakkbone-florist-companion'),
	'GF' => __('French Guiana', 'bakkbone-florist-companion'),
	'PF' => __('French Polynesia', 'bakkbone-florist-companion'),
	'TF' => __('French Southern Territories', 'bakkbone-florist-companion'),
	'GA' => __('Gabon', 'bakkbone-florist-companion'),
	'GM' => __('Gambia', 'bakkbone-florist-companion'),
	'GE' => __('Georgia', 'bakkbone-florist-companion'),
	'DE' => __('Germany', 'bakkbone-florist-companion'),
	'GH' => __('Ghana', 'bakkbone-florist-companion'),
	'GI' => __('Gibraltar', 'bakkbone-florist-companion'),
	'GR' => __('Greece', 'bakkbone-florist-companion'),
	'GL' => __('Greenland', 'bakkbone-florist-companion'),
	'GD' => __('Grenada', 'bakkbone-florist-companion'),
	'GP' => __('Guadeloupe', 'bakkbone-florist-companion'),
	'GU' => __('Guam', 'bakkbone-florist-companion'),
	'GT' => __('Guatemala', 'bakkbone-florist-companion'),
	'GG' => __('Guernsey', 'bakkbone-florist-companion'),
	'GN' => __('Guinea', 'bakkbone-florist-companion'),
	'GW' => __('Guinea-Bissau', 'bakkbone-florist-companion'),
	'GY' => __('Guyana', 'bakkbone-florist-companion'),
	'HT' => __('Haiti', 'bakkbone-florist-companion'),
	'HM' => __('Heard Island and McDonald Islands', 'bakkbone-florist-companion'),
	'VA' => __('Holy See', 'bakkbone-florist-companion'),
	'HN' => __('Honduras', 'bakkbone-florist-companion'),
	'HK' => __('Hong Kong', 'bakkbone-florist-companion'),
	'HU' => __('Hungary', 'bakkbone-florist-companion'),
	'IS' => __('Iceland', 'bakkbone-florist-companion'),
	'IN' => __('India', 'bakkbone-florist-companion'),
	'ID' => __('Indonesia', 'bakkbone-florist-companion'),
	'IR' => __('Iran (Islamic Republic of)', 'bakkbone-florist-companion'),
	'IQ' => __('Iraq', 'bakkbone-florist-companion'),
	'IE' => __('Ireland', 'bakkbone-florist-companion'),
	'IM' => __('Isle of Man', 'bakkbone-florist-companion'),
	'IL' => __('Israel', 'bakkbone-florist-companion'),
	'IT' => __('Italy', 'bakkbone-florist-companion'),
	'JM' => __('Jamaica', 'bakkbone-florist-companion'),
	'JP' => __('Japan', 'bakkbone-florist-companion'),
	'JE' => __('Jersey', 'bakkbone-florist-companion'),
	'JO' => __('Jordan', 'bakkbone-florist-companion'),
	'KZ' => __('Kazakhstan', 'bakkbone-florist-companion'),
	'KE' => __('Kenya', 'bakkbone-florist-companion'),
	'KI' => __('Kiribati', 'bakkbone-florist-companion'),
	'KP' => __('Korea (Democratic People\'s Republic of)', 'bakkbone-florist-companion'),
	'KR' => __('Korea, Republic of', 'bakkbone-florist-companion'),
	'KW' => __('Kuwait', 'bakkbone-florist-companion'),
	'KG' => __('Kyrgyzstan', 'bakkbone-florist-companion'),
	'LA' => __('Lao People\'s Democratic Republic', 'bakkbone-florist-companion'),
	'LV' => __('Latvia', 'bakkbone-florist-companion'),
	'LB' => __('Lebanon', 'bakkbone-florist-companion'),
	'LS' => __('Lesotho', 'bakkbone-florist-companion'),
	'LR' => __('Liberia', 'bakkbone-florist-companion'),
	'LY' => __('Libya', 'bakkbone-florist-companion'),
	'LI' => __('Liechtenstein', 'bakkbone-florist-companion'),
	'LT' => __('Lithuania', 'bakkbone-florist-companion'),
	'LU' => __('Luxembourg', 'bakkbone-florist-companion'),
	'MO' => __('Macao', 'bakkbone-florist-companion'),
	'MG' => __('Madagascar', 'bakkbone-florist-companion'),
	'MW' => __('Malawi', 'bakkbone-florist-companion'),
	'MY' => __('Malaysia', 'bakkbone-florist-companion'),
	'MV' => __('Maldives', 'bakkbone-florist-companion'),
	'ML' => __('Mali', 'bakkbone-florist-companion'),
	'MT' => __('Malta', 'bakkbone-florist-companion'),
	'MH' => __('Marshall Islands', 'bakkbone-florist-companion'),
	'MQ' => __('Martinique', 'bakkbone-florist-companion'),
	'MR' => __('Mauritania', 'bakkbone-florist-companion'),
	'MU' => __('Mauritius', 'bakkbone-florist-companion'),
	'YT' => __('Mayotte', 'bakkbone-florist-companion'),
	'MX' => __('Mexico', 'bakkbone-florist-companion'),
	'FM' => __('Micronesia (Federated States of)', 'bakkbone-florist-companion'),
	'MD' => __('Moldova, Republic of', 'bakkbone-florist-companion'),
	'MC' => __('Monaco', 'bakkbone-florist-companion'),
	'MN' => __('Mongolia', 'bakkbone-florist-companion'),
	'ME' => __('Montenegro', 'bakkbone-florist-companion'),
	'MS' => __('Montserrat', 'bakkbone-florist-companion'),
	'MA' => __('Morocco', 'bakkbone-florist-companion'),
	'MZ' => __('Mozambique', 'bakkbone-florist-companion'),
	'MM' => __('Myanmar', 'bakkbone-florist-companion'),
	'NA' => __('Namibia', 'bakkbone-florist-companion'),
	'NR' => __('Nauru', 'bakkbone-florist-companion'),
	'NP' => __('Nepal', 'bakkbone-florist-companion'),
	'NL' => __('Netherlands', 'bakkbone-florist-companion'),
	'NC' => __('New Caledonia', 'bakkbone-florist-companion'),
	'NZ' => __('New Zealand', 'bakkbone-florist-companion'),
	'NI' => __('Nicaragua', 'bakkbone-florist-companion'),
	'NE' => __('Niger', 'bakkbone-florist-companion'),
	'NG' => __('Nigeria', 'bakkbone-florist-companion'),
	'NU' => __('Niue', 'bakkbone-florist-companion'),
	'NF' => __('Norfolk Island', 'bakkbone-florist-companion'),
	'MK' => __('North Macedonia', 'bakkbone-florist-companion'),
	'MP' => __('Northern Mariana Islands', 'bakkbone-florist-companion'),
	'NO' => __('Norway', 'bakkbone-florist-companion'),
	'OM' => __('Oman', 'bakkbone-florist-companion'),
	'PK' => __('Pakistan', 'bakkbone-florist-companion'),
	'PW' => __('Palau', 'bakkbone-florist-companion'),
	'PS' => __('Palestine, State of', 'bakkbone-florist-companion'),
	'PA' => __('Panama', 'bakkbone-florist-companion'),
	'PG' => __('Papua New Guinea', 'bakkbone-florist-companion'),
	'PY' => __('Paraguay', 'bakkbone-florist-companion'),
	'PE' => __('Peru', 'bakkbone-florist-companion'),
	'PH' => __('Philippines', 'bakkbone-florist-companion'),
	'PN' => __('Pitcairn', 'bakkbone-florist-companion'),
	'PL' => __('Poland', 'bakkbone-florist-companion'),
	'PT' => __('Portugal', 'bakkbone-florist-companion'),
	'PR' => __('Puerto Rico', 'bakkbone-florist-companion'),
	'QA' => __('Qatar', 'bakkbone-florist-companion'),
	'RE' => __('Réunion', 'bakkbone-florist-companion'),
	'RO' => __('Romania', 'bakkbone-florist-companion'),
	'RU' => __('Russian Federation', 'bakkbone-florist-companion'),
	'RW' => __('Rwanda', 'bakkbone-florist-companion'),
	'BL' => __('Saint Barthélemy', 'bakkbone-florist-companion'),
	'SH' => __('Saint Helena, Ascension and Tristan da Cunha', 'bakkbone-florist-companion'),
	'KN' => __('Saint Kitts and Nevis', 'bakkbone-florist-companion'),
	'LC' => __('Saint Lucia', 'bakkbone-florist-companion'),
	'MF' => __('Saint Martin (French part)', 'bakkbone-florist-companion'),
	'PM' => __('Saint Pierre and Miquelon', 'bakkbone-florist-companion'),
	'VC' => __('Saint Vincent and the Grenadines', 'bakkbone-florist-companion'),
	'WS' => __('Samoa', 'bakkbone-florist-companion'),
	'SM' => __('San Marino', 'bakkbone-florist-companion'),
	'ST' => __('Sao Tome and Principe', 'bakkbone-florist-companion'),
	'SA' => __('Saudi Arabia', 'bakkbone-florist-companion'),
	'SN' => __('Senegal', 'bakkbone-florist-companion'),
	'RS' => __('Serbia', 'bakkbone-florist-companion'),
	'SC' => __('Seychelles', 'bakkbone-florist-companion'),
	'SL' => __('Sierra Leone', 'bakkbone-florist-companion'),
	'SG' => __('Singapore', 'bakkbone-florist-companion'),
	'SX' => __('Sint Maarten (Dutch part)', 'bakkbone-florist-companion'),
	'SK' => __('Slovakia', 'bakkbone-florist-companion'),
	'SI' => __('Slovenia', 'bakkbone-florist-companion'),
	'SB' => __('Solomon Islands', 'bakkbone-florist-companion'),
	'SO' => __('Somalia', 'bakkbone-florist-companion'),
	'ZA' => __('South Africa', 'bakkbone-florist-companion'),
	'GS' => __('South Georgia and the South Sandwich Islands', 'bakkbone-florist-companion'),
	'SS' => __('South Sudan', 'bakkbone-florist-companion'),
	'ES' => __('Spain', 'bakkbone-florist-companion'),
	'LK' => __('Sri Lanka', 'bakkbone-florist-companion'),
	'SD' => __('Sudan', 'bakkbone-florist-companion'),
	'SR' => __('Suriname', 'bakkbone-florist-companion'),
	'SJ' => __('Svalbard and Jan Mayen', 'bakkbone-florist-companion'),
	'SE' => __('Sweden', 'bakkbone-florist-companion'),
	'CH' => __('Switzerland', 'bakkbone-florist-companion'),
	'SY' => __('Syrian Arab Republic', 'bakkbone-florist-companion'),
	'TW' => __('Taiwan, Province of China', 'bakkbone-florist-companion'),
	'TJ' => __('Tajikistan', 'bakkbone-florist-companion'),
	'TZ' => __('Tanzania, United Republic of', 'bakkbone-florist-companion'),
	'TH' => __('Thailand', 'bakkbone-florist-companion'),
	'TL' => __('Timor-Leste', 'bakkbone-florist-companion'),
	'TG' => __('Togo', 'bakkbone-florist-companion'),
	'TK' => __('Tokelau', 'bakkbone-florist-companion'),
	'TO' => __('Tonga', 'bakkbone-florist-companion'),
	'TT' => __('Trinidad and Tobago', 'bakkbone-florist-companion'),
	'TN' => __('Tunisia', 'bakkbone-florist-companion'),
	'TR' => __('Türkiye', 'bakkbone-florist-companion'),
	'TM' => __('Turkmenistan', 'bakkbone-florist-companion'),
	'TC' => __('Turks and Caicos Islands', 'bakkbone-florist-companion'),
	'TV' => __('Tuvalu', 'bakkbone-florist-companion'),
	'UG' => __('Uganda', 'bakkbone-florist-companion'),
	'UA' => __('Ukraine', 'bakkbone-florist-companion'),
	'AE' => __('United Arab Emirates', 'bakkbone-florist-companion'),
	'GB' => __('United Kingdom of Great Britain and Northern Ireland', 'bakkbone-florist-companion'),
	'UM' => __('United States Minor Outlying Islands', 'bakkbone-florist-companion'),
	'US' => __('United States of America', 'bakkbone-florist-companion'),
	'UY' => __('Uruguay', 'bakkbone-florist-companion'),
	'UZ' => __('Uzbekistan', 'bakkbone-florist-companion'),
	'VU' => __('Vanuatu', 'bakkbone-florist-companion'),
	'VE' => __('Venezuela (Bolivarian Republic of)', 'bakkbone-florist-companion'),
	'VN' => __('Viet Nam', 'bakkbone-florist-companion'),
	'VG' => __('Virgin Islands (British)', 'bakkbone-florist-companion'),
	'VI' => __('Virgin Islands (U.S.)', 'bakkbone-florist-companion'),
	'WF' => __('Wallis and Futuna', 'bakkbone-florist-companion'),
	'EH' => __('Western Sahara', 'bakkbone-florist-companion'),
	'YE' => __('Yemen', 'bakkbone-florist-companion'),
	'ZM' => __('Zambia', 'bakkbone-florist-companion'),
	'ZW' => __('Zimbabwe', 'bakkbone-florist-companion')
));
define('BKF_ISO_COUNTRIES_FIXED', array(
	'AF' => 'Afghanistan',
	'AX' => 'Åland Islands',
	'AL' => 'Albania',
	'DZ' => 'Algeria',
	'AS' => 'American Samoa',
	'AD' => 'Andorra',
	'AO' => 'Angola',
	'AI' => 'Anguilla',
	'AQ' => 'Antarctica',
	'AG' => 'Antigua and Barbuda',
	'AR' => 'Argentina',
	'AM' => 'Armenia',
	'AW' => 'Aruba',
	'AU' => 'Australia',
	'AT' => 'Austria',
	'AZ' => 'Azerbaijan',
	'BS' => 'Bahamas',
	'BH' => 'Bahrain',
	'BD' => 'Bangladesh',
	'BB' => 'Barbados',
	'BY' => 'Belarus',
	'BE' => 'Belgium',
	'BZ' => 'Belize',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BT' => 'Bhutan',
	'BO' => 'Bolivia (Plurinational State of)',
	'BQ' => 'Bonaire, Sint Eustatius and Saba',
	'BA' => 'Bosnia and Herzegovina',
	'BW' => 'Botswana',
	'BV' => 'Bouvet Island',
	'BR' => 'Brazil',
	'IO' => 'British Indian Ocean Territory',
	'BN' => 'Brunei Darussalam',
	'BG' => 'Bulgaria',
	'BF' => 'Burkina Faso',
	'BI' => 'Burundi',
	'CV' => 'Cabo Verde',
	'KH' => 'Cambodia',
	'CM' => 'Cameroon',
	'CA' => 'Canada',
	'KY' => 'Cayman Islands',
	'CF' => 'Central African Republic',
	'TD' => 'Chad',
	'CL' => 'Chile',
	'CN' => 'China',
	'CX' => 'Christmas Island',
	'CC' => 'Cocos (Keeling) Islands',
	'CO' => 'Colombia',
	'KM' => 'Comoros',
	'CG' => 'Congo',
	'CD' => 'Congo, Democratic Republic of the',
	'CK' => 'Cook Islands',
	'CR' => 'Costa Rica',
	'CI' => 'Côte d\'Ivoire',
	'HR' => 'Croatia',
	'CU' => 'Cuba',
	'CW' => 'Curaçao',
	'CY' => 'Cyprus',
	'CZ' => 'Czechia',
	'DK' => 'Denmark',
	'DJ' => 'Djibouti',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'EC' => 'Ecuador',
	'EG' => 'Egypt',
	'SV' => 'El Salvador',
	'GQ' => 'Equatorial Guinea',
	'ER' => 'Eritrea',
	'EE' => 'Estonia',
	'SZ' => 'Eswatini',
	'ET' => 'Ethiopia',
	'FK' => 'Falkland Islands (Malvinas)',
	'FO' => 'Faroe Islands',
	'FJ' => 'Fiji',
	'FI' => 'Finland',
	'FR' => 'France',
	'GF' => 'French Guiana',
	'PF' => 'French Polynesia',
	'TF' => 'French Southern Territories',
	'GA' => 'Gabon',
	'GM' => 'Gambia',
	'GE' => 'Georgia',
	'DE' => 'Germany',
	'GH' => 'Ghana',
	'GI' => 'Gibraltar',
	'GR' => 'Greece',
	'GL' => 'Greenland',
	'GD' => 'Grenada',
	'GP' => 'Guadeloupe',
	'GU' => 'Guam',
	'GT' => 'Guatemala',
	'GG' => 'Guernsey',
	'GN' => 'Guinea',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HT' => 'Haiti',
	'HM' => 'Heard Island and McDonald Islands',
	'VA' => 'Holy See',
	'HN' => 'Honduras',
	'HK' => 'Hong Kong',
	'HU' => 'Hungary',
	'IS' => 'Iceland',
	'IN' => 'India',
	'ID' => 'Indonesia',
	'IR' => 'Iran (Islamic Republic of)',
	'IQ' => 'Iraq',
	'IE' => 'Ireland',
	'IM' => 'Isle of Man',
	'IL' => 'Israel',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JP' => 'Japan',
	'JE' => 'Jersey',
	'JO' => 'Jordan',
	'KZ' => 'Kazakhstan',
	'KE' => 'Kenya',
	'KI' => 'Kiribati',
	'KP' => 'Korea (Democratic People\'s Republic of)',
	'KR' => 'Korea, Republic of',
	'KW' => 'Kuwait',
	'KG' => 'Kyrgyzstan',
	'LA' => 'Lao People\'s Democratic Republic',
	'LV' => 'Latvia',
	'LB' => 'Lebanon',
	'LS' => 'Lesotho',
	'LR' => 'Liberia',
	'LY' => 'Libya',
	'LI' => 'Liechtenstein',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'MO' => 'Macao',
	'MG' => 'Madagascar',
	'MW' => 'Malawi',
	'MY' => 'Malaysia',
	'MV' => 'Maldives',
	'ML' => 'Mali',
	'MT' => 'Malta',
	'MH' => 'Marshall Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MU' => 'Mauritius',
	'YT' => 'Mayotte',
	'MX' => 'Mexico',
	'FM' => 'Micronesia (Federated States of)',
	'MD' => 'Moldova, Republic of',
	'MC' => 'Monaco',
	'MN' => 'Mongolia',
	'ME' => 'Montenegro',
	'MS' => 'Montserrat',
	'MA' => 'Morocco',
	'MZ' => 'Mozambique',
	'MM' => 'Myanmar',
	'NA' => 'Namibia',
	'NR' => 'Nauru',
	'NP' => 'Nepal',
	'NL' => 'Netherlands',
	'NC' => 'New Caledonia',
	'NZ' => 'New Zealand',
	'NI' => 'Nicaragua',
	'NE' => 'Niger',
	'NG' => 'Nigeria',
	'NU' => 'Niue',
	'NF' => 'Norfolk Island',
	'MK' => 'North Macedonia',
	'MP' => 'Northern Mariana Islands',
	'NO' => 'Norway',
	'OM' => 'Oman',
	'PK' => 'Pakistan',
	'PW' => 'Palau',
	'PS' => 'Palestine, State of',
	'PA' => 'Panama',
	'PG' => 'Papua New Guinea',
	'PY' => 'Paraguay',
	'PE' => 'Peru',
	'PH' => 'Philippines',
	'PN' => 'Pitcairn',
	'PL' => 'Poland',
	'PT' => 'Portugal',
	'PR' => 'Puerto Rico',
	'QA' => 'Qatar',
	'RE' => 'Réunion',
	'RO' => 'Romania',
	'RU' => 'Russian Federation',
	'RW' => 'Rwanda',
	'BL' => 'Saint Barthélemy',
	'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
	'KN' => 'Saint Kitts and Nevis',
	'LC' => 'Saint Lucia',
	'MF' => 'Saint Martin (French part)',
	'PM' => 'Saint Pierre and Miquelon',
	'VC' => 'Saint Vincent and the Grenadines',
	'WS' => 'Samoa',
	'SM' => 'San Marino',
	'ST' => 'Sao Tome and Principe',
	'SA' => 'Saudi Arabia',
	'SN' => 'Senegal',
	'RS' => 'Serbia',
	'SC' => 'Seychelles',
	'SL' => 'Sierra Leone',
	'SG' => 'Singapore',
	'SX' => 'Sint Maarten (Dutch part)',
	'SK' => 'Slovakia',
	'SI' => 'Slovenia',
	'SB' => 'Solomon Islands',
	'SO' => 'Somalia',
	'ZA' => 'South Africa',
	'GS' => 'South Georgia and the South Sandwich Islands',
	'SS' => 'South Sudan',
	'ES' => 'Spain',
	'LK' => 'Sri Lanka',
	'SD' => 'Sudan',
	'SR' => 'Suriname',
	'SJ' => 'Svalbard and Jan Mayen',
	'SE' => 'Sweden',
	'CH' => 'Switzerland',
	'SY' => 'Syrian Arab Republic',
	'TW' => 'Taiwan, Province of China',
	'TJ' => 'Tajikistan',
	'TZ' => 'Tanzania, United Republic of',
	'TH' => 'Thailand',
	'TL' => 'Timor-Leste',
	'TG' => 'Togo',
	'TK' => 'Tokelau',
	'TO' => 'Tonga',
	'TT' => 'Trinidad and Tobago',
	'TN' => 'Tunisia',
	'TR' => 'Türkiye',
	'TM' => 'Turkmenistan',
	'TC' => 'Turks and Caicos Islands',
	'TV' => 'Tuvalu',
	'UG' => 'Uganda',
	'UA' => 'Ukraine',
	'AE' => 'United Arab Emirates',
	'GB' => 'United Kingdom of Great Britain and Northern Ireland',
	'UM' => 'United States Minor Outlying Islands',
	'US' => 'United States of America',
	'UY' => 'Uruguay',
	'UZ' => 'Uzbekistan',
	'VU' => 'Vanuatu',
	'VE' => 'Venezuela (Bolivarian Republic of)',
	'VN' => 'Viet Nam',
	'VG' => 'Virgin Islands (British)',
	'VI' => 'Virgin Islands (U.S.)',
	'WF' => 'Wallis and Futuna',
	'EH' => 'Western Sahara',
	'YE' => 'Yemen',
	'ZM' => 'Zambia',
	'ZW' => 'Zimbabwe'
));
define('BKF_ORDER_VIEW_TITLE', __('Petals Order #%s', 'bakkbone-florist-companion'));
define('BKF_PETALS_ON', __('Petals Order Number:', 'bakkbone-florist-companion'));
define('BKF_PETALS_AR', __('Accept/Reject', 'bakkbone-florist-companion'));
define('BKF_PETALS_ACCEPT', __('Accept', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT', __('Reject', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_293', __('Cannot deliver flowers', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_294', __('Don\'t have the required flowers', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_270', __('We cannot deliver to this location ever', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_280', __('Cannot deliver to this location today', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_281', __('Do not have these flowers but could do a florist choice', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_282', __('Do not have any flowers to meet delivery date', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_272', __('Need more information to deliver this order', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_283', __('Do not have this container but could do with a substitution of container', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_273', __('Do not do this product ever', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_274', __('There is a problem with this address', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_284', __('This area is restricted, can go on next run but not this delivery date', 'bakkbone-florist-companion'));
define('BKF_PETALS_REJECT_285', __('This area is restricted and can\'t be delivered until next week', 'bakkbone-florist-companion'));
define('BKF_PETALS_FULL_TRANSMISSION', __('<strong>Full transmission from Petals: </strong><br>', 'bakkbone-florist-companion'));
define('BKF_PETALS_MESSAGE_SEND_PROMPT', __("Send Message?", "bakkbone-florist-companion"));
define('BKF_PETALS_MESSAGE_WAIT', __("Please wait...", "bakkbone-florist-companion"));
define('BKF_PETALS_MESSAGE_VALIDATION', __("Please fill in both fields before attempting to send a message.", "bakkbone-florist-companion"));
define('BKF_PETALS_RESPONSE', __('Response from Petals:', 'bakkbone-florist-companion'));
define('BKF_PETALS_MESSAGE_SUCCESS', __('Message successfully sent to Petals:', 'bakkbone-florist-companion'));
define('BKF_PETALS_MESSAGE_FAIL', __('Message FAILED TO SEND to Petals. Your message:', 'bakkbone-florist-companion'));
define('BKF_DD_FULL_TEXT', __('Fully Booked', 'bakkbone-florist-companion'));
define('BKF_DD_CLOSED_TEXT', __('Closed', 'bakkbone-florist-companion'));
define('BKF_DD_UA_TEXT', __('Unavailable', 'bakkbone-florist-companion'));
define('BKF_DD_SDC_TEXT', __('Same Day Delivery Cutoff Passed', 'bakkbone-florist-companion'));
define('BKF_INVALID_NONCE', __('Your request is invalid or the page has been open too long. Please go back and try again.', 'bakkbone-florist-companion'));