<?php

/**
 * @author BAKKBONE Australia
 * @package BkfMetaBoxes
 * @license GNU General Public License (GPL) 3.0
**/


defined("BKF_EXEC") or die("Silent is golden");

/**
 * BkfPluginOptions
**/
class BkfPluginOptions{

	/**
	 * BkfPluginOptions:bkf_options_setting()
	**/
	private $bkf_options_setting = array();



	/**
	 * BkfPluginOptions:__construct()
	**/
	function __construct()
	{
		if(is_admin()){
			// options
			$this->bkf_options_setting = get_option("bkf_options_setting");
			add_action("admin_menu",array($this,"bkfAddOptionsPageOption"));
			add_action("admin_init",array($this,"bkfAddOptionsPageInit"));
			add_action("admin_footer",array($this,"bkfOptionsAdminFooter"));
			add_action("admin_enqueue_scripts",array($this,"bkfOptionsAdminEnqueueScripts"));
			
		}
	}




	// TODO: OPTIONS--------------------------------------------------------------------------


	/**
	 * BkfPluginOptions:bkfAddOptionsPageOption()
	 * @ref: https://developer.wordpress.org/reference/functions/add_menu_page/
	**/
	function bkfAddOptionsPageOption()
	{
		add_menu_page(
			__("Florist Options","bakkbone-florist-companion"), //$page_title
			__("Florist Options","bakkbone-florist-companion"), //$menu_title
			"manage_options", //$capability
			"bkf_options",//$menu_slug
			array($this,"bkfOptionsPageContent")//$function
		);
	}


	/**
	 * BkfPluginOptions:bkfOptionsPageContent()
	**/
	function bkfOptionsPageContent()
	{
		$this->bkf_options_setting = get_option("bkf_options_setting");
		?>
		<div class="wrap">
			<h1><?php _e("Florist Options","bakkbone-florist-companion") ?></h1>
			<div class="bkf-box">
				<div class="inside">
					<form method="post" action="options.php">
						<?php settings_fields("bkf_options_group"); ?>
						<?php do_settings_sections("bkf-options"); ?>
						<?php submit_button(); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * BkfPluginOptions:bkfAddOptionsPageInit()
	**/
	function bkfAddOptionsPageInit()
	{
		
		
		register_setting(
			"bkf_options_group",// group
			"bkf_options_setting", //setting name
			array($this,"bkfAddOptionsSanitize") //sanitize_callback
		);
		
		
		add_settings_section(
			"bkf_options_section", //id
			__("General Settings","bakkbone-florist-companion"), //title
			array($this,"bkfAddOptionsInfo"), //callback
			"bkf-options" //page
		);
		
		// card-length
		add_settings_field(
			"bkf_card_length", //id
			__("Card Message Length","bakkbone-florist-companion"), //title
			array($this,"bkfCardLengthCallback"), //callback
			"bkf-options", //page
			"bkf_options_section" //section
		);
		
		// excerpt-pa
		add_settings_field(
			"bkf_excerpt_pa", //id
			__("Product Archives","bakkbone-florist-companion"), //title
			array($this,"bkfExcerptPaCallback"), //callback
			"bkf-options", //page
			"bkf_options_section" //section
		);
		
		// cs-heading
		add_settings_field(
			"bkf_cs_heading", //id
			__("Cross-Sell Cart Heading","bakkbone-florist-companion"), //title
			array($this,"bkfCsHeadingCallback"), //callback
			"bkf-options", //page
			"bkf_options_section" //section
		);
	}


	/**
	 * BkfPluginOptions:bkfAddOptionsSanitize()
	**/
	function bkfAddOptionsSanitize($input)
	{
		$new_input = array();
		
		
		// card-length
		if(isset($input["bkf_card_length"]))
			$new_input["bkf_card_length"] = sanitize_text_field($input["bkf_card_length"]);
		
		
		
		// excerpt-pa
		if(isset($input["bkf_excerpt_pa"])){
			$new_input["bkf_excerpt_pa"] = true;
		}else{
			$new_input["bkf_excerpt_pa"] = false;
		}
		
		
		// cs-heading
		if(isset($input["bkf_cs_heading"]))
			$new_input["bkf_cs_heading"] = sanitize_text_field($input["bkf_cs_heading"]);
		
		return $new_input;
	}


	/**
	 * BkfPluginOptions:bkfAddOptionsInfo()
	**/
	function bkfAddOptionsInfo()
	{
		_e("Enter your settings below:","bakkbone-florist-companion");
	}
	
	
	/**
	 * BkfPluginOptions:bkfCardLengthCallback()
	**/
	function bkfCardLengthCallback(){
	
		if(isset($this->bkf_options_setting["bkf_card_length"])){
			$value = esc_attr($this->bkf_options_setting["bkf_card_length"]);
		}else{
			$value = "";
		}
		?>
		<input class="small-text" id="bkf-card-length" type="number" name="bkf_options_setting[bkf_card_length]" placeholder="250" value="<?php echo esc_attr( $value ); ?>" />
		<p class="description"><?php _e("Maximum number of characters (including spaces/punctuation) a customer will be able to enter in the Card Message field.","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	
	/**
	 * BkfPluginOptions:bkfExcerptPaCallback()
	**/
	function bkfExcerptPaCallback(){
	
		if(!isset($this->bkf_options_setting["bkf_excerpt_pa"])){
			$this->bkf_options_setting["bkf_excerpt_pa"] = false;
		}
		if($this->bkf_options_setting["bkf_excerpt_pa"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<p><input id="bkf-excerpt-pa" <?php echo $checked ?> type="checkbox" name="bkf_options_setting[bkf_excerpt_pa]" /> <?php _e("Display Short Description in product archives","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	
	/**
	 * BkfPluginOptions:bkfCsHeadingCallback()
	**/
	function bkfCsHeadingCallback(){
	
		if(isset($this->bkf_options_setting["bkf_cs_heading"])){
			$value = esc_attr($this->bkf_options_setting["bkf_cs_heading"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text" id="bkf-cs-heading" type="text" name="bkf_options_setting[bkf_cs_heading]" placeholder="How about adding..." value="<?php echo esc_attr( $value ); ?>" />
		<p class="description"><?php _e("Replaces the heading of the Cross-Sells section of the Cart page","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	
	
	
	/**
	 * BkfPluginOptions:bkfOptionsAdminFooter($hook)
	**/
	function bkfOptionsAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_options")
		{
			//card-length
			
			//excerpt-pa
			
			//cs-heading
			
		}
	}
	
	
	/**
	 * BkfPluginOptions:bkfOptionsAdminEnqueueScripts($hook)
	**/
	function bkfOptionsAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_options")
		{
			//card-length
			
			//excerpt-pa
			
			//cs-heading
			
		}
	}
	
	
}
