<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdFeesOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdFeesOptions{
	
    private $bkf_ddf_setting = array();
    private $bkf_wf_setting = array();

    function __construct(){
        $this->bkf_ddf_setting = get_option("bkf_ddf_setting");
        $this->bkf_wf_setting = get_option("bkf_wf_setting");
        add_action("admin_menu", array($this,"bkfee_admin_menu"),81);
        add_action("admin_init",array($this,"bkfAddFeesPageInit"));
		add_action("admin_footer",array($this,"bkfFeesAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfFeesAdminEnqueueScripts"));
    }
	
    function bkfee_admin_menu(){
        add_submenu_page(
        "bkf_dd",//parent slug
        __("Additional Fees","bakkbone-florist-companion"),//page title
        __("Fees","bakkbone-florist-companion"),//menu title
        "manage_options",//capability
        "bkf_fees",//menu slug
        array($this, "bkf_fees_settings_page"),//callback
        8.11
        );
    }
    
    function bkf_fees_settings_page()
    {
        $this->bkf_ddf_setting = get_option("bkf_ddf_setting");
        $this->bkf_wf_setting = get_option("bkf_wf_setting");
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Date Fees","bakkbone-florist-companion") ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_ddf_options_group"); ?>
                        <?php do_settings_sections("bkf-fees"); ?>
                        <?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'ddf_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
	function bkfAddFeesPageInit()
	{
		
		register_setting(
			"bkf_ddf_options_group",// group
			"bkf_ddf_setting", //setting name
			array($this,"bkfAddDdfOptionsSanitize") //sanitize_callback
		);
		register_setting(
			"bkf_ddf_options_group",// group
			"bkf_wf_setting", //setting name
			array($this,"bkfAddWfOptionsSanitize") //sanitize_callback
		);

		add_settings_section(
			"bkf_ddf_section", //id
			__("Taxable Status","bakkbone-florist-companion"), //title
			array($this,"bkfDdfInfo"), //callback
			"bkf-fees" //page
		);

		add_settings_section(
			"bkf_wf_section", //id
			__("Weekday Fees","bakkbone-florist-companion"), //title
			array($this,"bkfWfOptionsInfo"), //callback
			"bkf-fees" //page
		);

		add_settings_field(
			"bkf_ddtst", //id
			__("Time Slot Fees","bakkbone-florist-companion"), //title
			array($this,"bkfDdtstCallback"), //callback
			"bkf-fees", //page
			"bkf_ddf_section" //section
		);
		add_settings_field(
			"bkf_ddwft", //id
			__("Weekday Fees","bakkbone-florist-companion"), //title
			array($this,"bkfDdwftCallback"), //callback
			"bkf-fees", //page
			"bkf_ddf_section" //section
		);
		add_settings_field(
			"bkf_dddft", //id
			__("Date Fees","bakkbone-florist-companion"), //title
			array($this,"bkfDddftCallback"), //callback
			"bkf-fees", //page
			"bkf_ddf_section" //section
		);
				
		// monday
		add_settings_field(
			"bkf_wf_monday", //id
			__("Monday","bakkbone-florist-companion"), //title
			array($this,"bkfWfMondayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);

		// tuesday
		add_settings_field(
			"bkf_wf_tuesday", //id
			__("Tuesday","bakkbone-florist-companion"), //title
			array($this,"bkfWfTuesdayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);
		
		// wednesday
		add_settings_field(
			"bkf_wf_wednesday", //id
			__("Wednesday","bakkbone-florist-companion"), //title
			array($this,"bkfWfWednesdayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);
		
		// thursday
		add_settings_field(
			"bkf_wf_thursday", //id
			__("Thursday","bakkbone-florist-companion"), //title
			array($this,"bkfWfThursdayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);
		
		// friday
		add_settings_field(
			"bkf_wf_friday", //id
			__("Friday","bakkbone-florist-companion"), //title
			array($this,"bkfWfFridayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);
		
		// saturday
		add_settings_field(
			"bkf_wf_saturday", //id
			__("Saturday","bakkbone-florist-companion"), //title
			array($this,"bkfWfSaturdayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);
		
		// sunday
		add_settings_field(
			"bkf_wf_sunday", //id
			__("Sunday","bakkbone-florist-companion"), //title
			array($this,"bkfWfSundayCallback"), //callback
			"bkf-fees", //page
			"bkf_wf_section" //section
		);
	}
	
	function bkfAddDdfOptionsSanitize($input)
	{
		$new_input = array();

		if(isset($input["ddtst"])){
			$new_input["ddtst"] = true;
		}else{
			$new_input["ddtst"] = false;
		}

		if(isset($input["ddwft"])){
			$new_input["ddwft"] = true;
		}else{
			$new_input["ddwft"] = false;
		}

		if(isset($input["dddft"])){
			$new_input["dddft"] = true;
		}else{
			$new_input["dddft"] = false;
		}
	
		return $new_input;
	}	
	
	function bkfAddWfOptionsSanitize($input)
	{
		$new_input = array();


		if(isset($input["monday"])){
			$new_input["monday"] = sanitize_text_field($input["monday"]);
		}
		
		if(isset($input["tuesday"])){
			$new_input["tuesday"] = sanitize_text_field($input["tuesday"]);
		}
		
		if(isset($input["wednesday"])){
			$new_input["wednesday"] = sanitize_text_field($input["wednesday"]);
		}
		
		if(isset($input["thursday"])){
			$new_input["thursday"] = sanitize_text_field($input["thursday"]);
		}
		
		if(isset($input["friday"])){
			$new_input["friday"] = sanitize_text_field($input["friday"]);
		}
		
		if(isset($input["saturday"])){
			$new_input["saturday"] = sanitize_text_field($input["saturday"]);
		}
		
		if(isset($input["sunday"])){
			$new_input["sunday"] = sanitize_text_field($input["sunday"]);
		}
		
		return $new_input;
	}
	
	
	function bkfDdfInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("If taxable status is turned on, fees must be entered <em>exclusive</em> of tax. Time Slot Fees are managed on the Timeslots settings page.", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfWfOptionsInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("Optionally set an additional fee for delivery on specific weekdays. If taxable status is turned on, fees must be entered <em>exclusive</em> of tax. Leave a row blank to not apply a fee.", "bakkbone-florist-companion");
		echo '</p>';
	}

	function bkfDdwftCallback(){
		if(!isset($this->bkf_ddf_setting["ddwft"])){
			$this->bkf_ddf_setting["ddwft"] = false;
		}
		if($this->bkf_ddf_setting["ddwft"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Weekday Fees are taxable","bakkbone-florist-companion") ?><input id="bkf-ddwft" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_ddf_setting[ddwft]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfDdtstCallback(){
		if(!isset($this->bkf_ddf_setting["ddtst"])){
			$this->bkf_ddf_setting["ddtst"] = false;
		}
		if($this->bkf_ddf_setting["ddtst"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Time Slot Fees are taxable","bakkbone-florist-companion") ?><input id="bkf-ddtst" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_ddf_setting[ddtst]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfDddftCallback(){
		if(!isset($this->bkf_ddf_setting["dddft"])){
			$this->bkf_ddf_setting["dddft"] = false;
		}
		if($this->bkf_ddf_setting["dddft"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Date-Specific Fees are taxable","bakkbone-florist-companion") ?><input id="bkf-dddft" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_ddf_setting[dddft]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfWfMondayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["monday"]) || (get_option("bkf_dd_setting")["monday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["monday"])){
			$value = esc_attr($this->bkf_wf_setting["monday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-monday" type="text" name="bkf_wf_setting[monday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfWfTuesdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["tuesday"]) || (get_option("bkf_dd_setting")["tuesday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["tuesday"])){
			$value = esc_attr($this->bkf_wf_setting["tuesday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-tuesday" type="text" name="bkf_wf_setting[tuesday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfWfWednesdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["wednesday"]) || (get_option("bkf_dd_setting")["wednesday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["wednesday"])){
			$value = esc_attr($this->bkf_wf_setting["wednesday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-wednesday" type="text" name="bkf_wf_setting[wednesday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfWfThursdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["thursday"]) || (get_option("bkf_dd_setting")["thursday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["thursday"])){
			$value = esc_attr($this->bkf_wf_setting["thursday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-thursday" type="text" name="bkf_wf_setting[thursday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfWfFridayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["friday"]) || (get_option("bkf_dd_setting")["friday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["friday"])){
			$value = esc_attr($this->bkf_wf_setting["friday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-friday" type="text" name="bkf_wf_setting[friday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfWfSaturdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["saturday"]) || (get_option("bkf_dd_setting")["saturday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["saturday"])){
			$value = esc_attr($this->bkf_wf_setting["saturday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-saturday" type="text" name="bkf_wf_setting[saturday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfWfSundayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["sunday"]) || (get_option("bkf_dd_setting")["sunday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_wf_setting["sunday"])){
			$value = esc_attr($this->bkf_wf_setting["sunday"]);
		}else{
			$value = "";
		}
		echo get_woocommerce_currency_symbol(get_woocommerce_currency()); ?>
		<input class="regular-text bkf-form-control" id="bkf-wf-sunday" type="text" name="bkf_wf_setting[sunday]" placeholder="***.**" pattern="\d+\.\d{2,}" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php		
	}
	
	function bkfFeesAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_fees")
		{

		}
	}
	
	function bkfFeesAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_fees")
		{

		}
	}
	
}