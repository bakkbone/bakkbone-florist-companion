<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPluginOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfPluginOptions{

	private $bkf_options_setting = array();
	private $bkf_features_setting = array();
	private $bkf_audio_setting = array();
	private $bkf_advanced_setting = array();

	function __construct()
	{
		if(is_admin()){
			$this->bkf_options_setting = get_option("bkf_options_setting");
			$this->bkf_features_setting = get_option("bkf_features_setting");
			$this->bkf_audio_setting = get_option("bkf_audio_setting");
			$this->bkf_advanced_setting = get_option("bkf_advanced_setting");
			add_action("admin_menu",array($this,"bkfAddOptionsPageOption"),2);
			add_action("admin_init",array($this,"bkfAddOptionsPageInit"));
			add_action("admin_footer",array($this,"bkfOptionsAdminFooter"));
			add_action("admin_enqueue_scripts",array($this,"bkfOptionsAdminEnqueueScripts"));
		}
	}

	function bkfAddOptionsPageOption()
	{
		add_menu_page(
			null,
			__("Florist Options","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_options",
			null,
			BKF_SVG_FLOWERS,
			2.1
		);
		$admin_page = add_submenu_page(
			"bkf_options",
			__("Florist Options","bakkbone-florist-companion"),
			__("General Options","bakkbone-florist-companion"),
			"manage_woocommerce",
			"bkf_options",
			array($this,"bkfOptionsPageContent"),
			10
		);
		add_action( 'load-'.$admin_page, array($this, 'bkf_go_help_tab') );
    }
	
	function bkf_go_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_go_help';
		$callback = array($this, 'bkf_go_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_go_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/florist-options/general-options" target="_blank">https://docs.bkbn.au/v/bkf/florist-options/general-options</a>
		<?php
	}
	function bkfOptionsPageContent()
	{
		$this->bkf_options_setting = get_option("bkf_options_setting");
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php _e("Florist Options","bakkbone-florist-companion") ?></h1>
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

	function bkfAddOptionsPageInit()
	{
		register_setting(
			"bkf_options_group",
			"bkf_options_setting",
			array($this,"bkfAddOptionsSanitize")
		);
		register_setting(
			"bkf_options_group",
			"bkf_features_setting",
			array($this,"bkfAddFeaturesSanitize")
		);
		register_setting(
			"bkf_options_group",
			"bkf_advanced_setting",
			array($this,"bkfAddAdvancedSanitize")
		);

		
		add_settings_section(
			"bkf_options_section",
			__("General Settings","bakkbone-florist-companion"),
			array($this,"bkfAddOptionsInfo"),
			"bkf-options"
		);
		add_settings_section(
			"bkf_features_section",
			__("Other Features","bakkbone-florist-companion"),
			array($this,"bkfAddFeaturesInfo"),
			"bkf-options"
		);
		if(get_option('bkf_features_setting')['order_notifier'] == 1){
			register_setting(
				"bkf_options_group",
				"bkf_audio_setting",
				array($this,"bkfAddAudioSanitize")
			);
			add_settings_section(
				"bkf_audio_section",
				__("Audio","bakkbone-florist-companion"),
				array($this,"bkfAddAudioInfo"),
				"bkf-options"
			);
			add_settings_field(
				"notifier_audio",
				__("Audio File","bakkbone-florist-companion"),
				array($this,"bkfAudioCallback"),
				"bkf-options",
				"bkf_audio_section"
			);
		}
		add_settings_section(
			"bkf_advanced_section",
			__("Advanced Settings","bakkbone-florist-companion"),
			array($this,"bkfAddAdvancedInfo"),
			"bkf-options"
		);
			
		add_settings_field(
			"card_length",
			__("Card Message Length","bakkbone-florist-companion"),
			array($this,"bkfCardLengthCallback"),
			"bkf-options",
			"bkf_options_section"
		);
		
		add_settings_field(
			"excerpt_pa",
			__("Product Archives","bakkbone-florist-companion"),
			array($this,"bkfExcerptPaCallback"),
			"bkf-options",
			"bkf_features_section"
		);
		
		add_settings_field(
			"suburbs_on",
			__("Delivery Suburbs","bakkbone-florist-companion"),
			array($this,"bkfSuburbsOnCallback"),
			"bkf-options",
			"bkf_features_section"
		);
		
		add_settings_field(
			"petals_on",
			__("Petals Network","bakkbone-florist-companion"),
			array($this,"bkfPetalsOnCallback"),
			"bkf-options",
			"bkf_features_section"
		);
		
		add_settings_field(
			"disable_order_comments",
			__("Disable Order Comments","bakkbone-florist-companion"),
			array($this,"bkfDOCCallback"),
			"bkf-options",
			"bkf_features_section"
		);
		
		add_settings_field(
			"order_notifier",
			__("Enable Order Notifier","bakkbone-florist-companion"),
			array($this,"bkfONCallback"),
			"bkf-options",
			"bkf_features_section"
		);
		
		add_settings_field(
			"deactivation_purge",
			__("Purge on Deactivation","bakkbone-florist-companion"),
			array($this,"bkfPurgeCallback"),
			"bkf-options",
			"bkf_advanced_section"
		);
		
	}

	function bkfAddOptionsSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["card_length"]))
			$new_input["card_length"] = sanitize_text_field($input["card_length"]);
		
		return $new_input;
	}

	function bkfAddFeaturesSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["excerpt_pa"])){
			$new_input["excerpt_pa"] = true;
		}else{
			$new_input["excerpt_pa"] = false;
		}

		if(isset($input["suburbs_on"])){
			$new_input["suburbs_on"] = true;
		}else{
			$new_input["suburbs_on"] = false;
		}
		
		if(isset($input["petals_on"])){
			$new_input["petals_on"] = true;
		}else{
			$new_input["petals_on"] = false;
		}
		
		if(isset($input["disable_order_comments"])){
			$new_input["disable_order_comments"] = true;
		}else{
			$new_input["disable_order_comments"] = false;
		}
		
		if(isset($input["order_notifier"])){
			$new_input["order_notifier"] = true;
		}else{
			$new_input["order_notifier"] = false;
		}
		
		return $new_input;
	}

	function bkfAddAdvancedSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["deactivation_purge"])){
			$new_input["deactivation_purge"] = true;
		}else{
			$new_input["deactivation_purge"] = false;
		}
		
		return $new_input;
	}
	
	function bkfAddAudioSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["notifier_audio"]))
			$new_input["notifier_audio"] = sanitize_text_field($input["notifier_audio"]);
		
		return $new_input;
	}
	
	function bkfAddOptionsInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("Enter your settings below:","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfAddFeaturesInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("The below features are optional.","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfAddAdvancedInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("The below settings are more advanced.","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfAddAudioInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("These settings relate to the audio for the Order Notifier on the Orders list screen.","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfCardLengthCallback(){
	
		if(isset($this->bkf_options_setting["card_length"])){
			$value = esc_attr($this->bkf_options_setting["card_length"]);
		}else{
			$value = '250';
		}
		?>
		<input class="bkf-form-control small-text" id="bkf-card-length" type="number" name="bkf_options_setting[card_length]" placeholder="250" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Maximum number of characters (including spaces/punctuation) a customer will be able to enter in the Card Message field.","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfExcerptPaCallback(){
	
		if(!isset($this->bkf_features_setting["excerpt_pa"])){
			$this->bkf_features_setting["excerpt_pa"] = false;
		}
		if($this->bkf_features_setting["excerpt_pa"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Display Short Description in product archives","bakkbone-florist-companion") ?><input id="bkf-excerpt-pa" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[excerpt_pa]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSuburbsOnCallback(){
	
		if(!isset($this->bkf_features_setting["suburbs_on"])){
			$this->bkf_features_setting["suburbs_on"] = true;
		}
		if($this->bkf_features_setting["suburbs_on"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Enable Delivery Suburbs feature (allows you to restrict delivery methods by suburb instead of postcode)","bakkbone-florist-companion") ?><input id="bkf-suburbs-on" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[suburbs_on]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfPetalsOnCallback(){
	
		if(!isset($this->bkf_features_setting["petals_on"])){
			$this->bkf_features_setting["petals_on"] = false;
		}
		if($this->bkf_features_setting["petals_on"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Enable Petals Network Integration","bakkbone-florist-companion") ?><input id="bkf-petals-on" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[petals_on]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfDOCCallback(){
	
		if(!isset($this->bkf_features_setting["disable_order_comments"])){
			$this->bkf_features_setting["disable_order_comments"] = true;
		}
		if($this->bkf_features_setting["disable_order_comments"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Disable the Order Comments field (freetext field at checkout for order notes)","bakkbone-florist-companion") ?><input id="bkf-disable-order-comments" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[disable_order_comments]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfONCallback(){
	
		if(!isset($this->bkf_features_setting["order_notifier"])){
			$this->bkf_features_setting["order_notifier"] = true;
		}
		if($this->bkf_features_setting["order_notifier"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Enable the Order Notifier on the Orders List","bakkbone-florist-companion") ?><input id="bkf-order-notifier" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[order_notifier]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfPurgeCallback(){
	
		if(!isset($this->bkf_advanced_setting["deactivation_purge"])){
			$this->bkf_advanced_setting["deactivation_purge"] = false;
		}
		if($this->bkf_advanced_setting["deactivation_purge"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Purge all data from the database on deactivation","bakkbone-florist-companion") ?><input id="bkf-deactivation-purge" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_advanced_setting[deactivation_purge]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfAudioCallback(){
		$files = array(
			'01-reveal.wav' => __('Reveal', 'bakkbone-florist-companion'),
			'02-bells.wav' => __('Bells', 'bakkbone-florist-companion'),
			'03-alert.wav' => __('Alert', 'bakkbone-florist-companion'),
			'04-phone.mp3' => __('Phone', 'bakkbone-florist-companion'),
			'05-chime.mp3' => __('Chime', 'bakkbone-florist-companion'),
			'06-message.mp3' => __('Message', 'bakkbone-florist-companion'),
			'07-cute.mp3' => __('Cute', 'bakkbone-florist-companion'),
			'08-roll.mp3' => __('Roll', 'bakkbone-florist-companion'),
			'09-nit.mp3' => __('Nit', 'bakkbone-florist-companion')
		);
		$file_urls = array(
			'01-reveal.wav' => BKF_URL . '/assets/audio/' . '01-reveal.wav',
			'02-bells.wav' => BKF_URL . '/assets/audio/' . '02-bells.wav',
			'03-alert.wav' => BKF_URL . '/assets/audio/' . '03-alert.wav',
			'04-phone.mp3' => BKF_URL . '/assets/audio/' . '04-phone.mp3',
			'05-chime.mp3' => BKF_URL . '/assets/audio/' . '05-chime.mp3',
			'06-message.mp3' => BKF_URL . '/assets/audio/' . '06-message.mp3',
			'07-cute.mp3' => BKF_URL . '/assets/audio/' . '07-cute.mp3',
			'08-roll.mp3' => BKF_URL . '/assets/audio/' . '08-roll.mp3',
			'09-nit.mp3' => BKF_URL . '/assets/audio/' . '09-nit.mp3'
		);
		if(isset($this->bkf_audio_setting["notifier_audio"])){
			$value = esc_attr($this->bkf_audio_setting["notifier_audio"]);
		}else{
			$value = '01-reveal.wav';
		}
		?>
		<div style="display:flex;"><div class="bkf-select" style="width:250px;margin-right:10px;" onclick="bkfAudioSample()">
			<select id="bkf-notifier-audio" name="bkf_audio_setting[notifier_audio]" required onclick="bkfAudioSample()">
				<option value="" disabled><?php _e('Select a file...', 'bakkbone-florist-companion'); ?></option>
				<?php
				foreach($files as $file => $name){
					if($value == $file){
						echo '<option value="'.$file.'" selected>'.$name.'</option>';
					} else {
						echo '<option value="'.$file.'">'.$name.'</option>';
					}
				}
				?>
			</select>
		</div>
		<button type="button" class="button button-secondary" onclick="bkfPlayNotifier()">Play sample of the selected file</button></div>
		<audio id="bkf-notifier-audio-sample" src="<?php echo $file_urls[$value]; ?>"></audio>
		<p class="description"><?php _e("The audio to be played when a new order is detected.","bakkbone-florist-companion") ?></p>
		<script type="text/javascript">
			function bkfAudioSample(){
				const files = [
					<?php foreach($file_urls as $value => $url){ echo '{ value: "'.$value.'", url: "'.$url.'" }, '; } ?>
				];
				var value = document.getElementById("bkf-notifier-audio").value;
				var result = files.find(item => item.value === value);
				var audio = document.getElementById("bkf-notifier-audio-sample");
				audio.src = result.url;
			}
			function bkfPlayNotifier(){
				var audio = document.getElementById("bkf-notifier-audio-sample");
				audio.loop = false;
				audio.play();
			}
		</script>
		<?php
	}
	
	function bkfOptionsAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_options")
		{
			
		}
	}

	function bkfOptionsAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_options")
		{

		}
	}
	
}