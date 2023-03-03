<?php

/**
 * @author BAKKBONE Australia
 * @package BkfDdOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfDdOptions{

    private $bkf_ddi_setting = array();
    private $bkf_dd_setting = array();
    private $bkf_sd_setting = array();
    private $bkf_dm_setting = array();

    function __construct(){
        $this->bkf_ddi_setting = get_option("bkf_ddi_setting");
        $this->bkf_dd_setting = get_option("bkf_dd_setting");
        $this->bkf_sd_setting = get_option("bkf_sd_setting");
        $this->bkf_dm_setting = get_option("bkf_dm_setting");
        add_action("admin_menu", array($this,"bkf_admin_menu"),1);
        add_action("admin_init",array($this,"bkfAddDdPageInit"));
		add_action("admin_footer",array($this,"bkfDdAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfDdAdminEnqueueScripts"));
        add_action('add_meta_boxes', array($this, 'bkf_dd_metabox_init') );
        add_action('save_post', array($this, 'bkf_dd_save_metabox_data') );
    }

    function bkf_admin_menu(){
		add_menu_page(
			null,
			__("Delivery Dates","bakkbone-florist-companion"),
			"manage_options",
			"bkf_dd",
			null,
			'dashicons-clipboard',
			2.2
		);
        $ddpage = add_submenu_page(
        "bkf_dd",
        __("Delivery Dates","bakkbone-florist-companion"),
        __("General Options","bakkbone-florist-companion"),
        "manage_options",
        "bkf_dd",
        array($this, "bkf_dd_settings_page"),
        1
        );
		$wd = __("Weekdays","bakkbone-florist-companion");
        $wdpage = add_submenu_page(
        "bkf_dd",
        $wd,
        $wd,
        "manage_options",
        "bkf_dd_wd",
        array($this, "bkf_wd_settings_page"),
        2
        );
        $sdpage = add_submenu_page(
        "bkf_dd",
        __("Same Day Delivery Cutoffs","bakkbone-florist-companion"),
        __("Same Day","bakkbone-florist-companion"),
        "manage_options",
        "bkf_dd_sd",
        array($this, "bkf_sd_settings_page"),
        3.1
        );
        $dmpage = add_submenu_page(
        "bkf_dd",
        __("Delivery Method Restrictions","bakkbone-florist-companion"),
        __("Methods","bakkbone-florist-companion"),
        "manage_options",
        "bkf_dd_dm",
        array($this, "bkf_dm_settings_page"),
        4
        );
		add_action( 'load-'.$ddpage, array($this, 'bkf_dd_help_tab') );
		add_action( 'load-'.$wdpage, array($this, 'bkf_wd_help_tab') );
		add_action( 'load-'.$sdpage, array($this, 'bkf_sd_help_tab') );
		add_action( 'load-'.$dmpage, array($this, 'bkf_dm_help_tab') );
    }
	
	function bkf_dd_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_dd_help';
		$callback = array($this, 'bkf_dd_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_dd_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/general-options" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/general-options</a>
		<?php
	}
	    
	function bkf_wd_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_wd_help';
		$callback = array($this, 'bkf_wd_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_wd_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/weekdays" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/weekdays</a>
		<?php
	}
	    
	function bkf_sd_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_sd_help';
		$callback = array($this, 'bkf_sd_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_sd_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/same-day" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/same-day</a>
		<?php
	}
	    
	function bkf_dm_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_dm_help';
		$callback = array($this, 'bkf_dm_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_dm_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/delivery-dates/methods" target="_blank">https://docs.bkbn.au/v/bkf/delivery-dates/methods</a>
		<?php
	}
	    
    function bkf_dd_settings_page()
    {
        $this->bkf_ddi_setting = get_option("bkf_ddi_setting");
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Date Settings","bakkbone-florist-companion") ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_ddi_options_group"); ?>
                        <?php do_settings_sections("bkf-ddi"); ?>
                        <?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'ddi_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
	
    function bkf_wd_settings_page()
    {
        $this->bkf_dd_setting = get_option("bkf_dd_setting");
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Weekdays","bakkbone-florist-companion") ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_dd_options_group"); ?>
                        <?php do_settings_sections("bkf-dd"); ?>
                        <?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'dd_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
	
    function bkf_sd_settings_page()
    {
        $this->bkf_sd_setting = get_option("bkf_sd_setting");
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Same Day Delivery","bakkbone-florist-companion") ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_sd_options_group"); ?>
                        <?php do_settings_sections("bkf-sd"); ?>
                        <?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'sd_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
	
    function bkf_dm_settings_page()
    {
        $this->bkf_dm_setting = get_option("bkf_dm_setting");
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Method Restrictions","bakkbone-florist-companion") ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_dm_options_group"); ?>
                        <?php do_settings_sections("bkf-dm"); ?>
                        <?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'dm_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
	function bkfAddDdPageInit()
	{
		
		register_setting(
			"bkf_ddi_options_group",
			"bkf_ddi_setting",
			array($this,"bkfAddDdiOptionsSanitize")
		);
		register_setting(
			"bkf_dd_options_group",
			"bkf_dd_setting",
			array($this,"bkfAddDdOptionsSanitize")
		);
		register_setting(
			"bkf_sd_options_group",
			"bkf_sd_setting",
			array($this,"bkfAddSdOptionsSanitize")
		);
		register_setting(
			"bkf_dm_options_group",
			"bkf_dm_setting",
			array($this,"bkfAddDmOptionsSanitize")
		);

		add_settings_section(
			"bkf_ddi_section", //id
			__("Options","bakkbone-florist-companion"), //title
			array($this,"bkfDdiInfo"), //callback
			"bkf-ddi" //page
		);
		
		add_settings_section(
			"bkf_dd_section", //id
			__("Delivery Weekdays","bakkbone-florist-companion"), //title
			array($this,"bkfDdOptionsInfo"), //callback
			"bkf-dd" //page
		);
		
		add_settings_section(
			"bkf_sd_section", //id
			__("Same Day Cutoffs","bakkbone-florist-companion"), //title
			array($this,"bkfSdOptionsInfo"), //callback
			"bkf-sd" //page
		);

		add_settings_section(
			"bkf_dm_section", //id
			__("Delivery Methods","bakkbone-florist-companion"), //title
			array($this,"bkfDmOptionsInfo"), //callback
			"bkf-dm" //page
		);

		add_settings_field(
			"bkf_ddi", //id
			__("Pre-ordering","bakkbone-florist-companion"), //title
			array($this,"bkfDdiCallback"), //callback
			"bkf-ddi", //page
			"bkf_ddi_section" //section
		);
		add_settings_field(
			"bkf_ddt", //id
			__("Title","bakkbone-florist-companion"), //title
			array($this,"bkfDdtCallback"), //callback
			"bkf-ddi", //page
			"bkf_ddi_section" //section
		);
				
		// monday
		add_settings_field(
			"bkf_dd_monday", //id
			__("Monday","bakkbone-florist-companion"), //title
			array($this,"bkfMondayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_monday", //id
			__("Monday","bakkbone-florist-companion"), //title
			array($this,"bkfSdMondayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_monday", //id
			__("Monday","bakkbone-florist-companion"), //title
			array($this,"bkfDmMondayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);

		// tuesday
		add_settings_field(
			"bkf_dd_tuesday", //id
			__("Tuesday","bakkbone-florist-companion"), //title
			array($this,"bkfTuesdayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_tuesday", //id
			__("Tuesday","bakkbone-florist-companion"), //title
			array($this,"bkfSdTuesdayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_tuesday", //id
			__("Tuesday","bakkbone-florist-companion"), //title
			array($this,"bkfDmTuesdayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);
		
		// wednesday
		add_settings_field(
			"bkf_dd_wednesday", //id
			__("Wednesday","bakkbone-florist-companion"), //title
			array($this,"bkfWednesdayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_wednesday", //id
			__("Wednesday","bakkbone-florist-companion"), //title
			array($this,"bkfSdWednesdayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_wednesday", //id
			__("Wednesday","bakkbone-florist-companion"), //title
			array($this,"bkfDmWednesdayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);
		
		// thursday
		add_settings_field(
			"bkf_dd_thursday", //id
			__("Thursday","bakkbone-florist-companion"), //title
			array($this,"bkfThursdayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_thursday", //id
			__("Thursday","bakkbone-florist-companion"), //title
			array($this,"bkfSdThursdayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_thursday", //id
			__("Thursday","bakkbone-florist-companion"), //title
			array($this,"bkfDmThursdayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);
		
		// friday
		add_settings_field(
			"bkf_dd_friday", //id
			__("Friday","bakkbone-florist-companion"), //title
			array($this,"bkfFridayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_friday", //id
			__("Friday","bakkbone-florist-companion"), //title
			array($this,"bkfSdFridayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_friday", //id
			__("Friday","bakkbone-florist-companion"), //title
			array($this,"bkfDmFridayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);
		
		// saturday
		add_settings_field(
			"bkf_dd_saturday", //id
			__("Saturday","bakkbone-florist-companion"), //title
			array($this,"bkfSaturdayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_saturday", //id
			__("Saturday","bakkbone-florist-companion"), //title
			array($this,"bkfSdSaturdayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_saturday", //id
			__("Saturday","bakkbone-florist-companion"), //title
			array($this,"bkfDmSaturdayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);

		// sunday
		add_settings_field(
			"bkf_dd_sunday", //id
			__("Sunday","bakkbone-florist-companion"), //title
			array($this,"bkfSundayCallback"), //callback
			"bkf-dd", //page
			"bkf_dd_section" //section
		);
		add_settings_field(
			"bkf_sd_sunday", //id
			__("Sunday","bakkbone-florist-companion"), //title
			array($this,"bkfSdSundayCallback"), //callback
			"bkf-sd", //page
			"bkf_sd_section" //section
		);
		add_settings_field(
			"bkf_dm_sunday", //id
			__("Sunday","bakkbone-florist-companion"), //title
			array($this,"bkfDmSundayCallback"), //callback
			"bkf-dm", //page
			"bkf_dm_section" //section
		);

	}
	
	function bkfAddDdiOptionsSanitize($input)
	{
		$new_input = array();
			
		if(isset($input["ddi"])){
			$new_input["ddi"] = sanitize_text_field($input["ddi"]);
		}

		if(isset($input["ddt"])){
			$new_input["ddt"] = sanitize_text_field($input["ddt"]);
		}
	
		return $new_input;
	}	
	
	function bkfAddDdOptionsSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["monday"])){
			$new_input["monday"] = true;
		}else{
			$new_input["monday"] = false;
		}
		
		if(isset($input["tuesday"])){
			$new_input["tuesday"] = true;
		}else{
			$new_input["tuesday"] = false;
		}
		
		if(isset($input["wednesday"])){
			$new_input["wednesday"] = true;
		}else{
			$new_input["wednesday"] = false;
		}
		
		if(isset($input["thursday"])){
			$new_input["thursday"] = true;
		}else{
			$new_input["thursday"] = false;
		}
		
		if(isset($input["friday"])){
			$new_input["friday"] = true;
		}else{
			$new_input["friday"] = false;
		}
		
		if(isset($input["saturday"])){
			$new_input["saturday"] = true;
		}else{
			$new_input["saturday"] = false;
		}
		
		if(isset($input["sunday"])){
			$new_input["sunday"] = true;
		}else{
			$new_input["sunday"] = false;
		}
		
		return $new_input;
	}

	function bkfAddSdOptionsSanitize($input)
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
	
	function bkfAddSmOptionsSanitize($input)
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
	
	function bkfDdiInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("Enter your settings below.", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfDdOptionsInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("Select which days of the week you deliver.", "bakkbone-florist-companion");
		echo '</p>';
	}

	function bkfSdOptionsInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("Set your same day delivery cutoffs - save your delivery weekdays above to display the correct fields below. If you don't accept same day delivery orders, set the cutoff as midnight (12:00 am).", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfDmOptionsInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("If a particular delivery method is unavailable on a certain day you are otherwise open, indicate this here. Save your delivery weekdays above to display the correct fields below. Remember, delivery methods selected below will <strong>not</strong> be available on the day indicated.", "bakkbone-florist-companion");
		echo '</p><p class="bkf-pageinfo"><em>';
		_e("Hold down Ctrl (Windows) or Cmd (Mac) while selecting, to choose multiple options.", "bakkbone-florist-companion");
		echo '</em></p>';
	}

	function bkfDdiCallback(){
	
		if(isset($this->bkf_ddi_setting["ddi"])){
			$value = esc_attr($this->bkf_ddi_setting["ddi"]);
		}else{
			$value = "52";
		}
		?>
		<input class="bkf-form-control small-text" id="bkf-ddi" type="number" name="bkf_ddi_setting[ddi]" placeholder="52" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Maximum number of weeks in the future to enable at checkout.","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfDdtCallback(){
		$opt1 = __("Delivery Date", "bakkbone-florist-companion");
		$opt2 = __("Delivery/Collection Date", "bakkbone-florist-companion");
		$opt3 = __("Order Date", "bakkbone-florist-companion");
		
		if(isset($this->bkf_ddi_setting["ddt"])){
			$value = esc_attr($this->bkf_ddi_setting["ddt"]);
		}else{
			$value = $opt1;
		}
		?>
		<p class="description"><?php _e("What shall we call this field at checkout?","bakkbone-florist-companion") ?></p>
		<div style="display: inline;">
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt1" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt1; ?>"<?php if($value == $opt1){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt1; ?></label>
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt2" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt2; ?>"<?php if($value == $opt2){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt2; ?></label>
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt3" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt3; ?>"<?php if($value == $opt3){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt3; ?></label>
	</div>
		<?php
	}

	function bkfMondayCallback(){
		if(!isset($this->bkf_dd_setting["monday"])){
			$this->bkf_dd_setting["monday"] = false;
		}
		if($this->bkf_dd_setting["monday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Mondays","bakkbone-florist-companion") ?><input id="bkf-dd-monday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[monday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdMondayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["monday"]) || (get_option("bkf_dd_setting")["monday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["monday"])){
			$value = esc_attr($this->bkf_sd_setting["monday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-monday" type="time" step="300" name="bkf_sd_setting[monday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmMondayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["monday"]) || (get_option("bkf_dd_setting")["monday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-monday" name="bkf_dm_setting[monday][]" multiple <?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['monday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['monday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
	
	function bkfTuesdayCallback(){
		if(!isset($this->bkf_dd_setting["tuesday"])){
			$this->bkf_dd_setting["tuesday"] = false;
		}
		if($this->bkf_dd_setting["tuesday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Tuesdays","bakkbone-florist-companion") ?><input id="bkf-dd-tuesday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[tuesday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdTuesdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["tuesday"]) || (get_option("bkf_dd_setting")["tuesday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["tuesday"])){
			$value = esc_attr($this->bkf_sd_setting["tuesday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-tuesday" type="time" step="300" name="bkf_sd_setting[tuesday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}

	function bkfDmTuesdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["tuesday"]) || (get_option("bkf_dd_setting")["tuesday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }	
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-tuesday" name="bkf_dm_setting[tuesday][]" multiple<?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['tuesday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['tuesday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
		
	function bkfWednesdayCallback(){
		if(!isset($this->bkf_dd_setting["wednesday"])){
			$this->bkf_dd_setting["wednesday"] = false;
		}
		if($this->bkf_dd_setting["wednesday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Wednesdays","bakkbone-florist-companion") ?><input id="bkf-dd-wednesday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[wednesday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdWednesdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["wednesday"]) || (get_option("bkf_dd_setting")["wednesday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["wednesday"])){
			$value = esc_attr($this->bkf_sd_setting["wednesday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-wednesday" type="time" step="300" name="bkf_sd_setting[wednesday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmWednesdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["wednesday"]) || (get_option("bkf_dd_setting")["wednesday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }	
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-wednesday" name="bkf_dm_setting[wednesday][]" multiple<?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['wednesday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['wednesday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
	
	function bkfThursdayCallback(){
		if(!isset($this->bkf_dd_setting["thursday"])){
			$this->bkf_dd_setting["thursday"] = false;
		}
		if($this->bkf_dd_setting["thursday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Thursdays","bakkbone-florist-companion") ?><input id="bkf-dd-thursday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[thursday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdThursdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["thursday"]) || (get_option("bkf_dd_setting")["thursday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["thursday"])){
			$value = esc_attr($this->bkf_sd_setting["thursday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-thursday" type="time" step="300" name="bkf_sd_setting[thursday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmThursdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["thursday"]) || (get_option("bkf_dd_setting")["thursday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }	
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-thursday" name="bkf_dm_setting[thursday][]" multiple<?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['thursday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['thursday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
		
	function bkfFridayCallback(){
		if(!isset($this->bkf_dd_setting["friday"])){
			$this->bkf_dd_setting["friday"] = false;
		}
		if($this->bkf_dd_setting["friday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Fridays","bakkbone-florist-companion") ?><input id="bkf-dd-friday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[friday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdFridayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["friday"]) || (get_option("bkf_dd_setting")["friday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["friday"])){
			$value = esc_attr($this->bkf_sd_setting["friday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-friday" type="time" step="300" name="bkf_sd_setting[friday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}

	function bkfDmFridayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["friday"]) || (get_option("bkf_dd_setting")["friday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }	
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-friday" name="bkf_dm_setting[friday][]" multiple<?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['friday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['friday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
		
	function bkfSaturdayCallback(){
		if(!isset($this->bkf_dd_setting["saturday"])){
			$this->bkf_dd_setting["saturday"] = false;
		}
		if($this->bkf_dd_setting["saturday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Saturdays","bakkbone-florist-companion") ?><input id="bkf-dd-saturday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[saturday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdSaturdayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["saturday"]) || (get_option("bkf_dd_setting")["saturday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["saturday"])){
			$value = esc_attr($this->bkf_sd_setting["saturday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-saturday" type="time" step="300" name="bkf_sd_setting[saturday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmSaturdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["saturday"]) || (get_option("bkf_dd_setting")["saturday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-saturday" name="bkf_dm_setting[saturday][]" multiple<?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['saturday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['saturday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
		
	function bkfSundayCallback(){
		if(!isset($this->bkf_dd_setting["sunday"])){
			$this->bkf_dd_setting["sunday"] = false;
		}
		if($this->bkf_dd_setting["sunday"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Deliver on Sundays","bakkbone-florist-companion") ?><input id="bkf-dd-sunday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[sunday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSdSundayCallback(){
	    if(!isset(get_option("bkf_dd_setting")["sunday"]) || (get_option("bkf_dd_setting")["sunday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
	    
		if(isset($this->bkf_sd_setting["sunday"])){
			$value = esc_attr($this->bkf_sd_setting["sunday"]);
		}else{
			$value = "";
		}
		?>
		<input class="regular-text bkf-form-control" id="bkf-sd-sunday" type="time" step="300" name="bkf_sd_setting[sunday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<?php
	}

	function bkfDmSundayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = array();
	    $zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = array();
		foreach($zones as $zone){
			$methods = $zone->get_shipping_methods();
			
			foreach($methods as $method){
	            $method_is_taxable = $method->is_taxable();
	            $method_is_enabled = $method->is_enabled();
	            $method_instance_id = $method->get_instance_id();
	            $method_title = $method->get_method_title();
	            $method_description = $method->get_method_description();
	            $method_user_title = $method->get_title();
	            $method_rate_id = $method->get_rate_id();
				$sm[] = array(
					'enabled'		=>	$method_is_enabled,
					'taxable'		=>	$method_is_taxable,
					'instanceid'	=>	$method_instance_id,
					'title'			=>	$method_title,
					'description'	=>	$method_description,
					'usertitle'		=>	$method_user_title,
					'rateid'		=>	$method_rate_id
				);
			}
		}

	    if(!isset(get_option("bkf_dd_setting")["sunday"]) || (get_option("bkf_dd_setting")["sunday"] == false)){
	        $disabled = " disabled";
	    }else{
	        $disabled = "";
	    }
		?>
		<select class="regular-text bkf-form-control" id="bkf-dm-sunday" name="bkf_dm_setting[sunday][]" multiple<?php echo $disabled; ?>>
			<?php
			foreach($sm as $smethod){
				if(isset($this->bkf_dm_setting['sunday'])){
					if(in_array($smethod['rateid'], $this->bkf_dm_setting['sunday'])){
					$selected = ' selected';
				} else {
					$selected = '';
				}} else {
					$selected = '';
				}
				echo '<option value="'.$smethod['rateid'].'"'.$selected.'>'.$smethod['title'].' #'.$smethod['instanceid'].' - '.$smethod['usertitle'].'</option>';
			}
			?>
		</select>
		<?php
	}
	
	function bkfDdAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_dd")
		{

		}
		if($screen->id == "delivery-dates_page_bkf_dd_wd")
		{

		}
		if($screen->id == "delivery-dates_page_bkf_dd_sd")
		{

		}
		if($screen->id == "delivery-dates_page_bkf_dd_dm")
		{

		}
	}
	
	function bkfDdAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "delivery-dates_page_bkf_dd")
		{

		}
		if($screen->id == "delivery-dates_page_bkf_dd_wd")
		{

		}
		if($screen->id == "delivery-dates_page_bkf_dd_sd")
		{

		}
		if($screen->id == "delivery-dates_page_bkf_dd_dm")
		{

		}
	}
	
    public function bkf_dd_metabox_init(){
        add_meta_box('bkf_dd', __('Delivery Date', 'bakkbone-florist-companion'),array($this, 'bkf_dd_metabox_callback'),'shop_order','side','core');
    }
    
    public function bkf_dd_metabox_callback( $post ){
        ?>
        <?php
		$maxdate = get_option('bkf_ddi_setting')['ddi'];
        $delivery_date = get_post_meta( get_the_id(), '_delivery_date', true );
        $delivery_timeslot = get_post_meta( get_the_id(), '_delivery_timeslot', true );
		$order = new WC_Order($post->ID);
		$methods = $order->get_shipping_methods();
		$method = '';
		foreach($methods as $v){
			$method = $v->get_method_id().":".$v->get_instance_id();
		}

        if (null !== $delivery_date){
            $dd = ' value="' . $delivery_date . '"';
        }else{
            $dd = '';
        }
        if (null == $delivery_timeslot){
            $tsp = ' selected';
        }else{
            $tsp = '';
        }

        echo '<input type="hidden" name="bkf_dd_nonce" value="' . wp_create_nonce() . '">';
        ?><p style="text-align:center;">
        	<?php echo $delivery_date;
			if(null !== $delivery_timeslot){
				echo '<br>'.$delivery_timeslot;
			}
			?>
        </p>
		<input type="text" name="delivery_date" style="width:100%" class="delivery_date input-text form-control" id="delivery_date" placeholder="Delivery Date"<?php echo $dd ?> />
		<select name="delivery_timeslot" style="width:100%;" class="delivery_timeslot form-control" id="delivery_timeslot">
			<option value="" <?php echo $tsp; ?>><?php _e('Select a time slot...', 'bakkbone-florist-companion') ?></option>
			<?php
			global $wpdb;
			$ts = array();
			$timeslots = $wpdb->get_results(
				"
					SELECT id, method, day, start, end
					FROM {$wpdb->prefix}bkf_dd_timeslots
				"
			);
			foreach($timeslots as $timeslot){
				$ts[] = array(
					'id'		=>	$timeslot->id,
					'method'	=>	$timeslot->method,
					'day'		=>	$timeslot->day,
					'start'		=>	$timeslot->start,
					'end'		=>	$timeslot->end
				);
			}
			uasort($ts, function($a,$b){
				return strcmp($a['start'],$b['start']);} );
				
			foreach($ts as $tslot){
				if($tslot['day'] == strtolower(wp_date("l", strtotime($delivery_date))) && $tslot['method'] == $method ){
					$stringts = wp_date("g:i a", strtotime($tslot['start'])).' - '.wp_date("g:i a", strtotime($tslot['end']));
					if($stringts == $delivery_timeslot){
						$sel = ' selected';
					} else {
						$sel = '';
					}
					echo '<option value="'.$tslot['id'].'"'.$sel.'>'.$stringts.'</option>';
				}
			} ?>
		</select>
		<p class="description"><em><?php _e('Time slot choices will update after order is saved, if delivery date and/or delivery method changed.', 'bakkbone-florist-companion'); ?></em></p>
    	<script>
    	    jQuery(document).ready(function( $ ) {
    	        $(".delivery_date").datepicker( {
    	        	firstDay: 1,
    	        	maxDate: "+<?php echo $maxdate; ?>w",
    	        	dateFormat: "DD, d MM yy",
    	        	hideIfNoPrevNext: true,
    	        	constrainInput: true,
					beforeShowDay: blockedDates	 
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
			 }else{
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
				 }else{
					 echo '['.$jsdate.'],';		 	
					 }
					 $i++;
				 };}; ?>];
		 
		 function blockedDates(date) {
			 var m = date.getMonth();
			 var d = date.getDate();
			 var y = date.getFullYear();
			 
			 <?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
             if (date.getDay() == 1) {
                  return [false, "closed", 'Closed'];
              }<?php }; ?>
 			 <?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
              if (date.getDay() == 2) {
                   return [false, "closed", 'Closed'];
               }<?php }; ?>
  			 <?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
               if (date.getDay() == 3) {
                    return [false, "closed", 'Closed'];
                }<?php }; ?>
   			 <?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
                if (date.getDay() == 4) {
                     return [false, "closed", 'Closed'];
                 }<?php }; ?>
				 <?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
	             if (date.getDay() == 5) {
	                  return [false, "closed", 'Closed'];
	              }<?php }; ?>
	 			 <?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
	              if (date.getDay() == 6) {
	                   return [false, "closed", 'Closed'];
	               }<?php }; ?>
	  			 <?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
	               if (date.getDay() == 0) {
	                    return [false, "closed", 'Closed'];
	                }<?php }; ?>
			 
		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [false,"closed","Closed"];
		   }
		 }
	     for (i = 0; i < fullDatesList.length; i++) {
	       if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
	       {
	         return [false,"booked","Fully Booked"];
	       }
	     }
		 return [true];
	 }
	     } );
    	</script>
    	<?php  
    }
    
    function bkf_dd_save_metabox_data( $post_id ) {

    if ( ! isset( $_POST[ 'bkf_dd_nonce' ] ) && isset( $_POST['delivery_date'] ) )
        return $post_id;

    $nonce = $_POST[ 'bkf_dd_nonce' ];

    if ( ! wp_verify_nonce( $nonce ) )
        return $post_id;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if ( ! current_user_can( 'edit_shop_order', $post_id ) && ! current_user_can( 'edit_shop_orders', $post_id ) )
        return $post_id;

    update_post_meta( $post_id, '_delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
	
    if(isset($_POST['delivery_timeslot'])){
		if($_POST['delivery_timeslot'] !== ''){
			global $wpdb;
			$ts = array();
			$timeslots = $wpdb->get_results(
				"
					SELECT id, method, day, start, end
					FROM {$wpdb->prefix}bkf_dd_timeslots
				"
			);
			foreach($timeslots as $timeslot){
				$ts[] = array(
					'id'		=>	$timeslot->id,
					'method'	=>	$timeslot->method,
					'day'		=>	$timeslot->day,
					'start'		=>	$timeslot->start,
					'end'		=>	$timeslot->end
				);
			}
			$tscol = array_column($ts, 'id');
			$thistsid = array_search($_POST['delivery_timeslot'], $tscol);
			$thists = $ts[$thistsid];
			$text = wp_date("g:i a", strtotime($thists['start'])).' - '.wp_date("g:i a", strtotime($thists['end']));
			update_post_meta( $post_id, '_delivery_timeslot_id',  sanitize_text_field( $_POST['delivery_timeslot'] ) );
			update_post_meta( $post_id, '_delivery_timeslot',  $text );
		} else {
			delete_post_meta( $post_id, '_delivery_timeslot_id');
			delete_post_meta( $post_id, '_delivery_timeslot');			
		}
	}
	}
    
}