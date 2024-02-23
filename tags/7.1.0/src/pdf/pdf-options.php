<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_PDF_Options
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Dompdf\Dompdf;

class BKF_PDF_Options{

	private $bkf_pdf_setting = [];

	function __construct(){
		global $bkf_pdf_version;
		$bkf_pdf_version = 1;
		add_action("plugins_loaded", [$this, "pdf_version_check"]);
		$this->bkf_pdf_setting = get_option("bkf_pdf_setting");
		add_action('admin_footer', [$this, 'footer']);
		add_filter('woocommerce_email_attachments', [$this, 'bkf_attach_pdf_to_emails'], 10, 4 );
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
	
	function footer(){
		$wsnonce = wp_create_nonce("bkf");
		$wsurl = admin_url( 'admin-ajax.php?action=bkf_dw' . '&nonce=' . $wsnonce . '&order_id=' );
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

	function add_pdf_downloads( $order ) {
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$wstitle = get_option('bkf_pdf_setting')['ws_title'];
		$order_id = $order->get_id();
		$petalson = $order->get_meta('_petals_on',true);
		$nonce = wp_create_nonce("bkf");
		$invurl = admin_url( 'admin-ajax.php?action=bkf_di&order_id=' . $order_id . '&nonce=' . $nonce );
		$wsurl = admin_url( 'admin-ajax.php?action=bkf_dw&order_id=' . $order_id . '&nonce=' . $nonce );
		if($petalson == null){
			echo '<div class="form-field form-field-wide wc-customer-user" style="display:flex;gap:5px;"><a href="'.$wsurl.'"><button class="button button-primary" type="button">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $wstitle)).'</button></a><a href="'.$invurl.'"><button class="button button-secondary" type="button">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $invtitle)).'</button></a></div>';
		} else {
			echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$wsurl.'"><button class="button button-primary" type="button">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $wstitle)).'</button></a></p>';
		}
	}
	
	function pdf_thankyou ( $order ) {
		$order_id = $order->get_id();
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$invnonce = wp_create_nonce("bkf");
		$invurl = admin_url( 'admin-ajax.php?action=bkf_di&order_id=' . $order_id . '&nonce=' . $invnonce );
		echo '<p class="form-field form-field-wide wc-customer-user"><a href="'.$invurl.'">'.esc_html(sprintf(__('Download %s', 'bakkbone-florist-companion'), $invtitle)).'</a></p>';	
	}
	
	function order_actions($actions, $order) {
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$actions['send_order_details'] = esc_html(sprintf(__('Send %s to customer', 'bakkbone-florist-companion'), $invtitle));
		return $actions;
	}
	
}