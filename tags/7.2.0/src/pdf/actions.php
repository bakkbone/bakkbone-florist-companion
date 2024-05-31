<?php
/**
 * @author BAKKBONE Australia
 * @package BKF_PDF_Actions
 * @license GNU General Public License (GPL) 3.0
**/

defined("__BKF_EXEC__") or die("Ah, sweet silence.");
use Dompdf\Dompdf;

class BKF_PDF_Actions{
	
	function __construct(){
		add_filter( 'woocommerce_admin_order_actions', [$this, 'invoiceactions'], PHP_INT_MAX, 2 );
		add_action( 'admin_head', [$this, 'actionscss'], 10 );
	}
	
	function invoiceactions($actions, $order){
		$order_id = $order->get_id();
		$petalson = $order->get_meta('_petals_on',true);
		$nonce = wp_create_nonce("bkf");
		if($petalson == null){
			$default_actions['invoice'] = array(
				'url'	=> admin_url( 'admin-ajax.php?action=bkf_di&order_id=' . $order_id . '&nonce=' . $nonce ),
				'name'   => get_option('bkf_pdf_setting')['inv_title'],
				'action' => 'view invoice'
				);
			$default_actions['worksheet'] = array(
				'url'	=> admin_url( 'admin-ajax.php?action=bkf_dw&order_id=' . $order_id . '&nonce=' . $nonce ),
				'name'   => get_option('bkf_pdf_setting')['ws_title'],
				'action' => 'view worksheet'
				);
		
			$actions = array_merge( $default_actions, $actions );
			return $actions;		
		} else {
			$default_actions['worksheet'] = array(
				'url'	=> admin_url( 'admin-ajax.php?action=bkf_dw&order_id=' . $order_id . '&nonce=' . $nonce ),
				'name'   => get_option('bkf_pdf_setting')['ws_title'],
				'action' => 'view worksheet'
				);
		
			$actions = array_merge( $default_actions, $actions );
			return $actions;			
		}
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