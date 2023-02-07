<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPdfActions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");
use Dompdf\Dompdf;
class BkfPdfActions{
	
	function __construct(){
	    add_filter( 'woocommerce_admin_order_actions', array( $this, 'invoiceactions' ), PHP_INT_MAX, 2 );
		add_action( 'wp_ajax_bkf_invoice_pdf_download', array($this, 'bkf_invoice_pdf_download'));
		add_action( 'wp_ajax_bkf_worksheet_pdf_download', array($this, 'bkf_worksheet_pdf_download'));
	    add_action( 'admin_head', array( $this, 'actionscss' ), 10 );
	}
	
	function invoiceactions($actions, $order){
        $order_id = $order->get_id();
		$petalson = get_post_meta($order_id,'_petals_on',true);
		$invnonce = wp_create_nonce("bkf_invoice_pdf");
		$wsnonce = wp_create_nonce("bkf_worksheet_pdf");
		if($petalson == null){
			$default_actions['invoice'] = array(
			    'url'    => admin_url( 'admin-ajax.php?action=bkf_invoice_pdf_download&order_id=' . $order_id . '&nonce=' . $invnonce ),
				'name'   => get_option('bkf_pdf_setting')['inv_title'],
				'action' => 'view invoice'
				);
			$default_actions['worksheet'] = array(
			    'url'    => admin_url( 'admin-ajax.php?action=bkf_worksheet_pdf_download&order_id=' . $order_id . '&nonce=' . $wsnonce ),
				'name'   => get_option('bkf_pdf_setting')['ws_title'],
				'action' => 'view worksheet'
				);
        
	        $actions = array_merge( $default_actions, $actions );
	        return $actions;		
		} else {
			$default_actions['worksheet'] = array(
			    'url'    => admin_url( 'admin-ajax.php?action=bkf_worksheet_pdf_download&order_id=' . $order_id . '&nonce=' . $wsnonce ),
				'name'   => get_option('bkf_pdf_setting')['ws_title'],
				'action' => 'view worksheet'
				);
        
	        $actions = array_merge( $default_actions, $actions );
	        return $actions;			
		}
	}
	
	function bkf_invoice_pdf_download(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'bkf_invoice_pdf')) {
          exit('No funny business please');
        }

		$order_id = $_REQUEST['order_id'];
		$invtitle = get_option('bkf_pdf_setting')['inv_title'];
		$pdf = new BkfPdf();
		$thepdf = $pdf->invoice($order_id);
		$thepdf->stream($invtitle.' #'.$order_id.'.pdf');
	}

	function bkf_worksheet_pdf_download(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'bkf_worksheet_pdf')) {
          exit('No funny business please');
        }

		$order_id = $_REQUEST['order_id'];
		$wstitle = get_option('bkf_pdf_setting')['ws_title'];
		$pdf = new BkfPdf();
		$thepdf = $pdf->worksheet($order_id);
		$thepdf->stream($wstitle.' #'.$order_id.'.pdf');
	}
	
	function actionscss() {
			echo '<style id="bkf_pdf_actions">
			.view.invoice::after {
			    color: #000099 !important;
			    content: "\f498" !important;
			}
			.view.worksheet::after {
			    color: #009900 !important;
			    content: "\f498" !important;
			}
				</style>
			';
		}
}