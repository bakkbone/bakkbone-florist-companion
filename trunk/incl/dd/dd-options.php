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

    function __construct(){
        $this->bkf_ddi_setting = get_option("bkf_ddi_setting");
        $this->bkf_dd_setting = get_option("bkf_dd_setting");
        $this->bkf_sd_setting = get_option("bkf_sd_setting");
        add_action("admin_menu", array($this,"bkf_admin_menu"));
        add_action("admin_init",array($this,"bkfAddDdPageInit"));
		add_action("admin_footer",array($this,"bkfDdAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfDdAdminEnqueueScripts"));
        add_action('add_meta_boxes', array($this, 'bkf_dd_metabox_init') );
        add_action('save_post', array($this, 'bkf_dd_save_metabox_data') );
    }

    function bkf_admin_menu(){
        add_submenu_page(
        "bkf_options",//parent slug
        __("Delivery Dates","bakkbone-florist-companion"),//page title
        __("Delivery Dates","bakkbone-florist-companion"),//menu title
        "manage_options",//capability
        "bkf_dd",//menu slug
        array($this, "bkf_dd_settings_page"),//callback
        30
        );
    }
    
    function bkf_dd_settings_page()
    {
        $this->bkf_dd_setting = get_option("bkf_dd_setting");
        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("Delivery Date Settings","bakkbone-florist-companion") ?></h1>
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
    
	function bkfAddDdPageInit()
	{
		
		register_setting(
			"bkf_dd_options_group",// group
			"bkf_ddi_setting", //setting name
			array($this,"bkfAddDdiOptionsSanitize") //sanitize_callback
		);
		register_setting(
			"bkf_dd_options_group",// group
			"bkf_dd_setting", //setting name
			array($this,"bkfAddDdOptionsSanitize") //sanitize_callback
		);
		register_setting(
			"bkf_dd_options_group",// group
			"bkf_sd_setting", //setting name
			array($this,"bkfAddSdOptionsSanitize") //sanitize_callback
		);

		// add Ddi section
		add_settings_section(
			"bkf_ddi_section", //id
			__("Options","bakkbone-florist-companion"), //title
			array($this,"bkfDdiInfo"), //callback
			"bkf-dd" //page
		);
		
		// add Dd section
		add_settings_section(
			"bkf_dd_section", //id
			__("Delivery Weekdays","bakkbone-florist-companion"), //title
			array($this,"bkfDdOptionsInfo"), //callback
			"bkf-dd" //page
		);
		
		// add Sd section
		add_settings_section(
			"bkf_sd_section", //id
			__("Same Day Cutoffs","bakkbone-florist-companion"), //title
			array($this,"bkfSdOptionsInfo"), //callback
			"bkf-dd" //page
		);

		add_settings_field(
			"bkf_ddi", //id
			__("Pre-ordering","bakkbone-florist-companion"), //title
			array($this,"bkfDdiCallback"), //callback
			"bkf-dd", //page
			"bkf_ddi_section" //section
		);
		add_settings_field(
			"bkf_ddt", //id
			__("Title","bakkbone-florist-companion"), //title
			array($this,"bkfDdtCallback"), //callback
			"bkf-dd", //page
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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
			"bkf-dd", //page
			"bkf_sd_section" //section
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

	function bkfDdAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_dd")
		{

		}
	}
	
	function bkfDdAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_dd")
		{
		wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.2/themes/overcast/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );  	
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
        ?><input type="text" name="delivery_date" style="width:100%" class="delivery_date input-text form-control" id="delivery_date" placeholder="Delivery Date"<?php echo $dd ?> />
		<select name="delivery_timeslot" style="width:100%;" class="delivery_timeslot form-control" id="delivery_timeslot">
			<option value="" disabled<?php echo $tsp; ?>><?php _e('Select a time slot...', 'bakkbone-florist-companion') ?></option>
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
				if($tslot['day'] == strtolower(date("l", strtotime($delivery_date))) && $tslot['method'] == $method ){
					if($tslot['id'] == $delivery_timeslot){
						$sel = ' selected';
					} else {
						$sel = '';
					}
					echo '<option value="'.$tslot['id'].'"'.$sel.'>'.date("g:i a", strtotime($tslot['start'])).' - '.date("g:i a", strtotime($tslot['end'])).'</option>';
				}
			} ?>
		</select>
		<p class="description"><em><?php _e('Time slot choices will update if delivery date and/or delivery method changed.', 'bakkbone-florist-companion'); ?></em></p>
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
					 $jsdate = date('n,j,Y',$ts);
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
					 $jsdate = date('n,j,Y',$ts);
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

    update_post_meta( $post_id, '_delivery_date', sanitize_text_field( $_POST[ 'delivery_date' ] ) );
    if(isset($_POST['delivery_timeslot'])){
		update_post_meta( $post_id, '_delivery_timeslot', sanitize_text_field( $_POST[ 'delivery_timeslot' ] ) );
	}
    }
    
}