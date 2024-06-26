<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_PDF_Options
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Ah, sweet silence.");
use Dompdf\Dompdf;

class BKF_PDF_Options{

	private $bkf_pdf_setting = [];
	private $htmlplaceholder = "";
	private $htmltags = "";

	function __construct(){
		global $bkf_pdf_version;
		$bkf_pdf_version = 1;
		add_action("plugins_loaded", [$this, "pdf_version_check"]);
		$this->bkf_pdf_setting = get_option("bkf_pdf_setting");
		add_action('admin_footer', [$this, 'header']);
		add_filter('woocommerce_email_attachments', [$this, 'bkf_attach_pdf_to_emails'], 10, 4 );
		add_action("admin_menu", [$this, "bkf_pdf_menu"], 20);
		add_action("admin_init", [$this, "bkfAddPdfPageInit"]);
		add_action('woocommerce_admin_order_data_after_order_details', [$this, 'add_pdf_downloads']);
		add_filter('woocommerce_order_details_before_order_table', [$this, 'pdf_thankyou'], PHP_INT_MAX , 1 );
		add_filter('woocommerce_order_actions', [$this, 'order_actions'], 10, 2);
	}
	
	function pdf_version_check(){
		global $bkf_pdf_version;
		$current_local = get_option('bkf_pdf_version',0);
		if($current_local < 1){
			$currentsettings = $this->bkf_pdf_setting;
			$currentsettings['page_size'] = 'a4';
			update_option('bkf_pdf_setting', $currentsettings);
		}
	}
	
	function header(){
		$wsnonce = wp_create_nonce("bkf");
		$wsurl = admin_url( 'admin-ajax.php?action=bkfdw' . '&nonce=' . $wsnonce . '&order_id=' );
		?>
		<script type="text/javascript" id="bkf_dw">
			jQuery(document).ready( function($){
				urlObj = new URL(window.location.href);
				isDwRequest = urlObj.searchParams.has('bkf_dw');
				if (isDwRequest){
					orderNumber = urlObj.searchParams.get('bkf_dw');
					url = "<?php echo $wsurl; ?>" + orderNumber;
					window.location.href = url;
					urlObj.searchParams.delete("bkf_dw")
					window.location.href = urlObj.toString();
				}
			});
		</script>
		<?php
	}
	
	function bkf_attach_pdf_to_emails( $attachments, $email_id, $order, $email ) {
		$email_ids = array( 'customer_invoice', 'customer_processing_order', 'customer_completed_order' );
		if ( in_array ( $email_id, $email_ids ) ) {
			$invtitle = get_option('bkf_pdf_setting')['inv_title'];
			$order_id = $order->get_id();
			$pdf = new BKF_PDF_Core();
			$thepdf = $pdf->invoice($order_id);
			$thispdf = $thepdf->output();
			$upload_dir = wp_upload_dir();
			$filename = $upload_dir['basedir'].'/bkfpdf/'.$invtitle.'_'.$order_id.'.pdf';
			$pdfdir = dirname($filename);
			if (!is_dir($pdfdir))
			{
				mkdir($pdfdir, 0755, true);
			}
			if(file_exists($filename)){
				unlink($filename);
			}
			$afile = fopen($filename, "x+");
			fwrite($afile, $thispdf);
			fclose($afile);
			$attachments[] = $filename;
		}
		$email_ids = array( 'new_order', 'petals_new' );
		if ( in_array ( $email_id, $email_ids ) ) {
			$wstitle = get_option('bkf_pdf_setting')['ws_title'];
			$order_id = $order->get_id();
			$pdf = new BKF_PDF_Core();
			$thepdf = $pdf->worksheet($order_id);
			$thispdf = $thepdf->output();
			$upload_dir = wp_upload_dir();
			$filename = $upload_dir['basedir'].'/bkfpdf/'.$wstitle.'_'.$order_id.'.pdf';
			$pdfdir = dirname($filename);
			if (!is_dir($pdfdir))
			{
				mkdir($pdfdir, 0755, true);
			}
			if(file_exists($filename)){
				unlink($filename);
			}
			$afile = fopen($filename, "x+");
			fwrite($afile, $thispdf);
			fclose($afile);
			$attachments[] = $filename;
		}
		return $attachments;
	}

	function bkf_pdf_menu(){
		$admin_page = add_submenu_page(
		"bkf_options",
		__("PDF Invoices and Worksheets","bakkbone-florist-companion"),
		__("PDFs","bakkbone-florist-companion"),
		"manage_woocommerce",
		"bkf_pdf",
		[$this, 'bkf_pdf_settings_page'],
		20
		);
		add_action( 'load-'.$admin_page, [$this, 'bkf_pdf_help_tab'] );
	}
	
	function bkf_pdf_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_pdf_help';
		$callback = [$this, 'bkf_pdf_help'];
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => __('Documentation','bakkbone-florist-companion'),
		   'callback' => $callback
		) );	
	}
	
	function bkf_pdf_help(){
		?>
		<h2><?php esc_html_e('View documentation for this page at: ','bakkbone-florist-companion'); ?></h2>
			<a href="https://docs.floristpress.org/florist-options/pdfs/" target="_blank">https://docs.floristpress.org/florist-options/pdfs/</a>
		<?php
	}
	
	function bkf_pdf_settings_page(){
		$this->bkf_pdf_setting = get_option("bkf_pdf_setting");

		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php esc_html_e("PDF Settings","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php settings_fields("bkf_pdf_options_group"); ?>
						<?php do_settings_sections("bkf-pdf"); ?>
						<?php submit_button(__('Save All Changes', 'bakkbone-florist-companion'), 'primary large', 'submit', true, array('id' => 'pdf_submit') ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	function bkfAddPdfPageInit(){
				
		register_setting(
			"bkf_pdf_options_group",
			"bkf_pdf_setting",
			[$this, 'bkfAddPdfOptionsSanitize']
		);

		add_settings_section(
			"bkf_pdf_global_section",
			__("Defaults","bakkbone-florist-companion"),
			[$this, 'bkfPdfGlobalInfo'],
			"bkf-pdf"
		);

		add_settings_field(
			"page_size",
			__("Page Size","bakkbone-florist-companion"),
			[$this, 'bkfPageSizeCallback'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_title",
			__("Invoice Title","bakkbone-florist-companion"),
			[$this, 'bkfInvTitleCallback'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_text",
			__("Invoice Text","bakkbone-florist-companion"),
			[$this, 'bkfInvTextCallback'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"ws_title",
			__("Worksheet Title","bakkbone-florist-companion"),
			[$this, 'bkfWsTitleCallback'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_sn",
			__("Store Name","bakkbone-florist-companion"),
			[$this, 'StoreName'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_a1",
			__("Address Line 1","bakkbone-florist-companion"),
			[$this, 'Address1'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_a2",
			__("Address Line 2","bakkbone-florist-companion"),
			[$this, 'Address2'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_sub",
			__("Suburb","bakkbone-florist-companion"),
			[$this, 'Suburb'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_state",
			__("State/Territory","bakkbone-florist-companion"),
			[$this, 'State'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_pc",
			__("Postcode","bakkbone-florist-companion"),
			[$this, 'Postcode'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_phone",
			__("Phone","bakkbone-florist-companion"),
			[$this, 'Phone'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_eml",
			__("Email Address","bakkbone-florist-companion"),
			[$this, 'Email'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_web",
			__("Website Address","bakkbone-florist-companion"),
			[$this, 'Website'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_tax_label",
			__("Tax ID Label","bakkbone-florist-companion"),
			[$this, 'TaxIDLabel'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_tax_value",
			__("Tax ID","bakkbone-florist-companion"),
			[$this, 'TaxIDValue'],
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
	}
	
	function bkfAddPdfOptionsSanitize($input){
		$new_input = [];

		if(isset($input["page_size"])){
			$new_input["page_size"] = sanitize_text_field($input["page_size"]);
		}
		
		if(isset($input["inv_text"])){
			$new_input["inv_text"] = sanitize_text_field($input["inv_text"]);
		}
		
		if(isset($input["inv_title"])){
			$new_input["inv_title"] = sanitize_text_field($input["inv_title"]);
		}
		
		if(isset($input["ws_title"])){
			$new_input["ws_title"] = sanitize_text_field($input["ws_title"]);
		}

		if(isset($input["inv_sn"])){
			$new_input["inv_sn"] = sanitize_text_field($input["inv_sn"]);
		}
		
		if(isset($input["inv_a1"])){
			$new_input["inv_a1"] = sanitize_text_field($input["inv_a1"]);
		}

		if(isset($input["inv_a2"])){
			$new_input["inv_a2"] = sanitize_text_field($input["inv_a2"]);
		}

		if(isset($input["inv_sub"])){
			$new_input["inv_sub"] = sanitize_text_field($input["inv_sub"]);
		}

		if(isset($input["inv_state"])){
			$new_input["inv_state"] = sanitize_text_field($input["inv_state"]);
		}

		if(isset($input["inv_pc"])){
			$new_input["inv_pc"] = sanitize_text_field($input["inv_pc"]);
		}

		if(isset($input["inv_phone"])){
			$new_input["inv_phone"] = sanitize_text_field($input["inv_phone"]);
		}

		if(isset($input["inv_eml"])){
			$new_input["inv_eml"] = sanitize_text_field($input["inv_eml"]);
		}

		if(isset($input["inv_web"])){
			$new_input["inv_web"] = sanitize_text_field($input["inv_web"]);
		}
		
		if(isset($input["inv_tax_label"])){
			$new_input["inv_tax_label"] = sanitize_text_field($input["inv_tax_label"]);
		}

		if(isset($input["inv_tax_value"])){
			$new_input["inv_tax_value"] = sanitize_text_field($input["inv_tax_value"]);
		}
		
		return $new_input;
	}

	function bkfPdfGlobalInfo(){
		echo '<p>';
		esc_html_e("These settings apply to all PDFs", "bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfPageSizeCallback(){
		if(isset($this->bkf_pdf_setting["page_size"])){
			$value = esc_attr($this->bkf_pdf_setting["page_size"]);
		} else {
			$value = 'a4';
		}
		
		$sizes = [
			'a4'		=> __('A4 (210mm x 297mm)', 'bakkbone-florist-companion'),
			'legal'	=> __('Legal (8.5" x 14")', 'bakkbone-florist-companion'),
			'letter'	=> __('Letter (8.5" x 11")', 'bakkbone-florist-companion'),
			'tabloid'	=> __('Tabloid (11" x 17")', 'bakkbone-florist-companion'),
		];
		
		?>
		<select class="bkf-form-control regular-text select2" id="page-size" name="bkf_pdf_setting[page_size]" required>
			<?php
			foreach($sizes as $size => $title){
				if($size == $value){
					echo '<option value="'.$size.'" selected>'.$title.'</option>';
				} else {
					echo '<option value="'.$size.'">'.$title.'</option>';
				}
			}
			?>
		</select>
		<script>
			jQuery(document).ready(function( $ ) {
				jQuery('.select2').select2({
					dropdownCssClass: ['bkf-font', 'bkf-select2']
				});
			});
		</script>
		<?php
	}

	function bkfInvTitleCallback(){
		
		if(isset($this->bkf_pdf_setting["inv_title"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_title"]);
		} else {
			$value = __('Tax Invoice', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-title" name="bkf_pdf_setting[inv_title]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Title for Invoice","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function bkfInvTextCallback(){
		
		if(isset($this->bkf_pdf_setting["inv_text"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_text"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="inv-text" name="bkf_pdf_setting[inv_text]" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Additional text for bottom of invoice","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfWsTitleCallback(){
		
		if(isset($this->bkf_pdf_setting["ws_title"])){
			$value = esc_attr($this->bkf_pdf_setting["ws_title"]);
		} else {
			$value = __('Worksheet', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="ws-title" name="bkf_pdf_setting[ws_title]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Title for Worksheet","bakkbone-florist-companion") ?></p>
		<?php
	}

	function StoreName(){
		
		if(isset($this->bkf_pdf_setting["inv_sn"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_sn"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-sn" name="bkf_pdf_setting[inv_sn]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Store Name as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Address1(){
		
		if(isset($this->bkf_pdf_setting["inv_a1"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_a1"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-a1" name="bkf_pdf_setting[inv_a1]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Address as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Address2(){
		
		if(isset($this->bkf_pdf_setting["inv_a2"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_a2"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-a2" name="bkf_pdf_setting[inv_a2]" value="<?php echo $value; ?>" />
		<?php
	}

	function Suburb(){
		
		if(isset($this->bkf_pdf_setting["inv_sub"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_sub"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-sub" name="bkf_pdf_setting[inv_sub]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Suburb as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function State(){
		
		if(isset($this->bkf_pdf_setting["inv_state"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_state"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-state" name="bkf_pdf_setting[inv_state]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("State/Territory as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Postcode(){
		
		if(isset($this->bkf_pdf_setting["inv_pc"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_pc"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-pc" name="bkf_pdf_setting[inv_pc]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Postcode as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Phone(){
		
		if(isset($this->bkf_pdf_setting["inv_phone"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_phone"]);
		} else {
			$value = "";
		}
		?>
		<input type="tel" class="bkf-form-control regular-text" id="inv-phone" name="bkf_pdf_setting[inv_phone]" value="<?php echo $value; ?>" />
		<p class="description"><?php esc_html_e("Phone as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Email(){
		
		if(isset($this->bkf_pdf_setting["inv_eml"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_eml"]);
		} else {
			$value = "";
		}
		?>
		<input type="email" class="bkf-form-control regular-text" id="inv-eml" name="bkf_pdf_setting[inv_eml]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Email as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Website(){
		
		if(isset($this->bkf_pdf_setting["inv_web"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_web"]);
		} else {
			$value = home_url();
		}
		?>
		<input type="url" class="bkf-form-control regular-text" id="inv-web" name="bkf_pdf_setting[inv_web]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Website as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function TaxIDLabel(){
		
		if(isset($this->bkf_pdf_setting["inv_tax_label"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_tax_label"]);
		} else {
			$value = __('ABN', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-tax-label" name="bkf_pdf_setting[inv_tax_label]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Label for Tax ID - eg. \"ABN\"","bakkbone-florist-companion") ?></p>
		<?php
	}

	function TaxIDValue(){
		
		if(isset($this->bkf_pdf_setting["inv_tax_value"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_tax_value"]);
		} else {
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-tax-value" name="bkf_pdf_setting[inv_tax_value]" value="<?php echo $value; ?>" required />
		<p class="description"><?php esc_html_e("Your business' Tax ID as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function add_pdf_downloads( $order ) {
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$wstitle = get_option('bkf_pdf_setting')['ws_title'];
		$order_id = $order->get_id();
		$petalson = $order->get_meta('_petals_on',true);
		$nonce = wp_create_nonce("bkf");
		$invurl = admin_url( 'admin-ajax.php?action=bkfdi&order_id=' . $order_id . '&nonce=' . $nonce );
		$wsurl = admin_url( 'admin-ajax.php?action=bkfdw&order_id=' . $order_id . '&nonce=' . $nonce );
		if($petalson == null){
			echo '<div class="form-field form-field-wide wc-customer-user" style="display:flex;gap:5px;"><a href="'.$wsurl.'"><button class="button button-primary">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $wstitle)).'</button></a><a href="'.$invurl.'"><button class="button button-secondary">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $invtitle)).'</button></a></div>';
		} else {
			echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$wsurl.'"><button class="button button-primary">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $wstitle)).'</button></a></p>';
		}
	}
	
	function pdf_thankyou ( $order ) {
		$order_id = $order->get_id();
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$invnonce = wp_create_nonce("bkf");
		$invurl = admin_url( 'admin-ajax.php?action=bkfdi&order_id=' . $order_id . '&nonce=' . $invnonce );
		echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$invurl.'">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $invtitle)).'</a></p>';	
	}
	
	function order_actions($actions, $order) {
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$actions['send_order_details'] = esc_html(sprintf(__('Send %s to customer', 'bakkbone-florist-companion'), $invtitle));
		return $actions;
	}
	
}