<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_Settings
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");

use Automattic\WooCommerce\Admin\PluginsHelper;

class BKF_Settings extends WC_Settings_Page {

	function __construct() {
		$this->id		= 'bkf';
		$this->label	= _x('FloristPress', 'Settings tab label', 'bakkbone-florist-companion');
		parent::__construct();

		add_action('woocommerce_admin_field_bkf_audio', [$this, 'woocommerce_admin_field_bkf_audio']);
		add_action('woocommerce_admin_field_petals_url', [$this, 'woocommerce_admin_field_petals_url']);
		add_action('woocommerce_admin_field_petals_category', [$this, 'woocommerce_admin_field_petals_category']);
		add_action('woocommerce_admin_field_petals_product', [$this, 'woocommerce_admin_field_petals_product']);
		
		add_filter('woocommerce_get_settings_point-of-sale', [$this, 'pos_settings'], 10, 2);
	}
	
	function pos_settings($settings, $current_section){
	    error_log($current_section);
	    if ( $current_section == '' ) {
	        error_log(wp_json_encode($settings));
    		$settings[] = [
    			'title' => __('PDF Defaults', 'bakkbone-florist-companion'),
    			'type'  => 'title',
    			'desc'  => __('These settings apply to FloristPress-generated PDFs', 'bakkbone-florist-companion'),
    			'id'    => 'bkf_pdf_settings'
    		];
    		$settings[] = [
    			'title'		=> __('Page Size', 'bakkbone-florist-companion'),
    			'type'		=> 'select',
    			'options'   => [
    				'a4'    	=> __('A4 (210mm x 297mm)', 'bakkbone-florist-companion'),
    				'legal'		=> __('Legal (8.5" x 14")', 'bakkbone-florist-companion'),
    				'letter'	=> __('Letter (8.5" x 11")', 'bakkbone-florist-companion'),
    				'tabloid'	=> __('Tabloid (11" x 17")', 'bakkbone-florist-companion')
    			],
    			'id'		=> 'bkf_pdf_setting[page_size]',
    		];
    		$settings[] = [
    			'title'		=> __('Invoice Title', 'bakkbone-florist-companion'),
    			'type'		=> 'text',
    			'desc'		=> __('Title for Invoice', 'bakkbone-florist-companion'),
    			'desc_tip'	=> true,
    			'id'		=> 'bkf_pdf_setting[inv_title]',
    		];
    		$settings[] = [
    			'title'		=> __('Invoice Text', 'bakkbone-florist-companion'),
    			'type'		=> 'text',
    			'desc'		=> __('Additional text for bottom of invoice', 'bakkbone-florist-companion'),
    			'desc_tip'	=> true,
    			'id'		=> 'bkf_pdf_setting[inv_text]',
    		];
    		$settings[] = [
    			'title'		=> __('Worksheet Title', 'bakkbone-florist-companion'),
    			'type'		=> 'text',
    			'desc'		=> __('Title for Worksheet', 'bakkbone-florist-companion'),
    			'desc_tip'	=> true,
    			'id'		=> 'bkf_pdf_setting[ws_title]',
    		];
    		$settings[] = [
    			'title'		=> __('Website Address', 'bakkbone-florist-companion'),
    			'type'		=> 'url',
    			'desc'		=> __('Website as it appears on invoices', 'bakkbone-florist-companion'),
    			'desc_tip'	=> true,
    			'id'		=> 'bkf_pdf_setting[inv_web]',
    		];
    		$settings[] = [
    			'title'		=> __('Tax ID Label', 'bakkbone-florist-companion'),
    			'type'		=> 'text',
    			'desc'		=> __('Label for Tax ID – eg. "ABN"', 'bakkbone-florist-companion'),
    			'desc_tip'	=> true,
    			'id'		=> 'bkf_pdf_setting[inv_tax_label]',
    		];
    		$settings[] = [
    			'title'		=> __('Tax ID', 'bakkbone-florist-companion'),
    			'type'		=> 'text',
    			'desc'		=> __("Your business' Tax ID as it appears on invoices", 'bakkbone-florist-companion'),
    			'desc_tip'	=> true,
    			'id'		=> 'bkf_pdf_setting[inv_tax_value]',
    		];
    		$settings[] = [
    			'type'	=> 'sectionend',
    			'id'	=> 'bkf_pdf_settings'
    		];
    		return $settings;
	    } else {
	        return $settings;
	    }
	}

	function get_own_sections()	{
		$sections = [
			''				=> __('General', 'bakkbone-florist-companion'),
			'localisation'	=> __('Localization', 'bakkbone-florist-companion')
		];
		if (get_option('bkf_features_setting')['petals_on']) {
			$sections['petals'] = __('Petals Network');
		}
		return $sections;
	}

	function woocommerce_admin_field_bkf_audio($value) {
		$tooltip_html = $value['desc_tip'] ? wc_help_tip($value['desc']) : '';
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
			'01-reveal.wav' => __BKF_URL__ . '/assets/audio/' . '01-reveal.wav',
			'02-bells.wav' => __BKF_URL__ . '/assets/audio/' . '02-bells.wav',
			'03-alert.wav' => __BKF_URL__ . '/assets/audio/' . '03-alert.wav',
			'04-phone.mp3' => __BKF_URL__ . '/assets/audio/' . '04-phone.mp3',
			'05-chime.mp3' => __BKF_URL__ . '/assets/audio/' . '05-chime.mp3',
			'06-message.mp3' => __BKF_URL__ . '/assets/audio/' . '06-message.mp3',
			'07-cute.mp3' => __BKF_URL__ . '/assets/audio/' . '07-cute.mp3',
			'08-roll.mp3' => __BKF_URL__ . '/assets/audio/' . '08-roll.mp3',
			'09-nit.mp3' => __BKF_URL__ . '/assets/audio/' . '09-nit.mp3'
		);
		if(!isset($value['value']) || $value['value'] == ''){
			$value['value'] = '01-reveal.wav';
		}
		?>
		<tr valign="top" class="bkf_audio"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
		<div style="display:flex;">
			<select id="bkf-notifier-audio" name="bkf_audio_setting[notifier_audio]" required onchange="bkfAudioSample()">
				<option value="" disabled><?php esc_html_e('Select a file...', 'bakkbone-florist-companion'); ?></option>
				<?php
				foreach($files as $file => $name){
					if($value['value'] == $file){
						echo '<option value="'.$file.'" selected>'.$name.'</option>';
					} else {
						echo '<option value="'.$file.'">'.$name.'</option>';
					}
				}
				?>
			</select>
			<button type="button" class="button button-secondary" onclick="bkfPlayNotifier()"><?php esc_html_e('Play sample of the selected file', 'bakkbone-florist-companion'); ?></button>
		</div>
	<audio id="bkf-notifier-audio-sample" src="<?php echo $file_urls[$value['value']]; ?>"></audio></td></tr>
		<script type="text/javascript">
			function bkfAudioSample(){
				const files = [
					<?php foreach($file_urls as $val => $url){ echo '{ value: "'.$val.'", url: "'.$url.'" }, '; } ?>
				];
				var value = jQuery("#bkf-notifier-audio").val();
				var result = files.find(item => item.value === value);
				var audio = document.getElementById("bkf-notifier-audio-sample");
				audio.src = result.url;
				audio.load();
			}
			function bkfPlayNotifier(){
				var audio = document.getElementById("bkf-notifier-audio-sample");
				audio.loop = false;
				audio.play();
			}
			</script>
		<?php
	}

	function woocommerce_admin_field_petals_url($value) {
		$url = admin_url('admin-ajax.php?action=petals_outbound');
		$tooltip_html = $value['desc_tip'] ? wc_help_tip($value['desc']) : '';
		?>
		<tr valign="top" class="petals_url"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<input disabled class="<?php echo esc_attr($value['class']); ?>" style="<?php echo esc_attr($value['css']); ?>" id="bkfCopy" type="text" value="<?php echo $url; ?>" /><div class="bkfptooltip" style="width:100%;"><button type="button" id="bkfCopyBtn" class="button button-small"><?php esc_html_e('Copy to clipboard', 'bakkbone-florist-companion'); ?></button><span class="bkfptooltiptext" id="bkfpTooltip"><?php esc_html_e('Click to copy to clipboard', 'bakkbone-florist-companion'); ?></span></div>
				<p class="description"><?php echo wp_kses_post(__('Provide the link above to the Petals <a href="mailto:eflorist@petalsnetwork.com?subject=XML Opt-In&body=Shop Name: %0D%0AMy Full Name: %0D%0APhone: %0D%0AEmail: %0D%0AMember Number: %0D%0AXML Link: %0D%0A%0D%0APlease opt my store into XML orders alongside the Exchange, using the details above.">eFlorist Team</a>, requesting to <em>opt in to XML orders alongside the Exchange to the link provided</em>. The link can be copied to your clipboard by simply clicking the link above.', 'bakkbone-florist-companion')); ?></p>
				<p class="description"><?php echo wp_kses_post(__('<strong>Please note:</strong> The integration will not work unless all other fields on this page are completed <em>and</em> the request is sent to Petals as per the above.', 'bakkbone-florist-companion')); ?></p>
			</td>
		</tr>
		<?php
	}

	function woocommerce_admin_field_petals_category($value) {
		$args = array(
			"show_option_none" => __("Select a Category...", 'bakkbone-florist-companion'),
			"option_none_value" => "",
			"value" => "term_id",
			"hierarchical" => true,
			"taxonomy" => "product_cat",
			"show_count" => false,
			"hide_empty" => false,
			"echo" => true,
			"id" => "bkf-petals-cat",
			"name" => "bkf_petals_product_setting[cat]",
			"selected" => $value['value'],
			"class" => "postform form-control"
		);
		$tooltip_html = $value['desc_tip'] ? wc_help_tip($value['desc']) : '';
		?>
		<tr valign="top" class="petals_category"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<?php wp_dropdown_categories($args); ?>
			</td>
		</tr>
		<?php
	}

	function woocommerce_admin_field_petals_product($value) {
		$tooltip_html = $value['desc_tip'] ? wc_help_tip($value['desc']) : '';

		if(empty(get_option('bkf_petals_product_setting')["cat"])){
			$productdisabled = " disabled";
		} else {
			$productdisabled = "";
		}
		?>
		<tr valign="top" class="petals_product"<?php echo $value['row_class'] ? ' class="' . esc_attr( $value['row_class'] ) . '"' : '' ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
			<select id="bkf-petals-product" class="form-control" name="bkf_petals_product_setting[product]"<?php echo $productdisabled ?>>
			<?php
		$orderby = 'term_order';
		$order = 'asc';
		$category = get_terms('product_cat', array("include" => get_option('bkf_petals_product_setting')["cat"]));
		$cat = $category[0]->slug;
		$args = array(
			'category'	=> [$cat],
			'orderby'	=> $orderby,
			'order'		=> $order,
			'limit'		=> '-1',
			);
		$products = wc_get_products( $args );
		if(empty($value['value'])) {
			echo "<option value=\"\" selected disabled>".esc_html__('Select a Product...', 'bakkbone-florist-companion')."</option>";
		} else {
			echo "<option value=\"\" disabled>".esc_html__('Select a Product...', 'bakkbone-florist-companion')."</option>";
		}
		foreach($products as $key => $product){
			if($value['value'] == $product->get_id()){
				echo "<option selected value=\"" . $product->get_id() . "\">" . $product->get_name() . "</option>";
			} else {
				echo "<option value=\"" . $product->get_id() . "\">" . $product->get_name() . "</option>";
			}
		}
		?>
		</select>
		<?php
		if(empty($value['value']) && !empty(get_option('bkf_petals_product_setting')["cat"])) {
			$nonce = wp_create_nonce("bkf");
			$ajaxurl = admin_url('admin-ajax.php?action=bkf_cpp&nonce='.$nonce);
		?><a data-nonce="<?php echo $nonce ?>" href="<?php echo $ajaxurl ?>"><?php esc_html_e("Or click here to generate a compatible product in one click.", "bakkbone-florist-companion"); ?></a></td></tr><?php
		}
	}

	function get_settings_for_default_section() {
		$settings = [];
		$settings[] = [
			'title' => __('General Settings', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_general_settings'
		];
		$settings[] = [
			'title'		=> __('Card Message Length', 'bakkbone-florist-companion'),
			'type'		=> 'number',
			'desc'		=> __('Maximum number of characters (including spaces/punctuation) a customer will be able to enter in the Card Message field.', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_options_setting[card_length]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_general_settings'
		];
		$settings[] = [
			'title' => __('Other Features', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_other_features'
		];
		$settings[] = [
			'title'		=> __('Product Archives', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Display Short Description in product archives', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[excerpt_pa]',
			'value'		=> get_option('bkf_features_setting')['excerpt_pa'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Petals Network', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Enable Petals Network Integration', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[petals_on]',
			'value'		=> get_option('bkf_features_setting')['petals_on'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Disable Order Comments', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Disable the Order Comments field (freetext field at checkout for order notes)', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[disable_order_comments]',
			'value'		=> get_option('bkf_features_setting')['disable_order_comments'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Enable Order Notifier', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Enable the Order Notifier on the Orders List', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[order_notifier]',
			'value'		=> get_option('bkf_features_setting')['order_notifier'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Confirm Email', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Require email confirmation at checkout for logged-out users', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[confirm_email]',
			'value'		=> get_option('bkf_features_setting')['confirm_email'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Auto Process Orders', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Automatically change all orders to Processed', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[autoprocess]',
			'value'		=> get_option('bkf_features_setting')['autoprocess'] ? 'yes' : 'no',
		];
		$settings[] = [
			'title'		=> __('Admin Bar', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Add WooCommerce settings to the Admin Bar', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_features_setting[settingsbar]',
			'value'		=> get_option('bkf_features_setting')['settingsbar'] ? 'yes' : 'no',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_other_features'
		];
		$settings[] = [
			'title' => __('Audio', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'	=> __('These settings relate to the audio for the Order Notifier on the Orders list screen.', 'bakkbone-florist-companion'),
			'id'    => 'bkf_audio_settings'
		];
		$settings[] = [
			'title'		=> __('Audio File', 'bakkbone-florist-companion'),
			'type'		=> 'bkf_audio',
			'desc'		=> __('The audio to be played when a new order is detected.', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_audio_setting[notifier_audio]'
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_audio_settings'
		];
		$settings[] = [
			'title' => __('Advanced Settings', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_advanced'
		];
		$settings[] = [
			'title'		=> __('Purge on Deactivation', 'bakkbone-florist_companion'),
			'type'		=> 'checkbox',
			'desc'		=> __('Purge all data from the database on deactivation', 'bakkbone-florist-companion'),
			'id'		=> 'bkf_advanced_setting[deactivation_purge]',
			'value'		=> get_option('bkf_advanced_setting')['deactivation_purge'] ? 'yes' : 'no',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_advanced'
		];
		return apply_filters( 'bkf_general_settings', $settings );
	}

	function get_settings_for_pdf_section() {
		$settings = [];
		$settings[] = [
			'title' => __('Defaults', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'  => __('These settings apply to all PDFs', 'bakkbone-florist-companion'),
			'id'    => 'bkf_pdf_settings'
		];
		$settings[] = [
			'title'		=> __('Page Size', 'bakkbone-florist-companion'),
			'type'		=> 'select',
			'options'   => [
				'a4'    	=> __('A4 (210mm x 297mm)', 'bakkbone-florist-companion'),
				'legal'		=> __('Legal (8.5" x 14")', 'bakkbone-florist-companion'),
				'letter'	=> __('Letter (8.5" x 11")', 'bakkbone-florist-companion'),
				'tabloid'	=> __('Tabloid (11" x 17")', 'bakkbone-florist-companion')
			],
			'id'		=> 'bkf_pdf_setting[page_size]',
		];
		$settings[] = [
			'title'		=> __('Invoice Title', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Title for Invoice', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_title]',
		];
		$settings[] = [
			'title'		=> __('Invoice Text', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Additional text for bottom of invoice', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_text]',
		];
		$settings[] = [
			'title'		=> __('Worksheet Title', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Title for Worksheet', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[ws_title]',
		];
		$settings[] = [
			'title'		=> __('Store Name', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Store Name as it appears on invoices', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_sn]',
		];
		$settings[] = [
			'title'		=> __('Address Line 1', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Address as it appears on invoices', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_a1]',
		];
		$settings[] = [
			'title'		=> __('Address Line 2', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_pdf_setting[inv_a2]',
		];
		$settings[] = [
			'title'		=> get_option('bkf_localisation_setting')['global_label_suburb'],
			'type'		=> 'text',
			'desc'		=> sprintf(__('%s as it appears in invoices', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_suburb']),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_sub]',
		];
		$settings[] = [
			'title'		=> get_option('bkf_localisation_setting')['global_label_state'],
			'type'		=> 'text',
			'desc'		=> sprintf(__('%s as it appears in invoices', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_state']),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_state]',
		];
		$settings[] = [
			'title'		=> get_option('bkf_localisation_setting')['global_label_postcode'],
			'type'		=> 'text',
			'desc'		=> sprintf(__('%s as it appears in invoices', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_postcode']),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_pc]',
		];
		$settings[] = [
			'title'		=> get_option('bkf_localisation_setting')['global_label_telephone'],
			'type'		=> 'tel',
			'desc'		=> sprintf(__('%s as it appears in invoices', 'bakkbone-florist-companion'), get_option('bkf_localisation_setting')['global_label_telephone']),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_phone]',
		];
		$settings[] = [
			'title'		=> __('Email', 'bakkbone-florist-companion'),
			'type'		=> 'email',
			'desc'		=> __('Email as it appears on invoices', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_eml]',
		];
		$settings[] = [
			'title'		=> __('Website Address', 'bakkbone-florist-companion'),
			'type'		=> 'url',
			'desc'		=> __('Website as it appears on invoices', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_web]',
		];
		$settings[] = [
			'title'		=> __('Tax ID Label', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Label for Tax ID – eg. "ABN"', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_tax_label]',
		];
		$settings[] = [
			'title'		=> __('Tax ID', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __("Your business' Tax ID as it appears on invoices", 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_pdf_setting[inv_tax_value]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_pdf_settings'
		];
		return apply_filters( 'bkf_pdf_settings', $settings );
	}

	function get_settings_for_localisation_section() {
		$settings = [];
		$settings[] = [
			'title' => __('Global Fields', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_localisation_global_settings'
		];
		$settings[] = [
			'title'		=> __('Label: State/Territory', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[global_label_state]',
		];
		$settings[] = [
			'title'		=> __('Label: Suburb', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[global_label_suburb]',
		];
		$settings[] = [
			'title'		=> __('Label: Postcode', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[global_label_postcode]',
		];
		$settings[] = [
			'title'		=> __('Label: Country', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[global_label_country]',
		];
		$settings[] = [
			'title'		=> __('Label: Telephone', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[global_label_telephone]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_localisation_global_settings'
		];
		$settings[] = [
			'title' => __('Billing Fields', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_localisation_billing_settings'
		];
		$settings[] = [
			'title'		=> __('Label: Business Name', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[billing_label_business]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_localisation_billing_settings'
		];
		$settings[] = [
			'title' => __('Delivery Fields', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_localisation_delivery_settings'
		];
		$settings[] = [
			'title'		=> __('Label: Business Name', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[delivery_label_business]',
		];
		$settings[] = [
			'title'		=> __('Description: Business Name', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[delivery_description_business]',
		];
		$settings[] = [
			'title'		=> __('Label: Delivery Notes', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[delivery_label_notes]',
		];
		$settings[] = [
			'title'		=> __('Description: Delivery Notes', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'id'		=> 'bkf_localisation_setting[delivery_description_notes]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_localisation_delivery_settings'
		];
		$settings[] = [
			'title' => __('Additional Fields', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_localisation_additional_settings'
		];
		$settings[] = [
			'title'		=> __('Description: Card Message', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> sprintf(__('Insert <strong>%%s</strong> where your maximum characters will appear.<br><em>eg. Entering "Maximum %%s characters" will display "Maximum %d characters" at checkout.</em>',"bakkbone-florist-companion"), get_option('bkf_options_setting')['card_length']),
			'id'		=> 'bkf_localisation_setting[additional_description_cardmessage]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_localisation_additional_settings'
		];
		$settings[] = [
			'title' => __('Customer Experience Flow', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'id'    => 'bkf_localisation_cx_settings'
		];
		$settings[] = [
			'title'		=> __('Heading: Cart Cross-Sells', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __('Replaces the heading of the Cross-Sells section of the Cart page', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_localisation_setting[csheading]',
		];
		$settings[] = [
			'title'		=> __('Error: No-Ship', 'bakkbone-florist-companion'),
			'type'		=> 'text',
			'desc'		=> __("Displays at checkout if the delivery address' suburb is not serviced.", 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_localisation_setting[noship]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_localisation_cx_settings'
		];
		return apply_filters( 'bkf_localisation_settings', $settings );
	}

	function get_settings_for_petals_section() {
		$settings = [];
		$settings[] = [
			'title' => __('Petals Network Integration', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'	=> __('Your site will use this information to communicate with Petals.', 'bakkbone-florist-companion'),
			'id'    => 'bkf_petals_settings'
		];
		$settings[] = [
			'title'		=> __('Member Number', 'bakkbone-florist-companion'),
			'type'		=> 'number',
			'desc'		=> __('Your Petals Exchange member number.', 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_petals_setting[mn]',
		];
		$settings[] = [
			'title'		=> __('Password', 'bakkbone-florist-companion'),
			'type'		=> 'password',
			'desc'		=> __("Your Petals Exchange password. This is encrypted before being stored in your site's database, and is used only to communicate directly with Petals Network.", 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'value'		=> esc_attr(base64_decode(get_option('bkf_petals_setting')['ppw'])),
			'id'		=> 'bkf_petals_setting[ppw]',
		];
		$settings[] = [
			'title'		=> __('URL', 'bakkbone-florist-companion'),
			'type'		=> 'petals_url',
			'desc'		=> __("Your Petals Exchange password. This is encrypted before being stored in your site's database, and is used only to communicate directly with Petals Network.", 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'value'		=> esc_attr(base64_decode(get_option('bkf_petals_setting')['ppw'])),
			'id'		=> 'bkfCopy',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_petals_settings'
		];
		$settings[] = [
			'title' => __('Outbound Order Product', 'bakkbone-florist-companion'),
			'type'  => 'title',
			'desc'	=> __('These settings relate to the WooCommerce product that will be used when an order from Petals is received.', 'bakkbone-florist-companion'),
			'id'    => 'bkf_petals_order_settings'
		];
		$settings[] = [
			'title'		=> __('Product Category', 'bakkbone-florist-companion'),
			'type'		=> 'petals_category',
			'desc'		=> __("Please select the category which will contain the product.", 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_petals_product_setting[cat]',
		];
		$settings[] = [
			'title'		=> __('Product', 'bakkbone-florist-companion'),
			'type'		=> 'petals_product',
			'desc'		=> __("Select a category above first, and Save Changes, then this option will become available.", 'bakkbone-florist-companion'),
			'desc_tip'	=> true,
			'id'		=> 'bkf_petals_product_setting[product]',
		];
		$settings[] = [
			'type'	=> 'sectionend',
			'id'	=> 'bkf_petals_order_settings'
		];
		return $settings;
	}
}

new BKF_Settings;