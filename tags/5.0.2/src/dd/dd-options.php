<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Delivery_Date_Options
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class BKF_Delivery_Date_Options{

	private $bkf_ddi_setting = [];
	private $bkf_dd_setting = [];
	private $bkf_sd_setting = [];
	private $bkf_dm_setting = [];

	function __construct(){
		$this->bkf_ddi_setting = get_option("bkf_ddi_setting");
		$this->bkf_dd_setting = get_option("bkf_dd_setting");
		$this->bkf_sd_setting = get_option("bkf_sd_setting");
		$this->bkf_dm_setting = get_option("bkf_dm_setting");
		add_action("admin_menu", [$this, 'bkf_admin_menu'], 1);
		add_action("admin_init", [$this, 'bkfAddDdPageInit']);
		add_action('add_meta_boxes', [$this, 'bkf_dd_metabox_init']);
		add_action('save_post', [$this, 'bkf_dd_save_metabox_data']);
		add_action('woocommerce_process_shop_order_meta', [$this, 'bkf_dd_save_metabox_data']);
	}

	function bkf_admin_menu(){
		add_menu_page(
			null,
			__("Delivery Dates","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_dd",
			null,
			'dashicons-clipboard',
			2.3
		);
		$ddpage = add_submenu_page(
			"bkf_dd",
			__("Delivery Dates","bakkbone-florist-companion"),
			__("General Options","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_dd",
			[$this, 'bkf_dd_settings_page'],
			1
		);
		$wdpage = add_submenu_page(
			"bkf_dd",
			__("Weekdays and Lead Times","bakkbone-florist-companion"),
			__("Weekdays","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_dd_wd",
			[$this, 'bkf_wd_settings_page'],
			2
		);
		$dmpage = add_submenu_page(
			"bkf_dd",
			__("Delivery Method Restrictions","bakkbone-florist-companion"),
			'— ' . __("Method Restrictions","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_dd_dm",
			[$this, 'bkf_dm_settings_page'],
			4
		);
		add_action( 'load-'.$ddpage, [$this, 'bkf_dd_help_tab'] );
		add_action( 'load-'.$wdpage, [$this, 'bkf_wd_help_tab'] );
		add_action( 'load-'.$dmpage, [$this, 'bkf_dm_help_tab'] );
	}
	
	function bkf_dd_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_dd_help';
		$callback = [$this, 'bkf_dd_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_dd_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/dd-options/" target="_blank">https://docs.floristpress.org/dd/dd-options/</a>
		<?php
	}
		
	function bkf_wd_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_wd_help';
		$callback = [$this, 'bkf_wd_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_wd_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/weekdays/" target="_blank">https://docs.floristpress.org/dd/weekdays/</a>
		<?php
	}
	
	function bkf_sd_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/lead-times/" target="_blank">https://docs.floristpress.org/dd/lead-times/</a>
		<?php
	}
		
	function bkf_dm_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_dm_help';
		$callback = [$this, 'bkf_dm_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_dm_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/dd/methods/" target="_blank">https://docs.floristpress.org/dd/methods/</a>
		<?php
	}
		
	function bkf_dd_settings_page(){
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Delivery Date Settings","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php
						settings_fields("bkf_ddi_options_group");
						do_settings_sections("bkf-ddi");
						submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'ddi_submit') ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	function bkf_wd_settings_page(){
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Weekdays and Lead Times","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php
						settings_fields("bkf_dd_options_group");
						do_settings_sections("bkf-dd");
						submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'dd_submit') ); ?>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript" id="enable_disable">
			jQuery(document.body).on('change', 'input[type="checkbox"]', function($){
				mon = jQuery('#bkf-dd-monday').is(':checked');
				tue = jQuery('#bkf-dd-tuesday').is(':checked');
				wed = jQuery('#bkf-dd-wednesday').is(':checked');
				thu = jQuery('#bkf-dd-thursday').is(':checked');
				fri = jQuery('#bkf-dd-friday').is(':checked');
				sat = jQuery('#bkf-dd-saturday').is(':checked');
				sun = jQuery('#bkf-dd-sunday').is(':checked');
				
				monElSd = jQuery('#bkf-sd-monday');
				tueElSd = jQuery('#bkf-sd-tuesday');
				wedElSd = jQuery('#bkf-sd-wednesday');
				thuElSd = jQuery('#bkf-sd-thursday');
				friElSd = jQuery('#bkf-sd-friday');
				satElSd = jQuery('#bkf-sd-saturday');
				sunElSd = jQuery('#bkf-sd-sunday');
				
				monElLt = jQuery('#bkf-sd-mondaylead');
				tueElLt = jQuery('#bkf-sd-tuesdaylead');
				wedElLt = jQuery('#bkf-sd-wednesdaylead');
				thuElLt = jQuery('#bkf-sd-thursdaylead');
				friElLt = jQuery('#bkf-sd-fridaylead');
				satElLt = jQuery('#bkf-sd-saturdaylead');
				sunElLt = jQuery('#bkf-sd-sundaylead');
				
				if (mon) {
					monElSd.prop('disabled', false);
					monElLt.prop('disabled', false);
					monElSd.prop('required', true);
					monElLt.prop('required', true);
				} else {
					monElSd.prop('disabled', true);
					monElLt.prop('disabled', true);
					monElSd.prop('required', false);
					monElLt.prop('required', false);
				}
				if (tue) {
					tueElSd.prop('disabled', false);
					tueElLt.prop('disabled', false);
					tueElSd.prop('required', true);
					tueElLt.prop('required', true);
				} else {
					tueElSd.prop('disabled', true);
					tueElLt.prop('disabled', true);
					tueElSd.prop('required', false);
					tueElLt.prop('required', false);
				}
				if (wed) {
					wedElSd.prop('disabled', false);
					wedElLt.prop('disabled', false);
					wedElSd.prop('required', true);
					wedElLt.prop('required', true);
				} else {
					wedElSd.prop('disabled', true);
					wedElLt.prop('disabled', true);
					wedElSd.prop('required', false);
					wedElLt.prop('required', false);
				}
				if (thu) {
					thuElSd.prop('disabled', false);
					thuElLt.prop('disabled', false);
					thuElSd.prop('required', true);
					thuElLt.prop('required', true);
				} else {
					thuElSd.prop('disabled', true);
					thuElLt.prop('disabled', true);
					thuElSd.prop('required', false);
					thuElLt.prop('required', false);
				}
				if (fri) {
					friElSd.prop('disabled', false);
					friElLt.prop('disabled', false);
					friElSd.prop('required', true);
					friElLt.prop('required', true);
				} else {
					friElSd.prop('disabled', true);
					friElLt.prop('disabled', true);
					friElSd.prop('required', false);
					friElLt.prop('required', false);
				}
				if (sat) {
					satElSd.prop('disabled', false);
					satElLt.prop('disabled', false);
					satElSd.prop('required', true);
					satElLt.prop('required', true);
				} else {
					satElSd.prop('disabled', true);
					satElLt.prop('disabled', true);
					satElSd.prop('required', false);
					satElLt.prop('required', false);
				}
				if (sun) {
					sunElSd.prop('disabled', false);
					sunElLt.prop('disabled', false);
					sunElSd.prop('required', true);
					sunElLt.prop('required', true);
				} else {
					sunElSd.prop('disabled', true);
					sunElLt.prop('disabled', true);
					sunElSd.prop('required', false);
					sunElLt.prop('required', false);
				}
			});
		</script>
		<?php
	}
	
	function bkf_dm_settings_page(){
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("Delivery Method Restrictions","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php
						settings_fields("bkf_dm_options_group");
						do_settings_sections("bkf-dm");
						submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'dm_submit') ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	function bkfAddDdPageInit(){
		
		register_setting(
			"bkf_ddi_options_group",
			"bkf_ddi_setting",
			[$this, 'bkfAddDdiOptionsSanitize']
		);
		register_setting(
			"bkf_dd_options_group",
			"bkf_dd_setting",
			[$this, 'bkfAddDdOptionsSanitize']
		);
		register_setting(
			"bkf_dd_options_group",
			"bkf_sd_setting",
			[$this, 'bkfAddSdOptionsSanitize']
		);
		register_setting(
			"bkf_dm_options_group",
			"bkf_dm_setting",
			[$this, 'bkfAddDmOptionsSanitize']
		);

		add_settings_section(
			"bkf_ddi_section",
			__("Options","bakkbone-florist-companion"),
			[$this, 'bkfDdiInfo'],
			"bkf-ddi"
		);
		
		add_settings_section(
			"bkf_dd_section",
			__("Delivery Weekdays","bakkbone-florist-companion"),
			[$this, 'bkfDdOptionsInfo'],
			"bkf-dd"
		);
		
		add_settings_section(
			"bkf_sd_section",
			__("Lead Times","bakkbone-florist-companion"),
			[$this, 'bkfSdOptionsInfo'],
			"bkf-sd"
		);

		add_settings_section(
			"bkf_dm_section",
			__("Delivery Methods","bakkbone-florist-companion"),
			[$this, 'bkfDmOptionsInfo'],
			"bkf-dm"
		);

		add_settings_field(
			"bkf_ddi",
			__("Pre-ordering","bakkbone-florist-companion"),
			[$this, 'bkfDdiCallback'],
			"bkf-ddi",
			"bkf_ddi_section"
		);
		add_settings_field(
			"bkf_ddt",
			__("Title","bakkbone-florist-companion"),
			[$this, 'bkfDdtCallback'],
			"bkf-ddi",
			"bkf_ddi_section"
		);
				
		add_settings_field(
			"bkf_dd_monday",
			__('Monday', 'bakkbone-florist-companion'),
			[$this, 'bkfMondayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_monday",
			__('Monday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmMondayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);

		add_settings_field(
			"bkf_dd_tuesday",
			__('Tuesday', 'bakkbone-florist-companion'),
			[$this, 'bkfTuesdayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_tuesday",
			__('Tuesday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmTuesdayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);
		
		add_settings_field(
			"bkf_dd_wednesday",
			__('Wednesday', 'bakkbone-florist-companion'),
			[$this, 'bkfWednesdayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_wednesday",
			__('Wednesday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmWednesdayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);
		
		add_settings_field(
			"bkf_dd_thursday",
			__('Thursday', 'bakkbone-florist-companion'),
			[$this, 'bkfThursdayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_thursday",
			__('Thursday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmThursdayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);
		
		add_settings_field(
			"bkf_dd_friday",
			__('Friday', 'bakkbone-florist-companion'),
			[$this, 'bkfFridayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_friday",
			__('Friday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmFridayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);
		
		add_settings_field(
			"bkf_dd_saturday",
			__('Saturday', 'bakkbone-florist-companion'),
			[$this, 'bkfSaturdayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_saturday",
			__('Saturday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmSaturdayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);

		add_settings_field(
			"bkf_dd_sunday",
			__('Sunday', 'bakkbone-florist-companion'),
			[$this, 'bkfSundayCallback'],
			"bkf-dd",
			"bkf_dd_section"
		);
		add_settings_field(
			"bkf_dm_sunday",
			__('Sunday', 'bakkbone-florist-companion'),
			[$this, 'bkfDmSundayCallback'],
			"bkf-dm",
			"bkf_dm_section"
		);

	}
	
	function bkfAddDdiOptionsSanitize($input){
		$new_input = [];
			
		if(isset($input["ddi"])){
			$new_input["ddi"] = sanitize_text_field($input["ddi"]);
		}

		if(isset($input["ddt"])){
			$new_input["ddt"] = sanitize_text_field($input["ddt"]);
		}
	
		return $new_input;
	}	
	
	function bkfAddDdOptionsSanitize($input){
		$new_input = [];
		
		if(isset($input["monday"])){
			$new_input["monday"] = true;
		} else {
			$new_input["monday"] = false;
		}
		
		if(isset($input["tuesday"])){
			$new_input["tuesday"] = true;
		} else {
			$new_input["tuesday"] = false;
		}
		
		if(isset($input["wednesday"])){
			$new_input["wednesday"] = true;
		} else {
			$new_input["wednesday"] = false;
		}
		
		if(isset($input["thursday"])){
			$new_input["thursday"] = true;
		} else {
			$new_input["thursday"] = false;
		}
		
		if(isset($input["friday"])){
			$new_input["friday"] = true;
		} else {
			$new_input["friday"] = false;
		}
		
		if(isset($input["saturday"])){
			$new_input["saturday"] = true;
		} else {
			$new_input["saturday"] = false;
		}
		
		if(isset($input["sunday"])){
			$new_input["sunday"] = true;
		} else {
			$new_input["sunday"] = false;
		}
		
		return $new_input;
	}

	function bkfAddSdOptionsSanitize($input){
		$new_input = [];

		if(isset($input["monday"])){
			$new_input["monday"] = sanitize_text_field($input["monday"]);
		}
		if(isset($input["mondaylead"])){
			$new_input["mondaylead"] = sanitize_text_field($input["mondaylead"]);
		}
		
		if(isset($input["tuesday"])){
			$new_input["tuesday"] = sanitize_text_field($input["tuesday"]);
		}
		if(isset($input["tuesdaylead"])){
			$new_input["tuesdaylead"] = sanitize_text_field($input["tuesdaylead"]);
		}
		
		if(isset($input["wednesday"])){
			$new_input["wednesday"] = sanitize_text_field($input["wednesday"]);
		}
		if(isset($input["wednesdaylead"])){
			$new_input["wednesdaylead"] = sanitize_text_field($input["wednesdaylead"]);
		}
		
		if(isset($input["thursday"])){
			$new_input["thursday"] = sanitize_text_field($input["thursday"]);
		}
		if(isset($input["thursdaylead"])){
			$new_input["thursdaylead"] = sanitize_text_field($input["thursdaylead"]);
		}
		
		if(isset($input["friday"])){
			$new_input["friday"] = sanitize_text_field($input["friday"]);
		}
		if(isset($input["fridaylead"])){
			$new_input["fridaylead"] = sanitize_text_field($input["fridaylead"]);
		}
		
		if(isset($input["saturday"])){
			$new_input["saturday"] = sanitize_text_field($input["saturday"]);
		}
		if(isset($input["saturdaylead"])){
			$new_input["saturdaylead"] = sanitize_text_field($input["saturdaylead"]);
		}
		
		if(isset($input["sunday"])){
			$new_input["sunday"] = sanitize_text_field($input["sunday"]);
		}
		if(isset($input["sundaylead"])){
			$new_input["sundaylead"] = sanitize_text_field($input["sundaylead"]);
		}
		
		return $new_input;
	}
	
	function bkfAddSmOptionsSanitize($input){
		$new_input = [];

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
	
	function bkfDdiInfo(){
		echo '<p class="bkf-pageinfo">';
		esc_html_e("Enter your settings below.", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfDdOptionsInfo(){
		echo '<p class="bkf-pageinfo">';
		esc_html_e("Select which days of the week you deliver, then set your order lead times. For same day delivery, set the number on the right to 0, and the time to your same day delivery cutoff. To set a lead time, enter the number of days' notice on the right and the time on the cutoff day to close orders. For example, To close orders on Monday afternoon for Wednesday, you might enter 5:00 pm and 2 to close orders at 5pm, 2 days prior.", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfDmOptionsInfo(){
		echo '<p class="bkf-pageinfo">'.wp_kses_post(__("If a particular delivery method is unavailable on a certain day you are otherwise open, indicate this here. Save your weekdays to display the correct fields below. Remember, delivery methods selected below will <strong>not</strong> be available on the day indicated.", "bakkbone-florist-companion")).'</p><p class="bkf-pageinfo"><em>'.wp_kses_post(__("Hold down Ctrl (Windows) or Cmd (Mac) while selecting, to choose multiple options.", "bakkbone-florist-companion")).'</em></p>';
	}

	function bkfDdiCallback(){
	
		if(isset($this->bkf_ddi_setting["ddi"])){
			$value = esc_attr($this->bkf_ddi_setting["ddi"]);
		} else {
			$value = "8";
		}
		?>
		<input class="bkf-form-control small-text" id="bkf-ddi" type="number" name="bkf_ddi_setting[ddi]" placeholder="8" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Maximum number of weeks in the future to enable at checkout.","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfDdtCallback(){
		$opt1 = __('Delivery Date', 'bakkbone-florist-companion');
		$opt2 = __("Delivery/Collection Date", "bakkbone-florist-companion");
		$opt3 = __("Order Date", "bakkbone-florist-companion");
		$opt4 = __('Collection Date', 'bakkbone-florist-companion');
		
		if(isset($this->bkf_ddi_setting["ddt"])){
			$value = esc_attr($this->bkf_ddi_setting["ddt"]);
		} else {
			$value = $opt1;
		}
		?>
		<p class="description"><?php esc_html_e("What shall we call this field at checkout?","bakkbone-florist-companion") ?></p>
		<div style="display: inline;">
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt1" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt1; ?>"<?php if($value == $opt1){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt1; ?></label>
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt2" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt2; ?>"<?php if($value == $opt2){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt2; ?></label>
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt3" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt3; ?>"<?php if($value == $opt3){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt3; ?></label>
		<label class="bkf-radio-container"><input class="bkf-form-control" id="bkf-ddt4" type="radio" name="bkf_ddi_setting[ddt]" value="<?php echo $opt4; ?>"<?php if($value == $opt4){ echo "checked"; } ?> /><span class="bkf-radio-checkmark"></span><?php echo $opt4; ?></label>
	</div>
		<?php
	}

	function bkfMondayCallback(){
		if(!isset($this->bkf_dd_setting["monday"])){
			$this->bkf_dd_setting["monday"] = false;
		}
		if($this->bkf_dd_setting["monday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-monday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[monday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["monday"]) || (get_option("bkf_dd_setting")["monday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["monday"])){
			$value = esc_attr($this->bkf_sd_setting["monday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["mondaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["mondaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-monday" type="time" step="60" name="bkf_sd_setting[monday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-mondaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[mondaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmMondayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
		if($this->bkf_dd_setting["tuesday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-tuesday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[tuesday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["tuesday"]) || (get_option("bkf_dd_setting")["tuesday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["tuesday"])){
			$value = esc_attr($this->bkf_sd_setting["tuesday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["tuesdaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["tuesdaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-tuesday" type="time" step="60" name="bkf_sd_setting[tuesday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-tuesdaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[tuesdaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}

	function bkfDmTuesdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
		if($this->bkf_dd_setting["wednesday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-wednesday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[wednesday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["wednesday"]) || (get_option("bkf_dd_setting")["wednesday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["wednesday"])){
			$value = esc_attr($this->bkf_sd_setting["wednesday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["wednesdaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["wednesdaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-wednesday" type="time" step="60" name="bkf_sd_setting[wednesday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-wednesdaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[wednesdaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmWednesdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
		if($this->bkf_dd_setting["thursday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-thursday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[thursday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["thursday"]) || (get_option("bkf_dd_setting")["thursday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["thursday"])){
			$value = esc_attr($this->bkf_sd_setting["thursday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["thursdaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["thursdaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-thursday" type="time" step="60" name="bkf_sd_setting[thursday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-thursdaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[thursdaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmThursdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
		if($this->bkf_dd_setting["friday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-friday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[friday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["friday"]) || (get_option("bkf_dd_setting")["friday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["friday"])){
			$value = esc_attr($this->bkf_sd_setting["friday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["fridaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["fridaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-friday" type="time" step="60" name="bkf_sd_setting[friday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-fridaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[fridaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}

	function bkfDmFridayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
		if($this->bkf_dd_setting["saturday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-saturday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[saturday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["saturday"]) || (get_option("bkf_dd_setting")["saturday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["saturday"])){
			$value = esc_attr($this->bkf_sd_setting["saturday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["saturdaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["saturdaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-saturday" type="time" step="60" name="bkf_sd_setting[saturday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-saturdaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[saturdaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}
	
	function bkfDmSaturdayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
		if($this->bkf_dd_setting["sunday"]){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php esc_html_e("Enabled","bakkbone-florist-companion") ?><input id="bkf-dd-sunday" <?php echo $checked ?> type="checkbox" class="bkf-form-control" name="bkf_dd_setting[sunday]" /><span class="bkf-check-checkmark"></span></label>
		<?php
		if(!isset(get_option("bkf_dd_setting")["sunday"]) || (get_option("bkf_dd_setting")["sunday"] == false)){
			$disabled = " disabled";
		} else {
			$disabled = " required";
		}
		
		if(isset($this->bkf_sd_setting["sunday"])){
			$value = esc_attr($this->bkf_sd_setting["sunday"]);
		} else {
			$value = "";
		}
		if(isset($this->bkf_sd_setting["sundaylead"])){
			$leadvalue = esc_attr($this->bkf_sd_setting["sundaylead"]);
		} else {
			$leadvalue = "0";
		}
		?>
		<input class="bkf-form-control" id="bkf-sd-sunday" type="time" step="60" name="bkf_sd_setting[sunday]" placeholder="" value="<?php echo $value; ?>"<?php echo $disabled; ?> />
		<input class="small-text bkf-form-control" id="bkf-sd-sundaylead" type="number" step="1" min="0" max="<?php echo $this->bkf_ddi_setting['ddi'] * 7 - 1; ?>" name="bkf_sd_setting[sundaylead]" placeholder="" value="<?php echo $leadvalue; ?>"<?php echo $disabled; ?> />
		<?php
	}

	function bkfDmSundayCallback(){
		$allzones = WC_Data_Store::load('shipping-zone');
		$rawzones = $allzones->get_zones();
		$zones = [];
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach($rawzones as $rawzone){
			$zones[] = new WC_Shipping_Zone( $rawzone );
		}
		
		$sm = [];
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
		} else {
			$disabled = " required";
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
	
	public function bkf_dd_metabox_init(){
		$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		add_meta_box('bkf_dd', __('Delivery Date', 'bakkbone-florist-companion'), [$this, 'bkf_dd_metabox_callback'], $screen, 'side', 'core');
	}
	
	public function bkf_dd_metabox_callback( $post_or_order_object ){
	    $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		$maxdate = get_option('bkf_ddi_setting')['ddi'];
		$delivery_date = $order->get_meta('_delivery_date', true );
		$delivery_timeslot = $order->get_meta('_delivery_timeslot', true );
		$delivery_timeslot_id = $order->get_meta('_delivery_timeslot_id', true );
		$tsid = $delivery_timeslot_id !== null && $delivery_timeslot_id !== '' && $delivery_timeslot_id ? 'ts'.$delivery_timeslot_id : false;
		$timeslot = $tsid ? bkf_get_timeslots_associative()['ts'.$delivery_timeslot_id] : null;
		$methods = $order->get_shipping_methods();
		$method = '';
		foreach($methods as $v){
			$method = $v->get_method_id().":".$v->get_instance_id();
		}
		
		if (null !== $delivery_date){
			$dd = ' value="' . $delivery_date . '"';
		} else {
			$dd = '';
		}
		if (null == $delivery_timeslot || '' == $delivery_timeslot){
			$tsp = ' selected';
		} else {
			$tsp = '';
		}

		echo '<input type="hidden" name="bkf_dd_nonce" value="' . wp_create_nonce() . '">';
		?><p style="text-align:center;">
			<?php echo $delivery_date;
			if(null !== $timeslot){
				echo '<br>'.date("g:i a", strtotime($timeslot['start'])).' - '.date("g:i a", strtotime($timeslot['end']));
			} elseif(null !== $delivery_timeslot) {
				echo '<br>'.$delivery_timeslot;
			}
			?>
		</p>
		<input type="text" name="delivery_date" style="width:100%" class="delivery_date input-text form-control" id="delivery_date" placeholder="Delivery Date"<?php echo $dd ?> />
		<select name="delivery_timeslot" style="width:100%;" class="delivery_timeslot form-control" id="delivery_timeslot">
			<option value="" <?php echo $tsp; ?>><?php esc_html_e('Select a timeslot...', 'bakkbone-florist-companion') ?></option>
			<?php
			$day = date("l", strtotime($delivery_date));
			$validts = bkf_get_timeslots_for_order($method,$day);
			foreach($validts as $tslot){
				$id = $tslot['id'];
				$stringts = bkf_get_timeslot_string($id);
				if($stringts == $delivery_timeslot || $id == $delivery_timeslot_id){
					$sel = ' selected';
				} else {
					$sel = '';
				}
				echo '<option value="'.$tslot['id'].'"'.$sel.'>'.$stringts.'</option>';
			} ?>
		</select>
		<p class="description"><em><?php esc_html_e('Timeslot choices will update after order is saved, if delivery date and/or delivery method changed.', 'bakkbone-florist-companion'); ?></em></p>
		<script>
			jQuery(document).ready(function( $ ) {
				jQuery(".delivery_date").datepicker( {
					minDate: 0,
					maxDate: "+<?php echo $maxdate; ?>w",
					dateFormat: "DD, d MM yy",
					hideIfNoPrevNext: true,
					firstDay: 1,
					constrainInput: true,
					beforeShowDay: blockedDates,
					showButtonPanel: true,
					showOtherMonths: true,
					selectOtherMonths: true,
					changeMonth: true,
					changeYear: true,
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
			 } else {
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
				 } else {
					 echo '['.$jsdate.'],';		 	
					 }
					 $i++;
				 };}; ?>];
		 
		 function blockedDates(date) {
			 var w = date.getDay();
			 var m = date.getMonth();
			 var d = date.getDate();
			 var y = date.getFullYear();
			 
			 <?php if(get_option('bkf_dd_setting')['monday'] == false){ ?>
			 if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 1) {
				  return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			  }<?php }; ?>
 			 <?php if(get_option('bkf_dd_setting')['tuesday'] == false){ ?>
			  if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 2) {
				   return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
			   }<?php }; ?>
  			 <?php if(get_option('bkf_dd_setting')['wednesday'] == false){ ?>
			   if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 3) {
					return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				}<?php }; ?>
   			 <?php if(get_option('bkf_dd_setting')['thursday'] == false){ ?>
				if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 4) {
					 return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				 }<?php }; ?>
				 <?php if(get_option('bkf_dd_setting')['friday'] == false){ ?>
				 if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 5) {
					  return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				  }<?php }; ?>
	 			 <?php if(get_option('bkf_dd_setting')['saturday'] == false){ ?>
				  if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 6) {
					   return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
				   }<?php }; ?>
	  			 <?php if(get_option('bkf_dd_setting')['sunday'] == false){ ?>
				   if (date >= new Date("<?php echo bkf_get_monday(); ?>") && w == 0) {
						return [true, "closed", "<?php esc_html_e('Closed', 'bakkbone-florist-companion'); ?>"];
					}<?php }; ?>
			 
		 for (i = 0; i < closedDatesList.length; i++) {
		   if ((m == closedDatesList[i][0] - 1) && (d == closedDatesList[i][1]) && (y == closedDatesList[i][2]))
		   {
		   	 return [true,"closed","Closed"];
		   }
		 }
		 for (i = 0; i < fullDatesList.length; i++) {
		   if ((m == fullDatesList[i][0] - 1) && (d == fullDatesList[i][1]) && (y == fullDatesList[i][2]))
		   {
			 return [true,"booked","Fully Booked"];
		   }
		 }
		 return [true];
	 }
		 } );
		</script>
		<?php  
	}
	
	function bkf_dd_save_metabox_data( $post_id ) {
		if(isset($_POST['delivery_date'])){
			if ( ! isset( $_POST[ 'bkf_dd_nonce' ] ) ){
						return $post_id;
					} else {
						$nonce = $_POST[ 'bkf_dd_nonce' ];

						if ( ! wp_verify_nonce( $nonce ) )
							return $post_id;

						if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
							return $post_id;

						if ( ! current_user_can( 'manage_woocommerce', $post_id ) )
							return $post_id;
						
						$order = new WC_Order($post_id);
						$order->update_meta_data( '_delivery_date', wc_sanitize_textarea( $_POST[ 'delivery_date' ] ) );
						$order->update_meta_data( '_delivery_timestamp', (string)strtotime(wc_sanitize_textarea( $_POST[ 'delivery_date' ] )));
						$order->save();
					}	
		} else {
			return $post_id;
		}
		
		if(isset($_POST['delivery_timeslot'])){
			if($_POST['delivery_timeslot'] !== ''){
				$order = new WC_Order($post_id);
				$tsid = $_POST['delivery_timeslot'];
				$thists = bkf_get_timeslots_associative()['ts'.$tsid];
				$text = date("g:i a", strtotime($thists['start'])).' - '.date("g:i a", strtotime($thists['end']));
				$order->update_meta_data( '_delivery_timeslot_id',  sanitize_text_field( $_POST['delivery_timeslot'] ) );
				$order->update_meta_data( '_delivery_timeslot',  $text );
				$order->save();
			} else {
				$order = new WC_Order($post_id);
				$order->delete_meta_data( '_delivery_timeslot_id');
				$order->delete_meta_data( '_delivery_timeslot');
				$order->save();
			}
		}
	}
	
}