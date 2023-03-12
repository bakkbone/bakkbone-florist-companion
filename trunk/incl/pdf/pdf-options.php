<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPdfOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");
use Dompdf\Dompdf;
class BkfPdfOptions{

    private $bkf_pdf_setting = array();
	private $htmlplaceholder = "";
	private $htmltags = "";

    function __construct(){
        $this->bkf_pdf_setting = get_option("bkf_pdf_setting");
		add_filter('woocommerce_email_attachments', array($this,'bkf_attach_pdf_to_emails'), 10, 4 );
        add_action("admin_menu", array($this,"bkf_pdf_menu"),20);
        add_action("admin_init",array($this,"bkfAddPdfPageInit"));
		add_action("admin_footer",array($this,"bkfPdfAdminFooter"));
		add_action("admin_enqueue_scripts",array($this,"bkfPdfAdminEnqueueScripts"));
		add_action('woocommerce_admin_order_data_after_order_details',array($this,'add_pdf_downloads' ));
        add_filter('woocommerce_order_details_before_order_table', array($this, 'pdf_thankyou'), PHP_INT_MAX , 1 );
    }
	
	function bkf_attach_pdf_to_emails( $attachments, $email_id, $order, $email ) {
	    $email_ids = array( 'customer_invoice', 'customer_processing_order', 'customer_completed_order' );
	    if ( in_array ( $email_id, $email_ids ) ) {
			$invtitle = get_option('bkf_pdf_setting')['inv_title'];
			$order_id = $order->get_id();
			$pdf = new BkfPdf();
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
			$pdf = new BkfPdf();
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
        "manage_options",
        "bkf_pdf",
        array($this, "bkf_pdf_settings_page"),
        20
        );
		add_action( 'load-'.$admin_page, array($this, 'bkf_pdf_help_tab') );
    }
	
	function bkf_pdf_help_tab(){
		$screen = get_current_screen();
		$id = 'bkf_pdf_help';
		$callback = array($this, 'bkf_pdf_help');
		$screen->add_help_tab( array( 
		   'id' => $id,
		   'title' => BKF_HELP_TITLE,
		   'callback' => $callback
		) );	
	}
	
	function bkf_pdf_help(){
		?>
		<h2><?php echo BKF_HELP_SUBTITLE; ?></h2>
			<a href="https://docs.bkbn.au/v/bkf/florist-options/pdfs" target="_blank">https://docs.bkbn.au/v/bkf/florist-options/pdfs</a>
		<?php
	}
    
    function bkf_pdf_settings_page()
    {
        $this->bkf_pdf_setting = get_option("bkf_pdf_setting");

        ?>
        <div class="wrap">
            <div class="bkf-box">
            <h1><?php _e("PDF Settings","bakkbone-florist-companion") ?></h1>
                <div class="inside">
                    <form method="post" action="options.php">
                        <?php settings_fields("bkf_pdf_options_group"); ?>
                        <?php do_settings_sections("bkf-pdf"); ?>
                        <?php submit_button(SAVEALLCHANGESTEXT, 'primary large', 'submit', true, array('id' => 'pdf_submit') ); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
	function bkfAddPdfPageInit()
	{
		
		register_setting(
			"bkf_pdf_options_group",
			"bkf_pdf_setting",
			array($this,"bkfAddPdfOptionsSanitize")
		);

		add_settings_section(
			"bkf_pdf_global_section",
			__("Defaults","bakkbone-florist-companion"),
			array($this,"bkfPdfGlobalInfo"),
			"bkf-pdf"
		);
		
		add_settings_field(
			"inv_title",
			__("Invoice Title","bakkbone-florist-companion"),
			array($this,"bkfInvTitleCallback"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_text",
			__("Invoice Text","bakkbone-florist-companion"),
			array($this,"bkfInvTextCallback"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"ws_title",
			__("Worksheet Title","bakkbone-florist-companion"),
			array($this,"bkfWsTitleCallback"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_sn",
			__("Store Name","bakkbone-florist-companion"),
			array($this,"StoreName"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_a1",
			__("Address Line 1","bakkbone-florist-companion"),
			array($this,"Address1"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_a2",
			__("Address Line 2","bakkbone-florist-companion"),
			array($this,"Address2"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_sub",
			__("Suburb","bakkbone-florist-companion"),
			array($this,"Suburb"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_state",
			__("State/Territory","bakkbone-florist-companion"),
			array($this,"State"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_pc",
			__("Postcode","bakkbone-florist-companion"),
			array($this,"Postcode"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_phone",
			__("Phone","bakkbone-florist-companion"),
			array($this,"Phone"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_eml",
			__("Email Address","bakkbone-florist-companion"),
			array($this,"Email"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

		add_settings_field(
			"inv_web",
			__("Website Address","bakkbone-florist-companion"),
			array($this,"Website"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_tax_label",
			__("Tax ID Label","bakkbone-florist-companion"),
			array($this,"TaxIDLabel"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);
		
		add_settings_field(
			"inv_tax_value",
			__("Tax ID","bakkbone-florist-companion"),
			array($this,"TaxIDValue"),
			"bkf-pdf",
			"bkf_pdf_global_section"
		);

	}
	
	function bkfAddPdfOptionsSanitize($input)
	{
		$new_input = array();
		
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

	function bkfPdfGlobalInfo()
	{
		echo '<p>';
		_e("These settings apply to all PDFs", "bakkbone-florist-companion");
		echo '</p>';
	}

	function bkfInvTitleCallback(){
		
		if(isset($this->bkf_pdf_setting["inv_title"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_title"]);
		}else{
			$value = __('Tax Invoice', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-title" name="bkf_pdf_setting[inv_title]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Title for Invoice","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function bkfInvTextCallback(){
		
		if(isset($this->bkf_pdf_setting["inv_text"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_text"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control large-text" id="inv-text" name="bkf_pdf_setting[inv_text]" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Additional text for bottom of invoice","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfWsTitleCallback(){
		
		if(isset($this->bkf_pdf_setting["ws_title"])){
			$value = esc_attr($this->bkf_pdf_setting["ws_title"]);
		}else{
			$value = __('Worksheet', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="ws-title" name="bkf_pdf_setting[ws_title]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Title for Worksheet","bakkbone-florist-companion") ?></p>
		<?php
	}

	function StoreName(){
		
		if(isset($this->bkf_pdf_setting["inv_sn"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_sn"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-sn" name="bkf_pdf_setting[inv_sn]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Store Name as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Address1(){
		
		if(isset($this->bkf_pdf_setting["inv_a1"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_a1"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-a1" name="bkf_pdf_setting[inv_a1]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Address as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Address2(){
		
		if(isset($this->bkf_pdf_setting["inv_a2"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_a2"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-a2" name="bkf_pdf_setting[inv_a2]" value="<?php echo $value; ?>" />
		<?php
	}

	function Suburb(){
		
		if(isset($this->bkf_pdf_setting["inv_sub"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_sub"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-sub" name="bkf_pdf_setting[inv_sub]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Suburb as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function State(){
		
		if(isset($this->bkf_pdf_setting["inv_state"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_state"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-state" name="bkf_pdf_setting[inv_state]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("State/Territory as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Postcode(){
		
		if(isset($this->bkf_pdf_setting["inv_pc"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_pc"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-pc" name="bkf_pdf_setting[inv_pc]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Postcode as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Phone(){
		
		if(isset($this->bkf_pdf_setting["inv_phone"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_phone"]);
		}else{
			$value = "";
		}
		?>
		<input type="tel" class="bkf-form-control regular-text" id="inv-phone" name="bkf_pdf_setting[inv_phone]" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Phone as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Email(){
		
		if(isset($this->bkf_pdf_setting["inv_eml"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_eml"]);
		}else{
			$value = "";
		}
		?>
		<input type="email" class="bkf-form-control regular-text" id="inv-eml" name="bkf_pdf_setting[inv_eml]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Email as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function Website(){
		
		if(isset($this->bkf_pdf_setting["inv_web"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_web"]);
		}else{
			$value = home_url();
		}
		?>
		<input type="url" class="bkf-form-control regular-text" id="inv-web" name="bkf_pdf_setting[inv_web]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Website as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function TaxIDLabel(){
		
		if(isset($this->bkf_pdf_setting["inv_tax_label"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_tax_label"]);
		}else{
			$value = __('ABN', 'bakkbone-florist-companion');
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-tax-label" name="bkf_pdf_setting[inv_tax_label]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Label for Tax ID - eg. \"ABN\"","bakkbone-florist-companion") ?></p>
		<?php
	}

	function TaxIDValue(){
		
		if(isset($this->bkf_pdf_setting["inv_tax_value"])){
			$value = esc_attr($this->bkf_pdf_setting["inv_tax_value"]);
		}else{
			$value = "";
		}
		?>
		<input type="text" class="bkf-form-control regular-text" id="inv-tax-value" name="bkf_pdf_setting[inv_tax_value]" value="<?php echo $value; ?>" required />
		<p class="description"><?php _e("Your business' Tax ID as it appears on invoices","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfPdfAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_pdf")
		{

		}
	}
	
	function bkfPdfAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_pdf")
		{
		}
	}
	
	function add_pdf_downloads( $order ) {
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$wstitle = get_option('bkf_pdf_setting')['ws_title'];
        $order_id = $order->get_id();
		$petalson = get_post_meta($order_id,'_petals_on',true);
		$invnonce = wp_create_nonce("bkf_invoice_pdf");
		$invurl = admin_url( 'admin-ajax.php?action=bkf_invoice_pdf_download&order_id=' . $order_id . '&nonce=' . $invnonce );
		$wsnonce = wp_create_nonce("bkf_worksheet_pdf");
		$wsurl = admin_url( 'admin-ajax.php?action=bkf_worksheet_pdf_download&order_id=' . $order_id . '&nonce=' . $wsnonce );
		if($petalson == null){
			echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$invurl.'">'.DOWNLOADTEXT.' '.$invtitle.'</a></p>';
		}
		echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$wsurl.'">'.DOWNLOADTEXT.' '.$wstitle.'</a></p>';
	}
	
    function pdf_thankyou ( $order ) {
		$order_id = $order->get_id();
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$invnonce = wp_create_nonce("bkf_invoice_pdf");
		$invurl = admin_url( 'admin-ajax.php?action=bkf_invoice_pdf_download&order_id=' . $order_id . '&nonce=' . $invnonce );
		echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$invurl.'">'.DOWNLOADTEXT.' '.$invtitle.'</a></p>';
		
    }
    
}