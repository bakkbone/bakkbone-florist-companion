<?php

/**
 * @author BAKKBONE Australia
 * @package BkfLocalisation
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfLocalisation{
    
	private $bkf_localisation_setting = array();
		
	function __construct() {
		global $bkf_localisation_version;
		$bkf_localisation_version = 2.1;
		add_action("plugins_loaded",array($this,"bkf_update_localisation_check"));
        $this->bkf_localisation_setting = get_option("bkf_localisation_setting");
        add_action("admin_menu", array($this,"bkf_localisation_menu"),60);
        add_action("admin_init",array($this,"bkfAddLocalisationPageInit"));
		add_action("admin_footer",array($this,"bkfLocalisationAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfLocalisationAdminEnqueueScripts"));
        if (in_array("gravityforms/gravityforms.php", apply_filters("active_plugins", get_option("active_plugins")))){
            add_filter( "gform_phone_formats" , array($this, "bkf_au_phone_format") );
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
			}
		}
	}
	
    function bkf_localisation_menu(){
		$localisation = __("Localisation","bakkbone-florist-companion");
        $admin_page = add_submenu_page(
	        "bkf_options",
	        $localisation,
	        $localisation,
	        "manage_options",
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
			<a href="https://docs.bkbn.au/v/bkf/florist-options/localisation" target="_blank">https://docs.bkbn.au/v/bkf/florist-options/localisation</a>
		<?php
	}
    
    function bkf_localisation_settings_page()
    {
        $this->bkf_localisation_setting = get_option("bkf_localisation_setting");

        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Localisation Settings","bakkbone-florist-companion") ?></h1>
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
		<p class="description"><?php _e('Insert <strong>%s</strong> where your maximum characters will appear.<br><em>eg. Entering "Maximum %s characters" will display "Maximum 250 characters" at checkout.</em>',"bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function bkfCsHeadingCallback(){
	
		if(isset($this->bkf_localisation_setting["csheading"])){
			$value = esc_attr($this->bkf_localisation_setting["csheading"]);
		}else{
			$value = default_csheading;
		}
		?>
		<input class="bkf-form-control large-text" id="csheading" type="text" name="bkf_localisation_setting[csheading]" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Replaces the heading of the Cross-Sells section of the Cart page","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfNoshipCallback(){
		
		if(isset($this->bkf_localisation_setting["noship"])){
			$value = esc_attr($this->bkf_localisation_setting["noship"]);
		}else{
			$value = default_noship;
		}
		?>
		<input class="bkf-form-control large-text" id="noship" type="text" name="bkf_localisation_setting[noship]" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Displays at checkout if the delivery address' suburb is not serviced.","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function bkfLocalisationAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_localisation")
		{

		}
	}
	
	function bkfLocalisationAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_localisation")
		{
		}
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
	
}